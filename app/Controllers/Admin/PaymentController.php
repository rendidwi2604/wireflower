<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;

class PaymentController extends Controller
{
    public function index(): void
    {
        require_admin();
        $payments = $this->model(PaymentModel::class);

        if (isset($_POST['update_payment_status'])) {
            $paymentId = (int) $_POST['payment_id'];
            $newStatus = $_POST['status'];

            $payments->updateStatus($paymentId, $newStatus);

            // Jika admin set success → otomatis update order jadi dikemas + kirim notifikasi
            if ($newStatus === 'success') {
                $orders  = $this->model(OrderModel::class);
                $notifs  = $this->model(NotificationModel::class);
                $payment = $payments->find($paymentId);

                if ($payment) {
                    $order = $orders->find((int) $payment['order_id']);
                    if ($order && $order['status'] === 'belum_bayar') {
                        $orders->updateStatus((int) $order['id'], 'dikemas');
                        $notifs->send(
                            (int) $order['user_id'],
                            'Pembayaran Terverifikasi',
                            "Pembayaran untuk pesanan #{$order['order_code']} telah diverifikasi. Pesanan sedang dikemas.",
                            'payment_success'
                        );
                        $notifs->send(
                            (int) $order['user_id'],
                            'Pesanan Sedang Dikemas',
                            "Pesanan #{$order['order_code']} sedang dikemas oleh WireFlower.",
                            'order_packing'
                        );
                    }
                }
            }

            flash('success', 'Status pembayaran diperbarui.');
            redirect('admin/pembayaran.php');
        }

        $this->render('admin/payments', [
            'page_title' => 'Kelola Pembayaran',
            'payments'   => $payments->allWithOrder(),
        ], 'admin');
    }
}
