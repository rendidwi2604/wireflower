<div class="container" style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem;">

  <!-- Page hero -->
  <div class="page-hero">
    <div class="page-hero-copy">
      <span class="section-eyebrow">Koleksi WireFlower</span>
      <h1>Temukan Bunga Favoritmu</h1>
      <p>Pilih dari berbagai kategori bunga kawat bulu handmade untuk setiap momen spesial.</p>
    </div>
    <img class="page-hero-img"
         src="https://images.unsplash.com/photo-1526047932273-341f2a7631f9?auto=format&fit=crop&w=700&q=85"
         alt="Koleksi bunga WireFlower">
  </div>

  <!-- Filter pills -->
  <div class="filter-pills">
    <a href="<?= site_url('kategori.php') ?>"
       class="filter-pill <?= !($slug ?? '') ? 'active' : '' ?>">
      Semua
    </a>
    <?php foreach ($kategori_list ?? [] as $k): ?>
      <a href="<?= site_url('kategori.php?slug=' . $k['slug']) ?>"
         class="filter-pill <?= ($slug ?? '') === $k['slug'] ? 'active' : '' ?>">
        <?= e($k['name']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (($slug ?? '') !== '' && !($active_category ?? null)): ?>
    <!-- Not found -->
    <div style="text-align:center;padding:4rem 2rem;">
      <i class="bi bi-search" style="font-size:2.5rem;color:var(--border);display:block;margin-bottom:1rem;"></i>
      <h4 style="font-family:Georgia,serif;margin-bottom:.5rem;">Kategori tidak ditemukan</h4>
      <p style="color:var(--text-muted);">Coba pilih kategori lain di atas.</p>
      <a href="<?= site_url('kategori.php') ?>" class="btn btn-primary" style="margin-top:1rem;">Lihat Semua</a>
    </div>

  <?php elseif ($active_category ?? null): ?>
    <!-- Category products -->
    <div class="section-header">
      <div>
        <h2 class="section-title"><?= e($active_category['name']) ?></h2>
        <?php if (!empty($active_category['description'])): ?>
          <p style="color:var(--text-muted);margin:.35rem 0 0;font-size:.9rem;"><?= e($active_category['description']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php require VIEW_PATH . '/partials/product_grid.php'; ?>

  <?php else: ?>
    <!-- All categories as cards -->
    <?php
    $cat_images = [
      'https://images.unsplash.com/photo-1477039181047-efb4432d0d27?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1526047932273-341f2a7631f9?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=600&q=85',
      'https://images.unsplash.com/photo-1593691509543-c55fb32e5cee?auto=format&fit=crop&w=600&q=85',
    ];
    ?>
    <div class="category-grid" style="grid-template-columns:repeat(3,1fr);">
      <?php foreach ($kategori_list ?? [] as $i => $k): ?>
      <a class="category-card" href="<?= site_url('kategori.php?slug=' . $k['slug']) ?>">
        <div class="category-card-img">
          <img src="<?= $cat_images[$i % count($cat_images)] ?>" alt="<?= e($k['name']) ?>">
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
  <?php endif; ?>

</div>
