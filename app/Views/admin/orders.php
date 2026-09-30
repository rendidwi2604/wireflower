<h4 class="fw-bold mb-4">Kelola Pesanan</h4>

<div class="mb-3">
  <a href="<?= site_url('admin/pesanan.php') ?>" class="btn btn-sm <?= !$status_filter ? 'btn-pink' : 'btn-outline-pink' ?>">Semua</a>
  <?php foreach ($statuses as $s): ?>
    <a href="?status=<?= $s ?>" class="btn btn-sm <?= $status_filter == $s ? 'btn-pink' : 'btn-outline-pink' ?>"><?= ucfirst(str_replace('_', ' ', $s)) ?></a>
  <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm p-3">
  <table class="table align-middle">
    <thead><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= e($o['order_code']) ?></td>
          <td><?= e($o['customer_name']) ?></td>
          <td><?= rupiah($o['total']) ?></td>
          <td><span class="badge bg-secondary"><?= e($o['status']) ?></span></td>
          <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td>
            <form method="POST" class="d-flex gap-1">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <select name="status" class="form-select form-select-sm">
                <?php foreach ($statuses as $s): ?>
                  <option value="<?= $s ?>" <?= $o['status'] == $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" name="update_status" class="btn btn-sm btn-pink">Update</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
