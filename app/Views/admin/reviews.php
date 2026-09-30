<h4 class="fw-bold mb-4">Kelola Ulasan</h4>
<div class="card border-0 shadow-sm p-3">
  <table class="table align-middle">
    <thead><tr><th>Produk</th><th>Pengguna</th><th>Rating</th><th>Ulasan</th><th>Foto</th><th>Tanggal</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($reviews as $r): ?>
        <tr>
          <td><?= e($r['product_name']) ?></td>
          <td><?= e($r['user_name']) ?></td>
          <td><?= str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']) ?></td>
          <td style="max-width:250px;"><?= e($r['comment']) ?></td>
          <td><?php if ($r['photo']): ?><img src="<?= site_url('assets/img/' . $r['photo']) ?>" width="50" style="border-radius:6px;"><?php else: ?>-<?php endif; ?></td>
          <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
          <td><a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus ulasan ini?')">Hapus</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
