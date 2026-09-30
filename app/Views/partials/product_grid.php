<?php
// $products = array of product rows
if (empty($products)) {
    echo '<p style="color:var(--text-muted);font-size:.9rem;">Belum ada produk.</p>';
    return;
}
?>
<div class="product-grid">
  <?php foreach ($products as $p):
    $img = asset_img($p['image'], 'https://placehold.co/400x400/fce8ef/c9516f?text=🌸');
    $avg  = round($p['avg_rating'] ?? 0, 1);
    $reviews = (int)($p['review_count'] ?? 0);
    $full  = (int)$avg;
    $half  = ($avg - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
  ?>
  <div class="product-card">
    <!-- Wishlist button -->
    <button class="product-card-wishlist" type="button" aria-label="Tambah ke wishlist">
      <i class="bi bi-heart"></i>
    </button>

    <!-- Image -->
    <a href="<?= site_url('produk.php?slug=' . $p['slug']) ?>" class="product-card-img" style="display:block;text-decoration:none;">
      <img src="<?= $img ?>" alt="<?= e($p['name']) ?>">
    </a>

    <!-- Body -->
    <div class="product-card-body">
      <a href="<?= site_url('produk.php?slug=' . $p['slug']) ?>" style="text-decoration:none;color:inherit;">
        <div class="product-card-name"><?= e($p['name']) ?></div>
      </a>
      <div class="product-card-price"><?= rupiah($p['price']) ?></div>

      <div class="product-card-footer">
        <div class="product-card-rating">
          <span class="stars">
            <?php for ($i = 0; $i < $full;  $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
            <?php for ($i = 0; $i < $half;  $i++): ?><i class="bi bi-star-half"></i><?php endfor; ?>
            <?php for ($i = 0; $i < $empty; $i++): ?><i class="bi bi-star"></i><?php endfor; ?>
          </span>
          <?php if ($reviews > 0): ?>
            <span>(<?= $reviews ?>)</span>
          <?php endif; ?>
        </div>
        <!-- Quick add to cart -->
        <form method="POST" action="<?= site_url('cart_actions.php') ?>" style="margin:0;">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
          <input type="hidden" name="quantity"   value="1">
          <input type="hidden" name="action"     value="add">
          <button type="submit" class="product-card-add" aria-label="Tambah ke keranjang">
            <i class="bi bi-plus"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
