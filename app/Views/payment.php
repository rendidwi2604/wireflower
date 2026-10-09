<div class="container" style="max-width:560px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Pembayaran</h1>
    <p>Pesanan #<?= e($order['order_code']) ?></p>
  </div>

  <div class="card-wf">

    <!-- Total -->
    <div style="text-align:center;padding:1.5rem 1rem 1.25rem;border-bottom:1px solid var(--border);margin-bottom:1.25rem;">
      <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:.35rem;">Total Pembayaran</div>
      <div style="font-family:Georgia,serif;font-size:2.2rem;font-weight:700;color:var(--pink-deep);"><?= rupiah($order['total']) ?></div>
      <div style="margin-top:.65rem;">
        <?php if ($payment['status'] === 'success'): ?>
          <span class="status-badge status-delivered"><i class="bi bi-check-circle-fill"></i> Sudah Dibayar</span>
        <?php else: ?>
          <span class="status-badge status-pending"><i class="bi bi-clock"></i> Menunggu Pembayaran</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($payment['status'] === 'pending'): ?>
    <form method="POST" id="payForm">

      <!-- ── Pilih Metode ── -->
      <div class="form-group" style="margin-bottom:1.25rem;">
        <label class="form-label" style="margin-bottom:.65rem;">Metode Pembayaran</label>
        <div style="display:flex;flex-direction:column;gap:.5rem;">
          <label class="pay-method-label" data-target="detail-transfer">
            <input type="radio" name="method" value="transfer_bank" checked style="accent-color:var(--pink-deep);" onchange="switchMethod(this)">
            <i class="bi bi-bank" style="font-size:1.15rem;color:var(--pink-deep);"></i>
            <span style="font-weight:500;font-size:.9rem;">Transfer Bank</span>
          </label>
          <label class="pay-method-label" data-target="detail-cod">
            <input type="radio" name="method" value="cod" style="accent-color:var(--pink-deep);" onchange="switchMethod(this)">
            <i class="bi bi-cash-coin" style="font-size:1.15rem;color:var(--pink-deep);"></i>
            <span style="font-weight:500;font-size:.9rem;">COD (Bayar di Tempat)</span>
          </label>
          <label class="pay-method-label" data-target="detail-qris">
            <input type="radio" name="method" value="qris" style="accent-color:var(--pink-deep);" onchange="switchMethod(this)">
            <i class="bi bi-qr-code" style="font-size:1.15rem;color:var(--pink-deep);"></i>
            <span style="font-weight:500;font-size:.9rem;">QRIS</span>
          </label>
        </div>
      </div>

      <!-- ── Detail Transfer Bank ── -->
      <div id="detail-transfer" class="pay-detail" style="margin-bottom:1.25rem;">

        <label class="form-label" style="display:block;margin-bottom:.5rem;">Pilih Bank Tujuan</label>

        <!-- Hidden input nilai bank -->
        <input type="hidden" name="bank_name" id="inp-bank-name" value="">

        <!-- Custom dropdown with logo -->
        <div class="bank-dropdown" id="bankDropdown">
          <!-- Trigger -->
          <div class="bank-dd-trigger" id="bankTrigger" onclick="toggleDropdown()">
            <span class="bank-dd-placeholder" id="bankPlaceholder">
              <span style="color:#aaa;">— Pilih Bank —</span>
            </span>
            <i class="bi bi-chevron-down bank-dd-arrow" id="bankArrow"></i>
          </div>
          <!-- Menu -->
          <div class="bank-dd-menu" id="bankMenu">
            <!-- diisi JS -->
          </div>
        </div>

        <!-- Info rekening tujuan -->
        <div id="rekening-info" style="display:none;background:#f0faf5;border:1px solid #b7e4cc;
             border-radius:var(--radius-sm);padding:1rem 1.1rem;font-size:.875rem;margin-top:.85rem;">
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
            <img id="rek-logo" src="" alt=""
                 style="width:38px;height:38px;object-fit:contain;border-radius:6px;
                        background:#fff;padding:3px;border:1px solid #dde;flex-shrink:0;">
            <div>
              <div style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#1a6b3a;">Rekening Tujuan</div>
              <div style="font-weight:700;font-size:.92rem;color:#1e1212;" id="rek-bank-name">—</div>
            </div>
          </div>
          <table style="width:100%;border-collapse:collapse;">
            <tr>
              <td style="color:#555;padding:.25rem 0;width:40%;font-size:.83rem;">No. Rekening</td>
              <td>
                <span style="font-weight:700;font-size:1rem;color:#1e1212;letter-spacing:.06em;">901757779319</span>
                <button type="button" onclick="copyRek()" title="Salin"
                        style="background:none;border:none;cursor:pointer;color:var(--pink-deep);
                               font-size:.85rem;margin-left:.35rem;vertical-align:middle;">
                  <i class="bi bi-copy" id="copy-icon"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td style="color:#555;padding:.25rem 0;font-size:.83rem;">Atas Nama</td>
              <td style="font-weight:600;color:#1e1212;">WireFlower</td>
            </tr>
            <tr>
              <td style="color:#555;padding:.25rem 0;font-size:.83rem;">Jumlah Transfer</td>
              <td style="font-weight:700;color:var(--pink-deep);"><?= rupiah($order['total']) ?></td>
            </tr>
          </table>
          <div style="margin-top:.75rem;font-size:.75rem;color:#666;line-height:1.5;">
            <i class="bi bi-exclamation-triangle" style="color:#e67e22;"></i>
            Transfer tepat sesuai jumlah di atas. Pesanan dikonfirmasi setelah admin memverifikasi.
          </div>
        </div>

        <!-- Catatan -->
        <div class="form-group" style="margin-top:.85rem;margin-bottom:0;">
          <label class="form-label">Nama Pengirim / Catatan <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
          <input type="text" name="transfer_note" class="form-input" placeholder="Mis: Budi Santoso / WF-20261009">
        </div>
      </div>

      <!-- ── Detail COD ── -->
      <div id="detail-cod" class="pay-detail" style="display:none;margin-bottom:1.25rem;">
        <div style="background:#fffbea;border:1px solid #f0d070;border-radius:var(--radius-sm);padding:1rem 1.1rem;font-size:.875rem;">
          <div style="font-weight:700;margin-bottom:.5rem;"><i class="bi bi-truck" style="color:#c07a00;"></i> Bayar di Tempat (COD)</div>
          <ul style="margin:.4rem 0 0 1rem;padding:0;color:#555;line-height:1.9;font-size:.83rem;">
            <li>Siapkan uang tunai sesuai total pesanan</li>
            <li>Pembayaran dilakukan kepada kurir saat barang tiba</li>
            <li>Pastikan nomor telepon aktif agar kurir bisa menghubungi</li>
          </ul>
        </div>
      </div>

      <!-- ── Detail QRIS ── -->
      <div id="detail-qris" class="pay-detail" style="display:none;margin-bottom:1.25rem;">
        <div style="background:#f5f0ff;border:1px solid #c9b8f0;border-radius:var(--radius-sm);
             padding:1.25rem 1.1rem;text-align:center;font-size:.875rem;">
          <div style="font-weight:700;margin-bottom:.75rem;color:#5a3ea0;font-size:.95rem;">
            <i class="bi bi-qr-code-scan"></i> Scan QRIS
          </div>
          <div style="width:190px;height:190px;margin:0 auto .85rem;background:#fff;border-radius:12px;
               display:flex;align-items:center;justify-content:center;color:#5a3ea0;font-size:3.5rem;
               border:2px solid #c9b8f0;">
            <i class="bi bi-qr-code"></i>
          </div>
          <div style="color:#666;font-size:.8rem;line-height:1.65;">
            Scan dengan GoPay, OVO, Dana, ShopeePay, m-Banking, atau aplikasi QRIS lainnya
          </div>
          <div style="margin-top:.75rem;font-weight:700;color:#5a3ea0;font-size:1rem;"><?= rupiah($order['total']) ?></div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary"
              style="width:100%;justify-content:center;border-radius:var(--radius-sm);">
        <i class="bi bi-check2-circle"></i> Konfirmasi Pembayaran
      </button>

    </form>

    <?php else: ?>
      <div style="text-align:center;padding:1rem 0;">
        <i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#1a6b3a;display:block;margin-bottom:.75rem;"></i>
        <p style="font-size:.9rem;color:var(--text-muted);margin-bottom:.4rem;">
          Pembayaran dikonfirmasi pada <?= date('d M Y, H:i', strtotime($payment['paid_at'])) ?>.
        </p>
        <?php if (!empty($payment['method'])): ?>
        <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:1.25rem;">
          Metode: <strong><?= e($payment['method']) ?></strong>
        </p>
        <?php endif; ?>
        <a href="<?= site_url('pesanan.php') ?>" class="btn btn-primary" style="border-radius:var(--radius-sm);">
          <i class="bi bi-receipt"></i> Lihat Pesanan Saya
        </a>
      </div>
    <?php endif; ?>

  </div>
