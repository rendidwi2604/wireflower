<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\OrderModel;

class ReportController extends Controller
{
    public function index(): void
    {
        require_admin();

        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-d');
        $orders = $this->model(OrderModel::class);

        $this->render('admin/report', [
            'page_title' => 'Laporan Penjualan',
            'start' => $start,
            'end' => $end,
            'summary' => $orders->reportSummary($start, $end),
            'produk_terlaris' => $orders->reportTopProducts($start, $end),
            'orders' => $orders->reportOrders($start, $end),
        ], 'admin');
    }
}
