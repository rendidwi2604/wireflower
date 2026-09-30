<div class="container" style="max-width:680px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Profil Saya</h1>
    <p>Informasi akun dan alamat pengiriman kamu.</p>
  </div>

  <!-- Profile card -->
  <div class="card-wf" style="margin-bottom:1.25rem;">
    <div style="display:flex;align-items:center;gap:1.25rem;margin-bottom:1.5rem;">
      <div style="width:68px;height:68px;border-radius:50%;background:var(--pink-soft);display:grid;place-items:center;flex-shrink:0;">
        <i class="bi bi-person-fill" style="font-size:2rem;color:var(--pink-deep);"></i>
      </div>
      <div>
        <strong style="font-size:1.1rem;"><?= e($user['name']) ?></strong>
        <span style="display:block;font-size:.8rem;color:var(--text-muted);margin-top:.15rem;">
          <?= $user['role'] === 'admin' ? 'Administrator' : 'Pelanggan' ?>
        </span>
      </div>
    </div>

    <div style="display:grid;gap:.75rem;">
      <?php
      $fields = [
        'Nama'         => $user['name'],
        'Email'        => $user['email'],
        'No. Telepon'  => $user['phone'] ?: '—',
      ];
      foreach ($fields as $label => $value): ?>
        <div style="display:flex;gap:1rem;align-items:center;padding:.65rem 0;border-bottom:1px solid var(--border);">
          <span style="width:130px;flex-shrink:0;font-size:.82rem;color:var(--text-muted);"><?= $label ?></span>
          <span style="font-size:.9rem;font-weight:500;"><?= e($value) ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <a href="<?= site_url('pengaturan.php') ?>" class="btn btn-outline"
       style="margin-top:1.25rem;border-radius:var(--radius-sm);">
      <i class="bi bi-pencil"></i> Edit Profil
    </a>
  </div>

  <!-- Saved addresses -->
  <div class="card-wf">
    <div class="card-wf-header">
      <h4 style="font-family:Georgia,serif;margin:0;">Alamat Tersimpan</h4>
      <a href="<?= site_url('pengaturan.php#alamat') ?>" class="btn btn-sm"
         style="border:1px solid var(--border);background:var(--white);border-radius:50px;font-size:.8rem;color:var(--text);">
        <i class="bi bi-plus"></i> Tambah
      </a>
    </div>

    <?php if (empty($addresses)): ?>
      <p style="font-size:.875rem;color:var(--text-muted);margin:0;">Belum ada alamat tersimpan.</p>
    <?php endif; ?>

    <?php foreach ($addresses as $a): ?>
      <div style="padding:.85rem 1rem;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:.6rem;">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem;">
          <strong style="font-size:.875rem;"><?= e($a['label']) ?></strong>
          <?php if ($a['is_default']): ?>
            <span class="status-badge status-delivered" style="font-size:.68rem;padding:.2rem .55rem;">Utama</span>
          <?php endif; ?>
        </div>
        <div style="font-size:.82rem;color:var(--text);"><?= e($a['recipient_name']) ?> · <?= e($a['phone']) ?></div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.15rem;">
          <?= e($a['full_address']) ?>, <?= e($a['city']) ?>, <?= e($a['province']) ?> <?= e($a['postal_code']) ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>
