
<?php
// Gambar bunga asli (natural) — kategori, verified Unsplash flower photos
$cat_images = [
  'https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=700&q=80', // pink roses bouquet
  'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=700&q=80', // red rose single
  'https://images.unsplash.com/photo-1487530811015-780eddf6e9a6?auto=format&fit=crop&w=700&q=80', // sunflower bouquet
  'https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=700&q=80', // mixed bouquet pastel
];
// Gambar reveal — wire/crafted flower, berbeda dari header hero
$wire_images = [
  'https://images.unsplash.com/photo-1595351298020-038700609878?auto=format&fit=crop&w=700&q=80', // dried wire pampas
  'https://images.unsplash.com/photo-1606041008023-472dfb5e530f?auto=format&fit=crop&w=700&q=80', // colourful craft flowers
  'https://images.unsplash.com/photo-1526047932273-341f2a7631f9?auto=format&fit=crop&w=700&q=80', // pink tulip close
  'https://images.unsplash.com/photo-1477039181047-efb4432d0d27?auto=format&fit=crop&w=700&q=80', // white daisy bouquet
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
<style>
/* ── Home-specific tokens ── */
:root {
  --wf-cream   : #faf8f5;
  --wf-rose    : #c9516f;
  --wf-rose-lt : #fce8ef;
  --wf-text    : #1e1212;
  --wf-muted   : #7a6060;
  --wf-border  : #eeddd9;
  --wf-radius  : 18px;
  --wf-shadow  : 0 8px 32px rgba(180,100,120,.10);
}

/* ── utility ── */
.wf-container { width:100%; max-width:1180px; margin:0 auto; padding:0 1.5rem; }
.wf-eyebrow {
  display:inline-flex; align-items:center; gap:.4rem;
  font-size:.68rem; font-weight:800; letter-spacing:.12em;
  text-transform:uppercase; color:var(--wf-rose);
  background:var(--wf-rose-lt); padding:.3rem .85rem;
  border-radius:50px; margin-bottom:.85rem;
}

/* ── HERO ── */
.wf-hero {
  position:relative; min-height:90vh;
  display:flex; align-items:center;
  overflow:hidden; background:#1a0d0f;
}
.wf-hero__bg {
  position:absolute; inset:0; width:100%; height:100%;
  object-fit:cover; object-position:60% center;
  opacity:.45; transform:scale(1.08);
}
.wf-hero__noise {
  position:absolute; inset:0;
  background:linear-gradient(135deg, rgba(26,13,15,.92) 0%, rgba(26,13,15,.45) 55%, transparent 100%);
}
.wf-hero__content {
  position:relative; z-index:2;
  max-width:600px; padding:0 1.5rem;
  margin-left:clamp(1.5rem, 8vw, 120px);
}
.wf-hero__tag {
  display:inline-flex; align-items:center; gap:.5rem;
  font-size:.72rem; font-weight:700; letter-spacing:.14em;
  text-transform:uppercase; color:rgba(252,232,239,.9);
  border:1px solid rgba(252,232,239,.25); border-radius:50px;
  padding:.32rem 1rem; margin-bottom:1.5rem;
  backdrop-filter:blur(8px); background:rgba(201,81,111,.18);
}
.wf-hero__title {
  font-family:Georgia,serif;
  font-size:clamp(2.8rem, 6vw, 5.2rem);
  font-weight:700; line-height:1.05;
  color:#fff; margin:0 0 1.25rem; letter-spacing:-.02em;
}
.wf-hero__title em {
  font-style:normal; color:#f0a0b8;
  display:block;
}
.wf-hero__sub {
  font-size:clamp(.9rem, 1.5vw, 1.05rem);
  color:rgba(255,255,255,.72); line-height:1.7;
  margin-bottom:2.25rem; max-width:420px;
}
.wf-hero__actions { display:flex; gap:.85rem; flex-wrap:wrap; }
.wf-btn-primary {
  display:inline-flex; align-items:center; gap:.55rem;
  background:var(--wf-rose); color:#fff;
  padding:.82rem 1.75rem; border-radius:50px;
  font-weight:700; font-size:.88rem; text-decoration:none;
  transition:background .2s, transform .15s, box-shadow .2s;
  box-shadow:0 4px 20px rgba(201,81,111,.45);
}
.wf-btn-primary:hover { background:#b8455f; transform:translateY(-2px); color:#fff; box-shadow:0 8px 28px rgba(201,81,111,.55); }
.wf-btn-ghost {
  display:inline-flex; align-items:center; gap:.55rem;
  background:rgba(255,255,255,.1); color:#fff;
  padding:.82rem 1.75rem; border-radius:50px;
  font-weight:600; font-size:.88rem; text-decoration:none;
  border:1.5px solid rgba(255,255,255,.3);
  backdrop-filter:blur(8px);
  transition:background .2s, transform .15s;
}
.wf-btn-ghost:hover { background:rgba(255,255,255,.2); transform:translateY(-2px); color:#fff; }

/* floating stat cards */
.wf-hero__stats {
  position:absolute; bottom:2.5rem; right:clamp(1.5rem, 6vw, 5rem);
  display:flex; flex-direction:column; gap:.75rem; z-index:2;
}
.wf-stat-card {
  background:rgba(255,255,255,.1); backdrop-filter:blur(16px);
  border:1px solid rgba(255,255,255,.18); border-radius:14px;
  padding:.7rem 1.1rem; display:flex; align-items:center; gap:.75rem;
  min-width:170px;
}
.wf-stat-icon {
  width:36px; height:36px; border-radius:10px;
  background:rgba(201,81,111,.35); display:flex; align-items:center; justify-content:center;
  font-size:1rem; color:#f5b8c8; flex-shrink:0;
}
.wf-stat-num { font-size:1.15rem; font-weight:800; color:#fff; line-height:1; }
.wf-stat-lbl { font-size:.65rem; color:rgba(255,255,255,.6); margin-top:.1rem; }

/* scroll cue */
.wf-scroll-cue {
  position:absolute; bottom:2rem; left:50%; transform:translateX(-50%);
  display:flex; flex-direction:column; align-items:center; gap:.35rem; z-index:2;
}
.wf-scroll-cue span { font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:rgba(255,255,255,.45); }
.wf-scroll-line {
  width:1.5px; height:36px;
  background:linear-gradient(to bottom, rgba(255,255,255,.4), transparent);
  animation:scrollPulse 1.8s ease-in-out infinite;
}
@keyframes scrollPulse { 0%,100%{opacity:.4;transform:scaleY(.8)} 50%{opacity:1;transform:scaleY(1)} }

/* ── SECTION COMMON ── */
.wf-section { padding:5rem 0; }
.wf-section-header {
  display:flex; align-items:flex-end; justify-content:space-between;
  gap:1rem; margin-bottom:2.5rem;
}
.wf-section-title {
  font-family:Georgia,serif;
  font-size:clamp(1.7rem, 3vw, 2.4rem);
  font-weight:700; color:var(--wf-text); margin:0; line-height:1.15;
}
.wf-view-all {
  display:inline-flex; align-items:center; gap:.35rem;
  font-size:.82rem; font-weight:700; color:var(--wf-rose);
  text-decoration:none; white-space:nowrap; flex-shrink:0;
  transition:gap .2s;
}
.wf-view-all:hover { gap:.6rem; color:var(--wf-rose); }

/* ── CATEGORY GRID ── */
.wf-cat-grid {
  display:grid;
  grid-template-columns:repeat(4, 1fr);
  gap:1rem;
}
.wf-cat-card {
  position:relative; border-radius:var(--wf-radius);
  overflow:hidden; aspect-ratio:3/4;
  text-decoration:none; display:block;
  background:#f0e8e8;
}
.wf-cat-card img {
  width:100%; height:100%; object-fit:cover;
  transition:transform .6s cubic-bezier(.25,.46,.45,.94);
}
.wf-cat-card:hover img { transform:scale(1.06); }
.wf-cat-card__overlay {
  position:absolute; inset:0;
  background:linear-gradient(to top, rgba(20,8,10,.75) 0%, rgba(20,8,10,.1) 60%, transparent 100%);
  transition:opacity .3s;
}
.wf-cat-card:hover .wf-cat-card__overlay { opacity:.85; }
.wf-cat-card__body {
  position:absolute; bottom:0; left:0; right:0;
  padding:1.25rem 1.1rem;
}
.wf-cat-card__name {
  font-family:Georgia,serif; font-size:1.05rem; font-weight:700;
  color:#fff; margin:0 0 .25rem; line-height:1.2;
}
.wf-cat-card__desc { font-size:.75rem; color:rgba(255,255,255,.7); margin:0; }
.wf-cat-card__arrow {
  position:absolute; top:1rem; right:1rem;
  width:34px; height:34px; border-radius:50%;
  background:rgba(255,255,255,.15); backdrop-filter:blur(8px);
  display:flex; align-items:center; justify-content:center;
  color:#fff; font-size:.8rem;
  opacity:0; transform:translate(4px,-4px);
  transition:opacity .25s, transform .25s;
}
.wf-cat-card:hover .wf-cat-card__arrow { opacity:1; transform:translate(0,0); }

/* Wire flower reveal layer — cursor-tracked spotlight */
.wf-cat-card__reveal {
  position:absolute; inset:0; z-index:1;
  /* clip-path updated live by JS */
  clip-path:circle(0px at 50% 50%);
  will-change:clip-path;
}
.wf-cat-card__reveal img {
  width:100%; height:100%; object-fit:cover; transition:none;
}
/* label wire flower */
.wf-cat-card__wire-tag {
  position:absolute; top:.75rem; left:.75rem; z-index:3;
  font-size:.62rem; font-weight:800; letter-spacing:.1em;
  text-transform:uppercase; color:#fff;
  background:rgba(201,81,111,.75); backdrop-filter:blur(6px);
  padding:.25rem .65rem; border-radius:50px;
  opacity:0; transform:translateY(-6px);
  transition:opacity .3s .2s, transform .3s .2s;
}
.wf-cat-card:hover .wf-cat-card__wire-tag { opacity:1; transform:translateY(0); }
/* overlay on top of reveal */
.wf-cat-card__reveal-overlay {
  position:absolute; inset:0; z-index:2;
  background:linear-gradient(to top, rgba(20,8,10,.7) 0%, rgba(20,8,10,.05) 55%, transparent 100%);
  opacity:0; transition:opacity .4s;
}
.wf-cat-card:hover .wf-cat-card__reveal-overlay { opacity:1; }

/* ── PRODUCT GRID ── */
.wf-prod-grid {
  display:grid;
  grid-template-columns:repeat(4, 1fr);
  gap:1.25rem;
}
.wf-prod-card {
  background:#fff; border-radius:var(--wf-radius);
  overflow:hidden; border:1px solid var(--wf-border);
  transition:box-shadow .25s, transform .25s;
  position:relative;
}
.wf-prod-card:hover {
  box-shadow:var(--wf-shadow);
  transform:translateY(-4px);
}
.wf-prod-card__img {
  display:block; aspect-ratio:1; overflow:hidden; background:var(--wf-rose-lt);
  position:relative;
}
.wf-prod-card__img img {
  width:100%; height:100%; object-fit:cover;
  transition:transform .5s cubic-bezier(.25,.46,.45,.94);
}
.wf-prod-card:hover .wf-prod-card__img img { transform:scale(1.07); }
.wf-prod-card__wish {
  position:absolute; top:.75rem; right:.75rem; z-index:2;
  width:32px; height:32px; border-radius:50%;
  background:#fff; border:1px solid var(--wf-border);
  display:flex; align-items:center; justify-content:center;
  font-size:.8rem; color:var(--wf-muted); cursor:pointer;
  transition:color .2s, border-color .2s, transform .2s;
}
.wf-prod-card__wish:hover { color:var(--wf-rose); border-color:var(--wf-rose); transform:scale(1.15); }
.wf-prod-card__body { padding:1rem 1.1rem; }
.wf-prod-card__name {
  font-size:.875rem; font-weight:600; color:var(--wf-text);
  margin:0 0 .3rem; line-height:1.35;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.wf-prod-card__price { font-size:.95rem; font-weight:800; color:var(--wf-rose); margin:0 0 .65rem; }
.wf-prod-card__footer { display:flex; align-items:center; justify-content:space-between; gap:.5rem; }
.wf-prod-card__stars { display:flex; align-items:center; gap:.2rem; font-size:.7rem; color:#f5a623; }
.wf-prod-card__stars span { font-size:.72rem; color:var(--wf-muted); margin-left:.15rem; }
.wf-prod-card__add {
  width:32px; height:32px; border-radius:50%;
  background:var(--wf-rose); color:#fff; border:none;
  display:flex; align-items:center; justify-content:center;
  font-size:.95rem; cursor:pointer; flex-shrink:0;
  transition:background .2s, transform .15s;
}
.wf-prod-card__add:hover { background:#b8455f; transform:scale(1.1) rotate(90deg); }

/* ── CUSTOM BANNER ── */
.wf-banner {
  border-radius:var(--wf-radius); overflow:hidden; position:relative;
  min-height:420px; display:flex; align-items:center;
  background:#1a0d0f;
}
.wf-banner__bg {
  position:absolute; inset:0; width:100%; height:100%;
  object-fit:cover; opacity:.35;
}
.wf-banner__overlay {
  position:absolute; inset:0;
  background:linear-gradient(105deg, rgba(20,8,10,.88) 40%, transparent 100%);
}
.wf-banner__content {
  position:relative; z-index:2; padding:3rem clamp(2rem,5vw,5rem);
  max-width:520px;
}
.wf-banner__title {
  font-family:Georgia,serif;
  font-size:clamp(1.8rem, 4vw, 3rem);
  font-weight:700; color:#fff; margin:.5rem 0 .85rem; line-height:1.15;
}
.wf-banner__sub { font-size:.9rem; color:rgba(255,255,255,.7); margin-bottom:1.75rem; }

/* ── BENEFITS ── */
.wf-benefits {
  display:grid; grid-template-columns:repeat(4, 1fr); gap:1.25rem;
}
.wf-benefit {
  background:#fff; border-radius:14px; border:1px solid var(--wf-border);
  padding:1.5rem 1.25rem; display:flex; flex-direction:column; gap:.6rem;
  transition:box-shadow .2s, transform .2s;
}
.wf-benefit:hover { box-shadow:var(--wf-shadow); transform:translateY(-3px); }
.wf-benefit__icon {
  width:44px; height:44px; border-radius:12px;
  background:var(--wf-rose-lt); display:flex; align-items:center; justify-content:center;
  font-size:1.2rem; color:var(--wf-rose);
}
.wf-benefit__title { font-size:.88rem; font-weight:700; color:var(--wf-text); margin:0; }
.wf-benefit__desc  { font-size:.78rem; color:var(--wf-muted); margin:0; line-height:1.5; }

/* ── DIVIDER ── */
.wf-divider {
  height:1px; background:linear-gradient(90deg, transparent, var(--wf-border), transparent);
  margin:0;
}

/* ── HERO SPOTLIGHT REVEAL ── */
.wf-hero__reveal-layer {
  position:absolute; inset:0; z-index:1;
  pointer-events:none;
  /* two images stacked — header1 base, header2 on top */
}
.wf-hero__reveal-img {
  position:absolute; inset:0; width:100%; height:100%;
  object-fit:cover; object-position:center;
}
.wf-hero__reveal-img--1 { opacity:.38; }
.wf-hero__reveal-img--2 {
  opacity:.45;
  clip-path:circle(0px at 50% 50%);
  will-change:clip-path;
}

/* ── Responsive ── */
@media(max-width:1024px) {
  .wf-cat-grid  { grid-template-columns:repeat(2, 1fr); }
  .wf-prod-grid { grid-template-columns:repeat(3, 1fr); }
  .wf-benefits  { grid-template-columns:repeat(2, 1fr); }
  .wf-hero__stats { display:none; }
}
@media(max-width:640px) {
  .wf-prod-grid { grid-template-columns:repeat(2, 1fr); gap:.75rem; }
  .wf-cat-grid  { grid-template-columns:repeat(2, 1fr); }
  .wf-benefits  { grid-template-columns:1fr; }
  .wf-hero { min-height:80vh; }
  .wf-section  { padding:3.5rem 0; }
  .wf-scroll-cue { display:none; }
}

/* ── GSAP initial hidden state ── */
.gs-fade-up  { opacity:0; transform:translateY(40px); }
.gs-fade-in  { opacity:0; }
.gs-fade-left{ opacity:0; transform:translateX(-30px); }
.gs-scale    { opacity:0; transform:scale(.94); }

/* Hero elements NOT use gs- classes — animated directly by GSAP */
.wf-hero__tag,
.wf-hero__title,
.wf-hero__sub,
.wf-hero__actions,
.wf-stat-card { opacity:0; }
</style>

<!-- ══════════════ HERO ══════════════ -->
<section class="wf-hero" id="wf-hero">
  <img class="wf-hero__bg"
       src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=1600&q=85"
       alt="WireFlower Hero">
  <div class="wf-hero__noise"></div>

  <!-- Reveal layer: header1 always visible softly, header2 follows cursor -->
  <div class="wf-hero__reveal-layer" id="heroRevealLayer">
    <img class="wf-hero__reveal-img wf-hero__reveal-img--1"
         src="<?= site_url('assets/img/header1.png') ?>"
         alt="">
    <img class="wf-hero__reveal-img wf-hero__reveal-img--2"
         src="<?= site_url('assets/img/header2.png') ?>"
         alt="" id="heroRevealImg2">
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
        <img src="<?= $img ?>" alt="<?= e($k['name']) ?>">
        <div class="wf-cat-card__overlay"></div>

        <?php /* Wire flower reveal layer */ ?>
        <div class="wf-cat-card__reveal">
          <img src="<?= $wireImg ?>" alt="Wire <?= e($k['name']) ?>">
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
          <img src="<?= $img ?>" alt="<?= e($p['name']) ?>" loading="lazy">
        </a>
        <button class="wf-prod-card__wish" type="button" aria-label="Wishlist">
          <i class="bi bi-heart"></i>
        </button>
        <div class="wf-prod-card__body">
          <a href="<?= site_url('produk.php?slug=' . $p['slug']) ?>" style="text-decoration:none;">
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
           src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=1400&q=80"
           alt="Custom bouquet">
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
<section class="wf-section" style="padding-top:0;">
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
          <img src="<?= $img ?>" alt="<?= e($p['name']) ?>" loading="lazy">
        </a>
        <button class="wf-prod-card__wish" type="button" aria-label="Wishlist">
          <i class="bi bi-heart"></i>
        </button>
        <div class="wf-prod-card__body">
          <a href="<?= site_url('produk.php?slug=' . $p['slug']) ?>" style="text-decoration:none;">
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
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
(function () {
  gsap.registerPlugin(ScrollTrigger);

  /* ══ HERO entrance — langsung animate ke opacity:1
        TIDAK pakai gs- class agar tidak ada konflik ══ */
  var heroTl = gsap.timeline({ defaults:{ ease:'power3.out' } });
  heroTl
    .to('.wf-hero__tag',     { opacity:1, y:0, duration:.7 }, .15)
    .to('.wf-hero__title',   { opacity:1, y:0, duration:.8 }, .28)
    .to('.wf-hero__sub',     { opacity:1, y:0, duration:.7 }, .45)
    .to('.wf-hero__actions', { opacity:1, y:0, duration:.6 }, .60)
    .to('.wf-stat-card',     { opacity:1, x:0, duration:.6, stagger:.15 }, .72);

  /* Set dari state */
  gsap.set('.wf-hero__tag',     { y:30 });
  gsap.set('.wf-hero__title',   { y:40 });
  gsap.set('.wf-hero__sub',     { y:30 });
  gsap.set('.wf-hero__actions', { y:25 });
  gsap.set('.wf-stat-card',     { x:40 });

  /* ── Parallax hero bg ── */
  gsap.to('#wf-hero .wf-hero__bg', {
    yPercent:20, ease:'none',
    scrollTrigger:{ trigger:'#wf-hero', start:'top top', end:'bottom top', scrub:true }
  });

  /* ══ ScrollTrigger: gs-fade-up ══ */
  gsap.utils.toArray('.gs-fade-up').forEach(function(el) {
    gsap.to(el, {
      opacity:1, y:0, duration:.7, ease:'power3.out',
      scrollTrigger:{ trigger:el, start:'top 88%', toggleActions:'play none none none' }
    });
  });

  /* ══ ScrollTrigger: gs-fade-in ══ */
  gsap.utils.toArray('.gs-fade-in').forEach(function(el) {
    gsap.to(el, {
      opacity:1, duration:.6, ease:'power2.out',
      scrollTrigger:{ trigger:el, start:'top 90%', toggleActions:'play none none none' }
    });
  });

  /* ══ ScrollTrigger: gs-scale (stagger per row) ══ */
  gsap.utils.toArray('.gs-scale').forEach(function(el, i) {
    gsap.to(el, {
      opacity:1, scale:1, duration:.65, ease:'power3.out',
      delay:(i % 4) * .08,
      scrollTrigger:{ trigger:el, start:'top 90%', toggleActions:'play none none none' }
    });
  });

  /* ══ Benefit stagger ══ */
  gsap.utils.toArray('.wf-benefit.gs-fade-up').forEach(function(el, i) {
    gsap.to(el, {
      opacity:1, y:0, duration:.6, ease:'power3.out', delay:i * .1,
      scrollTrigger:{ trigger:el, start:'top 90%', toggleActions:'play none none none' }
    });
  });

  /* ══ Micro: add-to-cart elastic ══ */
  document.querySelectorAll('.wf-prod-card__add').forEach(function(btn) {
    btn.addEventListener('click', function() {
      gsap.fromTo(btn, { scale:1.35 }, { scale:1, duration:.4, ease:'elastic.out(1,.5)' });
    });
  });

  /* ══ Micro: wishlist heart ══ */
  document.querySelectorAll('.wf-prod-card__wish').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var icon = btn.querySelector('i');
      var filled = icon.classList.contains('bi-heart-fill');
      icon.classList.toggle('bi-heart-fill', !filled);
      icon.classList.toggle('bi-heart', filled);
      gsap.fromTo(btn,
        { scale: filled ? 1 : .75 },
        { scale:1, duration:.45, ease:'elastic.out(1,.4)' }
      );
      gsap.to(btn, { color: filled ? '' : '#c9516f', duration:.2 });
    });
  });

  /* ══ Category reveal — cursor-tracked spotlight per card ══ */
  document.querySelectorAll('.wf-cat-card').forEach(function(card) {
    var reveal  = card.querySelector('.wf-cat-card__reveal');
    var name    = card.querySelector('.wf-cat-card__name');
    var desc    = card.querySelector('.wf-cat-card__desc');
    var radius  = 0;
    var cx = 50, cy = 50; // percent

    function applyClip() {
      reveal.style.clipPath = 'circle(' + radius + 'px at ' + cx + '% ' + cy + '%)';
    }

    card.addEventListener('mouseenter', function() {
      // Tween radius via gsap
      var obj = {r:0};
      gsap.to(obj, { r:120, duration:.5, ease:'power2.out',
        onUpdate: function(){ radius = obj.r; applyClip(); }
      });
      gsap.to([name, desc], { y:-4, duration:.3, ease:'power2.out', stagger:.05 });
    });

    card.addEventListener('mousemove', function(e) {
      var rect = card.getBoundingClientRect();
      cx = ((e.clientX - rect.left) / rect.width)  * 100;
      cy = ((e.clientY - rect.top)  / rect.height) * 100;
      applyClip();
    });

    card.addEventListener('mouseleave', function() {
      var obj = {r:radius};
      gsap.to(obj, { r:0, duration:.4, ease:'power2.in',
        onUpdate: function(){ radius = obj.r; applyClip(); }
      });
      gsap.to([name, desc], { y:0, duration:.25, ease:'power2.in', stagger:.03 });
    });
  });

  /* ══ Hero spotlight reveal — cursor follows mouse over hero ══ */
  (function(){
    var hero    = document.getElementById('wf-hero');
    var img2    = document.getElementById('heroRevealImg2');
    if (!hero || !img2) return;

    var radius  = 0;
    var cx = 50, cy = 50;

    function applyHeroClip() {
      img2.style.clipPath = 'circle(' + radius + 'px at ' + cx + '% ' + cy + '%)';
    }

    hero.addEventListener('mouseenter', function() {
      var obj = {r:0};
      gsap.to(obj, { r:220, duration:.7, ease:'power2.out',
        onUpdate: function(){ radius = obj.r; applyHeroClip(); }
      });
    });

    hero.addEventListener('mousemove', function(e) {
      var rect = hero.getBoundingClientRect();
      cx = ((e.clientX - rect.left) / rect.width)  * 100;
      cy = ((e.clientY - rect.top)  / rect.height) * 100;
      applyHeroClip();
    });

    hero.addEventListener('mouseleave', function() {
      var obj = {r:radius};
      gsap.to(obj, { r:0, duration:.5, ease:'power2.in',
        onUpdate: function(){ radius = obj.r; applyHeroClip(); }
      });
    });
  })();

})();
</script>
