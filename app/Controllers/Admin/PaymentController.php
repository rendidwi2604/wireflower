<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\PaymentModel;

class PaymentController extends Controller
{
    public function index(): void
    {
        require_admin();
        $payments = $this->model(PaymentModel::class);

        if (isset($_POST['update_payment_status'])) {
            $payments->updateStatus((int) $_POST['payment_id'], $_POST['status']);
            flash('success', 'Status pembayaran diperbarui.');
            redirect('admin/pembayaran.php');
        }

        $this->render('admin/payments', [
            'page_title' => 'Kelola Pembayaran',
            'payments' => $payments->allWithOrder(),
        ], 'admin');
    }
}
