<div class="container" style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem;">

<?php if (!$product): ?>
  <div style="text-align:center;padding:5rem 2rem;">
    <i class="bi bi-flower2" style="font-size:3rem;color:var(--border);display:block;margin-bottom:1rem;"></i>
    <h4 style="font-family:Georgia,serif;margin-bottom:.5rem;">Produk tidak ditemukan</h4>
    <a href="<?= site_url('kategori.php') ?>" class="btn btn-primary" style="margin-top:1rem;">Lihat Produk Lain</a>
  </div>

<?php else:
  $img = asset_img($product['image'], 'https://placehold.co/600x600/fce8ef/c9516f?text=🌸');
  $avg = round($avg_rating ?? 0, 1);
  $full  = (int)$avg;
  $half  = ($avg - $full) >= 0.5 ? 1 : 0;
  $empty = 5 - $full - $half;
?>

  <!-- Breadcrumb -->
  <nav style="font-size:.82rem;color:var(--text-muted);margin-bottom:1.5rem;">
    <a href="<?= site_url('index.php') ?>" style="color:var(--text-muted);">Home</a>
    <span style="margin:0 .4rem;opacity:.5;">/</span>
    <a href="<?= site_url('kategori.php') ?>" style="color:var(--text-muted);">Shop</a>
    <?php if (!empty($product['category_name'])): ?>
      <span style="margin:0 .4rem;opacity:.5;">/</span>
      <a href="<?= site_url('kategori.php?slug=' . ($product['category_slug'] ?? '')) ?>" style="color:var(--text-muted);"><?= e($product['category_name']) ?></a>
    <?php endif; ?>
    <span style="margin:0 .4rem;opacity:.5;">/</span>
    <span style="color:var(--text);"><?= e($product['name']) ?></span>
  </nav>

  <div class="product-detail" style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:3rem;align-items:start;">

    <!-- LEFT: Image -->
    <div>
      <img class="product-detail-img" src="<?= $img ?>" alt="<?= e($product['name']) ?>">
    </div>

    <!-- RIGHT: Info -->
    <div>
      <?php if (!empty($product['category_name'])): ?>
        <span class="product-detail-category"><?= e($product['category_name']) ?></span>
      <?php endif; ?>

      <h1 class="product-detail-title"><?= e($product['name']) ?></h1>

      <!-- Rating -->
      <div class="product-detail-rating">
        <span class="stars">
          <?php for ($i = 0; $i < $full;  $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
          <?php for ($i = 0; $i < $half;  $i++): ?><i class="bi bi-star-half"></i><?php endfor; ?>
          <?php for ($i = 0; $i < $empty; $i++): ?><i class="bi bi-star"></i><?php endfor; ?>
        </span>
        <span><?= number_format($avg, 1) ?></span>
        <span>(<?= count($reviews ?? []) ?> ulasan)</span>
      </div>

      <!-- Price -->
      <div class="product-detail-price"><?= rupiah($product['price']) ?></div>

      <!-- Description -->
      <?php if (!empty($product['description'])): ?>
        <p class="product-detail-desc"><?= nl2br(e($product['description'])) ?></p>
      <?php endif; ?>

      <!-- Stock -->
      <?php if ($product['stock'] > 10): ?>
        <span class="stock-badge"><i class="bi bi-check-circle-fill"></i> Stok tersedia (<?= $product['stock'] ?>)</span>
      <?php elseif ($product['stock'] > 0): ?>
        <span class="stock-badge low"><i class="bi bi-exclamation-circle"></i> Stok terbatas (<?= $product['stock'] ?> sisa)</span>
      <?php else: ?>
        <span class="stock-badge empty"><i class="bi bi-x-circle"></i> Stok habis</span>
      <?php endif; ?>

      <?php if ($product['stock'] > 0): ?>
      <!-- Quantity + Add to cart -->
      <form method="POST" action="<?= site_url('cart_actions.php') ?>" id="cartForm">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <input type="hidden" name="action"     value="add">

        <div style="margin-bottom:1.25rem;">
          <div style="font-size:.82rem;font-weight:600;margin-bottom:.5rem;color:var(--text);">Jumlah</div>
          <div class="qty-control">
            <button type="button" class="qty-btn" onclick="changeQty(-1)"><i class="bi bi-dash"></i></button>
            <input class="qty-input" type="number" name="quantity" id="qty"
                   value="1" min="1" max="<?= $product['stock'] ?>">
            <button type="button" class="qty-btn" onclick="changeQty(1)"><i class="bi bi-plus"></i></button>
          </div>
        </div>

        <div class="add-to-cart-group">
          <button type="submit" class="btn btn-outline" style="flex:1;justify-content:center;">
            <i class="bi bi-bag-plus"></i> Tambah ke Keranjang
          </button>
        </div>
      </form>

      <form method="POST" action="<?= site_url('checkout.php') ?>" style="margin-top:.75rem;">
        <input type="hidden" name="buy_now_product_id" value="<?= $product['id'] ?>">
        <input type="hidden" name="buy_now_quantity" id="qtyBuyNow" value="1">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"
                onclick="document.getElementById('qtyBuyNow').value=document.getElementById('qty').value;">
          <i class="bi bi-lightning-fill"></i> Beli Sekarang
        </button>
      </form>

      <?php else: ?>
        <div style="margin-top:1rem;padding:.75rem 1rem;background:#fdf0f0;border-radius:var(--radius-sm);color:#8b1a1a;font-size:.875rem;font-weight:600;">
          <i class="bi bi-x-circle me-1"></i> Stok sedang habis
        </div>
      <?php endif; ?>

      <!-- Trust badges -->
      <div style="display:flex;gap:1.5rem;margin-top:1.75rem;padding-top:1.5rem;border-top:1px solid var(--border);">
        <div style="display:flex;align-items:center;gap:.45rem;font-size:.78rem;color:var(--text-muted);">
          <i class="bi bi-truck" style="color:var(--pink-deep);"></i> Pengiriman ke seluruh Indonesia
        </div>
        <div style="display:flex;align-items:center;gap:.45rem;font-size:.78rem;color:var(--text-muted);">
          <i class="bi bi-shield-check" style="color:var(--pink-deep);"></i> Produk handmade berkualitas
        </div>
      </div>

    </div>
  </div>

  <!-- ===== REVIEWS ===== -->
  <hr class="section-divider">
  <h3 style="font-family:Georgia,serif;margin-bottom:1.5rem;">
    Ulasan Pembeli
    <span style="font-size:.85rem;font-weight:400;color:var(--text-muted);margin-left:.5rem;">
      (<?= count($reviews ?? []) ?> ulasan)
    </span>
  </h3>

  <?php if (empty($reviews)): ?>
    <div style="text-align:center;padding:3rem 2rem;color:var(--text-muted);">
      <i class="bi bi-chat-dots" style="font-size:2rem;display:block;margin-bottom:.75rem;opacity:.35;"></i>
      <p style="margin:0;font-size:.9rem;">Belum ada ulasan untuk produk ini.<br>Jadilah yang pertama memberikan ulasan!</p>
    </div>
  <?php else: ?>
    <div class="review-list">
      <?php foreach ($reviews as $r): ?>
      <div class="review-item">
        <div class="review-header">
          <span class="review-name"><?= e($r['user_name']) ?></span>
          <span class="review-date"><?= date('d M Y', strtotime($r['created_at'])) ?></span>
        </div>
        <div class="review-stars">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <i class="bi bi-star<?= $i <= $r['rating'] ? '-fill' : '' ?>"></i>
          <?php endfor; ?>
        </div>
        <p class="review-text"><?= nl2br(e($r['comment'])) ?></p>
        <?php if ($r['photo']): ?>
          <img class="review-photo" src="<?= site_url('assets/img/' . $r['photo']) ?>" alt="Foto ulasan">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

<?php endif; ?>
</div>

<script>
function changeQty(delta) {
  const input = document.getElementById('qty');
  if (!input) return;
  let val = parseInt(input.value) + delta;
  if (val < 1) val = 1;
  if (val > parseInt(input.max)) val = parseInt(input.max);
  input.value = val;
}
</script>
