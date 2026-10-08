<?php
$payStatusCfg = [
  'pending' => ['label'=>'Pending',  'color'=>'#f59e0b', 'bg'=>'#fff8e1', 'icon'=>'bi-hourglass-split'],
  'success' => ['label'=>'Berhasil', 'color'=>'#10b981', 'bg'=>'#ecfdf5', 'icon'=>'bi-check-circle-fill'],
  'failed'  => ['label'=>'Gagal',    'color'=>'#ef4444', 'bg'=>'#fef2f2', 'icon'=>'bi-x-circle-fill'],
];
$methodLabel = [
  'transfer_bank' => ['label'=>'Transfer Bank', 'icon'=>'bi-bank'],
  'e_wallet'      => ['label'=>'E-Wallet',      'icon'=>'bi-wallet2'],
  'cod'           => ['label'=>'COD',            'icon'=>'bi-cash-coin'],
];
function payStatusBadge(string $s, array $cfg): string {
  $c = $cfg[$s] ?? ['label'=>$s,'color'=>'#888','bg'=>'#f5f5f5','icon'=>'bi-dash'];
  return '<span style="display:inline-flex;align-items:center;gap:.3rem;padding:.22rem .7rem;border-radius:50px;
    font-size:.72rem;font-weight:700;background:'.$c['bg'].';color:'.$c['color'].';">
    <i class="bi '.$c['icon'].'"></i>'.htmlspecialchars($c['label']).'</span>';
}
?>

