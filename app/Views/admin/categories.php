<h4 class="fw-bold mb-4">Kelola Kategori</h4>
<div class="card border-0 shadow-sm p-3 mb-4">
  <form method="POST" class="d-flex gap-2">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
    <input type="text" name="name" class="form-control" placeholder="Nama Kategori" value="<?= e($edit['name'] ?? '') ?>" required>
    <button type="submit" name="save_category" class="btn btn-pink">Simpan</button>
    <?php if ($edit): ?><a href="<?= site_url('admin/kategori.php') ?>" class="btn btn-outline-secondary">Batal</a><?php endif; ?>
  </form>
</div>
<div class="card border-0 shadow-sm p-3">
  <table class="table align-middle">
    <thead><tr><th>Nama</th><th>Slug</th><th>Jumlah Produk</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($categories as $c): ?>
        <tr>
          <td><?= e($c['name']) ?></td>
          <td><?= e($c['slug']) ?></td>
          <td><?= $c['jml_produk'] ?></td>
          <td>
            <a href="?edit=<?= $c['id'] ?>" class="btn btn-sm btn-outline-pink">Edit</a>
            <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
