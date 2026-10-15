<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) && $page_title !== '' ? e($page_title) . ' — WireFlower' : 'WireFlower — Bunga yang Tak Pernah Layu' ?></title>

<?php
// ── SEO Meta ──
$seo_title       = isset($page_title) && $page_title !== ''
                    ? e($page_title) . ' — WireFlower'
                    : 'WireFlower — Bunga yang Tak Pernah Layu';
$seo_desc        = $page_meta_desc
                    ?? 'WireFlower — Toko bunga kawat bulu handmade premium. Dibuat satu per satu dengan penuh cinta. Tersedia single flower, bouquet, gift set & custom order. Pengiriman ke seluruh Indonesia.';
$seo_keywords    = $page_meta_keywords
                    ?? 'bunga kawat bulu, wire flower, handmade, bouquet, hadiah, kado, bunga artifisial, WireFlower';
$seo_url         = 'https://wireflower.my.id' . ($_SERVER['REQUEST_URI'] ?? '/');
$seo_image       = $page_og_image ?? 'https://wireflower.my.id/assets/img/logoWF.png';
$seo_type        = $page_og_type  ?? 'website';
$canonical_url   = rtrim(strtok($seo_url, '?'), '/') ?: 'https://wireflower.my.id';
?>

<!-- ── Primary SEO ── -->
<meta name="description"        content="<?= e($seo_desc) ?>">
<meta name="keywords"           content="<?= e($seo_keywords) ?>">
<meta name="author"             content="WireFlower">
<meta name="robots"             content="index, follow">
<link rel="canonical"           href="<?= e($canonical_url) ?>">

<!-- ── Open Graph (Facebook, WhatsApp, Telegram) ── -->
<meta property="og:type"        content="<?= e($seo_type) ?>">
<meta property="og:title"       content="<?= e($seo_title) ?>">
<meta property="og:description" content="<?= e($seo_desc) ?>">
<meta property="og:url"         content="<?= e($canonical_url) ?>">
<meta property="og:image"       content="<?= e($seo_image) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height"content="630">
<meta property="og:site_name"   content="WireFlower">
<meta property="og:locale"      content="id_ID">

<!-- ── Twitter Card ── -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="<?= e($seo_title) ?>">
<meta name="twitter:description" content="<?= e($seo_desc) ?>">
<meta name="twitter:image"       content="<?= e($seo_image) ?>">

<!-- ── Favicon & App Icons ── -->
<link rel="icon" type="image/svg+xml" href="<?= site_url('assets/img/favicon.svg') ?>">
<link rel="apple-touch-icon" sizes="180x180"                 href="<?= asset_url('assets/img/logoWF-180.webp') ?>">
<meta name="theme-color"     content="#c9516f">
<meta name="msapplication-TileColor" content="#c9516f">
<meta name="msapplication-TileImage" content="<?= asset_url('assets/img/logoWF-96.webp') ?>">

<!-- ── Mobile / PWA ── -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="WireFlower">

<!-- ── Schema.org Structured Data ── -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "OnlineStore",
  "name": "WireFlower",
  "url": "https://wireflower.my.id",
  "logo": "https://wireflower.my.id/assets/img/logoWF.png",
  "description": "Toko bunga kawat bulu handmade premium. Dibuat satu per satu dengan penuh cinta.",
  "sameAs": [
    "https://instagram.com/wireflower",
    "https://tiktok.com/@wireflower"
  ],
  "address": {
    "@type": "PostalAddress",
    "addressCountry": "ID"
  },
  "priceRange": "Rp 35.000 - Rp 500.000"
}
</script>
<link rel="stylesheet" href="<?= asset_url('assets/build/site.min.css') ?>">
<link rel="stylesheet" href="<?= asset_url('assets/build/icons.min.css') ?>">
<?php if (!empty($page_is_home)): ?>
<link rel="preload" as="image" type="image/webp" media="(max-width: 640px)" href="<?= asset_url('assets/img/header1-mobile.webp') ?>" fetchpriority="high">
<link rel="preload" as="image" type="image/webp" media="(min-width: 641px)" href="<?= asset_url('assets/img/header1-1536.webp') ?>" imagesrcset="<?= asset_url('assets/img/header1-960.webp') ?> 960w, <?= asset_url('assets/img/header1-1536.webp') ?> 1536w" imagesizes="100vw" fetchpriority="high">
<link rel="stylesheet" href="<?= asset_url('assets/build/home.min.css') ?>">
<link rel="preconnect" href="https://images.unsplash.com">
<?php endif; ?>

</head>
<body data-logged-in="<?= is_logged_in() ? 'true' : 'false' ?>" data-login-url="<?= site_url('auth/login.php') ?>">

