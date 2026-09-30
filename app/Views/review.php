<div class="container" style="max-width:540px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Beri Ulasan</h1>
    <p><?= e($product['name']) ?></p>
  </div>

  <div class="card-wf">
    <form method="POST" enctype="multipart/form-data">

      <!-- Star rating -->
      <div class="form-group">
        <label class="form-label">Rating</label>
        <div style="display:flex;gap:.5rem;flex-direction:row-reverse;justify-content:flex-end;">
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" name="rating" id="r<?= $i ?>" value="<?= $i ?>"
                   <?= $i === 5 ? 'checked' : '' ?>
                   style="display:none;">
            <label for="r<?= $i ?>" class="star-label"
                   style="font-size:2rem;cursor:pointer;color:var(--border);transition:color .12s;"
                   onmouseover="highlightStars(<?= $i ?>)"
                   onmouseout="resetStars()"
                   onclick="selectStar(<?= $i ?>)">★</label>
          <?php endfor; ?>
        </div>
        <small style="font-size:.78rem;color:var(--text-muted);margin-top:.3rem;display:block;" id="ratingLabel">5 bintang — Luar biasa!</small>
      </div>

      <!-- Comment -->
      <div class="form-group">
        <label class="form-label" for="comment">Ulasan kamu</label>
        <textarea class="form-input" id="comment" name="comment" rows="4" required
                  style="resize:vertical;"
                  placeholder="Ceritakan pengalamanmu dengan produk ini..."></textarea>
      </div>

      <!-- Photo upload -->
      <div class="form-group">
        <label class="form-label">Foto Produk <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
        <input type="file" name="photo" class="form-input" accept="image/*" style="padding:.5rem .75rem;cursor:pointer;">
        <small style="font-size:.75rem;color:var(--text-muted);margin-top:.3rem;display:block;">Format: JPG, PNG. Maks. 2MB.</small>
      </div>

      <button type="submit" class="btn btn-primary"
              style="width:100%;justify-content:center;border-radius:var(--radius-sm);margin-top:.5rem;">
        <i class="bi bi-send"></i> Kirim Ulasan
      </button>
    </form>
  </div>

</div>

<style>
.star-label { display:inline-block; }
/* When checked, highlight this and all stars after (visually before in DOM due to flex-row-reverse) */
input[type=radio]:checked ~ label { color: #f4b400; }
</style>

<script>
const ratingLabels = {5:'Luar biasa!',4:'Bagus',3:'Cukup',2:'Kurang',1:'Buruk'};
const starLabels = document.querySelectorAll('.star-label');
const ratingInputs = document.querySelectorAll('input[name=rating]');

function highlightStars(n) {
  starLabels.forEach(l => {
    l.style.color = parseInt(l.htmlFor.replace('r','')) <= n ? '#f4b400' : 'var(--border)';
  });
}
function resetStars() {
  const checked = document.querySelector('input[name=rating]:checked');
  const val = checked ? parseInt(checked.value) : 0;
  starLabels.forEach(l => {
    l.style.color = parseInt(l.htmlFor.replace('r','')) <= val ? '#f4b400' : 'var(--border)';
  });
}
function selectStar(n) {
  document.getElementById('ratingLabel').textContent = n + ' bintang — ' + (ratingLabels[n] || '');
  resetStars();
}
// Init
resetStars();
</script>
