<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;

class OrderController extends Controller
{
    private const CANCEL_REASONS = [
        'Salah memilih produk',
        'Ingin mengubah pesanan',
        'Menemukan harga lebih murah',
        'Pesanan terlalu lama diproses',
        'Alasan lainnya',
    ];

    public function index(): void
    {
        require_login();
        $userId        = $this->userId();
        $orders        = $this->model(OrderModel::class);
        $payments      = $this->model(PaymentModel::class);
        $notifications = $this->model(NotificationModel::class);

        // ── Tandai pesanan selesai ──
        if (isset($_POST['complete_order_id'])) {
            $orderId = (int) $_POST['complete_order_id'];
            if ($orders->complete($orderId, $userId)) {
                $notifications->send($userId, 'Pesanan Selesai',
                    'Pesanan kamu telah selesai. Terima kasih telah berbelanja di WireFlower!',
                    'order_done');
            }
            redirect('pesanan.php?status=selesai');
        }

        // ── Batalkan pesanan (belum_bayar ATAU dikemas) ──
        if (isset($_POST['cancel_order_id'])) {
            $reason = trim($_POST['cancel_reason'] ?? '');
            if (!in_array($reason, self::CANCEL_REASONS, true)) {
                flash('danger', 'Pilih alasan pembatalan terlebih dahulu.');
                redirect('pesanan.php');
            }
            $code = $orders->cancel(
                (int) $_POST['cancel_order_id'],
                $userId,
                $reason,
                trim($_POST['cancel_details'] ?? '')
            );
            if ($code) {
                $notifications->send($userId, 'Pesanan Dibatalkan',
                    "Pesanan #$code telah dibatalkan.", 'order_cancelled');
                flash('info', 'Pesanan berhasil dibatalkan.');
            }
            redirect('pesanan.php?status=dibatalkan');
        }

        // ── Upload bukti transfer ──
        if (isset($_POST['proof_order_id'])) {
            $orderId = (int) $_POST['proof_order_id'];
            $order   = $orders->findForUser($orderId, $userId);

            if ($order) {
                $payment   = $orders->latestPaymentForOrder($orderId);
                $proofUrl  = $this->uploadTransferProof();

                if ($proofUrl && $payment) {
                    $payments->saveTransferProof((int) $payment['id'], $proofUrl);
                    $notifications->send($userId, 'Bukti Transfer Dikirim',
                        "Bukti transfer untuk pesanan #{$order['order_code']} sudah diterima. Admin akan memverifikasi.",
                        'payment_proof');
                    flash('success', 'Bukti transfer berhasil dikirim. Admin akan segera memverifikasi.');
                } else {
                    flash('danger', 'Gagal mengunggah bukti transfer. Coba lagi.');
                }
            }
            redirect('pesanan.php?status=belum_bayar');
        }

        $active = $_GET['status'] ?? '';
        $items  = [];
        foreach ($orders->forUser($userId, $active) as $order) {
            $order['order_items'] = $orders->itemsOf((int) $order['id']);
            $order['payment']     = $orders->latestPaymentForOrder((int) $order['id']);
            $items[] = $order;
        }

        $this->render('orders', [
            'page_title'     => 'Pesanan Saya',
            'active'         => $active,
            'orders'         => $items,
            'order_counts'   => $orders->countsByStatus($userId),
            'status_tabs'    => OrderModel::STATUS_LABELS,
            'cancel_reasons' => self::CANCEL_REASONS,
        ]);
    }

    // ── Upload bukti transfer ke Supabase Storage ──
    private function uploadTransferProof(): ?string
    {
        if (empty($_FILES['transfer_proof']['name']) ||
            $_FILES['transfer_proof']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext     = strtolower(pathinfo($_FILES['transfer_proof']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        if (!in_array($ext, $allowed, true)) {
            flash('danger', 'Format bukti transfer tidak didukung. Gunakan JPG, PNG, WebP, atau PDF.');
            return null;
        }

        $maxSize = 5 * 1024 * 1024; // 5 MB
        if ($_FILES['transfer_proof']['size'] > $maxSize) {
            flash('danger', 'Ukuran file maksimal 5 MB.');
            return null;
        }

        $filename    = 'proof_' . uniqid() . '.' . $ext;
        $fileContent = file_get_contents($_FILES['transfer_proof']['tmp_name']);

        // ── Supabase Storage ──
        $supabaseUrl = '';
        $supabaseKey = '';
        foreach (['SUPABASE_URL', 'supabase_url'] as $k) {
            $v = getenv($k) ?: ($_ENV[$k] ?? '') ?: ($_SERVER[$k] ?? '');
            if ($v) { $supabaseUrl = $v; break; }
        }
        foreach (['SUPABASE_KEY', 'supabase_key'] as $k) {
            $v = getenv($k) ?: ($_ENV[$k] ?? '') ?: ($_SERVER[$k] ?? '');
            if ($v) { $supabaseKey = $v; break; }
        }

        if ($supabaseUrl && $supabaseKey) {
            $bucket   = 'transfer-proofs';
            $mimeType = $ext === 'pdf' ? 'application/pdf' : 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
            $endpoint = rtrim($supabaseUrl, '/') . '/storage/v1/object/' . $bucket . '/' . $filename;

            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $fileContent,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $supabaseKey,
                    'Content-Type: ' . $mimeType,
                    'x-upsert: true',
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 || $httpCode === 201) {
                return rtrim($supabaseUrl, '/') . '/storage/v1/object/public/' . $bucket . '/' . $filename;
            }
            error_log('[Supabase Storage] proof HTTP ' . $httpCode . ': ' . $response);
        }

        // ── Fallback: filesystem lokal ──
        $dest = BASE_PATH . '/assets/img/proofs/' . $filename;
        if (!is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }
        if (move_uploaded_file($_FILES['transfer_proof']['tmp_name'], $dest)) {
            return 'proofs/' . $filename;
        }

        return null;
    }
}
