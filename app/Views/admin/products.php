<?php
$ep = $edit_product ?? null;
// Format harga untuk display (angka saja, tanpa prefix)
$priceDisplay = $ep ? number_format((float)$ep['price'], 0, ',', '.') : '';
?>

<!-- ── Form Tambah / Edit ── -->
<div class="adm-card">
  <div class="adm-card-title"><?= $ep ? 'Edit Produk' : 'Tambah Produk Baru' ?></div>
  <form method="POST" enctype="multipart/form-data" id="formProduk">
    <input type="hidden" name="id" value="<?= $ep['id'] ?? '' ?>">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem;">

      <!-- Nama -->
      <div class="adm-form-group">
        <label class="adm-form-label">Nama Produk</label>
        <input type="text" name="name" class="adm-form-control"
               placeholder="Mis. Buket Mawar Merah"
               value="<?= e($ep['name'] ?? '') ?>" required>
      </div>

      <!-- Kategori -->
      <div class="adm-form-group">
        <label class="adm-form-label">Kategori</label>
        <select name="category_id" class="adm-form-control" required>
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"
              <?= (($ep['category_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
              <?= e($c['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Harga — format Rupiah -->
      <div class="adm-form-group">
        <label class="adm-form-label">Harga</label>
        <div style="position:relative;">
          <span style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);
                       font-size:.82rem;font-weight:600;color:#8a7070;pointer-events:none;">
            Rp
          </span>
          <input type="text" id="price_display" class="adm-form-control"
                 placeholder="0" value="<?= $priceDisplay ?>"
                 inputmode="numeric"
                 style="padding-left:2.4rem;">
          <!-- hidden field yang dikirim ke server (angka saja) -->
          <input type="hidden" name="price" id="price_value"
                 value="<?= e($ep['price'] ?? '') ?>">
        </div>
      </div>

      <!-- Stok -->
      <div class="adm-form-group">
        <label class="adm-form-label">Stok</label>
        <input type="number" name="stock" class="adm-form-control"
               placeholder="0" min="0"
               value="<?= e($ep['stock'] ?? '') ?>" required>
      </div>

      <!-- Gambar -->
      <div class="adm-form-group">
        <label class="adm-form-label">
          Foto Produk
          <?php if (!empty($ep['image'])): ?>
            <span style="font-weight:400;color:var(--text-muted,#8a7070);margin-left:.35rem;">
              (kosongkan jika tidak ingin ganti)
            </span>
          <?php endif; ?>
        </label>
        <input type="file" name="image" class="adm-form-control"
               accept="image/jpeg,image/png,image/webp,image/gif">
        <?php if (!empty($ep['image'])): ?>
          <div style="margin-top:.5rem;">
            <img src="<?= asset_img($ep['image'], '') ?>"
                 style="height:56px;width:56px;object-fit:cover;border-radius:8px;border:1px solid #eeddd9;">
          </div>
        <?php endif; ?>
      </div>

      <!-- Deskripsi (full width) -->
      <div class="adm-form-group" style="grid-column:1/-1;">
        <label class="adm-form-label">Deskripsi</label>
        <textarea name="description" class="adm-form-control" rows="3"
                  placeholder="Deskripsi singkat produk…"><?= e($ep['description'] ?? '') ?></textarea>
      </div>

    </div>

    <!-- Checkbox -->
    <div style="display:flex;gap:1.5rem;margin:.5rem 0 1rem;flex-wrap:wrap;">
      <label style="display:flex;align-items:center;gap:.45rem;font-size:.84rem;cursor:pointer;">
        <input type="checkbox" name="is_featured"
               <?= !empty($ep['is_featured']) ? 'checked' : '' ?>
               style="accent-color:#c9516f;width:15px;height:15px;">
        <span>Produk Unggulan</span>
      </label>
      <label style="display:flex;align-items:center;gap:.45rem;font-size:.84rem;cursor:pointer;">
        <input type="checkbox" name="is_active"
               <?= ($ep['is_active'] ?? true) ? 'checked' : '' ?>
               style="accent-color:#c9516f;width:15px;height:15px;">
        <span>Aktif (ditampilkan di website)</span>
      </label>
    </div>

    <div style="display:flex;gap:.5rem;">
      <button type="submit" name="save_product" class="btn-adm btn-adm-primary">
        <i class="bi bi-check2"></i>
        <?= $ep ? 'Simpan Perubahan' : 'Tambah Produk' ?>
      </button>
      <?php if ($ep): ?>
        <a href="<?= site_url('admin/produk.php') ?>" class="btn-adm btn-adm-outline">
          <i class="bi bi-x"></i> Batal
        </a>
      <?php endif; ?>
    </div>
  </form>
</div>

<!-- ── Daftar Produk ── -->
<div class="adm-card">
  <div class="adm-card-title">Daftar Produk (<?= count($products) ?>)</div>
  <?php if (empty($products)): ?>
    <p style="text-align:center;padding:2rem;color:#8a7070;font-size:.875rem;">
      Belum ada produk. Tambahkan produk pertamamu di atas.
    </p>
  <?php else: ?>
  <div style="overflow-x:auto;">
    <table>
      <thead>
        <tr>
          <th>Foto</th>
          <th>Nama</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Terjual</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td>
              <img src="<?= asset_img($p['image'], 'https://placehold.co/48x48/fce8ef/c9516f?text=WF') ?>"
                   width="48" height="48"
                   style="object-fit:cover;border-radius:8px;border:1px solid #eeddd9;">
            </td>
            <td style="font-weight:600;max-width:200px;">
              <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?= e($p['name']) ?>
              </div>
              <?php if ($p['is_featured']): ?>
                <span style="font-size:.68rem;background:#fff8e1;color:#a0640a;padding:.1rem .45rem;border-radius:50px;font-weight:600;">
                  ★ Unggulan
                </span>
              <?php endif; ?>
            </td>
            <td style="color:#8a7070;"><?= e($p['category_name']) ?></td>
            <td style="font-weight:600;"><?= rupiah($p['price']) ?></td>
            <td><?= $p['stock'] ?></td>
            <td><?= $p['sold_count'] ?></td>
            <td>
              <?php if ($p['is_active']): ?>
                <span class="badge-active">Aktif</span>
              <?php else: ?>
                <span class="badge-inactive">Nonaktif</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex;gap:.35rem;">
                <a href="?edit=<?= $p['id'] ?>" class="btn-adm btn-adm-outline btn-adm-sm">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="?delete=<?= $p['id'] ?>" class="btn-adm btn-adm-danger btn-adm-sm"
                   onclick="return confirm('Hapus produk \'<?= e(addslashes($p['name'])) ?>\'?')">
                  <i class="bi bi-trash"></i>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
/* ── Format Rupiah real-time ── */
(function () {
  var display = document.getElementById('price_display');
  var hidden  = document.getElementById('price_value');
  if (!display || !hidden) return;

  function toRaw(val) {
    // Hapus semua karakter non-digit
    return val.replace(/\D/g, '');
  }

  function formatRupiah(raw) {
    if (!raw) return '';
    // Format dengan titik sebagai thousand separator
    return parseInt(raw, 10).toLocaleString('id-ID');
  }

  // Saat user mengetik
  display.addEventListener('input', function () {
    var raw = toRaw(this.value);
    // Simpan posisi kursor sebelum format
    var pos = this.selectionStart;
    var prevLen = this.value.length;

    this.value = formatRupiah(raw);
    hidden.value = raw;

    // Adjust posisi kursor
    var diff = this.value.length - prevLen;
    this.setSelectionRange(pos + diff, pos + diff);
  });

  // Format saat load (edit mode — sudah ada nilai)
  if (display.value !== '') {
    var raw = toRaw(display.value);
    display.value = formatRupiah(raw);
    hidden.value  = raw;
  }

  // Validasi sebelum submit — pastikan hidden price terisi
  var form = document.getElementById('formProduk');
  if (form) {
    form.addEventListener('submit', function () {
      var raw = toRaw(display.value);
      hidden.value = raw;
    });
  }
})();
</script>
