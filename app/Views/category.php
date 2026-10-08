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

  <?php else: ?>
    <!-- Header: judul + sort -->
    <div class="section-header" style="margin-bottom:1.25rem;">
      <div>
        <?php if ($active_category ?? null): ?>
          <h2 class="section-title"><?= e($active_category['name']) ?></h2>
          <?php if (!empty($active_category['description'])): ?>
            <p style="color:var(--text-muted);margin:.35rem 0 0;font-size:.9rem;"><?= e($active_category['description']) ?></p>
          <?php endif; ?>
        <?php else: ?>
          <h2 class="section-title">Semua Produk</h2>
          <p style="color:var(--text-muted);margin:.35rem 0 0;font-size:.9rem;"><?= count($products ?? []) ?> produk tersedia</p>
        <?php endif; ?>
      </div>
      <!-- Sort (hanya tampil di Semua / per kategori) -->
      <form method="GET" action="<?= site_url('kategori.php') ?>" style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;">
        <?php if ($slug ?? ''): ?>
          <input type="hidden" name="slug" value="<?= e($slug) ?>">
        <?php endif; ?>
        <label for="sort-select" style="font-size:.82rem;color:var(--text-muted);white-space:nowrap;">Urutkan:</label>
        <select id="sort-select" name="sort" onchange="this.form.submit()"
                style="padding:.38rem .75rem;border:1.5px solid var(--border);border-radius:50px;font-size:.82rem;background:#fff;color:var(--text);cursor:pointer;outline:none;">
          <option value="terbaru"    <?= ($sort??'terbaru')==='terbaru'    ? 'selected' : '' ?>>Terbaru</option>
          <option value="terlaris"   <?= ($sort??'')==='terlaris'          ? 'selected' : '' ?>>Terlaris</option>
          <option value="harga_asc"  <?= ($sort??'')==='harga_asc'         ? 'selected' : '' ?>>Harga: Rendah</option>
          <option value="harga_desc" <?= ($sort??'')==='harga_desc'        ? 'selected' : '' ?>>Harga: Tinggi</option>
        </select>
      </form>
    </div>

    <?php require VIEW_PATH . '/partials/product_grid.php'; ?>

  <?php endif; ?>

</div>
