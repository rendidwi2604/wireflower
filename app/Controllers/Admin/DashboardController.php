<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\OrderModel;
use App\Models\UserModel;

class DashboardController extends Controller
{
    public function index(): void
    {
        require_admin();

        $stats = $this->model(OrderModel::class)->dashboardStats();

        $this->render('admin/dashboard', [
            'page_title' => 'Dashboard',
            'total_produk' => $stats['produk'],
            'total_pesanan' => $stats['pesanan'],
            'total_pengguna' => $this->model(UserModel::class)->countCustomers(),
            'total_pendapatan' => $stats['pendapatan'],
            'pesanan_baru' => $this->model(OrderModel::class)->recentOrders(5),
        ], 'admin');
    }
}
