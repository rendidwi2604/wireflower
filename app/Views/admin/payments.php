<h4 class="fw-bold mb-4">Kelola Pembayaran</h4>
<div class="card border-0 shadow-sm p-3">
  <table class="table align-middle">
    <thead><tr><th>Kode Pesanan</th><th>Pelanggan</th><th>Metode</th><th>Jumlah</th><th>Status</th><th>Tgl Bayar</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($payments as $p): ?>
        <tr>
          <td>#<?= e($p['order_code']) ?></td>
          <td><?= e($p['customer_name']) ?></td>
          <td><?= e($p['method']) ?></td>
          <td><?= rupiah($p['amount']) ?></td>
          <td><span class="badge bg-<?= $p['status'] == 'success' ? 'success' : ($p['status'] == 'failed' ? 'danger' : 'warning text-dark') ?>"><?= e($p['status']) ?></span></td>
          <td><?= $p['paid_at'] ? date('d M Y H:i', strtotime($p['paid_at'])) : '-' ?></td>
          <td>
            <form method="POST" class="d-flex gap-1">
              <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
              <select name="status" class="form-select form-select-sm">
                <option value="pending" <?= $p['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="success" <?= $p['status'] == 'success' ? 'selected' : '' ?>>Success</option>
                <option value="failed" <?= $p['status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
              </select>
              <button type="submit" name="update_payment_status" class="btn btn-sm btn-pink">Update</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