<!-- ══════════ PAGE LOADER ══════════ -->
<div id="wf-loader" role="status" aria-label="Memuat halaman">
  <div class="wf-loader-brand">
    <img src="<?= asset_url('assets/img/logoWF-96.webp') ?>" alt="" width="34" height="34">
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
      <img src="<?= asset_url('assets/img/logoWF-96.webp') ?>" alt="WireFlower Logo" class="nav-brand-img" width="28" height="28">
      WireFlower
    </a>

    <!-- Nav links -->
    <ul class="nav-links" id="navLinks">
      <li><a href="<?= site_url('index.php') ?>"
             class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Beranda</a></li>
      <li><a href="<?= site_url('kategori.php') ?>"
             class="<?= basename($_SERVER['PHP_SELF']) === 'kategori.php' ? 'active' : '' ?>">Kategori</a></li>

      <?php if (is_logged_in()): ?>
        <li><a class="nav-link-icon" href="<?= site_url('chat.php') ?>"><i class="bi bi-chat-dots"></i> Chat</a></li>
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

      <!-- ── Mobile-only extras (hidden on desktop via CSS) ── -->
      <li class="nav-mobile-extra" style="display:none;">
        <div style="height:1px;background:var(--border);margin:.4rem 0;"></div>
      </li>
      <?php if (is_logged_in()): ?>
        <li class="nav-mobile-extra" style="display:none;">
          <a href="<?= site_url('profil.php') ?>"><i class="bi bi-person"></i> Profil Saya</a>
        </li>
        <li class="nav-mobile-extra" style="display:none;">
          <a href="<?= site_url('pengaturan.php') ?>"><i class="bi bi-gear"></i> Pengaturan</a>
        </li>
        <?php if (is_admin()): ?>
        <li class="nav-mobile-extra" style="display:none;">
          <a href="<?= site_url('admin/index.php') ?>"><i class="bi bi-speedometer2"></i> Admin Panel</a>
        </li>
        <?php endif; ?>
        <li class="nav-mobile-extra" style="display:none;">
          <a href="<?= site_url('auth/logout.php') ?>" style="color:#c0392b;">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      <?php else: ?>
        <li class="nav-mobile-extra" style="display:none;">
          <a href="<?= site_url('auth/login.php') ?>"><i class="bi bi-box-arrow-in-right"></i> Login</a>
        </li>
        <li class="nav-mobile-extra" style="display:none;">
          <a href="<?= site_url('auth/register.php') ?>" style="color:var(--pink-deep);font-weight:700;">
            <i class="bi bi-person-plus"></i> Daftar Gratis
          </a>
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

<!-- ══════════ LOGIN PROMPT MODAL ══════════ -->
<div id="wf-login-modal" role="dialog" aria-modal="true" aria-labelledby="wfLoginModalTitle"
     style="display:none;position:fixed;inset:0;z-index:99998;align-items:center;justify-content:center;">
  <!-- Backdrop -->
  <div id="wf-modal-backdrop"
       style="position:absolute;inset:0;background:rgba(20,8,10,.55);backdrop-filter:blur(3px);"></div>
  <!-- Box -->
  <div style="position:relative;z-index:1;background:#fff;border-radius:20px;
              padding:2rem 2rem 1.75rem;max-width:360px;width:calc(100% - 2rem);
              box-shadow:0 24px 60px rgba(0,0,0,.18);text-align:center;
              animation:wfModalIn .3s cubic-bezier(.34,1.56,.64,1) both;">
    <!-- Icon -->
    <div style="width:60px;height:60px;border-radius:50%;background:#fce8ef;
                display:flex;align-items:center;justify-content:center;
                margin:0 auto 1rem;font-size:1.6rem;color:#c9516f;">
      <i class="bi bi-person-lock"></i>
    </div>
    <h3 id="wfLoginModalTitle"
        style="font-family:Georgia,serif;font-size:1.25rem;margin:0 0 .5rem;color:#1e1212;">
      Login Dulu, Yuk!
    </h3>
    <p style="font-size:.875rem;color:#7a6060;margin:0 0 1.5rem;line-height:1.6;">
      Kamu perlu login untuk menambahkan produk ke keranjang.
    </p>
    <div style="display:flex;gap:.65rem;">
      <button id="wf-modal-cancel"
              style="flex:1;padding:.7rem;border:1.5px solid #eeddd9;border-radius:50px;
                     background:#fff;color:#7a6060;font-size:.875rem;font-weight:600;cursor:pointer;">
        Batal
      </button>
      <a id="wf-modal-login"
         href="<?= site_url('auth/login.php') ?>"
         style="flex:1;padding:.7rem;border-radius:50px;background:#c9516f;color:#fff;
                font-size:.875rem;font-weight:700;text-decoration:none;
                display:flex;align-items:center;justify-content:center;gap:.4rem;">
        <i class="bi bi-box-arrow-in-right"></i> Login
      </a>
    </div>
    <a href="<?= site_url('auth/register.php') ?>"
       style="display:block;margin-top:1rem;font-size:.8rem;color:#c9516f;font-weight:600;">
      Belum punya akun? Daftar gratis
    </a>
  </div>
</div>


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
          <img src="<?= asset_url('assets/img/logoWF-96.webp') ?>" alt="WireFlower Logo" class="footer-brand-img" width="28" height="28" loading="lazy" decoding="async">
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

<script src="<?= asset_url('assets/build/site.min.js') ?>" defer></script>
<?php if (!empty($page_is_home)): ?>
<script src="<?= site_url('assets/vendor/gsap-3.12.5.min.js') ?>" defer></script>
<script src="<?= site_url('assets/vendor/ScrollTrigger-3.12.5.min.js') ?>" defer></script>
<script src="<?= asset_url('assets/build/home.min.js') ?>" defer></script>
<?php endif; ?>
</body>
</html>
