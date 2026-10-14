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

        $filename = 'proof_' . uniqid('', true) . '.' . $ext;
        $mimeType = $ext === 'pdf' ? 'application/pdf' : 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
        $content  = file_get_contents($_FILES['transfer_proof']['tmp_name']);

        // Coba Supabase Storage
        $url = supabase_upload('transfer-proofs', $filename, $content, $mimeType);
        if ($url) {
            return $url;
        }

        // Fallback: filesystem lokal
        $dir = BASE_PATH . '/assets/img/proofs/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (move_uploaded_file($_FILES['transfer_proof']['tmp_name'], $dir . $filename)) {
            return site_url('assets/img/proofs/' . $filename);
        }

        return null;
    }
}
