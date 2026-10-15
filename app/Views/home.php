
<?php
// Gambar bunga asli (natural) — kategori, all verified visually
$cat_images = [
  'https://images.unsplash.com/photo-1520763185298-1b434c919102?auto=format&fit=crop&w=700&q=80', // tulip pink
  'https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=700&q=80', // poppy orange field
  'https://images.unsplash.com/photo-1508610048659-a06b669e3321?auto=format&fit=crop&w=700&q=80', // mixed pastel bouquet
  'https://images.unsplash.com/photo-1522748906645-95d8adfd52c7?auto=format&fit=crop&w=700&q=80', // cherry blossom / sakura
];
// Gambar reveal — bunga berbeda, semua verified visually
$wire_images = [
  'https://images.unsplash.com/photo-1462275646964-a0e3386b89fa?auto=format&fit=crop&w=700&q=80', // tangan + bouquet warna-warni
  'https://images.unsplash.com/photo-1591886960571-74d43a9d4166?auto=format&fit=crop&w=700&q=80', // peony bouquet
  'https://images.unsplash.com/photo-1444021465936-c6ca81d39b84?auto=format&fit=crop&w=700&q=80', // tulip merah putih
  'https://images.unsplash.com/photo-1597848212624-a19eb35e2651?auto=format&fit=crop&w=700&q=80', // sunflower field
];
$shown = array_slice($kategori ?? [], 0, 4);
if (empty($shown)) {
  $shown = [
    ['name'=>'Single Flower','slug'=>'single-flower','description'=>'Mulai Rp 35.000'],
    ['name'=>'Bouquet',      'slug'=>'bouquet',       'description'=>'Mulai Rp 90.000'],
    ['name'=>'Gift Set',     'slug'=>'gift-set',      'description'=>'Mulai Rp 75.000'],
    ['name'=>'Custom',       'slug'=>'custom-bouquet','description'=>'Sesukamu'],
  ];
}
?>

<!-- ════════════════════════════════════════════
     GSAP + ScrollTrigger
════════════════════════════════════════════ -->


<!-- ══════════════ HERO ══════════════ -->
<section class="wf-hero" id="wf-hero">
  <div class="wf-hero__noise"></div>

  <!-- Reveal layer: header1 always visible softly, header2 follows cursor -->
  <div class="wf-hero__reveal-layer" id="heroRevealLayer">
    <picture>
      <source media="(max-width: 640px)" srcset="<?= asset_url('assets/img/header1-mobile.webp') ?>">
      <img class="wf-hero__reveal-img wf-hero__reveal-img--1"
           src="<?= asset_url('assets/img/header1-1536.webp') ?>"
           srcset="<?= asset_url('assets/img/header1-960.webp') ?> 960w, <?= asset_url('assets/img/header1-1536.webp') ?> 1536w" sizes="100vw"
           width="1536" height="864" fetchpriority="high" loading="eager" decoding="async" alt="">
    </picture>
    <picture>
      <source media="(max-width: 640px)" data-srcset="<?= asset_url('assets/img/header2-mobile.webp') ?>">
    <img class="wf-hero__reveal-img wf-hero__reveal-img--2"
         src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'/%3E"
         data-src="<?= asset_url('assets/img/header2-1536.webp') ?>"
         data-srcset="<?= asset_url('assets/img/header2-960.webp') ?> 960w, <?= asset_url('assets/img/header2-1536.webp') ?> 1536w" sizes="100vw"
         width="1536" height="864" decoding="async" alt="" id="heroRevealImg2">
    </picture>
  </div>

  <div class="wf-hero__content">
    <div class="wf-hero__tag">
      <i class="bi bi-stars"></i> Handmade Wire Flower
    </div>
    <h1 class="wf-hero__title">
      Bunga yang<br><em>Tak Pernah Layu.</em>
    </h1>
    <p class="wf-hero__sub">
      Dibuat satu per satu dengan kawat bulu premium — hadiah abadi yang selalu memesona.
    </p>
    <div class="wf-hero__actions">
      <a href="<?= site_url('kategori.php') ?>" class="wf-btn-primary">
        <i class="bi bi-bag-heart"></i> Shop Now
      </a>
      <a href="<?= site_url('kategori.php?slug=custom-bouquet') ?>" class="wf-btn-ghost">
        <i class="bi bi-pencil-square"></i> Custom Bouquet
      </a>
    </div>
  </div>

  <div class="wf-hero__stats">
    <div class="wf-stat-card">
      <div class="wf-stat-icon"><i class="bi bi-heart-fill"></i></div>
      <div>
        <div class="wf-stat-num">1.2K+</div>
        <div class="wf-stat-lbl">Happy Customers</div>
      </div>
    </div>
    <div class="wf-stat-card">
      <div class="wf-stat-icon"><i class="bi bi-star-fill"></i></div>
      <div>
        <div class="wf-stat-num">4.9</div>
        <div class="wf-stat-lbl">Average Rating</div>
      </div>
    </div>
  </div>

  <div class="wf-scroll-cue">
    <span>Scroll</span>
    <div class="wf-scroll-line"></div>
  </div>
