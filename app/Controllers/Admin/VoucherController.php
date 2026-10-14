<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\VoucherModel;

class VoucherController extends Controller
{
    public function index(): void
    {
        require_admin();
        $model = $this->model(VoucherModel::class);

        // ── Hapus ──
        if (isset($_POST['delete_voucher'])) {
            $model->delete((int) $_POST['voucher_id']);
            flash('success', 'Voucher berhasil dihapus.');
            redirect('admin/voucher.php');
        }

        // ── Toggle aktif/nonaktif ──
        if (isset($_POST['toggle_voucher'])) {
            $model->toggleActive((int) $_POST['voucher_id']);
            flash('success', 'Status voucher diperbarui.');
            redirect('admin/voucher.php');
        }

        // ── Simpan (tambah / edit) ──
        if (isset($_POST['save_voucher'])) {
            $id   = (int) ($_POST['voucher_id'] ?? 0);
            $data = [
                'code'           => $_POST['code']           ?? '',
                'type'           => $_POST['type']           ?? 'fixed',
                'discount_value' => $_POST['discount_value'] ?? '0',
                'max_discount'   => $_POST['max_discount']   ?? '',
                'min_order'      => $_POST['min_order']      ?? '',
                'usage_limit'    => $_POST['usage_limit']    ?? '',
                'valid_from'     => $_POST['valid_from']     ?? '',
                'valid_until'    => $_POST['valid_until']    ?? '',
                'is_active'      => $_POST['is_active']      ?? null,
                'description'    => $_POST['description']    ?? '',
            ];

            if (empty(trim($data['code']))) {
                flash('danger', 'Kode voucher tidak boleh kosong.');
                redirect('admin/voucher.php');
            }

            if (!array_key_exists($data['type'], VoucherModel::TYPES)) {
                flash('danger', 'Tipe voucher tidak valid.');
                redirect('admin/voucher.php');
            }

            if ($id > 0) {
                $model->update($id, $data);
                flash('success', 'Voucher berhasil diperbarui.');
            } else {
                $model->create($data);
                flash('success', 'Voucher berhasil ditambahkan.');
            }
            redirect('admin/voucher.php');
        }

        // ── Edit mode: ambil data voucher ──
        $editVoucher = null;
        if (!empty($_GET['edit'])) {
            $editVoucher = $model->find((int) $_GET['edit']);
        }

        $this->render('admin/vouchers', [
            'page_title'  => 'Kelola Voucher',
            'vouchers'    => $model->all(),
            'types'       => VoucherModel::TYPES,
            'editVoucher' => $editVoucher,
        ], 'admin');
    }
}
