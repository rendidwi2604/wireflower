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

  /* ── PAGE LOADER ── */
  #wf-loader {
    position: fixed; inset: 0; z-index: 99999;
    background: #faf8f5;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 1.5rem;
    transition: opacity .45s ease, visibility .45s ease;
  }
  #wf-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }

  .wf-loader-brand {
    display: flex; align-items: center; gap: .55rem;
    font-family: Georgia, serif; font-size: 1.35rem; font-weight: 700;
    color: #1e1212; opacity: 0;
    animation: loaderFadeIn .5s .1s ease forwards;
  }
  .wf-loader-brand img { width: 34px; height: 34px; object-fit: contain; }

  /* 3 flower dots */
  .wf-loader-dots {
    display: flex; align-items: flex-end; gap: .65rem;
    height: 44px;
  }
  .wf-loader-dot {
    width: 14px; height: 14px; border-radius: 50%;
    animation: flowerBounce 1.1s ease-in-out infinite;
    position: relative;
  }
  /* petal ring inside dot */
  .wf-loader-dot::after {
    content: '';
    position: absolute; inset: 2px;
    border-radius: 50%;
    background: rgba(255,255,255,.45);
  }
  .wf-loader-dot:nth-child(1) { background: #e8799a; animation-delay: 0s; }    /* rose */
  .wf-loader-dot:nth-child(2) { background: #f4a460; animation-delay: .18s; }  /* peach */
  .wf-loader-dot:nth-child(3) { background: #c9516f; animation-delay: .36s; }  /* deep rose */

  @keyframes flowerBounce {
    0%, 80%, 100% { transform: translateY(0) scale(1); box-shadow: 0 4px 10px rgba(0,0,0,.08); }
    40%           { transform: translateY(-18px) scale(1.15); box-shadow: 0 14px 18px rgba(0,0,0,.12); }
  }
  @keyframes loaderFadeIn {
    to { opacity: 1; }
  }

  .wf-loader-text {
    font-size: .75rem; font-weight: 600; letter-spacing: .12em;
    text-transform: uppercase; color: #b09090;
    opacity: 0; animation: loaderFadeIn .5s .35s ease forwards;
  }

  /* ── LAZY LOAD — product card entrance ── */
  .lazy-card {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity .45s ease, transform .45s ease;
  }
  .lazy-card.visible {
    opacity: 1;
    transform: translateY(0);
  }

  /* ── IMG lazy placeholder ── */
  img.lazy-img {
    background: #fce8ef;
    min-height: 100px;
  }
  img.lazy-img.loaded { background: none; }
</style>
</head>
<body>

<!-- ══════════ PAGE LOADER ══════════ -->
<div id="wf-loader" role="status" aria-label="Memuat halaman">
  <div class="wf-loader-brand">
    <img src="<?= site_url('assets/img/logoWF.png') ?>" alt="">
    WireFlower
  </div>
  <div class="wf-loader-dots" aria-hidden="true">
    <span class="wf-loader-dot"></span>
    <span class="wf-loader-dot"></span>
    <span class="wf-loader-dot"></span>
  </div>
  <p class="wf-loader-text">Menyiapkan bunga untuk kamu…</p>
</div>

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
    </ul>

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
          <a class="footer-social-btn" href="https://instagram.com/wireflower" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a class="footer-social-btn" href="https://tiktok.com/@wireflower" target="_blank" rel="noopener" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
          <a class="footer-social-btn" href="https://wa.me/6281234567890" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>

      <!-- Links -->
      <div>
        <div class="footer-heading">Toko</div>
        <ul class="footer-links">
          <li><a href="<?= site_url('index.php') ?>">Home</a></li>
          <li><a href="<?= site_url('kategori.php') ?>">Shop</a></li>
          <li><a href="<?= site_url('kategori.php?slug=custom-bouquet') ?>">Custom Bouquet</a></li>
          <li><a href="<?= site_url('index.php#wf-benefits') ?>">About</a></li>
        </ul>
      </div>

      <div>
        <div class="footer-heading">Bantuan</div>
        <ul class="footer-links">
          <li><a href="<?= site_url('pesanan.php') ?>">Cara Pemesanan</a></li>
          <li><a href="<?= site_url('checkout.php') ?>">Pengiriman</a></li>
          <li><a href="<?= site_url('pesanan.php') ?>">Pengembalian</a></li>
          <li><a href="<?= site_url('index.php#wf-benefits') ?>">FAQ</a></li>
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

function closeNav() {
  if (!navLinks) return;
  navLinks.classList.remove('open');
  const icon = toggler ? toggler.querySelector('i') : null;
  if (icon) icon.className = 'bi bi-list';
}

if (toggler && navLinks) {
  toggler.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = navLinks.classList.toggle('open');
    const icon = toggler.querySelector('i');
    icon.className = isOpen ? 'bi bi-x' : 'bi bi-list';
  });

  // Close when any nav link is clicked (mobile)
  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => closeNav());
  });

  // Close when clicking outside
  document.addEventListener('click', (e) => {
    if (!navLinks.contains(e.target) && !toggler.contains(e.target)) {
      closeNav();
    }
  });
}

// Auto-dismiss flash
setTimeout(() => {
  document.querySelectorAll('.flash').forEach(el => el.remove());
}, 5000);
</script>
</body>
</html>
