<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;

class PaymentController extends Controller
{
    private const METHODS = ['transfer_bank', 'e_wallet', 'cod'];

    public function show(): void
    {
        require_login();
        $userId = $this->userId();
        $payments = $this->model(PaymentModel::class);
        $orders = $this->model(OrderModel::class);

        $order = $orders->findForUser((int) ($_GET['order_id'] ?? 0), $userId);
        if (!$order) {
            redirect('pesanan.php');
        }
        $payment = $payments->latestForOrder((int) $order['id']);

        // Proses pilih metode & "bayar" (simulasi)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $method = in_array($_POST['method'] ?? '', self::METHODS, true) ? $_POST['method'] : 'transfer_bank';
            $payments->pay((int) $payment['id'], $method);
            $orders->updateStatus((int) $order['id'], 'dikemas');

            $notifications = $this->model(NotificationModel::class);
            $code = $order['order_code'];
            $notifications->send($userId, 'Pembayaran Berhasil', "Pembayaran untuk pesanan #$code berhasil dikonfirmasi (simulasi). Pesanan sedang dikemas.", 'payment_success');
            $notifications->send($userId, 'Pesanan Sedang Dikemas', "Pesanan #$code sedang dikemas oleh Wire Flower.", 'order_packing');

            redirect('pembayaran.php?order_id=' . $order['id']);
        }

        $this->render('payment', [
            'page_title' => 'Pembayaran',
            'order' => $order,
            'payment' => $payment,
        ]);
    }
}
