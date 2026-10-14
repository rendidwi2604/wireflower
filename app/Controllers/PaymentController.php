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
            }

            $notifications->send($userId, 'Bukti Pembayaran Dikirim',
                "Bukti pembayaran untuk pesanan #$code sudah diterima. Menunggu verifikasi admin.",
                'payment_proof');

            flash('success', 'Bukti pembayaran berhasil dikirim! Pesanan akan diproses setelah admin memverifikasi.');
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
        if (empty($_FILES['transfer_proof']['name']) ||
            $_FILES['transfer_proof']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext     = strtolower(pathinfo($_FILES['transfer_proof']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        if (!in_array($ext, $allowed, true)) {
            flash('danger', 'Format bukti tidak didukung. Gunakan JPG, PNG, WebP, atau PDF.');
            return null;
        }

        if ($_FILES['transfer_proof']['size'] > 5 * 1024 * 1024) {
            flash('danger', 'Ukuran file maksimal 5 MB.');
            return null;
        }

        $filename    = 'proof_' . uniqid('', true) . '.' . $ext;
        $fileContent = file_get_contents($_FILES['transfer_proof']['tmp_name']);
        $mimeType    = $ext === 'pdf' ? 'application/pdf' : 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);

        // ── Supabase Storage ──
        // Coba semua kemungkinan nama env var
        $supabaseUrl = _env('SUPABASE_URL', '');
        $supabaseKey = _env('SUPABASE_SERVICE_KEY',
                        _env('SUPABASE_KEY',
                        _env('SUPABASE_ANON_KEY', '')));

        if ($supabaseUrl && $supabaseKey) {
            // Supabase Storage memakai PUT untuk upload
            $endpoint = rtrim($supabaseUrl, '/') . '/storage/v1/object/transfer-proofs/' . $filename;
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'PUT',
                CURLOPT_POSTFIELDS     => $fileContent,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $supabaseKey,
                    'Content-Type: ' . $mimeType,
                    'x-upsert: true',
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 30,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 || $httpCode === 201) {
                return rtrim($supabaseUrl, '/') . '/storage/v1/object/public/transfer-proofs/' . $filename;
            }
            error_log('[PaymentController] Supabase upload HTTP ' . $httpCode . ' | curl: ' . $curlErr . ' | response: ' . $response);
        } else {
            error_log('[PaymentController] Supabase env vars not set. SUPABASE_URL=' . ($supabaseUrl ?: 'empty'));
        }

        // Fallback: filesystem lokal (hanya bekerja di non-Vercel / localhost)
        $dir = BASE_PATH . '/assets/img/proofs/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $dest = $dir . $filename;
        if (move_uploaded_file($_FILES['transfer_proof']['tmp_name'], $dest)) {
            return site_url('assets/img/proofs/' . $filename);
        }

        error_log('[PaymentController] All upload methods failed for file: ' . $_FILES['transfer_proof']['name']);
        return null;
    }
}
