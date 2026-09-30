<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class OrderController extends Controller
{
    private const MESSAGES = [
        'dikemas' => ['Pesanan Sedang Dikemas', 'sedang dikemas.', 'order_packing'],
        'dikirim' => ['Pesanan Telah Dikirim', 'telah dikirim. Silakan pantau pengirimanmu.', 'order_shipped'],
        'selesai' => ['Pesanan Selesai', 'telah selesai. Terima kasih!', 'order_done'],
        'dibatalkan' => ['Pesanan Dibatalkan', 'telah dibatalkan.', 'order_cancelled'],
    ];

    public function index(): void
    {
        require_admin();
        $orders = $this->model(OrderModel::class);
        $products = $this->model(ProductModel::class);

        if (isset($_POST['update_status'])) {
            $orderId = (int) $_POST['order_id'];
            $status = $_POST['status'];
            $order = $orders->updateStatus($orderId, $status);

            if (isset(self::MESSAGES[$status])) {
                [$title, $suffix, $type] = self::MESSAGES[$status];
                $this->model(NotificationModel::class)->send(
                    (int) $order['user_id'],
                    $title,
                    "Pesanan #{$order['order_code']} $suffix",
                    $type
                );
            }
            if ($status === 'selesai') {
                foreach ($orders->itemsOf($orderId) as $it) {
                    $products->incrementSold((int) $it['product_id'], (int) $it['quantity']);
                }
            }
            flash('success', 'Status pesanan diperbarui.');
            redirect('admin/pesanan.php');
        }

        $this->render('admin/orders', [
            'page_title' => 'Kelola Pesanan',
            'status_filter' => $_GET['status'] ?? '',
            'statuses' => OrderModel::STATUSES,
            'orders' => $orders->allWithCustomer($_GET['status'] ?? ''),
        ], 'admin');
    }
}
