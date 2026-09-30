<h4 class="fw-bold mb-4">Kelola Pengguna</h4>
<div class="card border-0 shadow-sm p-3">
  <table class="table align-middle">
    <thead><tr><th>Nama</th><th>Email</th><th>No. Telepon</th><th>Role</th><th>Bergabung</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= e($u['name']) ?></td>
          <td><?= e($u['email']) ?></td>
          <td><?= e($u['phone'] ?: '-') ?></td>
          <td><span class="badge bg-<?= $u['role'] == 'admin' ? 'pink' : 'secondary' ?>"><?= e($u['role']) ?></span></td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td>
            <form method="POST" class="d-inline">
              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
              <button type="submit" name="toggle_role" class="btn btn-sm btn-outline-pink">Jadikan <?= $u['role'] == 'admin' ? 'Customer' : 'Admin' ?></button>
            </form>
            <?php if ($u['id'] != current_user_id()): ?>
              <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
