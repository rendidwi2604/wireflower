<div class="container" style="max-width:720px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Pengaturan</h1>
    <p>Kelola profil, password, dan preferensi akunmu.</p>
  </div>

  <!-- Edit Profile -->
  <div class="card-wf" style="margin-bottom:1.25rem;">
    <h4 style="font-family:Georgia,serif;margin-bottom:1.25rem;">Edit Profil</h4>
    <form method="POST">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem;">
        <div class="form-group">
          <label class="form-label" for="sName">Nama</label>
          <input class="form-input" type="text" id="sName" name="name"
                 value="<?= e($user['name']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input class="form-input" type="email" value="<?= e($user['email']) ?>"
                 disabled style="opacity:.65;cursor:not-allowed;">
        </div>
        <div class="form-group">
          <label class="form-label" for="sPhone">No. Telepon</label>
          <input class="form-input" type="text" id="sPhone" name="phone"
                 value="<?= e($user['phone']) ?>" placeholder="08xxxxxxxxxx">
        </div>
      </div>
      <button type="submit" name="update_profile"
              class="btn btn-primary btn-sm"
              style="margin-top:.75rem;border-radius:var(--radius-sm);">
        Simpan Profil
      </button>
    </form>
  </div>

  <!-- Change Password -->
  <div class="card-wf" style="margin-bottom:1.25rem;">
    <h4 style="font-family:Georgia,serif;margin-bottom:1.25rem;">Ubah Password</h4>
    <form method="POST">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem;">
        <div class="form-group">
          <label class="form-label" for="sOldPw">Password Lama</label>
          <input class="form-input" type="password" id="sOldPw" name="old_password"
                 placeholder="••••••••" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="sNewPw">Password Baru</label>
          <input class="form-input" type="password" id="sNewPw" name="new_password"
                 placeholder="Min. 6 karakter" required minlength="6">
        </div>
      </div>
      <button type="submit" name="change_password"
              class="btn btn-primary btn-sm"
              style="margin-top:.75rem;border-radius:var(--radius-sm);">
        Ubah Password
      </button>
    </form>
  </div>

  <!-- Addresses -->
  <div class="card-wf" style="margin-bottom:1.25rem;" id="alamat">
    <div class="card-wf-header">
      <h4 style="font-family:Georgia,serif;margin:0;">Alamat Tersimpan</h4>
    </div>

    <?php foreach ($addresses as $a): ?>
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:.85rem 1rem;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:.6rem;">
        <div>
          <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
            <strong style="font-size:.875rem;"><?= e($a['label']) ?></strong>
            <?php if ($a['is_default']): ?>
              <span class="status-badge status-delivered" style="font-size:.68rem;padding:.15rem .5rem;">Utama</span>
            <?php endif; ?>
          </div>
          <div style="font-size:.82rem;"><?= e($a['recipient_name']) ?> · <?= e($a['phone']) ?></div>
          <div style="font-size:.78rem;color:var(--text-muted);margin-top:.1rem;">
            <?= e($a['full_address']) ?>, <?= e($a['city']) ?>, <?= e($a['province']) ?> <?= e($a['postal_code']) ?>
          </div>
        </div>
        <form method="POST" onsubmit="return confirm('Hapus alamat ini?')">
          <input type="hidden" name="delete_address" value="<?= $a['id'] ?>">
          <button type="submit"
                  style="width:32px;height:32px;border:1px solid #f0c0c0;border-radius:50%;background:var(--white);color:#c0392b;display:grid;place-items:center;cursor:pointer;">
            <i class="bi bi-trash" style="font-size:.8rem;"></i>
          </button>
        </form>
      </div>
    <?php endforeach; ?>

    <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);">
      <h5 style="font-size:.9rem;font-weight:700;margin-bottom:1rem;">Tambah Alamat Baru</h5>
      <form method="POST">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
          <div class="form-group">
            <label class="form-label">Label <span style="font-weight:400;color:var(--text-muted);">(mis. Rumah)</span></label>
            <input type="text" name="label" class="form-input" placeholder="Rumah">
          </div>
          <div class="form-group">
            <label class="form-label">Nama Penerima</label>
            <input type="text" name="recipient_name" class="form-input" placeholder="Nama lengkap" required>
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-input" placeholder="08xxxxxxxxxx" required>
          </div>
        </div>

        <!-- Cascade Wilayah -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
          <div class="form-group">
            <label class="form-label">Provinsi</label>
            <select name="province" id="st_province" class="form-input" style="cursor:pointer;" required>
              <option value="">Memuat provinsi…</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Kota / Kabupaten</label>
            <select name="city" id="st_city" class="form-input" style="cursor:pointer;" disabled required>
              <option value="">-- Pilih Kota --</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Kecamatan</label>
            <select name="district" id="st_district" class="form-input" style="cursor:pointer;" disabled>
              <option value="">-- Pilih Kecamatan --</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Kelurahan / Desa</label>
            <select name="village" id="st_village" class="form-input" style="cursor:pointer;" disabled>
              <option value="">-- Pilih Kelurahan --</option>
            </select>
          </div>
        </div>

        <!-- Kode Pos otomatis -->
        <div class="form-group">
          <label class="form-label">
            Kode Pos
            <span id="st_postal_tag" style="display:none;font-weight:400;font-size:.72rem;
                  color:var(--pink-deep);background:var(--pink-soft);
                  padding:.15rem .5rem;border-radius:50px;margin-left:.4rem;">
              ✓ Otomatis
            </span>
          </label>
          <input type="text" name="postal_code" id="st_postal" class="form-input"
                 placeholder="Terisi otomatis saat memilih kelurahan">
        </div>

        <!-- Alamat Lengkap -->
        <div class="form-group">
          <label class="form-label">Alamat Lengkap</label>
          <textarea name="full_address" class="form-input" rows="2"
                    style="resize:vertical;" placeholder="Jl. nama jalan, nomor, RT/RW, dll." required></textarea>
        </div>

        <label style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;cursor:pointer;margin:.25rem 0 1rem;">
          <input type="checkbox" name="is_default" style="accent-color:var(--pink-deep);">
          Jadikan alamat utama
        </label>
        <button type="submit" name="add_address"
                class="btn btn-primary btn-sm"
                style="border-radius:var(--radius-sm);">
          <i class="bi bi-plus"></i> Tambah Alamat
        </button>
      </form>
    </div>
  </div>

  <!-- Preferences -->
  <div class="card-wf">    <h4 style="font-family:Georgia,serif;margin-bottom:1.25rem;">Notifikasi &amp; Privasi</h4>
    <form method="POST">
      <?php
      $prefs = [
        'notif_email'          => ['label'=>'Notifikasi via Email',          'key'=>'notif_email'],
        'notif_push'           => ['label'=>'Notifikasi Push/Aplikasi',       'key'=>'notif_push'],
        'privacy_public_profile'=> ['label'=>'Tampilkan profil saya pada ulasan publik','key'=>'privacy_public_profile'],
      ];
      foreach ($prefs as $name => $p): ?>
        <label style="display:flex;align-items:center;gap:.65rem;padding:.65rem 0;border-bottom:1px solid var(--border);cursor:pointer;font-size:.875rem;">
          <input type="checkbox" name="<?= $name ?>"
                 <?= ($user[$p['key']] ?? false) ? 'checked' : '' ?>
                 style="accent-color:var(--pink-deep);width:17px;height:17px;">
          <?= $p['label'] ?>
        </label>
      <?php endforeach; ?>
      <button type="submit" name="update_preferences"
              class="btn btn-primary btn-sm"
              style="margin-top:1rem;border-radius:var(--radius-sm);">
        Simpan Preferensi
      </button>
    </form>
  </div>

