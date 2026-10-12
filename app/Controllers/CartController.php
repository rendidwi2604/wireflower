<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CartModel;

class CartController extends Controller
{
    public function index(): void
    {
        require_login();

        $items = $this->model(CartModel::class)->items($this->userId());
        $total = 0;
        $itemCount = 0;
        foreach ($items as $it) {
            $total += $it['price'] * $it['quantity'];
            $itemCount += (int) $it['quantity'];
        }

        $this->render('cart', [
            'page_title' => 'Keranjang',
            'items' => $items,
            'total' => $total,
            'item_count' => $itemCount,
        ]);
    }

    public function handle(): void
    {
        require_login();

        $action = $_POST['action'] ?? '';
        $cart   = $this->model(CartModel::class);
        $userId = $this->userId();

        if ($action === 'add') {
            $cart->add($userId, (int) $_POST['product_id'], max(1, (int) $_POST['quantity']));
            // Jika AJAX request, return JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true]);
                exit;
            }
            flash('success', 'Produk ditambahkan ke keranjang.');
            redirect($this->refererOr('keranjang.php'));
        }

        if ($action === 'update') {
            $cart->updateQty((int) $_POST['cart_id'], max(1, (int) $_POST['quantity']), $userId);
            // AJAX update — return JSON dengan data terbaru
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                $items = $cart->items($userId);
                $total = 0;
                foreach ($items as $it) {
                    $total += $it['price'] * $it['quantity'];
                }
                echo json_encode(['ok' => true, 'total' => $total]);
                exit;
            }
            redirect('keranjang.php');
        }

        if ($action === 'delete') {
            $cart->remove((int) $_POST['cart_id'], $userId);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true]);
                exit;
            }
            flash('info', 'Produk dihapus dari keranjang.');
        }

        redirect('keranjang.php');
    }

    private function refererOr(string $fallback): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer === '') {
            return $fallback;
        }
        $parts = parse_url($referer);
        $target = $parts['path'] ?? '';
        if (!empty($parts['query'])) {
            $target .= '?' . $parts['query'];
        }
        return $target !== '' ? $target : $fallback;
    }
}
