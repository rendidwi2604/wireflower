<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\NotificationModel;
use App\Models\OrderModel;

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
        $userId = $this->userId();
        $orders = $this->model(OrderModel::class);
        $notifications = $this->model(NotificationModel::class);

        if (isset($_POST['complete_order_id'])) {
            $orderId = (int) $_POST['complete_order_id'];
            if ($orders->complete($orderId, $userId)) {
                $notifications->send($userId, 'Pesanan Selesai', 'Pesanan kamu telah selesai. Terima kasih telah berbelanja di Wire Flower!', 'order_done');
            }
            redirect('pesanan.php?status=selesai');
        }

        if (isset($_POST['cancel_order_id'])) {
            $reason = trim($_POST['cancel_reason'] ?? '');
            if (!in_array($reason, self::CANCEL_REASONS, true)) {
                flash('danger', 'Pilih alasan pembatalan terlebih dahulu.');
                redirect('pesanan.php');
            }
            $code = $orders->cancel((int) $_POST['cancel_order_id'], $userId, $reason, trim($_POST['cancel_details'] ?? ''));
            if ($code) {
                $notifications->send($userId, 'Pesanan Dibatalkan', "Pesanan #$code telah dibatalkan.", 'order_cancelled');
                flash('info', 'Pesanan berhasil dibatalkan.');
            }
            redirect('pesanan.php?status=dibatalkan');
        }

        $active = $_GET['status'] ?? '';
        $items = [];
        foreach ($orders->forUser($userId, $active) as $order) {
            $order['order_items'] = $orders->itemsOf((int) $order['id']);
            $items[] = $order;
        }

        $this->render('orders', [
            'page_title' => 'Pesanan Saya',
            'active' => $active,
            'orders' => $items,
            'order_counts' => $orders->countsByStatus($userId),
            'status_tabs' => OrderModel::STATUS_LABELS,
            'cancel_reasons' => self::CANCEL_REASONS,
        ]);
    }
}
