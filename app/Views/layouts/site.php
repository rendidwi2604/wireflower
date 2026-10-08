<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) && $page_title !== '' ? e($page_title) . ' — WireFlower' : 'WireFlower — Bunga yang Tak Pernah Layu' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="<?= site_url('assets/css/style.css') ?>">
<style>
  /* Prevent FOUC */
  .nav-links { transition: none; }
</style>
</head>
<body>

<?php $is_auth_page = in_array($page_title ?? '', ['Login', 'Daftar', 'Register', 'Daftar Akun']); ?>

<?php if (!$is_auth_page): ?>
<!-- ===== NAVBAR ===== -->
<nav class="site-navbar">
  <div class="container">

    <!-- Brand -->
    <a class="nav-brand" href="<?= site_url('index.php') ?>">
      <img src="<?= site_url('assets/img/logoWF.png') ?>" alt="WireFlower Logo" class="nav-brand-img">
      WireFlower
    </a>

    <!-- Nav links -->
    <ul class="nav-links" id="navLinks">
      <li><a href="<?= site_url('index.php') ?>"
             class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Beranda</a></li>
      <li><a href="<?= site_url('kategori.php') ?>"
             class="<?= basename($_SERVER['PHP_SELF']) === 'kategori.php' ? 'active' : '' ?>">Kategori</a></li>

      <?php if (is_logged_in()): ?>
        <li>
          <a href="<?= site_url('keranjang.php') ?>"
             class="nav-link-icon <?= basename($_SERVER['PHP_SELF']) === 'keranjang.php' ? 'active' : '' ?>">
            <i class="bi bi-cart3"></i> Keranjang
            <?php if (($cart_count ?? 0) > 0): ?>
              <span class="nav-badge"><?= $cart_count ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li>
          <a href="<?= site_url('notifikasi.php') ?>"
             class="nav-link-icon <?= basename($_SERVER['PHP_SELF']) === 'notifikasi.php' ? 'active' : '' ?>">
            <i class="bi bi-bell"></i> Notifikasi
            <?php if (($unread_count ?? 0) > 0): ?>
              <span class="nav-badge"><?= $unread_count ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li>
          <a href="<?= site_url('pesanan.php') ?>"
             class="<?= basename($_SERVER['PHP_SELF']) === 'pesanan.php' ? 'active' : '' ?>">Pesanan</a>
        </li>
      <?php endif; ?>

    <!-- Right actions -->
    <div class="nav-actions">
      <?php if (is_logged_in()): ?>
        <!-- User dropdown -->
        <div class="nav-dropdown">
          <button class="nav-dropdown-toggle" type="button">
            <i class="bi bi-person-circle"></i>
            <span><?= e($_SESSION['name']) ?></span>
            <i class="bi bi-chevron-down" style="font-size:.65rem;opacity:.6;"></i>
          </button>
          <ul class="nav-dropdown-menu">
            <li><a href="<?= site_url('profil.php') ?>"><i class="bi bi-person"></i>Profil Saya</a></li>
            <li><a href="<?= site_url('pengaturan.php') ?>"><i class="bi bi-gear"></i>Pengaturan</a></li>
            <?php if (is_admin()): ?>
              <li><a href="<?= site_url('admin/index.php') ?>"><i class="bi bi-speedometer2"></i>Admin Panel</a></li>
            <?php endif; ?>
            <li><div class="divider"></div></li>
            <li><a href="<?= site_url('auth/logout.php') ?>" class="danger"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
          </ul>
        </div>

      <?php else: ?>
        <a href="<?= site_url('auth/login.php') ?>" class="btn btn-outline btn-sm">Login</a>
        <a href="<?= site_url('auth/register.php') ?>" class="btn btn-primary btn-sm">Daftar</a>
      <?php endif; ?>

      <!-- Mobile toggler -->
      <button class="nav-toggler" id="navToggler" aria-label="Toggle menu">
        <i class="bi bi-list"></i>
      </button>
    </div>

  </div>
</nav>
<!-- /NAVBAR -->
<?php endif; ?>

<!-- Flash messages -->
<?php if (isset($_SESSION['flash'])): ?>
<div class="flash-container">
  <div class="flash flash-<?= e($_SESSION['flash']['type']) ?>">
    <span><?= e($_SESSION['flash']['message']) ?></span>
    <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
  </div>
</div>
<?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Page content -->
<?= $content ?>

<?php if (!$is_auth_page): ?>
<!-- ===== FOOTER ===== -->
<footer class="site-footer">
  <div class="container">

    <div class="footer-grid">
      <!-- Brand col -->
      <div>
        <div class="footer-brand-name">
          <img src="<?= site_url('assets/img/logoWF.png') ?>" alt="WireFlower Logo" class="footer-brand-img">
          WireFlower
        </div>
        <p class="footer-brand-desc">
          Bunga kawat bulu handmade yang dibuat satu per satu dengan penuh cinta.
          Hadiah cantik yang bisa bertahan selamanya.
        </p>
        <div class="footer-socials">
          <a class="footer-social-btn" href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a class="footer-social-btn" href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
          <a class="footer-social-btn" href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>

      <!-- Links -->
      <div>
        <div class="footer-heading">Toko</div>
        <ul class="footer-links">
          <li><a href="<?= site_url('index.php') ?>">Home</a></li>
          <li><a href="<?= site_url('kategori.php') ?>">Shop</a></li>
          <li><a href="<?= site_url('kategori.php?slug=custom-bouquet') ?>">Custom Bouquet</a></li>
          <li><a href="#">About</a></li>
        </ul>
      </div>

      <div>
        <div class="footer-heading">Bantuan</div>
        <ul class="footer-links">
          <li><a href="#">Cara Pemesanan</a></li>
          <li><a href="#">Pengiriman</a></li>
          <li><a href="#">Pengembalian</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>

      <div>
        <div class="footer-heading">Akun</div>
        <ul class="footer-links">
          <?php if (is_logged_in()): ?>
            <li><a href="<?= site_url('profil.php') ?>">Profil Saya</a></li>
            <li><a href="<?= site_url('pesanan.php') ?>">Pesanan</a></li>
            <li><a href="<?= site_url('keranjang.php') ?>">Keranjang</a></li>
          <?php else: ?>
            <li><a href="<?= site_url('auth/login.php') ?>">Login</a></li>
            <li><a href="<?= site_url('auth/register.php') ?>">Daftar</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      &copy; <?= date('Y') ?> WireFlower. All rights reserved.
    </div>

  </div>
</footer>
<!-- /FOOTER -->
<?php endif; ?>

<script>
// Mobile nav toggle
const toggler = document.getElementById('navToggler');
const navLinks = document.getElementById('navLinks');
if (toggler && navLinks) {
  toggler.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    const icon = toggler.querySelector('i');
    icon.className = navLinks.classList.contains('open') ? 'bi bi-x' : 'bi bi-list';
  });
}
// Auto-dismiss flash
setTimeout(() => {
  document.querySelectorAll('.flash').forEach(el => el.remove());
}, 5000);
</script>
</body>
</html>
