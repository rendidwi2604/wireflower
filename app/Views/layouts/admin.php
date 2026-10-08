<?php
// Deteksi halaman aktif dari REQUEST_URI (kompatibel Vercel + XAMPP)
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$currentUri = '/' . trim($currentUri, '/');

function adminNavActive(string $path, string $currentUri): string {
    // Cocokkan suffix path, misal '/admin/produk.php' ada di URI manapun
    return (str_ends_with($currentUri, $path) || str_ends_with($currentUri, rtrim($path, '/')))
        ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?> — Admin WireFlower</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ── Reset ── */
*, *::before, *::after { box-sizing: border-box; }
body { font-family: 'Inter', system-ui, sans-serif; font-size: 14px; background: #f7f5f3; color: #2c1a1e; display: flex; min-height: 100vh; margin: 0; padding: 0; }

/* ── Sidebar ── */
.adm-sidebar {
  width: 230px;
  flex-shrink: 0;
  background: #fff;
  border-right: 1px solid #eeddd9;
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0; left: 0; bottom: 0;
  overflow-y: auto;
  z-index: 100;
}

.adm-brand {
  display: flex;
  align-items: center;
  gap: .5rem;
  padding: 1.25rem 1.25rem 1rem;
  font-family: Georgia, serif;
  font-size: 1rem;
  font-weight: 700;
  color: #2c1a1e;
  border-bottom: 1px solid #eeddd9;
  text-decoration: none;
}
.adm-brand img { width: 26px; height: 26px; object-fit: contain; }
.adm-brand-dot { width: 8px; height: 8px; border-radius: 50%; background: #c9516f; flex-shrink: 0; }

.adm-nav { padding: .75rem .75rem 0; flex: 1; }

.adm-nav-label {
  font-size: .65rem;
  font-weight: 700;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: #b89898;
  padding: .85rem .5rem .35rem;
}

.adm-nav a {
  display: flex;
  align-items: center;
  gap: .6rem;
  padding: .55rem .75rem;
  border-radius: 8px;
  color: #5a4040;
  text-decoration: none;
  font-weight: 500;
  font-size: .83rem;
  transition: background .15s, color .15s;
  margin-bottom: 2px;
  white-space: nowrap;
}
.adm-nav a i { font-size: .95rem; width: 16px; text-align: center; flex-shrink: 0; }
.adm-nav a:hover { background: #fce8ef; color: #c9516f; }
.adm-nav a.active {
  background: linear-gradient(90deg, #fce8ef, #fdf4f6);
  color: #c9516f;
  font-weight: 600;
  border-left: 3px solid #c9516f;
  padding-left: calc(.75rem - 3px);
}
.adm-nav a.active i { color: #c9516f; }

.adm-nav-divider { height: 1px; background: #eeddd9; margin: .75rem .5rem; }

.adm-nav a.danger { color: #c0392b; }
.adm-nav a.danger:hover { background: #fdf0f0; color: #c0392b; }

/* ── Main content ── */
.adm-main {
  margin-left: 230px;
  flex: 1;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.adm-topbar {
  background: #fff;
  border-bottom: 1px solid #eeddd9;
  padding: .85rem 1.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 99;
}
.adm-topbar-title { font-family: Georgia, serif; font-size: 1.05rem; font-weight: 700; color: #2c1a1e; }
.adm-topbar-user { font-size: .8rem; color: #8a7070; display: flex; align-items: center; gap: .4rem; }

.adm-body { padding: 1.5rem 1.75rem; flex: 1; }

/* ── Flash alert ── */
.adm-alert {
  display: flex; align-items: center; gap: .75rem;
  padding: .8rem 1rem; border-radius: 10px;
  font-size: .85rem; font-weight: 500; margin-bottom: 1.25rem;
}
.adm-alert.success { background: #eafaf0; color: #1a6b3a; border: 1px solid #b2e0c4; }
.adm-alert.danger  { background: #fdf0f0; color: #8b1a1a; border: 1px solid #f0c0c0; }
.adm-alert.info    { background: #fce8ef; color: #c9516f; border: 1px solid #f0c0d0; }
.adm-alert.warning { background: #fff8e1; color: #a0640a; border: 1px solid #f5dfa0; }
.adm-alert-close { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 1rem; opacity: .5; color: inherit; }
.adm-alert-close:hover { opacity: 1; }

/* ── Cards & tables ── */
.adm-card { background: #fff; border-radius: 12px; border: 1px solid #eeddd9; padding: 1.25rem; margin-bottom: 1.25rem; }
.adm-card-title { font-family: Georgia, serif; font-size: .95rem; font-weight: 700; margin-bottom: 1rem; color: #2c1a1e; }

table { width: 100%; border-collapse: collapse; }
thead th { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #8a7070; padding: .6rem .85rem; border-bottom: 2px solid #eeddd9; white-space: nowrap; }
tbody td { padding: .7rem .85rem; border-bottom: 1px solid #f5eded; font-size: .84rem; vertical-align: middle; }
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover td { background: #fdfbfb; }

/* ── Buttons ── */
.btn-adm { display: inline-flex; align-items: center; gap: .35rem; padding: .42rem .9rem; border-radius: 7px; font-size: .8rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .15s; }
.btn-adm-primary { background: #c9516f; color: #fff; }
.btn-adm-primary:hover { background: #b8455f; color: #fff; }
.btn-adm-outline { background: transparent; color: #5a4040; border: 1.5px solid #eeddd9; }
.btn-adm-outline:hover { background: #fce8ef; color: #c9516f; border-color: #c9516f; }
.btn-adm-danger { background: transparent; color: #c0392b; border: 1.5px solid #f0c0c0; }
.btn-adm-danger:hover { background: #fdf0f0; }
.btn-adm-sm { padding: .3rem .7rem; font-size: .75rem; }

/* ── Form controls ── */
.adm-form-group { margin-bottom: .85rem; }
.adm-form-label { display: block; font-size: .78rem; font-weight: 600; color: #5a4040; margin-bottom: .35rem; }
.adm-form-control {
  width: 100%; padding: .6rem .85rem; border: 1.5px solid #eeddd9;
  border-radius: 8px; font-size: .85rem; font-family: inherit;
  background: #fdfcfb; color: #2c1a1e; transition: border-color .15s;
  outline: none;
}
.adm-form-control:focus { border-color: #e8799a; box-shadow: 0 0 0 3px rgba(232,121,154,.1); }

/* ── Badge ── */
.badge-active   { display:inline-block;padding:.2rem .6rem;border-radius:50px;font-size:.7rem;font-weight:700;background:#eafaf0;color:#1a6b3a; }
.badge-inactive { display:inline-block;padding:.2rem .6rem;border-radius:50px;font-size:.7rem;font-weight:700;background:#f5f5f5;color:#888; }

/* ── Responsive ── */
@media (max-width: 768px) {
  body { display: block; }

  /* Sidebar — slide in/out dari kiri */
  .adm-sidebar {
    transform: translateX(-100%);
    transition: transform .28s cubic-bezier(.4,0,.2,1);
    z-index: 300;
    width: 260px;
  }
  .adm-sidebar.open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,.15); }

  /* Overlay backdrop */
  .adm-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 299;
    background: rgba(20,8,10,.45);
    backdrop-filter: blur(2px);
  }
  .adm-overlay.show { display: block; }

  /* Main area full width */
  .adm-main { margin-left: 0; }

  /* Topbar: hamburger kiri, title tengah, user kanan */
  .adm-topbar {
    padding: .7rem 1rem;
    gap: .75rem;
  }
  .adm-topbar-title { font-size: .95rem; flex: 1; text-align: center; }
  .adm-topbar-user { font-size: .75rem; }
  .adm-toggler {
    display: flex !important;
    align-items: center; justify-content: center;
    width: 38px; height: 38px;
    border: none; background: none;
    font-size: 1.4rem; cursor: pointer;
    color: #2c1a1e; flex-shrink: 0;
    border-radius: 8px;
    transition: background .15s;
  }
  .adm-toggler:hover { background: #fce8ef; color: #c9516f; }

  /* Body padding kecil */
  .adm-body { padding: 1rem; }

  /* Card full width */
  .adm-card { padding: 1rem; }

  /* Tables — scrollable horizontal */
  .adm-card .table-wrap,
  .adm-body .table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 8px;
  }
  table { min-width: 500px; }
  thead th { font-size: .7rem; padding: .5rem .65rem; }
  tbody td { font-size: .8rem; padding: .55rem .65rem; }

  /* Bootstrap row/col pada dashboard */
  .row { margin: 0 -.4rem; }
  .col-md-3 { padding: 0 .4rem; }
}

/* Desktop: sembunyikan toggler dan overlay */
@media (min-width: 769px) {
  .adm-toggler { display: none; }
  .adm-overlay { display: none !important; }
}
</style>
</head>
<body>

<!-- ===== SIDEBAR OVERLAY (mobile) ===== -->
<div class="adm-overlay" id="admOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<aside class="adm-sidebar">
  <a href="<?= site_url('admin/index.php') ?>" class="adm-brand">
    <img src="<?= site_url('assets/img/logoWF.png') ?>" alt="Logo">
    WireFlower
    <span class="adm-brand-dot"></span>
  </a>

  <nav class="adm-nav">
    <div class="adm-nav-label">Menu Utama</div>

    <a href="<?= site_url('admin/index.php') ?>" class="<?= adminNavActive('/admin/index.php', $currentUri) ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="<?= site_url('admin/produk.php') ?>" class="<?= adminNavActive('/admin/produk.php', $currentUri) ?>">
      <i class="bi bi-flower1"></i> Kelola Produk
    </a>
    <a href="<?= site_url('admin/kategori.php') ?>" class="<?= adminNavActive('/admin/kategori.php', $currentUri) ?>">
      <i class="bi bi-tags"></i> Kelola Kategori
    </a>

    <div class="adm-nav-label">Transaksi</div>

    <a href="<?= site_url('admin/pesanan.php') ?>" class="<?= adminNavActive('/admin/pesanan.php', $currentUri) ?>">
      <i class="bi bi-bag-check"></i> Kelola Pesanan
    </a>
    <a href="<?= site_url('admin/pembayaran.php') ?>" class="<?= adminNavActive('/admin/pembayaran.php', $currentUri) ?>">
      <i class="bi bi-credit-card"></i> Pembayaran
    </a>

    <div class="adm-nav-label">Pengguna</div>

    <a href="<?= site_url('admin/pengguna.php') ?>" class="<?= adminNavActive('/admin/pengguna.php', $currentUri) ?>">
      <i class="bi bi-people"></i> Kelola Pengguna
    </a>
    <a href="<?= site_url('admin/ulasan.php') ?>" class="<?= adminNavActive('/admin/ulasan.php', $currentUri) ?>">
      <i class="bi bi-star"></i> Kelola Ulasan
    </a>
    <a href="<?= site_url('admin/laporan.php') ?>" class="<?= adminNavActive('/admin/laporan.php', $currentUri) ?>">
      <i class="bi bi-graph-up"></i> Laporan Penjualan
    </a>

    <div class="adm-nav-divider"></div>

    <a href="<?= site_url('index.php') ?>">
      <i class="bi bi-house"></i> Ke Website
    </a>
    <a href="<?= site_url('auth/logout.php') ?>" class="danger">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </nav>
</aside>

<!-- ===== MAIN ===== -->
<div class="adm-main">
  <!-- Topbar -->
  <div class="adm-topbar">
    <button class="adm-toggler" id="admToggler" aria-label="Toggle menu">
      <i class="bi bi-list"></i>
    </button>
    <span class="adm-topbar-title"><?= e($page_title ?? 'Admin') ?></span>
    <span class="adm-topbar-user">
      <i class="bi bi-person-circle"></i>
      <?= e($_SESSION['name'] ?? 'Admin') ?>
    </span>
  </div>

  <!-- Body -->
  <div class="adm-body">

    <!-- Flash -->
    <?php if (isset($_SESSION['flash'])): ?>
      <div class="adm-alert <?= e($_SESSION['flash']['type']) ?>" id="adm-flash">
        <i class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= e($_SESSION['flash']['message']) ?>
        <button class="adm-alert-close" onclick="document.getElementById('adm-flash').remove()">×</button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?= $content ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss flash after 4s
setTimeout(function(){ var f=document.getElementById('adm-flash'); if(f) f.remove(); }, 4000);
</script>
</body>
</html>