</div>

<style>
.pay-method-label {
  display:flex; align-items:center; gap:.75rem;
  padding:.85rem 1rem;
  border:1.5px solid var(--border); border-radius:var(--radius-sm);
  cursor:pointer; transition:border-color .15s, background .15s;
}
.pay-method-label:hover { border-color:var(--pink); }
.pay-method-label:has(input:checked) { border-color:var(--pink-deep); background:var(--pink-soft); }

/* Custom bank dropdown */
.bank-dropdown { position:relative; user-select:none; }

.bank-dd-trigger {
  display:flex; align-items:center; justify-content:space-between;
  padding:.65rem .9rem;
  border:1.5px solid var(--border); border-radius:var(--radius-sm);
  background:#fff; cursor:pointer;
  transition:border-color .15s;
  min-height:46px;
}
.bank-dd-trigger:hover { border-color:var(--pink); }
.bank-dd-trigger.open  { border-color:var(--pink-deep); border-radius:var(--radius-sm) var(--radius-sm) 0 0; }

.bank-dd-arrow { font-size:.75rem; color:var(--text-muted); transition:transform .2s; }
.bank-dd-trigger.open .bank-dd-arrow { transform:rotate(180deg); }

.bank-dd-menu {
  display:none;
  position:absolute; left:0; right:0; top:100%; z-index:200;
  background:#fff;
  border:1.5px solid var(--pink-deep); border-top:none;
  border-radius:0 0 var(--radius-sm) var(--radius-sm);
  max-height:300px; overflow-y:auto;
  box-shadow:0 8px 24px rgba(0,0,0,.10);
}
.bank-dd-menu.open { display:block; }

