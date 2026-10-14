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

/* ══════════════════════════════════════════
   ONGKIR — flat rate per zona provinsi
   ══════════════════════════════════════════ */
(function () {
  // Zona ongkir berdasarkan nama provinsi (lowercase, partial match)
  var ZONA = [
    {
      nama  : 'Jabodetabek',
      tarif : 15000,
      label : 'Reguler · Jabodetabek',
      match : ['dki jakarta','jawa barat','banten']   // juga ditangani kota di bawah
    },
    {
      nama  : 'Jawa',
      tarif : 20000,
      label : 'Reguler · Pulau Jawa',
      match : ['jawa','yogyakarta','di yogyakarta']
    },
    {
      nama  : 'Sumatera / Bali / NTB',
      tarif : 25000,
      label : 'Reguler · Sumatera & Bali',
      match : ['sumatera','sumatra','aceh','riau','jambi','bengkulu','lampung',
               'bangka','belitung','kepulauan riau','bali','nusa tenggara barat','ntb']
    },
    {
      nama  : 'Kalimantan / Sulawesi / NTT',
      tarif : 35000,
      label : 'Reguler · Kalimantan & Sulawesi',
      match : ['kalimantan','sulawesi','nusa tenggara timur','ntt','gorontalo']
    },
    {
      nama  : 'Maluku / Papua',
      tarif : 45000,
      label : 'Reguler · Maluku & Papua',
      match : ['maluku','papua']
    },
  ];

  // Kota-kota Jabodetabek untuk deteksi lebih presisi
  var JABODETABEK_CITY = ['jakarta','bogor','depok','tangerang','bekasi'];

  function getZona(provName, cityName) {
    var p = (provName  || '').toLowerCase();
    var c = (cityName  || '').toLowerCase();

    // Cek Jabodetabek dulu (lebih spesifik dari "Jawa Barat" umum)
    if (p.indexOf('dki jakarta') !== -1) return ZONA[0];
    for (var j = 0; j < JABODETABEK_CITY.length; j++) {
      if (c.indexOf(JABODETABEK_CITY[j]) !== -1) return ZONA[0];
    }

    for (var i = 0; i < ZONA.length; i++) {
      for (var k = 0; k < ZONA[i].match.length; k++) {
        if (p.indexOf(ZONA[i].match[k]) !== -1) return ZONA[i];
      }
    }
    return null; // belum bisa ditentukan
  }

  function toRupiah(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
  }

  function updateSummary(zona) {
    var shippingEl  = document.getElementById('co-shipping');
    var infoEl      = document.getElementById('co-shipping-info');
    var totalEl     = document.getElementById('co-total');
    var subtotalEl  = document.getElementById('co-subtotal');
    var inpShipping = document.getElementById('inp-shipping-cost');
    // Mobile bar
    var mobShipping = document.getElementById('mob-co-shipping');
    var mobTotal    = document.getElementById('mob-co-total');

    if (!shippingEl) return;

    var subtotal = parseInt((subtotalEl ? subtotalEl.dataset.raw : 0)) || <?= (int)$subtotal ?>;

    if (!zona) {
      shippingEl.innerHTML = '<span style="font-size:.8rem;color:var(--text-muted);">Pilih provinsi dulu</span>';
      if (mobShipping) mobShipping.innerHTML = '<span style="color:var(--text-muted);font-size:.75rem;">Pilih provinsi</span>';
      if (infoEl)      { infoEl.style.display = 'none'; infoEl.textContent = ''; }
      if (totalEl)     totalEl.textContent = toRupiah(subtotal);
      if (mobTotal)    mobTotal.textContent = toRupiah(subtotal);
      if (inpShipping) inpShipping.value = 0;
      // Reset voucher jika ada (ongkir belum diketahui)
      window._currentShipping = 0;
      recalcWithVoucher(subtotal, 0);
      return;
    }

    shippingEl.textContent = toRupiah(zona.tarif);
    if (mobShipping) mobShipping.textContent = toRupiah(zona.tarif);
    if (infoEl) {
      infoEl.textContent = zona.label;
      infoEl.style.display = 'block';
    }
    if (inpShipping) inpShipping.value = zona.tarif;
    // Simpan ongkir terkini supaya voucher bisa pakai nilai ini
    window._currentShipping = zona.tarif;
    recalcWithVoucher(subtotal, zona.tarif);
  }

  // Simpan subtotal raw di element biar mudah dibaca JS
  var subtotalEl = document.getElementById('co-subtotal');
  if (subtotalEl) subtotalEl.dataset.raw = <?= (int)$subtotal ?>;

  // Listen perubahan provinsi & kota (form alamat baru)
  var selProv = document.getElementById('sel_province');
  var selCity = document.getElementById('sel_city');

  function onLocationChange() {
    var prov = selProv ? selProv.value : '';
    var city = selCity ? selCity.value : '';
    var zona = getZona(prov, city);
    updateSummary(zona);
  }

  if (selProv) selProv.addEventListener('change', onLocationChange);
  if (selCity) selCity.addEventListener('change', onLocationChange);

  // ── Alamat tersimpan: baca province & city dari data-* lalu hitung ongkir ──
  window.onSavedAddressChange = function(sel) {
    // Toggle form alamat baru
    var newForm = document.getElementById('newAddressForm');
    if (newForm) newForm.style.display = sel.value === 'new' ? 'block' : 'none';

    if (sel.value === 'new') {
      // Pindah ke form baru — ongkir dikontrol oleh sel_province / sel_city
      onLocationChange();
      return;
    }

    var opt  = sel.options[sel.selectedIndex];
    var prov = opt.dataset.province || '';
    var city = opt.dataset.city     || '';
    var zona = getZona(prov, city);
    updateSummary(zona);
  };

  // Inisialisasi awal — hitung ongkir dari alamat yang sudah terpilih
  (function initShipping() {
    var savedSel = document.getElementById('sel_saved_address');
    if (savedSel && savedSel.value && savedSel.value !== 'new') {
      // Ada alamat tersimpan terpilih
      window.onSavedAddressChange(savedSel);
    } else {
      // Tidak ada alamat tersimpan — coba dari form baru
      onLocationChange();
    }
  })();

})();

