<?php
// Status config: label, warna dot, warna badge
$statusCfg = [
  'belum_bayar' => ['label'=>'Belum Bayar', 'color'=>'#f59e0b', 'bg'=>'#fff8e1', 'icon'=>'bi-clock'],
  'dikemas'     => ['label'=>'Dikemas',     'color'=>'#3b82f6', 'bg'=>'#eff6ff', 'icon'=>'bi-box-seam'],
  'dikirim'     => ['label'=>'Dikirim',     'color'=>'#8b5cf6', 'bg'=>'#f5f3ff', 'icon'=>'bi-truck'],
  'selesai'     => ['label'=>'Selesai',     'color'=>'#10b981', 'bg'=>'#ecfdf5', 'icon'=>'bi-check-circle'],
  'dibatalkan'  => ['label'=>'Dibatalkan',  'color'=>'#ef4444', 'bg'=>'#fef2f2', 'icon'=>'bi-x-circle'],
];
function orderStatusBadge(string $s, array $cfg): string {
  $c = $cfg[$s] ?? ['label'=>$s,'color'=>'#888','bg'=>'#f5f5f5','icon'=>'bi-dash'];
  return '<span style="display:inline-flex;align-items:center;gap:.3rem;padding:.22rem .7rem;border-radius:50px;
    font-size:.72rem;font-weight:700;background:'.$c['bg'].';color:'.$c['color'].';">
    <i class="bi '.$c['icon'].'"></i>'.htmlspecialchars($c['label']).'</span>';
}
?>

