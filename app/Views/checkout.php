<div class="container" style="max-width:1100px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Checkout</h1>
    <p>Lengkapi informasi pengiriman dan konfirmasi pesananmu.</p>
  </div>

  <form method="POST" action="<?= site_url('proses_checkout.php') ?>">
    <div style="display:grid;grid-template-columns:1fr 360px;gap:2rem;align-items:start;">

      <!-- Left: Shipping & Note -->
      <div>
        <!-- Shipping address -->
        <div class="card-wf" style="margin-bottom:1rem;">
          <h4 style="font-family:Georgia,serif;margin-bottom:1.1rem;">Alamat Pengiriman</h4>

          <?php if (empty($addresses)): ?>
            <p style="font-size:.875rem;color:var(--text-muted);margin-bottom:1rem;">
              Kamu belum punya alamat tersimpan. Isi alamat baru di bawah.
            </p>
          <?php else: ?>
            <div class="form-group">
              <label class="form-label">Pilih alamat tersimpan</label>
              <select name="address_id" class="form-input" style="cursor:pointer;"
                      onchange="document.getElementById('newAddressForm').style.display = this.value==='new' ? 'block':'none';">
                <?php foreach ($addresses as $a): ?>
                  <option value="<?= $a['id'] ?>">
                    <?= e($a['label']) ?> — <?= e($a['recipient_name']) ?>, <?= e($a['full_address']) ?>
                  </option>
                <?php endforeach; ?>
                <option value="new">+ Gunakan alamat baru</option>
              </select>
            </div>
          <?php endif; ?>

          <div id="newAddressForm" style="<?= empty($addresses) ? '' : 'display:none;' ?>margin-top:.75rem;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
              <div class="form-group">
                <label class="form-label">Nama Penerima</label>
                <input type="text" name="recipient_name" class="form-input" placeholder="Nama lengkap">
              </div>
              <div class="form-group">
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="phone" class="form-input" placeholder="08xxxxxxxxxx">
              </div>
            </div>

            <!-- Cascade: Provinsi → Kota → Kecamatan → Kelurahan -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
              <div class="form-group">
                <label class="form-label">Provinsi</label>
                <select name="province" id="sel_province" class="form-input" style="cursor:pointer;">
                  <option value="">Memuat provinsi…</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Kota / Kabupaten</label>
                <select name="city" id="sel_city" class="form-input" style="cursor:pointer;" disabled>
                  <option value="">-- Pilih Kota --</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Kecamatan</label>
                <select name="district" id="sel_district" class="form-input" style="cursor:pointer;" disabled>
                  <option value="">-- Pilih Kecamatan --</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Kelurahan / Desa</label>
                <select name="village" id="sel_village" class="form-input" style="cursor:pointer;" disabled>
                  <option value="">-- Pilih Kelurahan --</option>
                </select>
              </div>
            </div>

            <!-- Kode Pos — terisi otomatis saat pilih kelurahan -->
            <div class="form-group">
              <label class="form-label">
                Kode Pos
                <span id="postal_auto_tag" style="display:none;font-weight:400;font-size:.72rem;
                      color:var(--pink-deep);background:var(--pink-soft);
                      padding:.15rem .5rem;border-radius:50px;margin-left:.4rem;">
                  ✓ Otomatis
                </span>
              </label>
              <input type="text" name="postal_code" id="inp_postal" class="form-input"
                     placeholder="Terisi otomatis saat memilih kelurahan">
            </div>

            <!-- Alamat Lengkap — di bawah dropdown -->
            <div class="form-group">
              <label class="form-label">Alamat Lengkap</label>
              <textarea name="full_address" class="form-input" rows="3"
                        style="resize:vertical;" placeholder="Nama jalan, nomor rumah, RT/RW, dll."></textarea>
            </div>
          </div>
        </div>

        <!-- Order note -->
        <div class="card-wf">
          <h4 style="font-family:Georgia,serif;margin-bottom:1rem;">Catatan Pesanan</h4>
          <div class="form-group" style="margin:0;">
            <textarea name="note" class="form-input" rows="3" style="resize:vertical;"
                      placeholder="Mis: Tambahkan kartu ucapan 'Selamat Ulang Tahun'"></textarea>
          </div>
        </div>
      </div>

      <!-- Right: Summary -->
      <div class="order-summary-box" style="position:sticky;top:90px;">
        <h4>Ringkasan Pesanan</h4>

        <?php foreach ($items as $it): ?>
          <div class="order-summary-row">
            <span style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              <?= e($it['name']) ?> ×<?= $it['quantity'] ?>
            </span>
            <span><?= rupiah($it['price'] * $it['quantity']) ?></span>
          </div>
        <?php endforeach; ?>

        <div style="height:1px;background:var(--border);margin:.75rem 0;"></div>

        <div class="order-summary-row">
          <span>Subtotal</span>
          <span id="co-subtotal"><?= rupiah($subtotal) ?></span>
        </div>
        <div class="order-summary-row">
          <span>Ongkos kirim</span>
          <span id="co-shipping">
            <?= $shipping_cost > 0 ? rupiah($shipping_cost) : '<span style="font-size:.8rem;color:var(--text-muted);">Pilih provinsi dulu</span>' ?>
          </span>
        </div>
        <!-- Info zona ongkir -->
        <div id="co-shipping-info" style="font-size:.75rem;color:var(--text-muted);text-align:right;margin-top:.2rem;display:none;"></div>
        <div class="order-summary-row total">
          <span>Total</span>
          <span id="co-total"><?= rupiah($total) ?></span>
        </div>

        <!-- Hidden input ongkir — dikirim ke server -->
        <input type="hidden" name="shipping_cost" id="inp-shipping-cost" value="<?= (int)$shipping_cost ?>">

        <button type="submit"
                class="btn btn-primary"
                style="width:100%;justify-content:center;margin-top:1rem;border-radius:var(--radius-sm);">
          <i class="bi bi-check2-circle"></i> Konfirmasi Pesanan
        </button>
      </div>

    </div>
  </form>

