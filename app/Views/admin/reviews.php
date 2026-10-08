<?php
// Stats
$totalReviews  = count($reviews);
$avgRating     = $totalReviews ? round(array_sum(array_column($reviews,'rating')) / $totalReviews, 1) : 0;
$ratingDist    = array_fill(1, 5, 0);
foreach ($reviews as $r) { if (isset($ratingDist[$r['rating']])) $ratingDist[$r['rating']]++; }

function starHtml(int $rating): string {
  $out = '';
  for ($i = 1; $i <= 5; $i++) {
    $out .= $i <= $rating
      ? '<i class="bi bi-star-fill" style="color:#f59e0b;font-size:.82rem;"></i>'
      : '<i class="bi bi-star"      style="color:#d1d5db;font-size:.82rem;"></i>';
  }
  return $out;
}
?>
<style>
/* Stats */
.rvw-stats { display:grid; grid-template-columns:auto 1fr; gap:1rem; margin-bottom:1.25rem; }
.rvw-big-score {
  background:#fff; border:1px solid #eeddd9; border-radius:14px;
  padding:1.25rem 1.75rem; display:flex; flex-direction:column;
  align-items:center; justify-content:center; gap:.25rem; min-width:130px;
}
.rvw-big-num { font-size:2.8rem; font-weight:800; color:#2c1a1e; line-height:1; }
.rvw-big-stars { display:flex; gap:.1rem; }
.rvw-big-total { font-size:.73rem; color:#8a7070; font-weight:600; }

.rvw-dist-card {
  background:#fff; border:1px solid #eeddd9; border-radius:14px;
  padding:1rem 1.25rem; display:flex; flex-direction:column; gap:.45rem; justify-content:center;
}
.rvw-dist-row { display:flex; align-items:center; gap:.6rem; font-size:.78rem; }
.rvw-dist-bar-bg { flex:1; height:7px; background:#f0e8e8; border-radius:50px; overflow:hidden; }
.rvw-dist-bar-fill { height:100%; border-radius:50px; background:#f59e0b; transition:width .5s; }
.rvw-dist-count { font-weight:700; color:#2c1a1e; min-width:22px; text-align:right; font-size:.76rem; }

/* Toolbar */
.rvw-toolbar { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; margin-bottom:1.1rem; }
.rvw-search-wrap { position:relative; flex:1; min-width:180px; max-width:300px; }
.rvw-search-wrap i { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:#8a7070; font-size:.85rem; pointer-events:none; }
.rvw-search { width:100%; padding:.45rem .85rem .45rem 2rem; border:1.5px solid #eeddd9; border-radius:50px; font-size:.82rem; outline:none; background:#fff; color:#2c1a1e; transition:border-color .15s; }
.rvw-search:focus { border-color:#c9516f; }

.rvw-star-pills { display:flex; gap:.4rem; flex-wrap:wrap; }
.rvw-star-pill {
  display:inline-flex; align-items:center; gap:.3rem;
  padding:.3rem .75rem; border-radius:50px; font-size:.75rem; font-weight:600;
  border:1.5px solid #eeddd9; background:#fff; color:#5a4040; cursor:pointer; transition:all .15s;
}
.rvw-star-pill:hover  { border-color:#f59e0b; background:#fef9c3; color:#a16207; }
.rvw-star-pill.active { background:#f59e0b; border-color:#f59e0b; color:#fff; }

/* Cards grid */
.rvw-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:.85rem; }
.rvw-card {
  background:#fff; border:1px solid #eeddd9; border-radius:14px;
  padding:1rem 1.1rem; display:flex; flex-direction:column; gap:.65rem;
  transition:box-shadow .2s;
}
.rvw-card:hover { box-shadow:0 4px 16px rgba(180,100,120,.1); }
.rvw-card.hidden { display:none; }

.rvw-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; }
.rvw-user-row { display:flex; align-items:center; gap:.6rem; }
.rvw-avatar {
  width:32px; height:32px; border-radius:50%; flex-shrink:0;
  background:linear-gradient(135deg,#fce8ef,#f0d0dc);
  display:flex; align-items:center; justify-content:center;
  font-size:.78rem; font-weight:700; color:#c9516f;
}
.rvw-user-name { font-weight:600; font-size:.83rem; color:#2c1a1e; }
.rvw-user-date { font-size:.7rem; color:#8a7070; margin-top:.05rem; }

.rvw-product-chip {
  display:inline-flex; align-items:center; gap:.3rem;
  padding:.2rem .65rem; border-radius:8px; font-size:.73rem; font-weight:600;
  background:#fce8ef; color:#c9516f; max-width:100%; overflow:hidden;
  text-overflow:ellipsis; white-space:nowrap;
}
.rvw-comment {
  font-size:.82rem; color:#5a4040; line-height:1.55;
  display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;
}
.rvw-photo { width:60px; height:60px; border-radius:8px; object-fit:cover; border:1px solid #eeddd9; flex-shrink:0; }

.rvw-card-footer { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-top:auto; }
.btn-rvw-delete {
  display:inline-flex; align-items:center; gap:.3rem;
  padding:.28rem .65rem; border-radius:7px; font-size:.73rem; font-weight:600;
  border:1.5px solid #fecaca; background:#fff; color:#ef4444;
  cursor:pointer; text-decoration:none; transition:all .15s;
}
.btn-rvw-delete:hover { background:#fef2f2; }

.rvw-empty { text-align:center; padding:3rem 2rem; color:#8a7070; }
.rvw-empty i { font-size:2.2rem; color:#eeddd9; display:block; margin-bottom:.6rem; }

@media(max-width:768px) {
  .rvw-stats { grid-template-columns:1fr; }
  .rvw-big-score { flex-direction:row; gap:1rem; min-width:0; padding:1rem; }
}
@media(max-width:480px) {
  .rvw-grid { grid-template-columns:1fr; }
  .rvw-toolbar { flex-direction:column; align-items:stretch; }
  .rvw-search-wrap { max-width:none; }
}
</style>

<!-- Stats row -->
<div class="rvw-stats">
  <!-- Big score -->
  <div class="rvw-big-score">
    <div class="rvw-big-num"><?= number_format($avgRating, 1) ?></div>
    <div class="rvw-big-stars">
      <?php for ($i=1;$i<=5;$i++): ?>
        <i class="bi bi-star<?= $i <= round($avgRating) ? '-fill' : '' ?>" style="color:#f59e0b;font-size:1rem;"></i>
      <?php endfor; ?>
    </div>
    <div class="rvw-big-total"><?= $totalReviews ?> ulasan</div>
  </div>

  <!-- Rating distribution -->
  <div class="rvw-dist-card">
    <?php for ($star = 5; $star >= 1; $star--):
      $pct = $totalReviews ? round($ratingDist[$star] / $totalReviews * 100) : 0;
    ?>
    <div class="rvw-dist-row">
      <span style="display:flex;align-items:center;gap:.2rem;min-width:52px;">
        <i class="bi bi-star-fill" style="color:#f59e0b;font-size:.72rem;"></i>
        <span style="font-weight:600;color:#2c1a1e;"><?= $star ?></span>
      </span>
      <div class="rvw-dist-bar-bg">
        <div class="rvw-dist-bar-fill" style="width:<?= $pct ?>%;"></div>
      </div>
      <span class="rvw-dist-count"><?= $ratingDist[$star] ?></span>
    </div>
    <?php endfor; ?>
  </div>
</div>

<!-- Toolbar -->
<div class="rvw-toolbar">
  <div class="rvw-search-wrap">
    <i class="bi bi-search"></i>
    <input type="text" id="rvwSearch" class="rvw-search" placeholder="Cari produk atau nama…">
  </div>
  <div class="rvw-star-pills">
    <button class="rvw-star-pill active" data-star="0">
      <i class="bi bi-grid"></i> Semua
    </button>
    <?php for ($s = 5; $s >= 1; $s--): ?>
    <button class="rvw-star-pill" data-star="<?= $s ?>">
      <?php for ($i=0;$i<$s;$i++): ?><i class="bi bi-star-fill" style="color:<?= 'inherit' ?>;font-size:.65rem;"></i><?php endfor; ?>
      <?= $s ?>★
    </button>
    <?php endfor; ?>
  </div>
</div>

<!-- Cards -->
<?php if (empty($reviews)): ?>
  <div class="rvw-empty"><i class="bi bi-chat-square-text"></i><p>Belum ada ulasan dari pelanggan.</p></div>
<?php else: ?>
<div class="rvw-grid" id="rvwGrid">
  <?php foreach ($reviews as $r): ?>
  <div class="rvw-card"
       data-star="<?= (int)$r['rating'] ?>"
       data-text="<?= strtolower(e($r['product_name'].' '.$r['user_name'])) ?>">

    <!-- Top -->
    <div class="rvw-card-top">
      <div class="rvw-user-row">
        <div class="rvw-avatar"><?= mb_strtoupper(mb_substr($r['user_name'],0,1)) ?></div>
        <div>
          <div class="rvw-user-name"><?= e($r['user_name']) ?></div>
          <div class="rvw-user-date"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
        </div>
      </div>
      <!-- Photo if exists -->
      <?php if (!empty($r['photo'])): ?>
        <img src="<?= site_url('assets/img/'.e($r['photo'])) ?>" class="rvw-photo" alt="Foto ulasan">
      <?php endif; ?>
    </div>

    <!-- Product chip -->
    <span class="rvw-product-chip">
      <i class="bi bi-flower1"></i> <?= e($r['product_name']) ?>
    </span>

    <!-- Stars -->
    <div style="display:flex;align-items:center;gap:.5rem;">
      <span><?= starHtml((int)$r['rating']) ?></span>
      <span style="font-size:.75rem;font-weight:700;color:#2c1a1e;"><?= $r['rating'] ?>.0</span>
    </div>

    <!-- Comment -->
    <?php if (!empty($r['comment'])): ?>
      <p class="rvw-comment"><?= e($r['comment']) ?></p>
    <?php else: ?>
      <p style="font-size:.78rem;color:#b0a0a0;font-style:italic;">Tidak ada komentar.</p>
    <?php endif; ?>

    <!-- Footer -->
    <div class="rvw-card-footer">
      <span style="font-size:.72rem;color:#8a7070;">ID #<?= $r['id'] ?></span>
      <a href="<?= site_url('admin/ulasan.php?delete='.$r['id']) ?>"
         class="btn-rvw-delete"
         onclick="return confirm('Hapus ulasan ini?')">
        <i class="bi bi-trash3"></i> Hapus
      </a>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div id="rvwEmpty" style="display:none;" class="rvw-empty">
  <i class="bi bi-search"></i>
  <p>Tidak ada ulasan yang cocok.</p>
</div>
<?php endif; ?>

<script>
// ── Search + star filter ──
(function () {
  var search    = document.getElementById('rvwSearch');
  var pills     = document.querySelectorAll('.rvw-star-pill');
  var cards     = document.querySelectorAll('#rvwGrid .rvw-card');
  var emptyMsg  = document.getElementById('rvwEmpty');
  var activeStar = 0;

  function filterCards() {
    var q = (search ? search.value.toLowerCase() : '');
    var visible = 0;
    cards.forEach(function (c) {
      var matchStar  = (activeStar === 0 || parseInt(c.dataset.star) === activeStar);
      var matchQuery = (!q || c.dataset.text.includes(q));
      var show = matchStar && matchQuery;
      c.classList.toggle('hidden', !show);
      if (show) visible++;
    });
    if (emptyMsg) emptyMsg.style.display = (visible === 0 ? 'block' : 'none');
  }

  if (search) search.addEventListener('input', filterCards);
  pills.forEach(function (pill) {
    pill.addEventListener('click', function () {
      pills.forEach(function (p) { p.classList.remove('active'); });
      pill.classList.add('active');
      activeStar = parseInt(pill.dataset.star);
      filterCards();
    });
  });
})();
</script>