<style>
.orders-filter-bar {
  display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1.5rem;
}
.orders-filter-btn {
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.38rem .9rem; border-radius:50px; font-size:.78rem; font-weight:600;
  border:1.5px solid #eeddd9; background:#fff; color:#5a4040;
  cursor:pointer; text-decoration:none; transition:all .15s; white-space:nowrap;
}
.orders-filter-btn:hover { border-color:#c9516f; color:#c9516f; background:#fce8ef; }
.orders-filter-btn.active { background:#c9516f; border-color:#c9516f; color:#fff; }
.orders-filter-btn .count {
  background:rgba(0,0,0,.12); border-radius:50px;
  padding:.05rem .4rem; font-size:.68rem;
}
.orders-filter-btn.active .count { background:rgba(255,255,255,.3); }

.orders-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; border-radius:10px; }
.orders-table { width:100%; border-collapse:collapse; min-width:680px; }
.orders-table thead th {
  font-size:.71rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.06em; color:#8a7070; padding:.65rem 1rem;
  border-bottom:2px solid #f0e8e8; white-space:nowrap; background:#fdfbfb;
}
.orders-table thead th:first-child { border-radius:8px 0 0 0; }
.orders-table thead th:last-child  { border-radius:0 8px 0 0; }
.orders-table tbody td {
  padding:.8rem 1rem; border-bottom:1px solid #f5eded;
  font-size:.83rem; vertical-align:middle; color:#2c1a1e;
}
.orders-table tbody tr:last-child td { border-bottom:none; }
.orders-table tbody tr:hover td { background:#fdfbfb; }

.order-code {
  font-family:monospace; font-weight:700; font-size:.82rem;
  color:#c9516f; background:#fce8ef; padding:.2rem .55rem;
  border-radius:6px; white-space:nowrap;
}
.customer-info { display:flex; flex-direction:column; gap:.1rem; }
.customer-name { font-weight:600; font-size:.84rem; color:#2c1a1e; }
.customer-date { font-size:.72rem; color:#8a7070; }

.order-total { font-weight:700; color:#2c1a1e; white-space:nowrap; }
.order-items-count { font-size:.72rem; color:#8a7070; margin-top:.1rem; }

.status-select-form { display:flex; align-items:center; gap:.45rem; }
.status-select {
  padding:.35rem .65rem; border:1.5px solid #eeddd9; border-radius:8px;
  font-size:.78rem; background:#fff; color:#2c1a1e; cursor:pointer;
  outline:none; transition:border-color .15s; min-width:130px;
}
.status-select:focus { border-color:#c9516f; }
.btn-update {
  display:inline-flex; align-items:center; gap:.3rem;
  padding:.35rem .75rem; border-radius:8px; font-size:.78rem; font-weight:600;
  background:#c9516f; color:#fff; border:none; cursor:pointer;
  white-space:nowrap; transition:background .15s;
}
.btn-update:hover { background:#b8455f; }

.empty-state {
  text-align:center; padding:3.5rem 2rem; color:#8a7070;
}
.empty-state i { font-size:2.5rem; color:#eeddd9; display:block; margin-bottom:.75rem; }
.empty-state h5 { font-family:Georgia,serif; color:#2c1a1e; margin-bottom:.35rem; }

/* Stats row */
.order-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:.75rem; margin-bottom:1.5rem; }
.order-stat-card {
  background:#fff; border:1px solid #eeddd9; border-radius:12px;
  padding:.9rem 1rem; text-align:center;
}
.order-stat-card .stat-num { font-size:1.4rem; font-weight:800; color:#2c1a1e; line-height:1; }
.order-stat-card .stat-lbl { font-size:.7rem; color:#8a7070; margin-top:.2rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; }
.order-stat-card .stat-dot { width:8px;height:8px;border-radius:50%;display:inline-block;margin-bottom:.35rem; }

@media(max-width:768px) {
  .order-stats { grid-template-columns:repeat(3,1fr); }
  .orders-filter-bar { gap:.35rem; }
  .orders-filter-btn { padding:.32rem .7rem; font-size:.74rem; }
}
@media(max-width:480px) {
  .order-stats { grid-template-columns:repeat(2,1fr); }
}
</style>

<!-- Stats row -->
<div class="order-stats">
  <?php
  $statCounts = array_fill_keys(array_keys($statusCfg), 0);
  foreach ($orders as $o) {
    if (isset($statCounts[$o['status']])) $statCounts[$o['status']]++;
  }
  foreach ($statusCfg as $key => $cfg): ?>
  <div class="order-stat-card">
    <div><span class="stat-dot" style="background:<?= $cfg['color'] ?>"></span></div>
    <div class="stat-num"><?= $statCounts[$key] ?></div>
    <div class="stat-lbl"><?= $cfg['label'] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Filter bar -->
<div class="orders-filter-bar">
  <?php
  $allCount = count($orders);
  $isAll    = ($status_filter === '');
  ?>
  <a href="<?= site_url('admin/pesanan.php') ?>"
     class="orders-filter-btn <?= $isAll ? 'active' : '' ?>">
    <i class="bi bi-grid-3x3-gap"></i> Semua
    <span class="count"><?= $allCount ?></span>
  </a>
  <?php foreach ($statuses as $s):
    $cfg   = $statusCfg[$s] ?? ['label'=>ucfirst($s),'icon'=>'bi-dash'];
    $cnt   = $statCounts[$s];
    $isAct = ($status_filter === $s);
  ?>
  <a href="<?= site_url('admin/pesanan.php?status='.$s) ?>"
     class="orders-filter-btn <?= $isAct ? 'active' : '' ?>">
    <i class="bi <?= $cfg['icon'] ?>"></i> <?= $cfg['label'] ?>
    <span class="count"><?= $cnt ?></span>
  </a>
  <?php endforeach; ?>
</div>

<!-- Table card -->
<div class="adm-card" style="padding:0;overflow:hidden;">
  <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #f0e8e8;">
    <div style="font-family:Georgia,serif;font-weight:700;font-size:.95rem;color:#2c1a1e;">
      <?= $status_filter ? ($statusCfg[$status_filter]['label'] ?? ucfirst($status_filter)) : 'Semua Pesanan' ?>
      <span style="font-family:system-ui;font-weight:600;font-size:.78rem;color:#8a7070;margin-left:.35rem;">(<?= count($orders) ?>)</span>
    </div>
  </div>

  <?php if (empty($orders)): ?>
    <div class="empty-state">
      <i class="bi bi-bag-x"></i>
      <h5>Belum ada pesanan</h5>
      <p style="font-size:.84rem;">Pesanan akan muncul di sini saat pelanggan melakukan pembelian.</p>
    </div>
  <?php else: ?>
  <div class="orders-table-wrap">
    <table class="orders-table">
      <thead>
        <tr>
          <th>Kode Pesanan</th>
          <th>Pelanggan</th>
          <th>Total</th>
          <th>Status</th>
          <th>Update Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td>
            <span class="order-code">#<?= e($o['order_code']) ?></span>
          </td>
          <td>
            <div class="customer-info">
              <span class="customer-name"><?= e($o['customer_name']) ?></span>
              <span class="customer-date">
                <i class="bi bi-calendar3" style="font-size:.65rem;"></i>
                <?= date('d M Y, H:i', strtotime($o['created_at'])) ?>
              </span>
            </div>
          </td>
          <td>
            <div class="order-total"><?= rupiah($o['total']) ?></div>
            <?php if (!empty($o['shipping_cost'])): ?>
              <div class="order-items-count">+<?= rupiah($o['shipping_cost']) ?> ongkir</div>
            <?php endif; ?>
          </td>
          <td><?= orderStatusBadge($o['status'], $statusCfg) ?></td>
          <td>
            <form method="POST" action="<?= site_url('admin/pesanan.php') ?>" class="status-select-form">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <select name="status" class="status-select">
                <?php foreach ($statuses as $s):
                  $lbl = $statusCfg[$s]['label'] ?? ucfirst($s);
                ?>
                  <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" name="update_status" class="btn-update">
                <i class="bi bi-check2"></i> Simpan
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