</div>

<script>
/* ── Cascade Wilayah — Halaman Pengaturan ── */
(function () {
  var PROXY = '<?= site_url('api/wilayah.php') ?>';

  var el = {
    prov : document.getElementById('st_province'),
    city : document.getElementById('st_city'),
    dist : document.getElementById('st_district'),
    vill : document.getElementById('st_village'),
    zip  : document.getElementById('st_postal'),
    tag  : document.getElementById('st_postal_tag'),
  };

  if (!el.prov) return;

  function fetchWilayah(endpoint) {
    return fetch(PROXY + '?type=wilayah&endpoint=' + encodeURIComponent(endpoint))
      .then(function(r){ return r.json(); })
      .then(function(j){ return Array.isArray(j.data) ? j.data : []; })
      .catch(function(){ return []; });
  }

  function fetchKodePos(name) {
    return fetch(PROXY + '?type=kodepos&q=' + encodeURIComponent(name))
      .then(function(r){ return r.json(); })
      .then(function(j){ return j.data || []; })
      .catch(function(){ return []; });
  }

  function fillSel(sel, items, placeholder) {
    sel.innerHTML = '<option value="">' + placeholder + '</option>';
    items.forEach(function(it) {
      var o = document.createElement('option');
      o.value = it.value; o.dataset.code = it.code; o.textContent = it.value;
      sel.appendChild(o);
    });
    sel.disabled = false;
  }

  function resetBelow(list) {
    var ph = ['-- Pilih Kota --','-- Pilih Kecamatan --','-- Pilih Kelurahan --'];
    list.forEach(function(s,i){ s.innerHTML='<option value="">'+(ph[i]||'--')+'</option>'; s.disabled=true; });
    el.zip.value=''; el.zip.removeAttribute('readonly'); el.zip.style.background='';
    el.zip.placeholder='Terisi otomatis saat memilih kelurahan';
    if (el.tag) el.tag.style.display='none';
  }

  /* load provinsi */
  el.prov.innerHTML='<option value="">Memuat provinsi…</option>'; el.prov.disabled=true;
  fetchWilayah('states').then(function(data){
    if (!data.length){ el.prov.innerHTML='<option value="">Gagal memuat — coba refresh</option>'; return; }
    fillSel(el.prov, data, '-- Pilih Provinsi --');
  });

  el.prov.addEventListener('change', function(){
    resetBelow([el.city, el.dist, el.vill]);
    var code = this.options[this.selectedIndex].dataset.code;
    if (!code) return;
    el.city.innerHTML='<option value="">Memuat kota…</option>';
    fetchWilayah('states/'+code+'/cities').then(function(d){ fillSel(el.city,d,'-- Pilih Kota --'); });
  });

  el.city.addEventListener('change', function(){
    resetBelow([el.dist, el.vill]);
    var code = this.options[this.selectedIndex].dataset.code;
    if (!code) return;
    el.dist.innerHTML='<option value="">Memuat kecamatan…</option>';
    fetchWilayah('cities/'+code+'/districts').then(function(d){ fillSel(el.dist,d,'-- Pilih Kecamatan --'); });
  });

  el.dist.addEventListener('change', function(){
    resetBelow([el.vill]);
    var code = this.options[this.selectedIndex].dataset.code;
    if (!code) return;
    el.vill.innerHTML='<option value="">Memuat kelurahan…</option>';
    fetchWilayah('districts/'+code+'/villages').then(function(d){ fillSel(el.vill,d,'-- Pilih Kelurahan --'); });
  });

  el.vill.addEventListener('change', function(){
    el.zip.value=''; el.zip.removeAttribute('readonly'); el.zip.style.background='';
    if (el.tag) el.tag.style.display='none';
    var name = this.value;
    if (!name) return;
    el.zip.placeholder='Mencari kode pos…';
    fetchKodePos(name).then(function(hits){
      if (hits.length && hits[0].code){
        el.zip.value=String(hits[0].code);
        el.zip.setAttribute('readonly','readonly');
        el.zip.style.background='#f0faf5';
        el.zip.placeholder='';
        if (el.tag) el.tag.style.display='inline';
      } else {
        el.zip.placeholder='Isi kode pos secara manual';
      }
    });
  });

  el.zip.addEventListener('click', function(){
    if (this.hasAttribute('readonly')){
      this.removeAttribute('readonly'); this.style.background='';
      if (el.tag) el.tag.style.display='none';
    }
  });
})();
</script>
