<?php $nav = basename($_SERVER['SCRIPT_NAME']); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?> - Admin Wire Flower</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="<?= site_url('assets/css/style.css') ?>">
<style>
  .admin-sidebar { min-height: 100vh; background: #fff; border-right: 1px solid #eee; }
  .admin-sidebar a { display:block; padding:.6rem 1rem; color:#555; text-decoration:none; border-radius:8px; margin-bottom:2px; }
  .admin-sidebar a.active, .admin-sidebar a:hover { background: var(--pink-light); color: var(--pink-dark); }
</style>
</head>
<body>
<div class="d-flex">
  <div class="admin-sidebar p-3" style="width:230px;">
    <h5 class="text-pink fw-bold mb-4">🌸 Wire Flower Admin</h5>
    <a href="<?= site_url('admin/index.php') ?>" class="<?= $nav === 'index.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="<?= site_url('admin/produk.php') ?>" class="<?= $nav === 'produk.php' ? 'active' : '' ?>"><i class="bi bi-flower1 me-2"></i>Kelola Produk</a>
    <a href="<?= site_url('admin/kategori.php') ?>" class="<?= $nav === 'kategori.php' ? 'active' : '' ?>"><i class="bi bi-tags me-2"></i>Kelola Kategori</a>
    <a href="<?= site_url('admin/pesanan.php') ?>" class="<?= $nav === 'pesanan.php' ? 'active' : '' ?>"><i class="bi bi-bag-check me-2"></i>Kelola Pesanan</a>
    <a href="<?= site_url('admin/pembayaran.php') ?>" class="<?= $nav === 'pembayaran.php' ? 'active' : '' ?>"><i class="bi bi-credit-card me-2"></i>Kelola Pembayaran</a>
    <a href="<?= site_url('admin/pengguna.php') ?>" class="<?= $nav === 'pengguna.php' ? 'active' : '' ?>"><i class="bi bi-people me-2"></i>Kelola Pengguna</a>
    <a href="<?= site_url('admin/ulasan.php') ?>" class="<?= $nav === 'ulasan.php' ? 'active' : '' ?>"><i class="bi bi-star me-2"></i>Kelola Ulasan</a>
    <a href="<?= site_url('admin/laporan.php') ?>" class="<?= $nav === 'laporan.php' ? 'active' : '' ?>"><i class="bi bi-graph-up me-2"></i>Laporan Penjualan</a>
    <hr>
    <a href="<?= site_url('index.php') ?>"><i class="bi bi-house me-2"></i>Ke Website</a>
    <a href="<?= site_url('auth/logout.php') ?>" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
  </div>
  <div class="flex-grow-1 p-4">
    <?php if (isset($_SESSION['flash'])): ?>
      <div class="alert alert-<?= e($_SESSION['flash']['type']) ?> alert-dismissible fade show">
        <?= e($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?= $content ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
