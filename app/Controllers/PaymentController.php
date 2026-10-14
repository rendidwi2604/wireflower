<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;

class PaymentController extends Controller
{
    private const METHODS = ['transfer_bank', 'cod', 'qris'];

    public function show(): void
    {
        require_login();
        $userId   = $this->userId();
        $payments = $this->model(PaymentModel::class);
        $orders   = $this->model(OrderModel::class);

        $order = $orders->findForUser((int) ($_GET['order_id'] ?? 0), $userId);
        if (!$order) {
            redirect('pesanan.php');
        }
        $payment = $payments->latestForOrder((int) $order['id']);

        // ── Proses submit bukti/konfirmasi pembayaran ──
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $method   = in_array($_POST['method'] ?? '', self::METHODS, true)
                        ? $_POST['method'] : 'transfer_bank';
            $bankName = trim($_POST['bank_name'] ?? '');

            // Bangun label metode
            $methodLabel = match($method) {
                'qris' => 'QRIS',
                'cod'  => 'COD',
                default => $bankName !== '' ? 'Transfer ' . $bankName : 'Transfer Bank',
            };

            $notifications = $this->model(NotificationModel::class);
            $code = $order['order_code'];

            if ($method === 'cod') {
                // COD: langsung success + order dikemas (tidak butuh bukti)
                $payments->setMethod((int) $payment['id'], $methodLabel);
                $payments->pay((int) $payment['id'], $methodLabel);
                $orders->updateStatus((int) $order['id'], 'dikemas');

                $notifications->send($userId, 'Pesanan Dikonfirmasi (COD)',
                    "Pesanan #$code dikonfirmasi dengan COD. Pesanan sedang dikemas.",
                    'payment_success');

                flash('success', 'Pesanan dikonfirmasi! Pesanan sedang dikemas.');
                redirect('pembayaran.php?order_id=' . $order['id']);
            }

            // Transfer / QRIS: simpan metode + upload bukti, status tetap pending
            $payments->setMethod((int) $payment['id'], $methodLabel);

            // Upload bukti pembayaran
            $proofUrl = $this->uploadProof();
            if ($proofUrl) {
                $payments->saveTransferProof((int) $payment['id'], $proofUrl);

                $notifications->send($userId, 'Bukti Pembayaran Dikirim',
                    "Bukti pembayaran untuk pesanan #$code sudah diterima. Menunggu verifikasi admin.",
                    'payment_proof');

                flash('success', 'Bukti pembayaran berhasil dikirim! Pesanan akan diproses setelah admin memverifikasi.');
            } else {
                flash('danger', 'Gagal mengunggah bukti pembayaran. Pastikan format (JPG/PNG/PDF) dan ukuran file (maks 5 MB) sesuai, lalu coba lagi.');
            }

            redirect('pembayaran.php?order_id=' . $order['id']);
        }

        $this->render('payment', [
            'page_title' => 'Pembayaran',
            'order'      => $order,
            'payment'    => $payment,
        ]);
    }

    // ── Upload bukti ke Supabase Storage ──
    private function uploadProof(): ?string
    {
        // Tentukan field name sesuai metode yang dipilih
        $method    = $_POST['method'] ?? 'transfer_bank';
        $fieldName = ($method === 'qris') ? 'transfer_proof_qris' : 'transfer_proof_bank';

        if (empty($_FILES[$fieldName]['name']) ||
            $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext     = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        if (!in_array($ext, $allowed, true)) {
            flash('danger', 'Format bukti tidak didukung. Gunakan JPG, PNG, WebP, atau PDF.');
            return null;
        }

        if ($_FILES[$fieldName]['size'] > 5 * 1024 * 1024) {
            flash('danger', 'Ukuran file maksimal 5 MB.');
            return null;
        }

        $filename = 'proof_' . uniqid('', true) . '.' . $ext;
        $mimeType = $ext === 'pdf' ? 'application/pdf' : 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
        $content  = file_get_contents($_FILES[$fieldName]['tmp_name']);

        // Coba Supabase Storage (helper di helpers.php)
        $url = supabase_upload('transfer-proofs', $filename, $content, $mimeType);
        if ($url) {
            return $url;
        }

        // Fallback: filesystem lokal (hanya bekerja di localhost, Vercel read-only)
        $dir = BASE_PATH . '/assets/img/proofs/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $dir . $filename)) {
            return site_url('assets/img/proofs/' . $filename);
        }

        return null;
    }
}