<style>
/* ── Payment stats ── */
.pay-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; margin-bottom:1.5rem; }
.pay-stat-card {
  background:#fff; border:1px solid #eeddd9; border-radius:12px;
  padding:1rem 1.25rem; display:flex; align-items:center; gap:.85rem;
}
.pay-stat-icon {
  width:42px; height:42px; border-radius:10px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; font-size:1.1rem;
}
.pay-stat-num  { font-size:1.35rem; font-weight:800; color:#2c1a1e; line-height:1; }
.pay-stat-lbl  { font-size:.71rem; color:#8a7070; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-top:.15rem; }

/* ── Table ── */
.pay-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; border-radius:10px; }
.pay-table { width:100%; border-collapse:collapse; min-width:720px; }
.pay-table thead th {
  font-size:.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;
  color:#8a7070; padding:.65rem 1rem; border-bottom:2px solid #f0e8e8;
  white-space:nowrap; background:#fdfbfb;
}
.pay-table tbody td {
  padding:.85rem 1rem; border-bottom:1px solid #f5eded;
  font-size:.83rem; vertical-align:middle; color:#2c1a1e;
}
.pay-table tbody tr:last-child td { border-bottom:none; }
.pay-table tbody tr:hover td { background:#fdfbfb; }

.pay-code {
  font-family:monospace; font-weight:700; font-size:.82rem;
  color:#c9516f; background:#fce8ef; padding:.2rem .55rem; border-radius:6px;
}
.pay-method {
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.22rem .65rem; border-radius:8px; font-size:.75rem; font-weight:600;
  background:#f7f5f3; color:#5a4040; border:1px solid #eeddd9;
}
.pay-amount { font-weight:700; color:#2c1a1e; white-space:nowrap; }
.pay-date   { font-size:.78rem; color:#8a7070; }

.pay-select-form { display:flex; align-items:center; gap:.45rem; }
.pay-select {
  padding:.35rem .65rem; border:1.5px solid #eeddd9; border-radius:8px;
  font-size:.78rem; background:#fff; color:#2c1a1e; cursor:pointer;
  outline:none; transition:border-color .15s; min-width:110px;
}
.pay-select:focus { border-color:#c9516f; }
.btn-pay-update {
  display:inline-flex; align-items:center; gap:.3rem;
  padding:.35rem .75rem; border-radius:8px; font-size:.78rem; font-weight:600;
  background:#c9516f; color:#fff; border:none; cursor:pointer;
  white-space:nowrap; transition:background .15s;
}
.btn-pay-update:hover { background:#b8455f; }

.empty-state {
  text-align:center; padding:3.5rem 2rem; color:#8a7070;
}
.empty-state i { font-size:2.5rem; color:#eeddd9; display:block; margin-bottom:.75rem; }
.empty-state h5 { font-family:Georgia,serif; color:#2c1a1e; margin-bottom:.35rem; }

/* Filter tabs */
.pay-filter-bar { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1.25rem; }
.pay-filter-btn {
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.38rem .9rem; border-radius:50px; font-size:.78rem; font-weight:600;
  border:1.5px solid #eeddd9; background:#fff; color:#5a4040;
  cursor:pointer; text-decoration:none; transition:all .15s;
}
.pay-filter-btn:hover  { border-color:#c9516f; color:#c9516f; background:#fce8ef; }
.pay-filter-btn.active { background:#c9516f; border-color:#c9516f; color:#fff; }
.pay-filter-btn .count { background:rgba(0,0,0,.12); border-radius:50px; padding:.05rem .4rem; font-size:.68rem; }
.pay-filter-btn.active .count { background:rgba(255,255,255,.3); }

@media(max-width:768px) {
  .pay-stats { grid-template-columns:1fr 1fr; }
}
@media(max-width:480px) {
  .pay-stats { grid-template-columns:1fr; }
}
</style>

<?php
// Count per status
$counts = ['pending'=>0,'success'=>0,'failed'=>0];
$totalSuccess = 0;
foreach ($payments as $p) {
  if (isset($counts[$p['status']])) $counts[$p['status']]++;
  if ($p['status'] === 'success') $totalSuccess += (float)($p['amount'] ?? 0);
}
$activeFilter = $_GET['status'] ?? '';
$filtered = $activeFilter === '' ? $payments
  : array_filter($payments, fn($p) => $p['status'] === $activeFilter);
?>

<!-- Stats -->
<div class="pay-stats">
  <div class="pay-stat-card">
    <div class="pay-stat-icon" style="background:#ecfdf5;color:#10b981;">
      <i class="bi bi-check-circle-fill"></i>
    </div>
    <div>
      <div class="pay-stat-num"><?= $counts['success'] ?></div>
      <div class="pay-stat-lbl">Berhasil</div>
    </div>
  </div>
  <div class="pay-stat-card">
    <div class="pay-stat-icon" style="background:#fff8e1;color:#f59e0b;">
      <i class="bi bi-hourglass-split"></i>
    </div>
    <div>
      <div class="pay-stat-num"><?= $counts['pending'] ?></div>
      <div class="pay-stat-lbl">Pending</div>
    </div>
  </div>
  <div class="pay-stat-card">
    <div class="pay-stat-icon" style="background:#fce8ef;color:#c9516f;">
      <i class="bi bi-currency-dollar"></i>
    </div>
    <div>
      <div class="pay-stat-num" style="font-size:1rem;"><?= rupiah($totalSuccess) ?></div>
      <div class="pay-stat-lbl">Total Diterima</div>
    </div>
  </div>
</div>

<!-- Filter -->
<div class="pay-filter-bar">
  <a href="<?= site_url('admin/pembayaran.php') ?>"
     class="pay-filter-btn <?= $activeFilter==='' ? 'active' : '' ?>">
    <i class="bi bi-grid-3x3-gap"></i> Semua
    <span class="count"><?= count($payments) ?></span>
  </a>
  <?php foreach ($payStatusCfg as $key => $cfg): ?>
  <a href="<?= site_url('admin/pembayaran.php?status='.$key) ?>"
     class="pay-filter-btn <?= $activeFilter===$key ? 'active' : '' ?>">
    <i class="bi <?= $cfg['icon'] ?>"></i> <?= $cfg['label'] ?>
    <span class="count"><?= $counts[$key] ?></span>
  </a>
  <?php endforeach; ?>
</div>

<!-- Table card -->
<div class="adm-card" style="padding:0;overflow:hidden;">
  <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #f0e8e8;">
    <div style="font-family:Georgia,serif;font-weight:700;font-size:.95rem;color:#2c1a1e;">
      <?= $activeFilter ? ($payStatusCfg[$activeFilter]['label'] ?? ucfirst($activeFilter)) : 'Semua Pembayaran' ?>
      <span style="font-family:system-ui;font-weight:600;font-size:.78rem;color:#8a7070;margin-left:.35rem;">(<?= count($filtered) ?>)</span>
    </div>
  </div>

  <?php if (empty($filtered)): ?>
    <div class="empty-state">
      <i class="bi bi-credit-card"></i>
      <h5>Belum ada pembayaran</h5>
      <p style="font-size:.84rem;">Data pembayaran akan muncul di sini.</p>
    </div>
  <?php else: ?>
  <div class="pay-table-wrap">
    <table class="pay-table">
      <thead>
        <tr>
          <th>Kode Pesanan</th>
          <th>Pelanggan</th>
          <th>Metode</th>
          <th>Jumlah</th>
          <th>Status</th>
          <th>Tgl Bayar</th>
          <th>Update</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($filtered as $p): ?>
        <tr>
          <td><span class="pay-code">#<?= e($p['order_code']) ?></span></td>
          <td>
            <div style="font-weight:600;font-size:.84rem;"><?= e($p['customer_name']) ?></div>
            <div style="font-size:.72rem;color:#8a7070;">ID #<?= $p['id'] ?></div>
          </td>
          <td>
            <?php
              $m = $p['method'] ?? '-';
              $ml = $methodLabel[$m] ?? ['label'=>str_replace('_',' ',ucfirst($m)),'icon'=>'bi-credit-card'];
            ?>
            <span class="pay-method">
              <i class="bi <?= $ml['icon'] ?>"></i>
              <?= e($ml['label']) ?>
            </span>
          </td>
          <td><span class="pay-amount"><?= rupiah($p['amount']) ?></span></td>
          <td><?= payStatusBadge($p['status'], $payStatusCfg) ?></td>
          <td>
            <?php if ($p['paid_at']): ?>
              <div class="pay-date">
                <i class="bi bi-calendar3" style="font-size:.65rem;"></i>
                <?= date('d M Y', strtotime($p['paid_at'])) ?>
              </div>
              <div class="pay-date"><?= date('H:i', strtotime($p['paid_at'])) ?></div>
            <?php else: ?>
              <span style="color:#bbb;font-size:.78rem;">—</span>
            <?php endif; ?>
          </td>
          <td>
            <form method="POST" action="<?= site_url('admin/pembayaran.php') ?>" class="pay-select-form">
              <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
              <select name="status" class="pay-select">
                <?php foreach ($payStatusCfg as $key => $cfg): ?>
                  <option value="<?= $key ?>" <?= $p['status']===$key ? 'selected' : '' ?>><?= $cfg['label'] ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" name="update_payment_status" class="btn-pay-update">
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
