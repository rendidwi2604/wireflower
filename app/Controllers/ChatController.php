<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ChatModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class ChatController extends Controller
{
    public function index(): void
    {
        if (!is_logged_in()) {
            $productId = max(0, (int) ($_GET['product_id'] ?? 0));
            $_SESSION['chat_return_to'] = 'chat.php' . ($productId ? '?product_id=' . $productId : '');
            require_login();
        }
        // The storefront always opens the shopper's own room, including when
        // an administrator is browsing products. The inbox has its own route.
        if (!empty($_GET['product_id'])) {
            $product = $this->model(ProductModel::class)->find((int) $_GET['product_id']);
            if (!$product || !$product['is_active']) {
                flash('warning', 'Produk tidak tersedia untuk dikirim.');
                redirect('chat.php');
            }
            $intent = bin2hex(random_bytes(24));
            $_SESSION['chat_intents'][$intent] = (int) $product['id'];
            $_SESSION['chat_intents'] = array_slice($_SESSION['chat_intents'], -20, null, true);
            redirect('chat.php?intent=' . $intent);
        }
        $intent = (string) ($_GET['intent'] ?? '');
        $pending = isset($_SESSION['chat_intents'][$intent]) ? $intent : '';
        $this->page(false, $pending);
    }

    public function admin(): void
    {
        require_login();
        if (!$this->canManageChats()) {
            redirect('chat.php');
        }
        $this->page(true);
    }

    private function canManageChats(): bool
    {
        $user = $this->model(UserModel::class)->find($this->userId());
        if (!$user) {
            return false;
        }
        // A previously cached admin role must not grant access after demotion.
        $_SESSION['role'] = $user['role'];
        return $user['role'] === 'admin';
    }

    private function page(bool $admin, string $pending = ''): void
    {
        $_SESSION['chat_csrf'] ??= bin2hex(random_bytes(32));
        $thread = null;
        $error = '';
        try {
            $chat = $this->model(ChatModel::class);
            $thread = $admin
                ? $chat->find((int) ($_GET['thread_id'] ?? 0))
                : $chat->forCustomer($this->userId());
            if ($admin && !empty($_GET['thread_id']) && !$thread) {
                http_response_code(404);
                $error = 'Percakapan tidak ditemukan.';
            }
        } catch (\Throwable $e) {
            error_log('[chat] ' . $e->getMessage());
            http_response_code(503);
            $error = 'Chat belum dapat dimuat. Silakan coba lagi nanti.';
        }
        $this->render('chat', [
            'page_title' => $admin ? 'Chat Pelanggan' : 'Chat Admin',
            'chat_admin' => $admin, 'thread' => $thread, 'chat_error' => $error,
            'pending_intent' => $pending, 'csrf' => $_SESSION['chat_csrf'],
        ], $admin ? 'admin' : 'site');
    }

    public function api(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        if (!is_logged_in()) {
            $this->json(['error' => 'Sesi berakhir. Silakan login kembali.'], 401);
            return;
        }
        $method = $_SERVER['REQUEST_METHOD'];
        if (!in_array($method, ['GET', 'POST'], true)) {
            header('Allow: GET, POST');
            $this->json(['error' => 'Metode tidak didukung.'], 405);
            return;
        }
        if ($method === 'POST' && (empty($_SESSION['chat_csrf']) || !hash_equals($_SESSION['chat_csrf'], (string) ($_POST['csrf'] ?? '')))) {
            $this->json(['error' => 'Sesi tidak valid. Muat ulang halaman.'], 403);
            return;
        }
        try {
            $chat = $this->model(ChatModel::class);
            $input = $method === 'POST' ? $_POST : $_GET;
            $context = (string) ($input['context'] ?? 'customer');
            if (!in_array($context, ['customer', 'admin'], true)) {
                throw new \InvalidArgumentException('Konteks chat tidak valid. Muat ulang halaman.');
            }
            $admin = $context === 'admin';
            if ($admin && !$this->canManageChats()) {
                $this->json(['error' => 'Akses ditolak.'], 403);
                return;
            }
            if ($method === 'GET' && ($_GET['action'] ?? '') === 'inbox') {
                if (!$admin) {
                    $this->json(['error' => 'Akses ditolak.'], 403);
                    return;
                }
                $this->json(['threads' => $chat->inbox()]);
                return;
            }
            $thread = $chat->find((int) ($input['thread_id'] ?? 0));
            if (!$thread || (!$admin && (int) $thread['customer_id'] !== $this->userId())) {
                $this->json(['error' => 'Percakapan tidak ditemukan.'], 404);
                return;
            }
            $role = $admin ? 'admin' : 'customer';
            if ($method === 'GET') {
                $messages = $chat->messages((int) $thread['id'], max(0, (int) ($_GET['after'] ?? 0)), max(0, (int) ($_GET['before'] ?? 0)));
                $this->json(['messages' => array_map([$this, 'serializeMessage'], $messages)]);
                return;
            }
            $action = (string) ($_POST['action'] ?? '');
            if ($action === 'read') {
                $chat->markRead((int) $thread['id'], $role, max(0, (int) ($_POST['through_id'] ?? 0)));
                $this->json(['ok' => true]);
                return;
            }
            $key = (string) ($_POST['request_key'] ?? '');
            if (!preg_match('/^[a-f0-9]{32,64}$/D', $key)) {
                throw new \InvalidArgumentException('Permintaan pesan tidak valid.');
            }
            $snapshot = null;
            $body = '';
            if ($action === 'product' && !$admin) {
                $id = $_SESSION['chat_intents'][$key] ?? 0;
                $product = $this->model(ProductModel::class)->find((int) $id);
                if (!$product || !$product['is_active']) {
                    throw new \InvalidArgumentException('Produk tidak tersedia. Kembali ke halaman produk untuk memulai chat.');
                }
                $snapshot = array_intersect_key($product, array_flip(['id', 'name', 'slug', 'price', 'stock', 'description', 'image']));
            } elseif ($action === 'send') {
                $body = trim((string) ($_POST['body'] ?? ''));
                if ($body === '' || mb_strlen($body) > 5000) {
                    throw new \InvalidArgumentException('Pesan harus berisi 1 sampai 5.000 karakter.');
                }
            } else {
                throw new \InvalidArgumentException('Aksi chat tidak valid.');
            }
            $message = $chat->send((int) $thread['id'], $this->userId(), $role, $body, $snapshot, $key);
            $this->json(['message' => $this->serializeMessage($message)]);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            error_log('[chat] ' . $e->getMessage());
            $this->json(['error' => 'Chat sedang tidak tersedia. Coba lagi sebentar.'], 503);
        }
    }

    private function serializeMessage(array $message): array
    {
        $product = $message['product_snapshot'] ? json_decode($message['product_snapshot'], true) : null;
        if ($product) {
            $product['url'] = site_url('produk.php?slug=' . rawurlencode($product['slug']));
            $product['image_url'] = asset_img($product['image'] ?? '', site_url('assets/img/logoWF.png'));
        }
        return [
            'id' => (int) $message['id'], 'body' => $message['body'], 'product' => $product,
            'sender_role' => $message['sender_role'], 'created_at' => $message['created_at'],
        ];
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