/* ══════════════════════════════════════════
   VOUCHER — AJAX validasi & kalkulasi
   ══════════════════════════════════════════ */
(function () {
  var VOUCHER_API = '<?= site_url('api/voucher.php') ?>';
  var RAW_SUBTOTAL = <?= (int)$subtotal ?>;

  // State voucher aktif
  window._activeVoucher = null;
  window._currentShipping = 0;

  /* Hitung ulang total dengan mempertimbangkan voucher aktif */
  window.recalcWithVoucher = function(subtotal, shipping) {
    var totalEl    = document.getElementById('co-total');
    var mobTotalEl = document.getElementById('mob-co-total');
    var discRow    = document.getElementById('co-discount-row');
    var discAmt    = document.getElementById('co-discount-amount');
    var mobDiscRow = document.getElementById('mob-discount-row');
    var mobDiscEl  = document.getElementById('mob-co-discount');
    var inpShip    = document.getElementById('inp-shipping-cost');
    var inpDisc    = document.getElementById('inp-discount-amount');
    var inpShipHidden = document.getElementById('inp-shipping-cost');
    var shippingEl = document.getElementById('co-shipping');
    var mobShip    = document.getElementById('mob-co-shipping');

    if (!window._activeVoucher) {
      // Tidak ada voucher — tampilkan total biasa
      if (discRow) discRow.style.display = 'none';
      if (mobDiscRow) mobDiscRow.style.display = 'none';
      var total = subtotal + shipping;
      if (totalEl)    totalEl.textContent    = toRupiah(total);
      if (mobTotalEl) mobTotalEl.textContent = toRupiah(total);
      if (inpDisc)    inpDisc.value          = 0;
      return;
    }

    var v = window._activeVoucher;
    var discount    = v.discount_amount;
    var newShipping = v.shipping_cost;
    var newTotal    = v.total;

    // Update ongkir tampilan jika gratis ongkir
    if (v.type === 'free_shipping') {
      if (shippingEl) shippingEl.innerHTML = '<span style="text-decoration:line-through;color:#aaa;font-size:.8rem;">'+toRupiah(shipping)+'</span> <span style="color:#1a6b3a;font-weight:700;font-size:.8rem;">Gratis</span>';
      if (mobShip)    mobShip.innerHTML    = '<span style="color:#1a6b3a;font-weight:700;">Gratis</span>';
      if (inpShipHidden) inpShipHidden.value = 0;
    }

    // Tampilkan baris diskon
    if (discRow)    discRow.style.display = 'flex';
    if (mobDiscRow) mobDiscRow.style.display = 'flex';
    if (discAmt)    discAmt.textContent    = '- ' + toRupiah(discount);
    if (mobDiscEl)  mobDiscEl.textContent  = '- ' + toRupiah(discount);

    // Update total
    if (totalEl)    totalEl.textContent    = toRupiah(newTotal);
    if (mobTotalEl) mobTotalEl.textContent = toRupiah(newTotal);

    // Hidden inputs
    if (inpDisc)         inpDisc.value          = discount;
    if (inpShipHidden && v.type !== 'free_shipping') {
      // Ongkir tidak berubah untuk tipe selain free_shipping
    }
  };

  /* Tampilkan feedback di bawah input voucher */
  function showFeedback(msg, isOk) {
    var el = document.getElementById('voucher-feedback');
    if (!el) return;
    el.style.display  = 'block';
    el.style.color    = isOk ? '#1a6b3a' : '#c0392b';
    el.innerHTML = (isOk ? '<i class="bi bi-check-circle-fill"></i> ' : '<i class="bi bi-x-circle-fill"></i> ') + msg;
  }

  function clearFeedback() {
    var el = document.getElementById('voucher-feedback');
    if (el) { el.style.display = 'none'; el.textContent = ''; }
  }

  /* Terapkan voucher */
  window.applyVoucher = function() {
    var codeEl = document.getElementById('inp-voucher-code');
    var code   = (codeEl ? codeEl.value : '').trim();
    if (!code) { showFeedback('Masukkan kode voucher terlebih dahulu.', false); return; }

    var btn = document.getElementById('btn-apply-voucher');
    if (btn) { btn.disabled = true; btn.textContent = 'Memeriksa…'; }

    var shipping = window._currentShipping || 0;

    var fd = new FormData();
    fd.append('code',     code);
    fd.append('subtotal', RAW_SUBTOTAL);
    fd.append('shipping', shipping);

    fetch(VOUCHER_API, { method:'POST', body:fd })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (btn) { btn.disabled = false; btn.textContent = 'Pakai'; }

        if (!res.ok) {
          showFeedback(res.message, false);
          return;
        }

        // Simpan state voucher
        window._activeVoucher = res;

        // Update hidden inputs
        var inpId   = document.getElementById('inp-voucher-id');
        var inpCode = document.getElementById('inp-voucher-code-val');
        if (inpId)   inpId.value   = res.voucher_id;
        if (inpCode) inpCode.value = res.voucher_code;

        // Tampilkan badge aktif
        var badge = document.getElementById('voucher-active-badge');
        var bdLbl = document.getElementById('voucher-badge-label');
        if (badge) badge.style.display = 'flex';
        if (bdLbl) bdLbl.textContent   = res.voucher_code + ' · ' + res.type_label;

        // Sembunyikan input, tampilkan badge
        if (codeEl)  codeEl.style.display  = 'none';
        if (btn)     btn.style.display     = 'none';
        clearFeedback();
        showFeedback(res.message, true);

        // Hapus label diskon di summary
        var lblEl = document.getElementById('co-discount-label');
        if (lblEl) lblEl.textContent = res.voucher_code + ' (' + res.type_label + ')';

        recalcWithVoucher(RAW_SUBTOTAL, shipping);
      })
      .catch(function() {
        if (btn) { btn.disabled = false; btn.textContent = 'Pakai'; }
        showFeedback('Terjadi kesalahan. Coba lagi.', false);
      });
  };

  /* Hapus voucher */
  window.removeVoucher = function() {
    window._activeVoucher = null;

    // Reset hidden inputs
    var inpId   = document.getElementById('inp-voucher-id');
    var inpCode = document.getElementById('inp-voucher-code-val');
    var inpDisc = document.getElementById('inp-discount-amount');
    var inpShip = document.getElementById('inp-shipping-cost');
    if (inpId)   inpId.value   = '';
    if (inpCode) inpCode.value = '';
    if (inpDisc) inpDisc.value = 0;

    // Restore ongkir
    var shipping = window._currentShipping || 0;
    if (inpShip) inpShip.value = shipping;
    var shippingEl = document.getElementById('co-shipping');
    var mobShip    = document.getElementById('mob-co-shipping');
    if (shippingEl) shippingEl.textContent = toRupiah(shipping);
    if (mobShip)    mobShip.textContent    = toRupiah(shipping);

    // Sembunyikan badge, tampilkan input
    var badge  = document.getElementById('voucher-active-badge');
    var codeEl = document.getElementById('inp-voucher-code');
    var btn    = document.getElementById('btn-apply-voucher');
    if (badge)  { badge.style.display  = 'none'; }
    if (codeEl) { codeEl.style.display = ''; codeEl.value = ''; }
    if (btn)    { btn.style.display    = ''; }

    clearFeedback();
    recalcWithVoucher(RAW_SUBTOTAL, shipping);
  };

  /* Enter key di input voucher */
  var codeInput = document.getElementById('inp-voucher-code');
  if (codeInput) {
    codeInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') { e.preventDefault(); window.applyVoucher(); }
    });
  }
})();
</script>