.bank-dd-group {
  font-size:.68rem; font-weight:800; letter-spacing:.1em;
  text-transform:uppercase; color:var(--text-muted);
  padding:.55rem 1rem .3rem; background:#faf8f5;
  border-bottom:1px solid var(--border);
}

.bank-dd-item {
  display:flex; align-items:center; gap:.75rem;
  padding:.55rem .9rem; cursor:pointer;
  transition:background .12s;
  border-bottom:1px solid #f5f0f0;
}
.bank-dd-item:last-child { border-bottom:none; }
.bank-dd-item:hover { background:var(--pink-soft); }
.bank-dd-item.selected { background:var(--pink-soft); }

.bank-dd-item img {
  width:32px; height:32px; object-fit:contain;
  flex-shrink:0; border-radius:5px;
  background:#f5f5f5; padding:2px;
  border:1px solid #eee;
}
.bank-dd-item .bank-fallback-icon {
  width:32px; height:32px; flex-shrink:0; border-radius:5px;
  background:var(--pink-soft); display:flex; align-items:center;
  justify-content:center; font-size:1rem; color:var(--pink-deep);
  border:1px solid var(--border);
}
.bank-dd-item span { font-size:.875rem; font-weight:500; color:var(--text); }

/* Selected preview in trigger */
.bank-dd-selected {
  display:flex; align-items:center; gap:.65rem;
}
.bank-dd-selected img {
  width:26px; height:26px; object-fit:contain;
  border-radius:4px; background:#f5f5f5; padding:1px;
  border:1px solid #eee; flex-shrink:0;
}
.bank-dd-selected span { font-size:.875rem; font-weight:600; color:var(--text); }
</style>