</section>

<div class="wf-divider"></div>

<!-- ══════════════ CATEGORIES ══════════════ -->
<section class="wf-section">
  <div class="wf-container">
    <div class="wf-section-header">
      <div>
        <div class="wf-eyebrow gs-fade-up"><i class="bi bi-grid-3x3-gap"></i> Collections</div>
        <h2 class="wf-section-title gs-fade-up">Temukan Bunga Favoritmu</h2>
      </div>
      <a href="<?= site_url('kategori.php') ?>" class="wf-view-all gs-fade-in">
        Lihat semua <i class="bi bi-arrow-right"></i>
      </a>
    </div>

    <div class="wf-cat-grid">
      <?php foreach ($shown as $i => $k):
        $img      = $cat_images[$i % count($cat_images)];
        $wireImg  = $wire_images[$i % count($wire_images)];
      ?>
      <a class="wf-cat-card gs-scale"
         href="<?= site_url('kategori.php?slug=' . ($k['slug'] ?? '')) ?>">
        <?php /* Base image (natural flower) */ ?>
        <img src="<?= e(str_replace(['w=700', 'q=80'], ['w=480', 'q=72'], $img)) ?>" alt="<?= e($k['name']) ?>" loading="lazy" decoding="async" width="480" height="600">
        <div class="wf-cat-card__overlay"></div>

        <?php /* Wire flower reveal layer */ ?>
        <div class="wf-cat-card__reveal">
          <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'/%3E" data-src="<?= e(str_replace(['w=700', 'q=80'], ['w=480', 'q=72'], $wireImg)) ?>" alt="Wire <?= e($k['name']) ?>" decoding="async" width="480" height="600">
        </div>
        <div class="wf-cat-card__reveal-overlay"></div>
        <span class="wf-cat-card__wire-tag">Wire Flower</span>

        <span class="wf-cat-card__arrow"><i class="bi bi-arrow-up-right"></i></span>
        <div class="wf-cat-card__body" style="z-index:4;position:relative;">
          <p class="wf-cat-card__name"><?= e($k['name']) ?></p>
          <?php if (!empty($k['description'])): ?>
            <p class="wf-cat-card__desc"><?= e($k['description']) ?></p>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="wf-divider"></div>

