<div class="container" style="max-width:1100px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Checkout</h1>
    <p>Lengkapi informasi pengiriman dan konfirmasi pesananmu.</p>
  </div>

  <form method="POST" action="<?= site_url('proses_checkout.php') ?>">
    <div class="checkout-layout">

      <!-- Left: Shipping & Note -->
      <div class="checkout-main">
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
              <select name="address_id" id="sel_saved_address" class="form-input" style="cursor:pointer;"
                      onchange="onSavedAddressChange(this)">
                <?php foreach ($addresses as $a): ?>
                  <option value="<?= $a['id'] ?>"
                          data-province="<?= e($a['province'] ?? '') ?>"
                          data-city="<?= e($a['city'] ?? '') ?>">
                    <?= e($a['label']) ?> — <?= e($a['recipient_name']) ?>, <?= e($a['full_address']) ?>
                  </option>
                <?php endforeach; ?>
                <option value="new" data-province="" data-city="">+ Gunakan alamat baru</option>
              </select>
            </div>
          <?php endif; ?>

          <div id="newAddressForm" style="<?= empty($addresses) ? '' : 'display:none;' ?>margin-top:.75rem;">
            <div class="checkout-two-col">
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
            <div class="checkout-two-col">
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
        <div class="card-wf" style="margin-bottom:6rem;">
          <h4 style="font-family:Georgia,serif;margin-bottom:1rem;">Catatan Pesanan</h4>
          <div class="form-group" style="margin:0;">
            <textarea name="note" class="form-input" rows="3" style="resize:vertical;"
                      placeholder="Mis: Tambahkan kartu ucapan 'Selamat Ulang Tahun'"></textarea>
          </div>
        </div>
      </div>

      <!-- Right: Summary (desktop only) -->
      <div class="order-summary-box checkout-summary-sticky">
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

        <!-- Baris diskon voucher — tersembunyi sampai voucher diterapkan -->
        <div class="order-summary-row" id="co-discount-row" style="display:none;color:#1a6b3a;">
          <span style="display:flex;align-items:center;gap:.35rem;">
            <i class="bi bi-ticket-perforated-fill"></i>
            <span id="co-discount-label">Voucher</span>
          </span>
          <span id="co-discount-amount" style="font-weight:700;">- Rp 0</span>
        </div>

        <div class="order-summary-row total">
          <span>Total</span>
          <span id="co-total"><?= rupiah($total) ?></span>
        </div>

        <!-- Input kode voucher -->
        <div style="margin-top:1rem;border-top:1px solid var(--border);padding-top:1rem;">
          <label class="form-label" style="font-size:.8rem;margin-bottom:.4rem;">
            <i class="bi bi-ticket-perforated" style="color:var(--pink-deep);"></i> Kode Voucher
          </label>
          <div style="display:flex;gap:.5rem;">
            <input type="text" id="inp-voucher-code" placeholder="Masukkan kode"
                   class="form-input" style="text-transform:uppercase;letter-spacing:.05em;font-size:.88rem;"
                   oninput="this.value=this.value.toUpperCase()">
            <button type="button" onclick="applyVoucher()"
                    id="btn-apply-voucher"
                    class="btn btn-outline"
                    style="white-space:nowrap;padding:.55rem .9rem;font-size:.82rem;border-radius:var(--radius-sm);flex-shrink:0;">
              Pakai
            </button>
          </div>
          <div id="voucher-feedback" style="margin-top:.45rem;font-size:.78rem;display:none;"></div>
          <!-- Badge voucher aktif -->
          <div id="voucher-active-badge"
               style="display:none;margin-top:.5rem;background:#eafaf0;border:1px solid #b2e0c4;
                      border-radius:8px;padding:.45rem .75rem;align-items:center;
                      justify-content:space-between;gap:.5rem;">
            <div style="display:flex;align-items:center;gap:.4rem;font-size:.8rem;font-weight:600;color:#1a6b3a;">
              <i class="bi bi-check-circle-fill"></i>
              <span id="voucher-badge-label">Voucher diterapkan</span>
            </div>
            <button type="button" onclick="removeVoucher()"
                    style="background:none;border:none;cursor:pointer;color:#888;font-size:.85rem;padding:0;line-height:1;"
                    title="Hapus voucher">
              <i class="bi bi-x"></i>
            </button>
          </div>
        </div>

        <!-- Hidden inputs untuk server -->
        <input type="hidden" name="shipping_cost"   id="inp-shipping-cost"   value="<?= (int)$shipping_cost ?>">
        <input type="hidden" name="voucher_id"      id="inp-voucher-id"      value="">
        <input type="hidden" name="voucher_code"    id="inp-voucher-code-val" value="">
        <input type="hidden" name="discount_amount" id="inp-discount-amount" value="0">

        <button type="submit"
                class="btn btn-primary"
                style="width:100%;justify-content:center;margin-top:1rem;border-radius:var(--radius-sm);">
          <i class="bi bi-check2-circle"></i> Konfirmasi Pesanan
        </button>
      </div>

    </div>

    <!-- ── Mobile sticky bar ── -->
    <div class="checkout-mobile-bar">
      <div class="checkout-mobile-bar-inner">
        <div class="checkout-mobile-totals">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.2rem;">
            <span style="font-size:.75rem;color:var(--text-muted);">Subtotal</span>
            <span style="font-size:.82rem;font-weight:600;" id="mob-co-subtotal"><?= rupiah($subtotal) ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.2rem;">
            <span style="font-size:.75rem;color:var(--text-muted);">Ongkir</span>
            <span style="font-size:.82rem;" id="mob-co-shipping">
              <?= $shipping_cost > 0 ? rupiah($shipping_cost) : '<span style="color:var(--text-muted);font-size:.75rem;">Pilih provinsi</span>' ?>
            </span>
          </div>
          <div id="mob-discount-row" style="display:none;justify-content:space-between;align-items:center;margin-bottom:.2rem;">
            <span style="font-size:.75rem;color:#1a6b3a;"><i class="bi bi-ticket-perforated-fill"></i> Voucher</span>
            <span style="font-size:.82rem;font-weight:700;color:#1a6b3a;" id="mob-co-discount">- Rp 0</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--border);padding-top:.3rem;">
            <span style="font-size:.8rem;font-weight:700;">Total</span>
            <span style="font-size:1rem;font-weight:800;color:var(--pink-deep);" id="mob-co-total"><?= rupiah($total) ?></span>
          </div>
        </div>
        <button type="submit"
                class="btn btn-primary"
                style="border-radius:var(--radius-sm);padding:.7rem 1.25rem;font-size:.9rem;white-space:nowrap;flex-shrink:0;">
          <i class="bi bi-check2-circle"></i> Pesan
        </button>
      </div>
    </div>

  </form>

