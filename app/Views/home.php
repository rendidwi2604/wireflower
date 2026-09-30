<!-- ===== HERO (full width within container) ===== -->
<div class="container">
  <section class="hero-section">
    <img class="hero-bg"
         src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=1400&q=85"
         alt="WireFlower bouquet">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <span class="hero-eyebrow"><i class="bi bi-flower2"></i> Handmade Wire Flower</span>
      <h1 class="hero-title">Bunga yang<br>Tak Pernah Layu.</h1>
      <p class="hero-subtitle">
        Handmade wire flower, dibuat satu per satu<br>
        untuk hadiah yang bisa bertahan lebih lama.
      </p>
      <div class="hero-actions">
        <a href="<?= site_url('kategori.php') ?>" class="btn btn-primary">
          <i class="bi bi-bag"></i> Shop Flowers
        </a>
        <a href="<?= site_url('kategori.php?slug=custom-bouquet') ?>" class="btn btn-outline">
          <i class="bi bi-pencil"></i> Custom Bouquet
        </a>
      </div>
    </div>
    <span class="hero-script">Small flowers,<br>big happiness ♥</span>
    <div class="hero-dots">
      <button class="hero-dot active" aria-label="Slide 1"></button>
      <button class="hero-dot" aria-label="Slide 2"></button>
      <button class="hero-dot" aria-label="Slide 3"></button>
    </div>
  </section>
</div>

<!-- ===== SHOP BY CATEGORY ===== -->
<div class="container" style="padding-bottom:3rem;">
  <div class="section-header">
    <div>
      <span class="section-eyebrow">Shop by Category</span>
      <h2 class="section-title">Temukan Bunga Favoritmu</h2>
    </div>
    <a href="<?= site_url('kategori.php') ?>" class="view-all">
      Lihat semua <i class="bi bi-arrow-right"></i>
    </a>
  </div>

  <div class="category-grid">
    <?php
    $cat_images = [
      'https://images.unsplash.com/photo-1477039181047-efb4432d0d27?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1526047932273-341f2a7631f9?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1593691509543-c55fb32e5cee?auto=format&fit=crop&w=600&q=85',
    ];
    $shown = array_slice($kategori ?? [], 0, 3);
    if (empty($shown)) {
      $shown = [
        ['name'=>'Single Flower','slug'=>'single-flower','description'=>'Mulai dari Rp 35.000'],
        ['name'=>'Bouquet',      'slug'=>'bouquet',       'description'=>'Mulai dari Rp 90.000'],
        ['name'=>'Gift Set',     'slug'=>'gift-set',      'description'=>'Mulai dari Rp 75.000'],
      ];
    }
    foreach ($shown as $i => $k):
      $img = $cat_images[$i % count($cat_images)];
    ?>
    <a class="category-card" href="<?= site_url('kategori.php?slug=' . ($k['slug'] ?? '')) ?>">
      <div class="category-card-img">
        <img src="<?= $img ?>" alt="<?= e($k['name']) ?>">
      </div>
      <div class="category-card-body">
        <div>
          <h4><?= e($k['name']) ?></h4>
          <?php if (!empty($k['description'])): ?>
            <small><?= e($k['description']) ?></small>
          <?php endif; ?>
        </div>
        <span class="category-card-arrow"><i class="bi bi-arrow-right"></i></span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- ===== BEST SELLERS ===== -->
<div class="container" style="padding-bottom:3rem;">
  <div class="section-header">
    <div>
      <span class="section-eyebrow">Best Sellers</span>
      <h2 class="section-title">Produk Pilihan Kami</h2>
    </div>
    <a href="<?= site_url('kategori.php') ?>" class="view-all">
      View all <i class="bi bi-arrow-right"></i>
    </a>
  </div>
  <?php $products = $rekomendasi ?? []; require VIEW_PATH . '/partials/product_grid.php'; ?>
</div>

<!-- ===== CUSTOM BOUQUET BANNER ===== -->
<div class="container" style="padding-bottom:3rem;">
  <div class="custom-banner">
    <img class="custom-banner-left"
         src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=500&q=85"
         alt="Custom bouquet">
    <div class="custom-banner-right">
      <div style="background:#fff;border-radius:14px;padding:1.1rem 1.25rem;max-width:160px;box-shadow:0 4px 16px rgba(0,0,0,.1);text-align:center;transform:rotate(3deg);">
        <p style="font-family:Georgia,serif;font-size:.88rem;color:#2c1a1e;margin:0 0 .2rem;font-style:italic;line-height:1.4;">For someone<br>special</p>
        <span style="color:#c9516f;font-size:1rem;">♥</span>
        <div style="margin-top:.3rem;font-size:.65rem;color:#5a7a5a;">🌸</div>
      </div>
    </div>
    <div class="custom-banner-content">
      <span class="section-eyebrow">Custom Bouquet</span>
      <h2>Buat Buketmu Sendiri</h2>
      <p>Pilih warna · Pilih bunga · Tambahkan kartu</p>
      <a href="<?= site_url('kategori.php?slug=custom-bouquet') ?>" class="btn btn-primary">
        Customize Now <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
</div>

<!-- ===== BENEFITS STRIP ===== -->
<div class="container" style="padding-bottom:3rem;">
  <div class="benefits-strip">
    <div class="benefit-item">
      <div class="benefit-icon"><i class="bi bi-shield-check"></i></div>
      <div>
        <strong>Produk Berkualitas</strong>
        <small>Dibuat dengan detail dan hati-hati</small>
      </div>
    </div>
    <div class="benefit-item">
      <div class="benefit-icon"><i class="bi bi-truck"></i></div>
      <div>
        <strong>Pengiriman Cepat</strong>
        <small>Ke seluruh Indonesia</small>
      </div>
    </div>
    <div class="benefit-item">
      <div class="benefit-icon"><i class="bi bi-heart"></i></div>
      <div>
        <strong>Packaging Aman</strong>
        <small>Bunga sampai dengan kondisi terbaik</small>
      </div>
    </div>
    <div class="benefit-item">
      <div class="benefit-icon"><i class="bi bi-headset"></i></div>
      <div>
        <strong>Customer Service</strong>
        <small>Siap membantu kapan saja</small>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($terbaru)): ?>
<!-- ===== NEW ARRIVALS ===== -->
<div class="container" style="padding-bottom:3rem;">
  <div class="section-header">
    <div>
      <span class="section-eyebrow">New Arrivals</span>
      <h2 class="section-title">Produk Terbaru</h2>
    </div>
    <a href="<?= site_url('kategori.php') ?>" class="view-all">
      View all <i class="bi bi-arrow-right"></i>
    </a>
  </div>
  <?php $products = $terbaru; require VIEW_PATH . '/partials/product_grid.php'; ?>
</div>
<?php endif; ?>
