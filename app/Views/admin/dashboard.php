<?php
$statusCfg = [
  'belum_bayar' => ['label'=>'Belum Bayar','color'=>'#f59e0b','bg'=>'#fff8e1','icon'=>'bi-clock'],
  'dikemas'     => ['label'=>'Dikemas',    'color'=>'#3b82f6','bg'=>'#eff6ff','icon'=>'bi-box-seam'],
  'dikirim'     => ['label'=>'Dikirim',    'color'=>'#8b5cf6','bg'=>'#f5f3ff','icon'=>'bi-truck'],
  'selesai'     => ['label'=>'Selesai',    'color'=>'#10b981','bg'=>'#ecfdf5','icon'=>'bi-check-circle'],
  'dibatalkan'  => ['label'=>'Dibatalkan', 'color'=>'#ef4444','bg'=>'#fef2f2','icon'=>'bi-x-circle'],
];
function dashStatusBadge(string $s, array $cfg): string {
  $c = $cfg[$s] ?? ['label'=>$s,'color'=>'#888','bg'=>'#f5f5f5','icon'=>'bi-dash'];
  return '<span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:50px;
    font-size:.71rem;font-weight:700;background:'.$c['bg'].';color:'.$c['color'].';">
    <i class="bi '.$c['icon'].'"></i>'.htmlspecialchars($c['label']).'</span>';
}
?>
<style>
/* ── KPI Cards ── */
.dash-kpi-grid {
  display:grid; grid-template-columns:repeat(4,1fr); gap:.85rem; margin-bottom:1.5rem;
}
.dash-kpi {
  background:#fff; border:1px solid #eeddd9; border-radius:14px;
  padding:1.1rem 1.25rem; display:flex; align-items:center; gap:.9rem;
  transition:box-shadow .2s, transform .2s;
}
.dash-kpi:hover { box-shadow:0 6px 20px rgba(180,100,120,.12); transform:translateY(-2px); }
.dash-kpi-icon {
  width:46px; height:46px; border-radius:12px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; font-size:1.25rem;
}
.dash-kpi-num  { font-size:1.55rem; font-weight:800; color:#2c1a1e; line-height:1.1; }
.dash-kpi-lbl  { font-size:.72rem; color:#8a7070; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-top:.15rem; }
.dash-kpi-trend {
  font-size:.7rem; font-weight:700; margin-top:.2rem;
  display:inline-flex; align-items:center; gap:.2rem;
}

/* ── 2-col grid ── */
.dash-two-col { display:grid; grid-template-columns:1.4fr 1fr; gap:.85rem; margin-bottom:.85rem; }

/* ── Card base ── */
.dash-card {
  background:#fff; border:1px solid #eeddd9; border-radius:14px; overflow:hidden;
}
.dash-card-header {
  display:flex; align-items:center; justify-content:space-between;
  padding:.9rem 1.1rem; border-bottom:1px solid #f5eded;
}
.dash-card-title {
  font-family:Georgia,serif; font-weight:700; font-size:.9rem; color:#2c1a1e;
}
.dash-card-body { padding:1.1rem; }

/* ── Recent orders table ── */
.dash-table { width:100%; border-collapse:collapse; }
.dash-table thead th {
  font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
  color:#8a7070; padding:.55rem .85rem; border-bottom:2px solid #f0e8e8;
  background:#fdfbfb; white-space:nowrap;
}
.dash-table tbody td {
  padding:.7rem .85rem; border-bottom:1px solid #f5eded;
  font-size:.82rem; vertical-align:middle;
}
.dash-table tbody tr:last-child td { border-bottom:none; }
.dash-table tbody tr:hover td { background:#fdfbfb; }
.dash-order-code {
  font-family:monospace; font-weight:700; font-size:.8rem;
  color:#c9516f; background:#fce8ef; padding:.18rem .5rem; border-radius:6px;
}

/* ── Quick actions ── */
.dash-actions { display:grid; grid-template-columns:1fr 1fr; gap:.6rem; }
.dash-action-btn {
  display:flex; align-items:center; gap:.6rem;
  padding:.75rem .9rem; border-radius:10px;
  border:1.5px solid #eeddd9; background:#fff;
  color:#2c1a1e; text-decoration:none; font-size:.82rem; font-weight:600;
  transition:all .15s;
}
.dash-action-btn:hover { border-color:#c9516f; background:#fce8ef; color:#c9516f; }
.dash-action-btn i { font-size:1rem; }

/* ── Activity feed ── */
.dash-activity { display:flex; flex-direction:column; gap:.7rem; }
.dash-activity-item {
  display:flex; align-items:flex-start; gap:.7rem;
}
.dash-activity-dot {
  width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:.35rem;
}
.dash-activity-text { font-size:.81rem; color:#2c1a1e; line-height:1.4; }
.dash-activity-time { font-size:.7rem; color:#8a7070; margin-top:.1rem; }

/* ── Greeting bar ── */
.dash-greeting {
  background:linear-gradient(135deg,#fce8ef 0%,#fff5f7 60%,#fff 100%);
  border:1px solid #eeddd9; border-radius:14px;
  padding:1.1rem 1.5rem; margin-bottom:1.25rem;
  display:flex; align-items:center; justify-content:space-between; gap:1rem;
}
.dash-greeting-left h2 { font-family:Georgia,serif; font-size:1.15rem; font-weight:700; color:#2c1a1e; margin:0 0 .2rem; }
.dash-greeting-left p  { font-size:.8rem; color:#8a7070; margin:0; }
.dash-greeting-badge {
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.3rem .85rem; border-radius:50px; font-size:.75rem; font-weight:700;
  background:#c9516f; color:#fff;
}

@media(max-width:1024px){ .dash-kpi-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:768px) {
  .dash-kpi-grid{ grid-template-columns:repeat(2,1fr); }
  .dash-two-col { grid-template-columns:1fr; }
  .dash-actions { grid-template-columns:1fr 1fr; }
}
@media(max-width:480px){
  .dash-kpi-grid{ grid-template-columns:1fr 1fr; }
}
</style>

<!-- Greeting -->
<div class="dash-greeting">
  <div class="dash-greeting-left">
    <h2>Selamat datang, <?= e($_SESSION['name'] ?? 'Admin') ?> 👋</h2>
    <p><?= date('l, d F Y') ?> — Berikut ringkasan aktivitas toko kamu hari ini.</p>
  </div>
  <span class="dash-greeting-badge"><i class="bi bi-shield-check"></i> Admin</span>
</div>

<!-- KPI Cards -->
<div class="dash-kpi-grid">
  <div class="dash-kpi">
    <div class="dash-kpi-icon" style="background:#fce8ef;color:#c9516f;"><i class="bi bi-flower1"></i></div>
    <div>
      <div class="dash-kpi-num"><?= number_format($total_produk) ?></div>
      <div class="dash-kpi-lbl">Total Produk</div>
      <div class="dash-kpi-trend" style="color:#10b981;"><i class="bi bi-arrow-up-short"></i> Aktif</div>
    </div>
  </div>
  <div class="dash-kpi">
    <div class="dash-kpi-icon" style="background:#eff6ff;color:#3b82f6;"><i class="bi bi-bag-check"></i></div>
    <div>
      <div class="dash-kpi-num"><?= number_format($total_pesanan) ?></div>
      <div class="dash-kpi-lbl">Total Pesanan</div>
      <div class="dash-kpi-trend" style="color:#3b82f6;"><i class="bi bi-graph-up"></i> Keseluruhan</div>
    </div>
  </div>
  <div class="dash-kpi">
    <div class="dash-kpi-icon" style="background:#f5f3ff;color:#8b5cf6;"><i class="bi bi-people"></i></div>
    <div>
      <div class="dash-kpi-num"><?= number_format($total_pengguna) ?></div>
      <div class="dash-kpi-lbl">Total Pengguna</div>
      <div class="dash-kpi-trend" style="color:#8b5cf6;"><i class="bi bi-person-plus"></i> Customer</div>
    </div>
  </div>
  <div class="dash-kpi">
    <div class="dash-kpi-icon" style="background:#ecfdf5;color:#10b981;"><i class="bi bi-currency-dollar"></i></div>
    <div>
      <div class="dash-kpi-num" style="font-size:1.05rem;"><?= rupiah($total_pendapatan) ?></div>
      <div class="dash-kpi-lbl">Total Pendapatan</div>
      <div class="dash-kpi-trend" style="color:#10b981;"><i class="bi bi-check-circle"></i> Selesai</div>
    </div>
  </div>
</div>

<!-- 2-col: recent orders + quick actions -->
<div class="dash-two-col">

  <!-- Recent orders -->
  <div class="dash-card">
    <div class="dash-card-header">
      <span class="dash-card-title">Pesanan Terbaru</span>
      <a href="<?= site_url('admin/pesanan.php') ?>"
         style="font-size:.78rem;font-weight:600;color:#c9516f;text-decoration:none;">
        Lihat semua <i class="bi bi-arrow-right"></i>
      </a>
    </div>
    <div style="overflow-x:auto;">
      <table class="dash-table">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Pelanggan</th>
            <th>Total</th>
            <th>Status</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($pesanan_baru)): ?>
            <tr><td colspan="5" style="text-align:center;color:#8a7070;padding:2rem;">Belum ada pesanan</td></tr>
          <?php endif; ?>
          <?php foreach ($pesanan_baru as $p): ?>
          <tr>
            <td><span class="dash-order-code">#<?= e($p['order_code']) ?></span></td>
            <td style="font-weight:500;"><?= e($p['customer_name']) ?></td>
            <td style="font-weight:700;white-space:nowrap;"><?= rupiah($p['total']) ?></td>
            <td><?= dashStatusBadge($p['status'], $statusCfg) ?></td>
            <td style="color:#8a7070;font-size:.78rem;white-space:nowrap;"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right column: quick actions + activity -->
  <div style="display:flex;flex-direction:column;gap:.85rem;">

    <!-- Quick actions -->
    <div class="dash-card">
      <div class="dash-card-header">
        <span class="dash-card-title">Aksi Cepat</span>
      </div>
      <div class="dash-card-body">
        <div class="dash-actions">
          <a href="<?= site_url('admin/produk.php?action=add') ?>" class="dash-action-btn">
            <i class="bi bi-plus-circle"></i> Tambah Produk
          </a>
          <a href="<?= site_url('admin/pesanan.php?status=belum_bayar') ?>" class="dash-action-btn">
            <i class="bi bi-clock"></i> Belum Bayar
          </a>
          <a href="<?= site_url('admin/kategori.php') ?>" class="dash-action-btn">
            <i class="bi bi-tags"></i> Kategori
          </a>
          <a href="<?= site_url('admin/laporan.php') ?>" class="dash-action-btn">
            <i class="bi bi-graph-up"></i> Laporan
          </a>
          <a href="<?= site_url('admin/pengguna.php') ?>" class="dash-action-btn">
            <i class="bi bi-people"></i> Pengguna
          </a>
          <a href="<?= site_url('admin/ulasan.php') ?>" class="dash-action-btn">
            <i class="bi bi-star"></i> Ulasan
          </a>
        </div>
      </div>
    </div>

    <!-- Info card -->
    <div class="dash-card">
      <div class="dash-card-header">
        <span class="dash-card-title">Info Sistem</span>
      </div>
      <div class="dash-card-body">
        <div style="display:flex;flex-direction:column;gap:.55rem;">
          <?php
          $infos = [
            ['bi-calendar3',   'Hari ini',  date('d F Y')],
            ['bi-clock',       'Waktu',     date('H:i') . ' WIB'],
            ['bi-server',      'PHP',       PHP_VERSION],
            ['bi-globe',       'Host',      $_SERVER['HTTP_HOST'] ?? 'localhost'],
          ];
          foreach ($infos as [$icon, $lbl, $val]):
          ?>
          <div style="display:flex;align-items:center;justify-content:space-between;font-size:.8rem;">
            <span style="color:#8a7070;display:flex;align-items:center;gap:.35rem;">
              <i class="bi <?= $icon ?>"></i> <?= $lbl ?>
            </span>
            <span style="font-weight:600;color:#2c1a1e;"><?= e($val) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  </div>
</div>
