<?php
$icon_map = [
    'order_created'   => ['icon' => 'bi-bag-check',    'color' => 'var(--pink-deep)'],
    'payment_success' => ['icon' => 'bi-credit-card',  'color' => '#1a6b3a'],
    'order_packing'   => ['icon' => 'bi-box-seam',     'color' => '#2c5ce0'],
    'order_shipped'   => ['icon' => 'bi-truck',        'color' => '#a0640a'],
    'order_done'      => ['icon' => 'bi-check-circle', 'color' => '#1a6b3a'],
];
?>
<div class="container" style="max-width:640px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Notifikasi</h1>
    <p>Pembaruan status pesanan dan aktivitas akunmu.</p>
  </div>

  <?php if (empty($notifs)): ?>
    <div style="text-align:center;padding:4rem 2rem;color:var(--text-muted);">
      <i class="bi bi-bell" style="font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.3;"></i>
      <p style="margin:0;font-size:.9rem;">Belum ada notifikasi.</p>
    </div>
  <?php endif; ?>

  <?php foreach ($notifs as $n):
    $ic = $icon_map[$n['type']] ?? ['icon'=>'bi-bell','color'=>'var(--pink-deep)'];
  ?>
  <div class="notif-item <?= !$n['is_read'] ? 'unread' : '' ?>">
    <div class="notif-icon" style="background:<?= $ic['color'] ?>1a;color:<?= $ic['color'] ?>;">
      <i class="bi <?= $ic['icon'] ?>"></i>
    </div>
    <div class="notif-text">
      <strong style="display:block;margin-bottom:.15rem;"><?= e($n['title']) ?></strong>
      <?= e($n['message']) ?>
      <span class="notif-time"><?= date('d M Y · H:i', strtotime($n['created_at'])) ?></span>
    </div>
    <?php if (!$n['is_read']): ?>
      <span style="width:8px;height:8px;border-radius:50%;background:var(--pink-deep);flex-shrink:0;margin-top:.35rem;"></span>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>

</div>
