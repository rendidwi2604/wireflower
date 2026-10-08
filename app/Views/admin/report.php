<?php
// Build chart data from orders
$dailyData = [];
$statusCount = ['belum_bayar'=>0,'dikemas'=>0,'dikirim'=>0,'selesai'=>0,'dibatalkan'=>0];
$dailyRevenue = [];

foreach ($orders as $o) {
  $day = date('d M', strtotime($o['created_at']));
  $dailyData[$day] = ($dailyData[$day] ?? 0) + (float)$o['total'];
  if (isset($statusCount[$o['status']])) $statusCount[$o['status']]++;
}

// Top 6 products for bar chart
$topProds = array_slice($produk_terlaris, 0, 6);

$statusCfg = [
  'belum_bayar'=>['label'=>'Belum Bayar','color'=>'#f59e0b'],
  'dikemas'    =>['label'=>'Dikemas',    'color'=>'#3b82f6'],
  'dikirim'    =>['label'=>'Dikirim',    'color'=>'#8b5cf6'],
  'selesai'    =>['label'=>'Selesai',    'color'=>'#10b981'],
  'dibatalkan' =>['label'=>'Dibatalkan', 'color'=>'#ef4444'],
];
?>
<style>
.rpt-filter-card {
  background:#fff; border:1px solid #eeddd9; border-radius:14px;
  padding:1rem 1.25rem; margin-bottom:1.25rem;
  display:flex; align-items:center; gap:1rem; flex-wrap:wrap;
}
.rpt-filter-card label { font-size:.78rem; font-weight:600; color:#5a4040; display:block; margin-bottom:.3rem; }
.rpt-date-input {
  padding:.48rem .85rem; border:1.5px solid #eeddd9; border-radius:8px;
  font-size:.83rem; color:#2c1a1e; outline:none; background:#fff;
  transition:border-color .15s;
}
.rpt-date-input:focus { border-color:#c9516f; }
.rpt-filter-btn {
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.48rem 1.1rem; border-radius:8px; background:#c9516f; color:#fff;
  border:none; font-size:.83rem; font-weight:700; cursor:pointer; transition:background .15s;
}
.rpt-filter-btn:hover { background:#b8455f; }

/* KPI row */
.rpt-kpi-row { display:grid; grid-template-columns:repeat(3,1fr); gap:.85rem; margin-bottom:1.25rem; }
.rpt-kpi {
  background:#fff; border:1px solid #eeddd9; border-radius:14px;
  padding:1rem 1.25rem; display:flex; align-items:center; gap:.85rem;
}
.rpt-kpi-icon { width:44px; height:44px; border-radius:11px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
.rpt-kpi-num  { font-size:1.35rem; font-weight:800; color:#2c1a1e; line-height:1.1; }
.rpt-kpi-lbl  { font-size:.71rem; color:#8a7070; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-top:.15rem; }

/* Charts grid */
.rpt-charts-row { display:grid; grid-template-columns:1.6fr 1fr; gap:.85rem; margin-bottom:1.25rem; }
.rpt-chart-card { background:#fff; border:1px solid #eeddd9; border-radius:14px; overflow:hidden; }
.rpt-chart-header { padding:.85rem 1.1rem; border-bottom:1px solid #f5eded; display:flex; align-items:center; justify-content:space-between; }
.rpt-chart-title  { font-family:Georgia,serif; font-weight:700; font-size:.88rem; color:#2c1a1e; }
.rpt-chart-body   { padding:1rem; }

/* Tables */
.rpt-card { background:#fff; border:1px solid #eeddd9; border-radius:14px; overflow:hidden; margin-bottom:.85rem; }
.rpt-card-header { padding:.85rem 1.1rem; border-bottom:1px solid #f5eded; display:flex; align-items:center; justify-content:space-between; }
.rpt-card-title  { font-family:Georgia,serif; font-weight:700; font-size:.88rem; color:#2c1a1e; }
.rpt-table-wrap  { overflow-x:auto; }
.rpt-table { width:100%; border-collapse:collapse; min-width:480px; }
.rpt-table thead th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#8a7070; padding:.6rem .9rem; border-bottom:2px solid #f0e8e8; background:#fdfbfb; white-space:nowrap; }
.rpt-table tbody td { padding:.72rem .9rem; border-bottom:1px solid #f5eded; font-size:.82rem; vertical-align:middle; }
.rpt-table tbody tr:last-child td { border-bottom:none; }
.rpt-table tbody tr:hover td { background:#fdfbfb; }
.rpt-rank { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; font-size:.7rem; font-weight:800; }

.rpt-order-code { font-family:monospace; font-weight:700; font-size:.8rem; color:#c9516f; background:#fce8ef; padding:.18rem .5rem; border-radius:6px; }

@media(max-width:900px)  { .rpt-charts-row { grid-template-columns:1fr; } }
@media(max-width:768px)  { .rpt-kpi-row { grid-template-columns:1fr 1fr; } }
@media(max-width:480px)  { .rpt-kpi-row { grid-template-columns:1fr; } }
</style>

<!-- Filter -->
<div class="rpt-filter-card">
  <i class="bi bi-funnel" style="color:#c9516f;font-size:1.1rem;flex-shrink:0;"></i>
  <form method="GET" action="" style="display:flex;align-items:flex-end;gap:.75rem;flex-wrap:wrap;flex:1;">
    <div>
      <label>Dari Tanggal</label>
      <input type="date" name="start" value="<?= e($start) ?>" class="rpt-date-input">
    </div>
    <div>
      <label>Sampai Tanggal</label>
      <input type="date" name="end" value="<?= e($end) ?>" class="rpt-date-input">
    </div>
    <button type="submit" class="rpt-filter-btn">
      <i class="bi bi-search"></i> Tampilkan
    </button>
  </form>
  <div style="font-size:.75rem;color:#8a7070;flex-shrink:0;">
    <?= date('d M Y', strtotime($start)) ?> — <?= date('d M Y', strtotime($end)) ?>
  </div>
</div>

<!-- KPI Row -->
<div class="rpt-kpi-row">
  <div class="rpt-kpi">
    <div class="rpt-kpi-icon" style="background:#ecfdf5;color:#10b981;"><i class="bi bi-currency-dollar"></i></div>
    <div>
      <div class="rpt-kpi-num"><?= rupiah($summary['total']) ?></div>
      <div class="rpt-kpi-lbl">Total Pendapatan</div>
    </div>
  </div>
  <div class="rpt-kpi">
    <div class="rpt-kpi-icon" style="background:#eff6ff;color:#3b82f6;"><i class="bi bi-bag-check"></i></div>
    <div>
      <div class="rpt-kpi-num"><?= number_format($summary['jumlah']) ?></div>
      <div class="rpt-kpi-lbl">Total Pesanan</div>
    </div>
  </div>
  <div class="rpt-kpi">
    <div class="rpt-kpi-icon" style="background:#fce8ef;color:#c9516f;"><i class="bi bi-receipt"></i></div>
    <div>
      <div class="rpt-kpi-num">
        <?= $summary['jumlah'] > 0 ? rupiah($summary['total'] / $summary['jumlah']) : 'Rp 0' ?>
      </div>
      <div class="rpt-kpi-lbl">Rata-rata / Pesanan</div>
    </div>
  </div>
</div>

<!-- Charts row -->
<div class="rpt-charts-row">
  <!-- Line/Bar chart: revenue per day -->
  <div class="rpt-chart-card">
    <div class="rpt-chart-header">
      <span class="rpt-chart-title"><i class="bi bi-bar-chart-line" style="color:#c9516f;margin-right:.4rem;"></i>Pendapatan Harian</span>
      <span style="font-size:.73rem;color:#8a7070;"><?= count($dailyData) ?> hari</span>
    </div>
    <div class="rpt-chart-body">
      <canvas id="chartRevenue" height="200"></canvas>
      <?php if (empty($dailyData)): ?>
        <p style="text-align:center;color:#8a7070;font-size:.82rem;padding:1.5rem 0;">Tidak ada data pada periode ini.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Pie chart: status distribution -->
  <div class="rpt-chart-card">
    <div class="rpt-chart-header">
      <span class="rpt-chart-title"><i class="bi bi-pie-chart" style="color:#c9516f;margin-right:.4rem;"></i>Status Pesanan</span>
    </div>
    <div class="rpt-chart-body" style="display:flex;flex-direction:column;align-items:center;gap:.85rem;">
      <canvas id="chartStatus" style="max-width:200px;max-height:200px;"></canvas>
      <!-- Legend -->
      <div style="width:100%;display:flex;flex-direction:column;gap:.35rem;">
        <?php foreach ($statusCfg as $key => $cfg): if ($statusCount[$key] === 0) continue; ?>
        <div style="display:flex;align-items:center;justify-content:space-between;font-size:.78rem;">
          <span style="display:flex;align-items:center;gap:.4rem;">
            <span style="width:10px;height:10px;border-radius:3px;background:<?= $cfg['color'] ?>;flex-shrink:0;display:inline-block;"></span>
            <?= $cfg['label'] ?>
          </span>
          <span style="font-weight:700;color:#2c1a1e;"><?= $statusCount[$key] ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (array_sum($statusCount) === 0): ?>
          <p style="text-align:center;color:#8a7070;font-size:.8rem;">Tidak ada data.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Bar chart: top products -->
<?php if (!empty($topProds)): ?>
<div class="rpt-chart-card" style="margin-bottom:.85rem;">
  <div class="rpt-chart-header">
    <span class="rpt-chart-title"><i class="bi bi-trophy" style="color:#f59e0b;margin-right:.4rem;"></i>Produk Terlaris</span>
    <span style="font-size:.73rem;color:#8a7070;">Top <?= count($topProds) ?></span>
  </div>
  <div class="rpt-chart-body">
    <canvas id="chartProducts" height="140"></canvas>
  </div>
</div>
<?php endif; ?>

<!-- Top products table -->
<div class="rpt-card">
  <div class="rpt-card-header">
    <span class="rpt-card-title">Detail Produk Terlaris</span>
    <span style="font-size:.75rem;color:#8a7070;"><?= count($produk_terlaris) ?> produk</span>
  </div>
  <?php if (empty($produk_terlaris)): ?>
    <div style="text-align:center;padding:2rem;color:#8a7070;font-size:.83rem;">Tidak ada data produk pada periode ini.</div>
  <?php else: ?>
  <div class="rpt-table-wrap">
    <table class="rpt-table">
      <thead>
        <tr><th>#</th><th>Nama Produk</th><th>Terjual</th><th>Pendapatan</th></tr>
      </thead>
      <tbody>
        <?php foreach ($produk_terlaris as $i => $p): ?>
        <tr>
          <td>
            <?php if ($i === 0): ?>
              <span class="rpt-rank" style="background:#fef9c3;color:#a16207;">🥇</span>
            <?php elseif ($i === 1): ?>
              <span class="rpt-rank" style="background:#f1f5f9;color:#64748b;">🥈</span>
            <?php elseif ($i === 2): ?>
              <span class="rpt-rank" style="background:#fff7ed;color:#c2410c;">🥉</span>
            <?php else: ?>
              <span class="rpt-rank" style="background:#f5eded;color:#8a7070;"><?= $i+1 ?></span>
            <?php endif; ?>
          </td>
          <td style="font-weight:600;"><?= e($p['product_name']) ?></td>
          <td>
            <span style="display:inline-flex;align-items:center;gap:.3rem;background:#eff6ff;color:#3b82f6;padding:.2rem .6rem;border-radius:50px;font-size:.75rem;font-weight:700;">
              <i class="bi bi-bag"></i> <?= number_format($p['total_terjual']) ?>
            </span>
          </td>
          <td style="font-weight:700;color:#10b981;"><?= rupiah($p['total_pendapatan']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Transactions table -->
<div class="rpt-card">
  <div class="rpt-card-header">
    <span class="rpt-card-title">Detail Transaksi</span>
    <span style="font-size:.75rem;color:#8a7070;"><?= count($orders) ?> transaksi</span>
  </div>
  <?php if (empty($orders)): ?>
    <div style="text-align:center;padding:2rem;color:#8a7070;font-size:.83rem;">Tidak ada transaksi pada periode ini.</div>
  <?php else: ?>
  <div class="rpt-table-wrap">
    <table class="rpt-table">
      <thead>
        <tr><th>Kode Pesanan</th><th>Total</th><th>Status</th><th>Tanggal</th></tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o):
          $sc = $statusCfg[$o['status']] ?? ['label'=>$o['status'],'color'=>'#888'];
        ?>
        <tr>
          <td><span class="rpt-order-code">#<?= e($o['order_code']) ?></span></td>
          <td style="font-weight:700;"><?= rupiah($o['total']) ?></td>
          <td>
            <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:50px;font-size:.71rem;font-weight:700;color:<?= $sc['color'] ?>;background:<?= $sc['color'] ?>18;">
              <?= e($sc['label']) ?>
            </span>
          </td>
          <td style="color:#8a7070;font-size:.78rem;"><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
Chart.defaults.font.size   = 11;
Chart.defaults.color       = '#8a7070';

// ── Revenue bar chart ──
(function () {
  var ctx = document.getElementById('chartRevenue');
  if (!ctx) return;
  var labels = <?= json_encode(array_keys($dailyData)) ?>;
  var data   = <?= json_encode(array_values($dailyData)) ?>;
  if (!labels.length) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Pendapatan (Rp)',
        data: data,
        backgroundColor: 'rgba(201,81,111,.18)',
        borderColor: '#c9516f',
        borderWidth: 2,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        x: { grid: { display: false }, ticks: { maxRotation: 45 } },
        y: {
          grid: { color: '#f5eded' },
          ticks: {
            callback: function(v) {
              return v >= 1000000 ? 'Rp ' + (v/1000000).toFixed(1) + 'jt'
                   : v >= 1000   ? 'Rp ' + (v/1000).toFixed(0) + 'rb'
                   : 'Rp ' + v;
            }
          }
        }
      }
    }
  });
})();

// ── Status pie chart ──
(function () {
  var ctx = document.getElementById('chartStatus');
  if (!ctx) return;
  var labels = <?= json_encode(array_map(fn($k,$v)=>$v['label'], array_keys($statusCfg), $statusCfg)) ?>;
  var data   = <?= json_encode(array_values($statusCount)) ?>;
  var colors = <?= json_encode(array_column($statusCfg, 'color')) ?>;
  if (data.reduce((a,b)=>a+b,0) === 0) return;
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{ data: data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }]
    },
    options: {
      responsive: true,
      cutout: '62%',
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: function(c){ return c.label + ': ' + c.raw; } } }
      }
    }
  });
})();

// ── Products bar chart ──
(function () {
  var ctx = document.getElementById('chartProducts');
  if (!ctx) return;
  var labels = <?= json_encode(array_column($topProds, 'product_name')) ?>;
  var data   = <?= json_encode(array_map(fn($p)=>(int)$p['total_terjual'], $topProds)) ?>;
  if (!labels.length) return;
  var colors = ['#c9516f','#e8799a','#f4a8be','#3b82f6','#8b5cf6','#10b981'];
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Terjual',
        data: data,
        backgroundColor: colors.slice(0, labels.length),
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color:'#f5eded' }, ticks: { stepSize: 1 } },
        y: { grid: { display: false } }
      }
    }
  });
})();
</script>