</div>

<script>
(function () {
  /* ── URL proxy lokal ── */
  const PROXY = '<?= site_url('api/wilayah.php') ?>';

  const el = {
    prov : document.getElementById('sel_province'),
    city : document.getElementById('sel_city'),
    dist : document.getElementById('sel_district'),
    vill : document.getElementById('sel_village'),
    zip  : document.getElementById('inp_postal'),
    tag  : document.getElementById('postal_auto_tag'),
  };

  if (!el.prov) return;

  /* ── fetch wilayah ── */
  async function fetchWilayah(endpoint) {
    const url = PROXY + '?type=wilayah&endpoint=' + encodeURIComponent(endpoint);
    try {
      const r = await fetch(url);
      const j = await r.json();
      return Array.isArray(j.data) ? j.data : [];
    } catch (e) {
      console.error('[Wilayah fetch error]', endpoint, e);
      return [];
    }
  }

  /* ── fetch kode pos ── */
  async function fetchKodePos(villageName) {
    const url = PROXY + '?type=kodepos&q=' + encodeURIComponent(villageName);
    try {
      const r = await fetch(url);
      const j = await r.json();
      return j.data || [];
    } catch (e) {
      return [];
    }
  }

  /* ── isi dropdown ── */
  function fillSel(sel, items, placeholder) {
    sel.innerHTML = '<option value="">' + placeholder + '</option>';
    items.forEach(function(it) {
      var o = document.createElement('option');
      o.value          = it.value;
      o.dataset.code   = it.code;
      o.textContent    = it.value;
      sel.appendChild(o);
    });
    sel.disabled = false;
  }

  /* ── reset dropdown ke bawah ── */
  function resetBelow(list) {
    var ph = ['-- Pilih Kota --','-- Pilih Kecamatan --','-- Pilih Kelurahan --'];
    list.forEach(function(sel, i) {
      sel.innerHTML = '<option value="">' + (ph[i] || '--') + '</option>';
      sel.disabled  = true;
    });
    el.zip.value = '';
    el.zip.removeAttribute('readonly');
    el.zip.style.background = '';
    el.zip.placeholder = 'Terisi otomatis saat memilih kelurahan';
    if (el.tag) el.tag.style.display = 'none';
  }

  /* ── 1. Load provinsi ── */
  el.prov.innerHTML = '<option value="">Memuat provinsi…</option>';
  el.prov.disabled  = true;
  fetchWilayah('states').then(function(data) {
    if (!data.length) {
      el.prov.innerHTML = '<option value="">Gagal memuat — coba refresh</option>';
      return;
    }
    fillSel(el.prov, data, '-- Pilih Provinsi --');
  });

  /* ── 2. Provinsi → Kota ── */
  el.prov.addEventListener('change', function() {
    resetBelow([el.city, el.dist, el.vill]);
    var code = this.options[this.selectedIndex].dataset.code;
    if (!code) return;
    el.city.innerHTML = '<option value="">Memuat kota…</option>';
    fetchWilayah('states/' + code + '/cities').then(function(data) {
      fillSel(el.city, data, '-- Pilih Kota --');
    });
  });

  /* ── 3. Kota → Kecamatan ── */
  el.city.addEventListener('change', function() {
    resetBelow([el.dist, el.vill]);
    var code = this.options[this.selectedIndex].dataset.code;
    if (!code) return;
    el.dist.innerHTML = '<option value="">Memuat kecamatan…</option>';
    fetchWilayah('cities/' + code + '/districts').then(function(data) {
      fillSel(el.dist, data, '-- Pilih Kecamatan --');
    });
  });

  /* ── 4. Kecamatan → Kelurahan ── */
  el.dist.addEventListener('change', function() {
    resetBelow([el.vill]);
    el.zip.value = '';
    if (el.tag) el.tag.style.display = 'none';
    var code = this.options[this.selectedIndex].dataset.code;
    if (!code) return;
    el.vill.innerHTML = '<option value="">Memuat kelurahan…</option>';
    fetchWilayah('districts/' + code + '/villages').then(function(data) {
      fillSel(el.vill, data, '-- Pilih Kelurahan --');
    });
  });

  /* ── 5. Kelurahan → kode pos otomatis ── */
  el.vill.addEventListener('change', function() {
    el.zip.value = '';
    el.zip.removeAttribute('readonly');
    el.zip.style.background = '';
    if (el.tag) el.tag.style.display = 'none';

    var name = this.value;
    if (!name) return;

    el.zip.placeholder = 'Mencari kode pos…';
    fetchKodePos(name).then(function(hits) {
      if (hits.length && hits[0].code) {
        el.zip.value = String(hits[0].code);
        el.zip.setAttribute('readonly', 'readonly');
        el.zip.style.background = '#f0faf5';
        el.zip.placeholder = '';
        if (el.tag) el.tag.style.display = 'inline';
      } else {
        el.zip.placeholder = 'Isi kode pos secara manual';
      }
    });
  });

  /* ── klik kode pos → bisa edit manual ── */
  el.zip.addEventListener('click', function() {
    if (this.hasAttribute('readonly')) {
      this.removeAttribute('readonly');
      this.style.background = '';
      if (el.tag) el.tag.style.display = 'none';
    }
  });

  /* ── validasi sebelum submit ── */
  var form = document.querySelector('form[action*="proses_checkout"]');
  if (form) {
    form.addEventListener('submit', function(e) {
      var newAddr = document.getElementById('newAddressForm');
      if (!newAddr || newAddr.style.display === 'none') return;
      var checks = [
        [el.prov, 'Pilih Provinsi terlebih dahulu.'],
        [el.city, 'Pilih Kota / Kabupaten.'],
        [el.dist, 'Pilih Kecamatan.'],
        [el.vill, 'Pilih Kelurahan / Desa.'],
      ];
      for (var i = 0; i < checks.length; i++) {
        if (!checks[i][0].value) {
          e.preventDefault();
          checks[i][0].scrollIntoView({behavior:'smooth', block:'center'});
          checks[i][0].focus();
          alert(checks[i][1]);
          return;
        }
      }
    });
  }

})();
</script>
