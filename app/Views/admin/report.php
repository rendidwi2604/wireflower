<h4 class="fw-bold mb-4">Laporan Penjualan</h4>

<form method="GET" class="row g-2 mb-4">
  <div class="col-auto"><input type="date" name="start" value="<?= e($start) ?>" class="form-control"></div>
  <div class="col-auto"><input type="date" name="end" value="<?= e($end) ?>" class="form-control"></div>
  <div class="col-auto"><button type="submit" class="btn btn-pink">Filter</button></div>
</form>

<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Total Pendapatan</div><h4 class="text-pink fw-bold"><?= rupiah($summary['total']) ?></h4></div></div>
  <div class="col-md-4"><div class="card border-0 shadow-sm p-3"><div class="text-muted small">Jumlah Pesanan</div><h4 class="text-pink fw-bold"><?= $summary['jumlah'] ?></h4></div></div>
</div>

<div class="card border-0 shadow-sm p-3 mb-4">
  <h5 class="fw-bold mb-3">Produk Terlaris pada Periode Ini</h5>
  <table class="table table-sm">
    <thead><tr><th>Produk</th><th>Jumlah Terjual</th><th>Pendapatan</th></tr></thead>
    <tbody>
      <?php foreach ($produk_terlaris as $p): ?>
        <tr><td><?= e($p['product_name']) ?></td><td><?= $p['total_terjual'] ?></td><td><?= rupiah($p['total_pendapatan']) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="card border-0 shadow-sm p-3">
  <h5 class="fw-bold mb-3">Detail Transaksi</h5>
  <table class="table table-sm">
    <thead><tr><th>Kode</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr><td>#<?= e($o['order_code']) ?></td><td><?= rupiah($o['total']) ?></td><td><?= e($o['status']) ?></td><td><?= date('d M Y', strtotime($o['created_at'])) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