<script>
/* ══ DATA BANK — logo dari assets lokal (sudah terverifikasi 200 OK di Vercel) ══ */
var BANKS = [
  /* Bank Pemerintah */
  { group:'Bank Pemerintah', name:'Bank BRI',        short:'BRI',       logo: 'https://wireflower.vercel.app/assets/img/banks/bri.svg' },
  { group:'Bank Pemerintah', name:'Bank BNI',        short:'BNI',       logo: 'https://wireflower.vercel.app/assets/img/banks/bni.svg' },
  { group:'Bank Pemerintah', name:'Bank Mandiri',    short:'Mandiri',   logo: 'https://wireflower.vercel.app/assets/img/banks/mandiri.svg' },
  { group:'Bank Pemerintah', name:'Bank BTN',        short:'BTN',       logo: 'https://wireflower.vercel.app/assets/img/banks/btn.svg' },
  { group:'Bank Pemerintah', name:'Bank BSI',        short:'BSI',       logo: 'https://wireflower.vercel.app/assets/img/banks/bsi.svg' },
  /* Bank Swasta */
  { group:'Bank Swasta',     name:'Bank BCA',        short:'BCA',       logo: 'https://wireflower.vercel.app/assets/img/banks/bca.svg' },
  { group:'Bank Swasta',     name:'Bank CIMB Niaga', short:'CIMB',      logo: 'https://wireflower.vercel.app/assets/img/banks/cimb.svg' },
  { group:'Bank Swasta',     name:'Bank Danamon',    short:'Danamon',   logo: 'https://wireflower.vercel.app/assets/img/banks/danamon.svg' },
  { group:'Bank Swasta',     name:'Bank Permata',    short:'Permata',   logo: 'https://wireflower.vercel.app/assets/img/banks/permata.svg' },
  { group:'Bank Swasta',     name:'Bank Maybank',    short:'Maybank',   logo: 'https://wireflower.vercel.app/assets/img/banks/maybank.svg' },
  { group:'Bank Swasta',     name:'Bank OCBC NISP',  short:'OCBC',      logo: 'https://wireflower.vercel.app/assets/img/banks/ocbc.svg' },
  { group:'Bank Swasta',     name:'Bank Mega',       short:'Mega',      logo: 'https://wireflower.vercel.app/assets/img/banks/mega.svg' },
  /* Bank Digital */
  { group:'Bank Digital',    name:'Jenius (BTPN)',   short:'Jenius',    logo: 'https://wireflower.vercel.app/assets/img/banks/jenius.svg' },
  { group:'Bank Digital',    name:'Bank Jago',       short:'Jago',      logo: 'https://wireflower.vercel.app/assets/img/banks/jago.svg' },
  { group:'Bank Digital',    name:'Seabank',         short:'Seabank',   logo: 'https://wireflower.vercel.app/assets/img/banks/seabank.svg' },
  { group:'Bank Digital',    name:'Allo Bank',       short:'Allo',      logo: 'https://wireflower.vercel.app/assets/img/banks/allo.svg' },
  /* Dompet Digital */
  { group:'Dompet Digital',  name:'GoPay',           short:'GoPay',     logo: 'https://wireflower.vercel.app/assets/img/banks/gopay.svg' },
  { group:'Dompet Digital',  name:'OVO',             short:'OVO',       logo: 'https://wireflower.vercel.app/assets/img/banks/ovo.svg' },
  { group:'Dompet Digital',  name:'DANA',            short:'DANA',      logo: 'https://wireflower.vercel.app/assets/img/banks/dana.svg' },
  { group:'Dompet Digital',  name:'ShopeePay',       short:'ShopeePay', logo: 'https://wireflower.vercel.app/assets/img/banks/shopeepay.svg' },
  { group:'Dompet Digital',  name:'LinkAja',         short:'LinkAja',   logo: 'https://wireflower.vercel.app/assets/img/banks/linkaja.svg' },
];

var NOREK = '901757779319';
var selectedBank = null;