</div>

<script>
/* ══════════════════════════════════════════════════════════════
   CHECKOUT — Ongkir + Voucher (satu blok terpadu)
   ══════════════════════════════════════════════════════════════ */
(function () {

  /* ── Helpers ── */
  function toRupiah(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
  }
  function el(id) { return document.getElementById(id); }

  /* ── State global ── */
  var RAW_SUBTOTAL    = <?= (int)$subtotal ?>;
  var _shipping       = 0;   // ongkir aktif (dari zona)
  var _activeVoucher  = null; // hasil JSON dari API voucher

  /* ─────────────────────────────────────────
     1. WILAYAH CASCADE
  ───────────────────────────────────────── */
  var PROXY = '<?= site_url('api/wilayah.php') ?>';

  var selProv = el('sel_province');
  var selCity = el('sel_city');
  var selDist = el('sel_district');
  var selVill = el('sel_village');
  var inpZip  = el('inp_postal');
  var tagZip  = el('postal_auto_tag');

  async function fetchWilayah(endpoint) {
    try {
      var r = await fetch(PROXY + '?type=wilayah&endpoint=' + encodeURIComponent(endpoint));
      var j = await r.json();
      return Array.isArray(j.data) ? j.data : [];
    } catch(e) { return []; }
  }

  async function fetchKodePos(name) {
    try {
      var r = await fetch(PROXY + '?type=kodepos&q=' + encodeURIComponent(name));
      var j = await r.json();
      return j.data || [];
    } catch(e) { return []; }
  }

  function fillSel(sel, items, ph) {
    sel.innerHTML = '<option value="">' + ph + '</option>';
    items.forEach(function(it) {
      var o = document.createElement('option');
      o.value = it.value; o.dataset.code = it.code; o.textContent = it.value;
      sel.appendChild(o);
    });
    sel.disabled = false;
  }

  function resetBelow(list) {
    var ph = ['-- Pilih Kota --','-- Pilih Kecamatan --','-- Pilih Kelurahan --'];
    list.forEach(function(s, i) {
      s.innerHTML = '<option value="">' + (ph[i]||'--') + '</option>';
      s.disabled = true;
    });
    if (inpZip) { inpZip.value = ''; inpZip.removeAttribute('readonly'); inpZip.style.background = ''; inpZip.placeholder = 'Terisi otomatis saat memilih kelurahan'; }
    if (tagZip) tagZip.style.display = 'none';
  }

  if (selProv) {
    selProv.innerHTML = '<option value="">Memuat provinsi…</option>';
    selProv.disabled  = true;
    fetchWilayah('states').then(function(data) {
      if (!data.length) { selProv.innerHTML = '<option value="">Gagal memuat</option>'; return; }
      fillSel(selProv, data, '-- Pilih Provinsi --');
    });

    selProv.addEventListener('change', function() {
      resetBelow([selCity, selDist, selVill]);
      var code = this.options[this.selectedIndex].dataset.code;
      if (!code) { onLocationChange(); return; }
      selCity.innerHTML = '<option value="">Memuat kota…</option>';
      fetchWilayah('states/' + code + '/cities').then(function(d) { fillSel(selCity, d, '-- Pilih Kota --'); });
      onLocationChange();
    });

    selCity.addEventListener('change', function() {
      resetBelow([selDist, selVill]);
      var code = this.options[this.selectedIndex].dataset.code;
      if (!code) { onLocationChange(); return; }
      selDist.innerHTML = '<option value="">Memuat kecamatan…</option>';
      fetchWilayah('cities/' + code + '/districts').then(function(d) { fillSel(selDist, d, '-- Pilih Kecamatan --'); });
      onLocationChange();
    });

    selDist.addEventListener('change', function() {
      resetBelow([selVill]);
      if (inpZip) { inpZip.value = ''; } if (tagZip) tagZip.style.display = 'none';
      var code = this.options[this.selectedIndex].dataset.code;
      if (!code) return;
      selVill.innerHTML = '<option value="">Memuat kelurahan…</option>';
      fetchWilayah('districts/' + code + '/villages').then(function(d) { fillSel(selVill, d, '-- Pilih Kelurahan --'); });
    });

    selVill.addEventListener('change', function() {
      if (inpZip) { inpZip.value = ''; inpZip.removeAttribute('readonly'); inpZip.style.background = ''; }
      if (tagZip) tagZip.style.display = 'none';
      var name = this.value; if (!name) return;
      if (inpZip) inpZip.placeholder = 'Mencari kode pos…';
      fetchKodePos(name).then(function(hits) {
        if (hits.length && hits[0].code) {
          if (inpZip) { inpZip.value = String(hits[0].code); inpZip.setAttribute('readonly','readonly'); inpZip.style.background = '#f0faf5'; inpZip.placeholder = ''; }
          if (tagZip) tagZip.style.display = 'inline';
        } else {
          if (inpZip) inpZip.placeholder = 'Isi kode pos secara manual';
        }
      });
    });

    if (inpZip) {
      inpZip.addEventListener('click', function() {
        if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.style.background = ''; if (tagZip) tagZip.style.display = 'none'; }
      });
    }
  }

  /* Validasi submit */
  var form = document.querySelector('form[action*="proses_checkout"]');
  if (form) {
    form.addEventListener('submit', function(e) {
      var newAddr = el('newAddressForm');
      if (!newAddr || newAddr.style.display === 'none') return;
      var checks = [
        [selProv, 'Pilih Provinsi terlebih dahulu.'],
        [selCity, 'Pilih Kota / Kabupaten.'],
        [selDist, 'Pilih Kecamatan.'],
        [selVill, 'Pilih Kelurahan / Desa.'],
      ];
      for (var i = 0; i < checks.length; i++) {
        if (checks[i][0] && !checks[i][0].value) {
          e.preventDefault();
          checks[i][0].scrollIntoView({behavior:'smooth', block:'center'});
          checks[i][0].focus();
          alert(checks[i][1]);
          return;
        }
      }
    });
  }

  /* ─────────────────────────────────────────
     2. ZONA ONGKIR
  ───────────────────────────────────────── */
  var ZONA = [
    { tarif:15000, label:'Reguler · Jabodetabek',           match:['dki jakarta','jawa barat','banten'] },
    { tarif:20000, label:'Reguler · Pulau Jawa',             match:['jawa','yogyakarta','di yogyakarta'] },
    { tarif:25000, label:'Reguler · Sumatera & Bali',        match:['sumatera','sumatra','aceh','riau','jambi','bengkulu','lampung','bangka','belitung','kepulauan riau','bali','nusa tenggara barat','ntb'] },
    { tarif:35000, label:'Reguler · Kalimantan & Sulawesi',  match:['kalimantan','sulawesi','nusa tenggara timur','ntt','gorontalo'] },
    { tarif:45000, label:'Reguler · Maluku & Papua',         match:['maluku','papua'] },
  ];
  var JABODETABEK_CITY = ['jakarta','bogor','depok','tangerang','bekasi'];

  function getZona(prov, city) {
    var p = (prov||'').toLowerCase(), c = (city||'').toLowerCase();
    if (p.indexOf('dki jakarta') !== -1) return ZONA[0];
    for (var j = 0; j < JABODETABEK_CITY.length; j++) { if (c.indexOf(JABODETABEK_CITY[j]) !== -1) return ZONA[0]; }
    for (var i = 0; i < ZONA.length; i++) { for (var k = 0; k < ZONA[i].match.length; k++) { if (p.indexOf(ZONA[i].match[k]) !== -1) return ZONA[i]; } }
    return null;
  }

  function onLocationChange() {
    var prov = selProv ? selProv.value : '';
    var city = selCity ? selCity.value : '';
    var zona = getZona(prov, city);
    applyZona(zona);
  }

  function applyZona(zona) {
    var shippingEl = el('co-shipping');
    var infoEl     = el('co-shipping-info');
    var inpShip    = el('inp-shipping-cost');
    var mobShip    = el('mob-co-shipping');

    if (!zona) {
      _shipping = 0;
      if (shippingEl) shippingEl.innerHTML = '<span style="font-size:.8rem;color:var(--text-muted);">Pilih provinsi dulu</span>';
      if (mobShip)    mobShip.innerHTML    = '<span style="color:var(--text-muted);font-size:.75rem;">Pilih provinsi</span>';
      if (infoEl)     { infoEl.style.display = 'none'; infoEl.textContent = ''; }
      if (inpShip)    inpShip.value = 0;
      recalcTotal();
      return;
    }

    _shipping = zona.tarif;
    if (shippingEl) shippingEl.textContent = toRupiah(zona.tarif);
    if (mobShip)    mobShip.textContent    = toRupiah(zona.tarif);
    if (infoEl)     { infoEl.textContent = zona.label; infoEl.style.display = 'block'; }
    if (inpShip)    inpShip.value = zona.tarif;
    recalcTotal();
  }

  /* Alamat tersimpan */
  window.onSavedAddressChange = function(sel) {
    var newForm = el('newAddressForm');
    if (newForm) newForm.style.display = sel.value === 'new' ? 'block' : 'none';
    if (sel.value === 'new') { onLocationChange(); return; }
    var opt  = sel.options[sel.selectedIndex];
    var zona = getZona(opt.dataset.province || '', opt.dataset.city || '');
    applyZona(zona);
  };

  /* Init saat load */
  (function() {
    var savedSel = el('sel_saved_address');
    if (savedSel && savedSel.value && savedSel.value !== 'new') {
      window.onSavedAddressChange(savedSel);
    } else {
      onLocationChange();
    }
  })();

  /* ─────────────────────────────────────────
     3. KALKULASI TOTAL (ongkir + voucher)
  ───────────────────────────────────────── */
  function recalcTotal() {
    var totalEl    = el('co-total');
    var mobTotalEl = el('mob-co-total');
    var discRow    = el('co-discount-row');
    var discAmt    = el('co-discount-amount');
    var discLbl    = el('co-discount-label');
    var mobDiscRow = el('mob-discount-row');
    var mobDiscEl  = el('mob-co-discount');
    var inpDisc    = el('inp-discount-amount');
    var inpShip    = el('inp-shipping-cost');
    var shippingEl = el('co-shipping');
    var mobShip    = el('mob-co-shipping');

    var subtotal = RAW_SUBTOTAL;
    var shipping = _shipping;
    var discount = 0;
    var finalShipping = shipping;

    if (_activeVoucher) {
      discount      = _activeVoucher.discount_amount;
      finalShipping = _activeVoucher.shipping_cost;

      // Update baris ongkir jika gratis ongkir
      if (_activeVoucher.type === 'free_shipping') {
        if (shippingEl) shippingEl.innerHTML =
          '<span style="text-decoration:line-through;color:#aaa;font-size:.8rem;">' + toRupiah(shipping) + '</span> ' +
          '<span style="color:#1a6b3a;font-weight:700;font-size:.8rem;">Gratis</span>';
        if (mobShip) mobShip.innerHTML = '<span style="color:#1a6b3a;font-weight:700;">Gratis</span>';
        if (inpShip) inpShip.value = 0;
      }

      // Tampilkan baris diskon
      if (discRow) discRow.style.display = 'flex';
      if (mobDiscRow) mobDiscRow.style.display = 'flex';
      if (discAmt) discAmt.textContent = '- ' + toRupiah(discount);
      if (mobDiscEl) mobDiscEl.textContent = '- ' + toRupiah(discount);
      if (discLbl) discLbl.textContent = _activeVoucher.voucher_code + ' (' + _activeVoucher.type_label + ')';
      if (inpDisc) inpDisc.value = discount;
    } else {
      // Tidak ada voucher
      if (discRow)    discRow.style.display    = 'none';
      if (mobDiscRow) mobDiscRow.style.display = 'none';
      if (inpDisc)    inpDisc.value = 0;
    }

    var total = Math.max(0, subtotal + finalShipping - discount);
    if (totalEl)    totalEl.textContent    = toRupiah(total);
    if (mobTotalEl) mobTotalEl.textContent = toRupiah(total);
  }

  /* ─────────────────────────────────────────
     4. VOUCHER
  ───────────────────────────────────────── */
  var VOUCHER_API = '<?= site_url('api/voucher.php') ?>';

  function showFeedback(msg, isOk) {
    var fb = el('voucher-feedback');
    if (!fb) return;
    fb.style.display = 'block';
    fb.style.color   = isOk ? '#1a6b3a' : '#c0392b';
    fb.innerHTML = (isOk
      ? '<i class="bi bi-check-circle-fill"></i> '
      : '<i class="bi bi-x-circle-fill"></i> ') + msg;
  }

  function clearFeedback() {
    var fb = el('voucher-feedback');
    if (fb) { fb.style.display = 'none'; fb.textContent = ''; }
  }

  window.applyVoucher = function() {
    var codeEl = el('inp-voucher-code');
    var code   = codeEl ? codeEl.value.trim() : '';
    if (!code) { showFeedback('Masukkan kode voucher terlebih dahulu.', false); return; }

    var btn = el('btn-apply-voucher');
    if (btn) { btn.disabled = true; btn.textContent = 'Memeriksa…'; }

    var fd = new FormData();
    fd.append('code',     code);
    fd.append('subtotal', RAW_SUBTOTAL);
    fd.append('shipping', _shipping);

    fetch(VOUCHER_API, { method:'POST', body:fd })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (btn) { btn.disabled = false; btn.textContent = 'Pakai'; }

        if (!res.ok) {
          showFeedback(res.message, false);
          return;
        }

        // Simpan state
        _activeVoucher = res;

        // Update hidden inputs
        var inpId   = el('inp-voucher-id');
        var inpCode = el('inp-voucher-code-val');
        if (inpId)   inpId.value   = res.voucher_id;
        if (inpCode) inpCode.value = res.voucher_code;

        // Sembunyikan input & tombol, tampilkan badge
        if (codeEl) codeEl.style.display = 'none';
        if (btn)    btn.style.display    = 'none';

        var badge = el('voucher-active-badge');
        var bdLbl = el('voucher-badge-label');
        if (badge) badge.style.display = 'flex';
        if (bdLbl) bdLbl.textContent   = res.voucher_code + ' · ' + res.type_label;

        clearFeedback();
        showFeedback(res.message, true);

        recalcTotal();
      })
      .catch(function() {
        if (btn) { btn.disabled = false; btn.textContent = 'Pakai'; }
        showFeedback('Terjadi kesalahan jaringan. Coba lagi.', false);
      });
  };

  window.removeVoucher = function() {
    _activeVoucher = null;

    // Reset hidden inputs
    var inpId   = el('inp-voucher-id');
    var inpCode = el('inp-voucher-code-val');
    var inpDisc = el('inp-discount-amount');
    var inpShip = el('inp-shipping-cost');
    if (inpId)   inpId.value   = '';
    if (inpCode) inpCode.value = '';
    if (inpDisc) inpDisc.value = 0;
    if (inpShip) inpShip.value = _shipping;

    // Restore tampilan ongkir (jika sebelumnya gratis ongkir)
    var shippingEl = el('co-shipping');
    var mobShip    = el('mob-co-shipping');
    if (shippingEl) shippingEl.textContent = _shipping ? toRupiah(_shipping) : 'Pilih provinsi dulu';
    if (mobShip)    mobShip.textContent    = _shipping ? toRupiah(_shipping) : 'Pilih provinsi';

    // Sembunyikan badge, tampilkan input & tombol kembali
    var badge  = el('voucher-active-badge');
    var codeEl = el('inp-voucher-code');
    var btn    = el('btn-apply-voucher');
    if (badge)  badge.style.display  = 'none';
    if (codeEl) { codeEl.style.display = ''; codeEl.value = ''; }
    if (btn)    btn.style.display    = '';

    clearFeedback();
    recalcTotal();
  };

  // Enter key di input voucher
  var codeInput = el('inp-voucher-code');
  if (codeInput) {
    codeInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') { e.preventDefault(); window.applyVoucher(); }
    });
  }

})();
</script>
