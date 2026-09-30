<h4 class="fw-bold mb-4">Kelola Produk</h4>

<div class="card border-0 shadow-sm p-3 mb-4">
  <h6 class="fw-bold"><?= $edit_product ? 'Edit Produk' : 'Tambah Produk Baru' ?></h6>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $edit_product['id'] ?? '' ?>">
    <div class="row g-2">
      <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="Nama Produk" value="<?= e($edit_product['name'] ?? '') ?>" required></div>
      <div class="col-md-3">
        <select name="category_id" class="form-select" required>
          <option value="">Pilih Kategori</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (($edit_product['category_id'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3"><input type="file" name="image" class="form-control"></div>
      <div class="col-md-3"><input type="number" name="price" class="form-control" placeholder="Harga" value="<?= e($edit_product['price'] ?? '') ?>" required></div>
      <div class="col-md-3"><input type="number" name="stock" class="form-control" placeholder="Stok" value="<?= e($edit_product['stock'] ?? '') ?>" required></div>
      <div class="col-md-6"><textarea name="description" class="form-control" placeholder="Deskripsi"><?= e($edit_product['description'] ?? '') ?></textarea></div>
    </div>
    <div class="form-check form-check-inline mt-2">
      <input class="form-check-input" type="checkbox" name="is_featured" <?= !empty($edit_product['is_featured']) ? 'checked' : '' ?>>
      <label class="form-check-label">Produk Unggulan / Promo</label>
    </div>
    <div class="form-check form-check-inline mt-2">
      <input class="form-check-input" type="checkbox" name="is_active" <?= ($edit_product['is_active'] ?? 1) ? 'checked' : '' ?>>
      <label class="form-check-label">Aktif (ditampilkan)</label>
    </div>
    <div class="mt-3">
      <button type="submit" name="save_product" class="btn btn-pink btn-sm">Simpan</button>
      <?php if ($edit_product): ?><a href="<?= site_url('admin/produk.php') ?>" class="btn btn-outline-secondary btn-sm">Batal</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="card border-0 shadow-sm p-3">
  <table class="table align-middle">
    <thead><tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Terjual</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><img src="<?= asset_img($p['image'], 'https://placehold.co/50') ?>" width="50" height="50" style="object-fit:cover;border-radius:8px;"></td>
          <td><?= e($p['name']) ?></td>
          <td><?= e($p['category_name']) ?></td>
          <td><?= rupiah($p['price']) ?></td>
          <td><?= $p['stock'] ?></td>
          <td><?= $p['sold_count'] ?></td>
          <td><span class="badge bg-<?= $p['is_active'] ? 'success' : 'secondary' ?>"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
          <td>
            <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-pink">Edit</a>
            <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
