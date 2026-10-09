<div class="container" style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem;">

  <!-- Page title -->
  <div class="page-title-bar">
    <h1>Keranjang Belanja</h1>
    <p>Periksa kembali produk pilihanmu sebelum melanjutkan pesanan.</p>
  </div>

  <?php if (empty($items)): ?>
    <div class="cart-empty">
      <i class="bi bi-bag"></i>
      <h3>Keranjang masih kosong</h3>
      <p>Yuk, temukan bunga cantik yang cocok untukmu.</p>
      <a href="<?= site_url('kategori.php') ?>" class="btn btn-primary" style="margin-top:.75rem;">Mulai Belanja</a>
    </div>

  <?php else: ?>
    <form id="checkoutForm" method="POST" action="<?= site_url('checkout.php') ?>"></form>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:2rem;align-items:start;">

      <!-- Cart items -->
      <div>
        <!-- Select all bar -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.7rem 1rem;background:var(--white);border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:1rem;">
          <label style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;font-weight:600;cursor:pointer;">
            <input type="checkbox" id="selectAllItems" style="accent-color:var(--pink-deep);width:17px;height:17px;">
            Pilih semua
          </label>
          <span style="font-size:.8rem;color:var(--text-muted);"><?= $item_count ?> item</span>
        </div>

        <?php foreach ($items as $it):
          $img = asset_img($it['image'], 'https://placehold.co/100x100/fce8ef/c9516f?text=🌸');
        ?>
        <div class="cart-item" style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-md);padding:1.1rem 1.25rem;margin-bottom:.75rem;">
          <input class="cart-item-check" type="checkbox" name="selected_items[]"
                 value="<?= $it['id'] ?>" form="checkoutForm"
                 data-price="<?= (int)$it['price'] ?>"
                 data-qty="<?= (int)$it['quantity'] ?>"
                 aria-label="Pilih <?= e($it['name']) ?>"
                 style="accent-color:var(--pink-deep);width:18px;height:18px;flex-shrink:0;margin-top:.15rem;">
          <img class="cart-item-img" src="<?= $img ?>" alt="<?= e($it['name']) ?>">
          <div class="cart-item-info">
            <a href="<?= site_url('produk.php?slug=' . $it['slug']) ?>"
               style="text-decoration:none;color:inherit;">
              <div class="cart-item-name"><?= e($it['name']) ?></div>
            </a>
            <div class="cart-item-price"><?= rupiah($it['price']) ?> / pcs</div>
            <!-- Qty control -->
            <form method="POST" action="<?= site_url('cart_actions.php') ?>" style="margin-top:.65rem;display:inline-block;">
              <input type="hidden" name="action"  value="update">
              <input type="hidden" name="cart_id" value="<?= $it['id'] ?>">
              <div class="qty-control">
                <button type="button" class="qty-btn" onclick="changeCartQty(this,-1,<?= $it['stock'] ?>)"><i class="bi bi-dash"></i></button>
                <input class="qty-input" type="number" name="quantity"
                       value="<?= $it['quantity'] ?>" min="1" max="<?= $it['stock'] ?>"
                       onchange="this.form.submit()">
                <button type="button" class="qty-btn" onclick="changeCartQty(this,1,<?= $it['stock'] ?>)"><i class="bi bi-plus"></i></button>
              </div>
            </form>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;flex-shrink:0;">
            <div class="cart-item-subtotal"><?= rupiah($it['price'] * $it['quantity']) ?></div>
            <form method="POST" action="<?= site_url('cart_actions.php') ?>">
              <input type="hidden" name="action"  value="delete">
              <input type="hidden" name="cart_id" value="<?= $it['id'] ?>">
              <button type="submit" class="cart-item-remove">
                <i class="bi bi-trash"></i> Hapus
              </button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Order summary -->
      <div class="order-summary-box" style="position:sticky;top:90px;">
        <h4>Ringkasan Pesanan</h4>
        <div class="order-summary-row">
          <span id="summary-label">Subtotal (0 item dipilih)</span>
          <span id="summary-subtotal">Rp 0</span>
        </div>
        <div class="order-summary-row">
          <span>Ongkos kirim</span>
          <span>Dihitung saat checkout</span>
        </div>
        <div class="order-summary-row total">
          <span>Total</span>
          <span id="summary-total">Rp 0</span>
        </div>
        <button type="submit" form="checkoutForm"
                id="btnCheckout"
                class="btn btn-primary"
                style="width:100%;justify-content:center;margin-top:1rem;border-radius:var(--radius-sm);opacity:.5;cursor:not-allowed;"
                disabled>
          Checkout Item Terpilih
        </button>
        <a href="<?= site_url('kategori.php') ?>"
           style="display:block;text-align:center;margin-top:.75rem;font-size:.85rem;color:var(--text-muted);">
          <i class="bi bi-arrow-left"></i> Lanjut Belanja
        </a>
      </div>

    </div>
  <?php endif; ?>

</div>

<script>
// ── Rupiah formatter ──
function toRupiah(n) {
  return 'Rp ' + n.toLocaleString('id-ID');
}

// ── Hitung ulang ringkasan berdasarkan item tercentang ──
function recalcSummary() {
  const checks  = document.querySelectorAll('.cart-item-check');
  const lblEl   = document.getElementById('summary-label');
  const subEl   = document.getElementById('summary-subtotal');
  const totEl   = document.getElementById('summary-total');
  const btnEl   = document.getElementById('btnCheckout');

  let subtotal = 0;
  let count    = 0;

  checks.forEach(function(c) {
    if (c.checked) {
      const price = parseInt(c.dataset.price) || 0;
      const qty   = parseInt(c.dataset.qty)   || 1;
      subtotal += price * qty;
      count++;
    }
  });

  if (lblEl) lblEl.textContent = 'Subtotal (' + count + ' item dipilih)';
  if (subEl) subEl.textContent = toRupiah(subtotal);
  if (totEl) totEl.textContent = toRupiah(subtotal);

  // Aktifkan tombol hanya jika ada yang dipilih
  if (btnEl) {
    btnEl.disabled = count === 0;
    btnEl.style.opacity  = count === 0 ? '.5'  : '1';
    btnEl.style.cursor   = count === 0 ? 'not-allowed' : 'pointer';
  }
}

// ── Select all ──
const selectAll  = document.getElementById('selectAllItems');
const itemChecks = document.querySelectorAll('.cart-item-check');

if (selectAll) {
  selectAll.addEventListener('change', function() {
    itemChecks.forEach(c => c.checked = selectAll.checked);
    recalcSummary();
  });
  itemChecks.forEach(function(c) {
    c.addEventListener('change', function() {
      selectAll.checked = [...itemChecks].every(c => c.checked);
      recalcSummary();
    });
  });
}

// Hitung awal saat halaman load
recalcSummary();

// ── Qty buttons ──
function changeCartQty(btn, delta, max) {
  const input = btn.parentElement.querySelector('.qty-input');
  let val = parseInt(input.value) + delta;
  if (val < 1) val = 1;
  if (val > max) val = max;
  input.value = val;
  input.form.submit();
}
</script>
