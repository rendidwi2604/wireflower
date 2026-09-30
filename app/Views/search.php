<div class="container" style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem;">

  <!-- Search hero -->
  <div class="search-hero">
    <form method="GET" action="<?= site_url('search.php') ?>">
      <input class="form-input" type="search" name="q"
             value="<?= e($q) ?>"
             placeholder="Cari buket, hadiah, atau nama produk..."
             style="flex:1;min-width:0;" autofocus>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-search"></i> Cari
      </button>
    </form>
  </div>

  <!-- Filters -->
  <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
    <form method="GET" action="<?= site_url('search.php') ?>" id="filterForm" style="display:contents;">
      <input type="hidden" name="q" value="<?= e($q) ?>">

      <select name="category_id" class="form-input"
              style="width:auto;border-radius:50px;padding:.45rem 1rem;cursor:pointer;font-size:.85rem;"
              onchange="document.getElementById('filterForm').submit()">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategori ?? [] as $k): ?>
          <option value="<?= $k['id'] ?>" <?= ($category_id ?? '') == $k['id'] ? 'selected' : '' ?>>
            <?= e($k['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="sort" class="form-input"
              style="width:auto;border-radius:50px;padding:.45rem 1rem;cursor:pointer;font-size:.85rem;"
              onchange="document.getElementById('filterForm').submit()">
        <option value="">Urutkan</option>
        <option value="terbaru"    <?= ($sort ?? '') === 'terbaru'    ? 'selected' : '' ?>>Terbaru</option>
        <option value="terlaris"   <?= ($sort ?? '') === 'terlaris'   ? 'selected' : '' ?>>Terlaris</option>
        <option value="harga_asc"  <?= ($sort ?? '') === 'harga_asc'  ? 'selected' : '' ?>>Harga Terendah</option>
        <option value="harga_desc" <?= ($sort ?? '') === 'harga_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
      </select>
    </form>
  </div>

  <!-- Results meta -->
  <p class="search-meta">
    <?php if ($q !== ''): ?>
      Menampilkan hasil untuk <strong>"<?= e($q) ?>"</strong>
      <?php if (!empty($products)): ?>
        — <?= count($products) ?> produk ditemukan
      <?php endif; ?>
    <?php else: ?>
      Masukkan kata kunci untuk mulai mencari.
    <?php endif; ?>
  </p>

  <?php require VIEW_PATH . '/partials/product_grid.php'; ?>

</div>