<!-- ══════════════ BEST SELLERS ══════════════ -->
<?php if (!empty($rekomendasi)): ?>
<section class="wf-section">
  <div class="wf-container">
    <div class="wf-section-header">
      <div>
        <div class="wf-eyebrow gs-fade-up"><i class="bi bi-fire"></i> Best Sellers</div>
        <h2 class="wf-section-title gs-fade-up">Produk Pilihan Kami</h2>
      </div>
      <a href="<?= site_url('kategori.php') ?>" class="wf-view-all gs-fade-in">
        View all <i class="bi bi-arrow-right"></i>
      </a>
    </div>

    <div class="wf-prod-grid">
    <?php foreach ($rekomendasi as $p):
      $img  = asset_img($p['image'], 'https://placehold.co/400x400/fce8ef/c9516f?text=🌸');
      $avg  = round($p['avg_rating'] ?? 0, 1);
      $full = (int)$avg; $half = ($avg - $full) >= 0.5 ? 1 : 0; $empty = 5 - $full - $half;
    ?>
      <div class="wf-prod-card gs-scale">
        <a href="<?= site_url('produk.php?slug=' . $p['slug']) ?>" class="wf-prod-card__img">
          <img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>" loading="lazy" decoding="async" width="400" height="400">
        </a>
        <button class="wf-prod-card__wish" type="button" aria-label="Wishlist">
          <i class="bi bi-heart"></i>
        </button>
        <div class="wf-prod-card__body">
          <a class="product-card-link" href="<?= e(site_url('produk.php?slug=' . rawurlencode($p['slug']))) ?>" style="text-decoration:none;">
            <p class="wf-prod-card__name"><?= e($p['name']) ?></p>
          </a>
          <p class="wf-prod-card__price"><?= rupiah($p['price']) ?></p>
          <div class="wf-prod-card__footer">
            <div class="wf-prod-card__stars">
              <?php for($i=0;$i<$full;$i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
              <?php for($i=0;$i<$half;$i++): ?><i class="bi bi-star-half"></i><?php endfor; ?>
              <?php for($i=0;$i<$empty;$i++): ?><i class="bi bi-star"></i><?php endfor; ?>
              <?php if(($p['review_count'] ?? 0) > 0): ?>
                <span>(<?= $p['review_count'] ?>)</span>
              <?php endif; ?>
            </div>
            <form method="POST" action="<?= site_url('cart_actions.php') ?>" style="margin:0;">
              <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
              <input type="hidden" name="quantity"   value="1">
              <input type="hidden" name="action"     value="add">
              <button type="submit" class="wf-prod-card__add" aria-label="Add to cart">
                <i class="bi bi-plus"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<div class="wf-divider"></div>
<?php endif; ?>

<!-- ══════════════ CUSTOM BANNER ══════════════ -->
<section class="wf-section">
  <div class="wf-container">
    <div class="wf-banner gs-scale">
      <img class="wf-banner__bg"
           src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=1200&q=72"
           srcset="https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=640&q=72 640w, https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=1200&q=72 1200w" sizes="(max-width: 640px) 100vw, 1180px"
           alt="Custom bouquet" loading="lazy" decoding="async" width="1200" height="600">
      <div class="wf-banner__overlay"></div>
      <div class="wf-banner__content">
        <div class="wf-eyebrow" style="color:rgba(252,232,239,.9);background:rgba(201,81,111,.25);">
          <i class="bi bi-pencil-square"></i> Custom Bouquet
        </div>
        <h2 class="wf-banner__title">Buat Buketmu<br>Sendiri</h2>
        <p class="wf-banner__sub">Pilih warna · Pilih bunga · Tambahkan kartu ucapan spesial</p>
        <a href="<?= site_url('kategori.php?slug=custom-bouquet') ?>" class="wf-btn-primary">
          Customize Now <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ BENEFITS ══════════════ -->
<section class="wf-section" id="wf-benefits" style="padding-top:0;">
  <div class="wf-container">
    <div class="wf-benefits">
      <?php
      $benefits = [
        ['bi-shield-check','Produk Berkualitas','Dibuat dengan detail dan penuh perhatian untuk setiap bunga.'],
        ['bi-truck',       'Pengiriman Cepat',  'Dikirim ke seluruh Indonesia dengan packaging aman.'],
        ['bi-box-seam',    'Packaging Premium', 'Bunga tiba dalam kondisi terbaik, siap dijadikan hadiah.'],
        ['bi-headset',     'Customer Service',  'Siap membantu kapan saja via WhatsApp & chat.'],
      ];
      foreach ($benefits as [$icon, $title, $desc]): ?>
      <div class="wf-benefit gs-fade-up">
        <div class="wf-benefit__icon"><i class="bi <?= $icon ?>"></i></div>
        <p class="wf-benefit__title"><?= $title ?></p>
        <p class="wf-benefit__desc"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════ NEW ARRIVALS ══════════════ -->
<?php if (!empty($terbaru)): ?>
<div class="wf-divider"></div>
<section class="wf-section">
  <div class="wf-container">
    <div class="wf-section-header">
      <div>
        <div class="wf-eyebrow gs-fade-up"><i class="bi bi-stars"></i> New Arrivals</div>
        <h2 class="wf-section-title gs-fade-up">Produk Terbaru</h2>
      </div>
      <a href="<?= site_url('kategori.php') ?>" class="wf-view-all gs-fade-in">
        View all <i class="bi bi-arrow-right"></i>
      </a>
    </div>
    <div class="wf-prod-grid">
    <?php foreach ($terbaru as $p):
      $img  = asset_img($p['image'], 'https://placehold.co/400x400/fce8ef/c9516f?text=🌸');
      $avg  = round($p['avg_rating'] ?? 0, 1);
      $full = (int)$avg; $half = ($avg - $full) >= 0.5 ? 1 : 0; $empty = 5 - $full - $half;
    ?>
      <div class="wf-prod-card gs-scale">
        <a href="<?= site_url('produk.php?slug=' . $p['slug']) ?>" class="wf-prod-card__img">
          <img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>" loading="lazy" decoding="async" width="400" height="400">
        </a>
        <button class="wf-prod-card__wish" type="button" aria-label="Wishlist">
          <i class="bi bi-heart"></i>
        </button>
        <div class="wf-prod-card__body">
          <a class="product-card-link" href="<?= e(site_url('produk.php?slug=' . rawurlencode($p['slug']))) ?>" style="text-decoration:none;">
            <p class="wf-prod-card__name"><?= e($p['name']) ?></p>
          </a>
          <p class="wf-prod-card__price"><?= rupiah($p['price']) ?></p>
          <div class="wf-prod-card__footer">
            <div class="wf-prod-card__stars">
              <?php for($i=0;$i<$full;$i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
              <?php for($i=0;$i<$half;$i++): ?><i class="bi bi-star-half"></i><?php endfor; ?>
              <?php for($i=0;$i<$empty;$i++): ?><i class="bi bi-star"></i><?php endfor; ?>
              <?php if(($p['review_count'] ?? 0) > 0): ?>
                <span>(<?= $p['review_count'] ?>)</span>
              <?php endif; ?>
            </div>
            <form method="POST" action="<?= site_url('cart_actions.php') ?>" style="margin:0;">
              <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
              <input type="hidden" name="quantity"   value="1">
              <input type="hidden" name="action"     value="add">
              <button type="submit" class="wf-prod-card__add" aria-label="Add to cart">
                <i class="bi bi-plus"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ══════════════ GSAP ══════════════ -->
