<?php
$adminCount    = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
$customerCount = count($users) - $adminCount;
$currentId     = $_SESSION['user_id'] ?? 0;
?>
<style>
/* Search bar */
.usr-toolbar {
  display:flex; align-items:center; justify-content:space-between;
  gap:.85rem; flex-wrap:wrap; margin-bottom:1.25rem;
}
.usr-search-wrap { position:relative; flex:1; min-width:200px; max-width:340px; }
.usr-search-wrap i { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:#8a7070; font-size:.9rem; pointer-events:none; }
.usr-search { width:100%; padding:.48rem .85rem .48rem 2.1rem; border:1.5px solid #eeddd9; border-radius:50px; font-size:.83rem; outline:none; background:#fff; color:#2c1a1e; transition:border-color .15s; }
.usr-search:focus { border-color:#c9516f; }

/* Stats row */
.usr-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; margin-bottom:1.25rem; }
.usr-stat {
  background:#fff; border:1px solid #eeddd9; border-radius:12px;
  padding:.85rem 1.1rem; display:flex; align-items:center; gap:.75rem;
}
.usr-stat-icon { width:40px; height:40px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:1rem; }
.usr-stat-num  { font-size:1.3rem; font-weight:800; color:#2c1a1e; line-height:1; }
.usr-stat-lbl  { font-size:.7rem; color:#8a7070; font-weight:600; text-transform:uppercase; letter-spacing:.05em; }

/* Filter pills */
.usr-filter-bar { display:flex; flex-wrap:wrap; gap:.45rem; margin-bottom:1.1rem; }
.usr-pill {
  display:inline-flex; align-items:center; gap:.3rem;
  padding:.32rem .8rem; border-radius:50px; font-size:.76rem; font-weight:600;
  border:1.5px solid #eeddd9; background:#fff; color:#5a4040;
  cursor:pointer; transition:all .15s;
}
.usr-pill:hover  { border-color:#c9516f; color:#c9516f; background:#fce8ef; }
.usr-pill.active { background:#c9516f; border-color:#c9516f; color:#fff; }

/* Table */
.usr-card { background:#fff; border:1px solid #eeddd9; border-radius:14px; overflow:hidden; }
.usr-card-header { padding:.85rem 1.1rem; border-bottom:1px solid #f5eded; display:flex; align-items:center; justify-content:space-between; }
.usr-card-title  { font-family:Georgia,serif; font-weight:700; font-size:.9rem; color:#2c1a1e; }
.usr-table-wrap  { overflow-x:auto; }
.usr-table { width:100%; border-collapse:collapse; min-width:560px; }
.usr-table thead th { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#8a7070; padding:.6rem .9rem; border-bottom:2px solid #f0e8e8; background:#fdfbfb; white-space:nowrap; }
.usr-table tbody td { padding:.8rem .9rem; border-bottom:1px solid #f5eded; font-size:.82rem; vertical-align:middle; }
.usr-table tbody tr:last-child td { border-bottom:none; }
.usr-table tbody tr:hover td { background:#fdfbfb; }
.usr-table tbody tr.hidden { display:none; }

/* Avatar */
.usr-avatar {
  width:34px; height:34px; border-radius:50%; flex-shrink:0;
  background:linear-gradient(135deg,#fce8ef,#f0d0dc);
  display:flex; align-items:center; justify-content:center;
  font-size:.82rem; font-weight:700; color:#c9516f;
}
.usr-name-cell { display:flex; align-items:center; gap:.65rem; }
.usr-name-text { font-weight:600; font-size:.84rem; color:#2c1a1e; }
.usr-email-text { font-size:.72rem; color:#8a7070; margin-top:.05rem; }

/* Role badge */
.usr-role-admin    { display:inline-flex; align-items:center; gap:.3rem; padding:.22rem .65rem; border-radius:50px; font-size:.72rem; font-weight:700; background:#fce8ef; color:#c9516f; }
.usr-role-customer { display:inline-flex; align-items:center; gap:.3rem; padding:.22rem .65rem; border-radius:50px; font-size:.72rem; font-weight:700; background:#f5f5f5; color:#5a4040; }

/* Action buttons */
.usr-action-wrap { display:flex; align-items:center; gap:.4rem; flex-wrap:wrap; }
.btn-usr-toggle { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .7rem; border-radius:7px; font-size:.75rem; font-weight:600; border:1.5px solid #eeddd9; background:#fff; color:#5a4040; cursor:pointer; transition:all .15s; }
.btn-usr-toggle:hover  { border-color:#c9516f; background:#fce8ef; color:#c9516f; }
.btn-usr-delete { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .7rem; border-radius:7px; font-size:.75rem; font-weight:600; border:1.5px solid #fecaca; background:#fff; color:#ef4444; cursor:pointer; text-decoration:none; transition:all .15s; }
.btn-usr-delete:hover  { background:#fef2f2; }
.usr-current-badge { font-size:.68rem; padding:.15rem .5rem; border-radius:50px; background:#ecfdf5; color:#10b981; font-weight:700; }

.empty-state { text-align:center; padding:3rem 2rem; color:#8a7070; }
.empty-state i { font-size:2.2rem; color:#eeddd9; display:block; margin-bottom:.6rem; }

@media(max-width:768px){ .usr-stats{ grid-template-columns:1fr 1fr; } }
@media(max-width:480px){ .usr-stats{ grid-template-columns:1fr; } .usr-toolbar{ flex-direction:column; align-items:stretch; } .usr-search-wrap{ max-width:none; } }
</style>

<!-- Stats -->
<div class="usr-stats">
  <div class="usr-stat">
    <div class="usr-stat-icon" style="background:#f5f3ff;color:#8b5cf6;"><i class="bi bi-people-fill"></i></div>
    <div><div class="usr-stat-num"><?= count($users) ?></div><div class="usr-stat-lbl">Total Pengguna</div></div>
  </div>
  <div class="usr-stat">
    <div class="usr-stat-icon" style="background:#fce8ef;color:#c9516f;"><i class="bi bi-shield-check"></i></div>
    <div><div class="usr-stat-num"><?= $adminCount ?></div><div class="usr-stat-lbl">Admin</div></div>
  </div>
  <div class="usr-stat">
    <div class="usr-stat-icon" style="background:#ecfdf5;color:#10b981;"><i class="bi bi-person-check"></i></div>
    <div><div class="usr-stat-num"><?= $customerCount ?></div><div class="usr-stat-lbl">Customer</div></div>
  </div>
</div>

<!-- Toolbar: search + filter -->
<div class="usr-toolbar">
  <div class="usr-search-wrap">
    <i class="bi bi-search"></i>
    <input type="text" id="usrSearch" class="usr-search" placeholder="Cari nama atau email…">
  </div>
  <div class="usr-filter-bar" style="margin-bottom:0;">
    <button class="usr-pill active" data-role="all"><i class="bi bi-grid"></i> Semua</button>
    <button class="usr-pill" data-role="admin"><i class="bi bi-shield-check"></i> Admin</button>
    <button class="usr-pill" data-role="customer"><i class="bi bi-person"></i> Customer</button>
  </div>
</div>

<!-- Table card -->
<div class="usr-card">
  <div class="usr-card-header">
    <span class="usr-card-title">Daftar Pengguna</span>
    <span id="usrCount" style="font-size:.78rem;color:#8a7070;"><?= count($users) ?> pengguna</span>
  </div>
  <?php if (empty($users)): ?>
    <div class="empty-state"><i class="bi bi-people"></i><p>Belum ada pengguna terdaftar.</p></div>
  <?php else: ?>
  <div class="usr-table-wrap">
    <table class="usr-table" id="usrTable">
      <thead>
        <tr>
          <th>Pengguna</th>
          <th>No. Telepon</th>
          <th>Role</th>
          <th>Bergabung</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr data-role="<?= e($u['role']) ?>"
            data-name="<?= strtolower(e($u['name'])) ?>"
            data-email="<?= strtolower(e($u['email'])) ?>">
          <td>
            <div class="usr-name-cell">
              <div class="usr-avatar"><?= mb_strtoupper(mb_substr($u['name'],0,1)) ?></div>
              <div>
                <div class="usr-name-text"><?= e($u['name']) ?>
                  <?php if ((int)$u['id'] === (int)$currentId): ?>
                    <span class="usr-current-badge">Kamu</span>
                  <?php endif; ?>
                </div>
                <div class="usr-email-text"><?= e($u['email']) ?></div>
              </div>
            </div>
          </td>
          <td style="color:#5a4040;"><?= e($u['phone'] ?: '—') ?></td>
          <td>
            <?php if ($u['role'] === 'admin'): ?>
              <span class="usr-role-admin"><i class="bi bi-shield-check"></i> Admin</span>
            <?php else: ?>
              <span class="usr-role-customer"><i class="bi bi-person"></i> Customer</span>
            <?php endif; ?>
          </td>
          <td style="color:#8a7070;font-size:.78rem;white-space:nowrap;">
            <?= date('d M Y', strtotime($u['created_at'])) ?>
          </td>
          <td>
            <div class="usr-action-wrap">
              <form method="POST" action="<?= site_url('admin/pengguna.php') ?>" style="margin:0;">
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                <button type="submit" name="toggle_role" class="btn-usr-toggle">
                  <i class="bi bi-arrow-left-right"></i>
                  Jadikan <?= $u['role'] === 'admin' ? 'Customer' : 'Admin' ?>
                </button>
              </form>
              <?php if ((int)$u['id'] !== (int)$currentId): ?>
                <a href="<?= site_url('admin/pengguna.php?delete='.$u['id']) ?>"
                   class="btn-usr-delete"
                   onclick="return confirm('Hapus pengguna <?= e(addslashes($u['name'])) ?>?')">
                  <i class="bi bi-trash3"></i> Hapus
                </a>
              <?php endif; ?>
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
// ── Search & filter ──
(function () {
  var search  = document.getElementById('usrSearch');
  var pills   = document.querySelectorAll('.usr-pill');
  var rows    = document.querySelectorAll('#usrTable tbody tr');
  var counter = document.getElementById('usrCount');
  var activeRole = 'all';

  function filterRows() {
    var q = (search ? search.value.toLowerCase() : '');
    var visible = 0;
    rows.forEach(function (r) {
      var matchRole  = (activeRole === 'all' || r.dataset.role === activeRole);
      var matchQuery = (!q || r.dataset.name.includes(q) || r.dataset.email.includes(q));
      var show = matchRole && matchQuery;
      r.classList.toggle('hidden', !show);
      if (show) visible++;
    });
    if (counter) counter.textContent = visible + ' pengguna';
  }

  if (search)  search.addEventListener('input',  filterRows);
  pills.forEach(function (pill) {
    pill.addEventListener('click', function () {
      pills.forEach(function (p) { p.classList.remove('active'); });
      pill.classList.add('active');
      activeRole = pill.dataset.role;
      filterRows();
    });
  });
})();
</script>