/* ── Build dropdown menu ── */
(function buildMenu() {
  var menu = document.getElementById('bankMenu');
  if (!menu) return;

  var lastGroup = null;
  BANKS.forEach(function(b) {
    if (b.group !== lastGroup) {
      var grp = document.createElement('div');
      grp.className = 'bank-dd-group';
      grp.textContent = b.group;
      menu.appendChild(grp);
      lastGroup = b.group;
    }

    var item = document.createElement('div');
    item.className = 'bank-dd-item';
    item.dataset.name = b.name;

    // Logo dengan fallback inisial jika SVG gagal dimuat
    var img = document.createElement('img');
    img.src = b.logo;
    img.alt = b.short;
    img.width  = 32;
    img.height = 32;
    img.style.cssText = 'object-fit:contain;flex-shrink:0;border-radius:5px;background:#f5f5f5;padding:2px;border:1px solid #eee;';
    img.onerror = function() {
      var fb = document.createElement('div');
      fb.style.cssText = 'width:32px;height:32px;flex-shrink:0;border-radius:5px;background:#fdf0f3;' +
        'display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:800;' +
        'color:#c2185b;border:1px solid #eee;text-align:center;line-height:1;';
      fb.textContent = b.short.substring(0, 3).toUpperCase();
      if (img.parentNode) img.parentNode.replaceChild(fb, img);
    };

    var lbl = document.createElement('span');
    lbl.textContent = b.name;

    item.appendChild(img);
    item.appendChild(lbl);
    item.addEventListener('click', function() { pickBank(b); });
    menu.appendChild(item);
  });
})();

function toggleDropdown() {
  var trigger = document.getElementById('bankTrigger');
  var menu    = document.getElementById('bankMenu');
  var open    = menu.classList.toggle('open');
  trigger.classList.toggle('open', open);
}

function pickBank(b) {
  selectedBank = b;
  document.getElementById('inp-bank-name').value = b.name;

  // Update trigger display
  var trigger = document.getElementById('bankTrigger');
  trigger.querySelector('#bankPlaceholder').innerHTML =
    '<div class="bank-dd-selected">' +
      '<img src="' + b.logo + '" alt="' + b.short + '" onerror="this.style.display=\'none\'">' +
      '<span>' + b.name + '</span>' +
    '</div>';

  // Highlight item terpilih
  document.querySelectorAll('.bank-dd-item').forEach(function(el) {
    el.classList.toggle('selected', el.dataset.name === b.name);
  });

  // Tutup dropdown
  document.getElementById('bankMenu').classList.remove('open');
  trigger.classList.remove('open');

  // Update info rekening
  document.getElementById('rek-logo').src = b.logo;
  document.getElementById('rek-bank-name').textContent = b.name;
  document.getElementById('rekening-info').style.display = 'block';
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function(e) {
  var dd = document.getElementById('bankDropdown');
  if (dd && !dd.contains(e.target)) {
    document.getElementById('bankMenu').classList.remove('open');
    document.getElementById('bankTrigger').classList.remove('open');
  }
});

function copyRek() {
  navigator.clipboard.writeText(NOREK).then(function() {
    var icon = document.getElementById('copy-icon');
    icon.className = 'bi bi-check2';
    setTimeout(function() { icon.className = 'bi bi-copy'; }, 1800);
  });
}

function switchMethod(radio) {
  document.querySelectorAll('.pay-detail').forEach(function(el) {
    el.style.display = 'none';
  });
  var target = radio.closest('.pay-method-label').dataset.target;
  if (target) {
    var el = document.getElementById(target);
    if (el) el.style.display = 'block';
  }
}

// Validasi bank wajib dipilih
var payForm = document.getElementById('payForm');
if (payForm) {
  payForm.addEventListener('submit', function(e) {
    var method = document.querySelector('input[name="method"]:checked');
    if (method && method.value === 'transfer_bank' && !selectedBank) {
      e.preventDefault();
      alert('Pilih bank tujuan terlebih dahulu.');
      document.getElementById('bankDropdown').scrollIntoView({ behavior:'smooth', block:'center' });
    }
  });
}

// Init
document.addEventListener('DOMContentLoaded', function() {
  var checked = document.querySelector('input[name="method"]:checked');
  if (checked) switchMethod(checked);
});
</script>
