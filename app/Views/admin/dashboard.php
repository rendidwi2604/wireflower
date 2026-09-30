<h4 class="fw-bold mb-4">Dashboard</h4>
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Total Produk</div><h3 class="fw-bold text-pink"><?= $total_produk ?></h3></div></div>
  <div class="col-md-3"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Total Pesanan</div><h3 class="fw-bold text-pink"><?= $total_pesanan ?></h3></div></div>
  <div class="col-md-3"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Total Pengguna</div><h3 class="fw-bold text-pink"><?= $total_pengguna ?></h3></div></div>
  <div class="col-md-3"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Total Pendapatan</div><h5 class="fw-bold text-pink"><?= rupiah($total_pendapatan) ?></h5></div></div>
</div>

<div class="card border-0 shadow-sm p-3">
  <h5 class="fw-bold mb-3">Pesanan Terbaru</h5>
  <table class="table table-sm align-middle">
    <thead><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
    <tbody>
      <?php foreach ($pesanan_baru as $p): ?>
        <tr>
          <td>#<?= e($p['order_code']) ?></td>
          <td><?= e($p['customer_name']) ?></td>
          <td><?= rupiah($p['total']) ?></td>
          <td><span class="badge bg-secondary"><?= e($p['status']) ?></span></td>
          <td><?= date('d M Y', strtotime($p['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
