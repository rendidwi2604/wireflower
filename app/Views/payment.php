<?php
$isCod      = ($payment['method'] ?? '') === 'COD';
$hasMethod  = !empty($payment['method']);
$hasProof   = !empty($payment['transfer_proof']);
$isPending  = $payment['status'] === 'pending';
$isSuccess  = $payment['status'] === 'success';
?>
<div class="container" style="max-width:560px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Pembayaran</h1>
    <p>Pesanan #<?= e($order['order_code']) ?></p>
  </div>

  <div class="card-wf">

    <!-- ── Total ── -->
    <div style="text-align:center;padding:1.5rem 1rem 1.25rem;border-bottom:1px solid var(--border);margin-bottom:1.25rem;">
      <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:.35rem;">Total Pembayaran</div>
      <div style="font-family:Georgia,serif;font-size:2.2rem;font-weight:700;color:var(--pink-deep);"><?= rupiah($order['total']) ?></div>
      <div style="margin-top:.65rem;">
        <?php if ($isSuccess): ?>
          <span class="status-badge status-delivered"><i class="bi bi-check-circle-fill"></i> Sudah Dibayar</span>
        <?php elseif ($hasProof): ?>
          <span class="status-badge status-processing"><i class="bi bi-hourglass-split"></i> Bukti Dikirim — Menunggu Verifikasi</span>
        <?php elseif ($hasMethod): ?>
          <span class="status-badge status-processing"><i class="bi bi-hourglass-split"></i> Menunggu Bukti Pembayaran</span>
        <?php else: ?>
          <span class="status-badge status-pending"><i class="bi bi-clock"></i> Menunggu Pembayaran</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($isSuccess): ?>
    <!-- ── Sudah dibayar ── -->
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

    <?php elseif ($isPending && $hasProof): ?>
    <!-- ── Bukti sudah dikirim, menunggu verifikasi ── -->
    <div style="background:#f0faf5;border:1px solid #b7e4cc;border-radius:var(--radius-sm);padding:1.1rem 1.15rem;margin-bottom:1.25rem;">
      <div style="display:flex;align-items:center;gap:.65rem;margin-bottom:.65rem;">
        <i class="bi bi-hourglass-split" style="color:#1a6b3a;font-size:1.3rem;"></i>
        <div>
          <div style="font-weight:700;font-size:.9rem;color:#1a6b3a;">Bukti pembayaran sudah dikirim</div>
          <div style="font-size:.78rem;color:#555;">Admin akan memverifikasi dalam 1×24 jam</div>
        </div>
      </div>
      <?php if (!empty($payment['method'])): ?>
        <div style="font-size:.8rem;color:#555;margin-bottom:.5rem;">
          Metode: <strong><?= e($payment['method']) ?></strong>
        </div>
      <?php endif; ?>
      <a href="<?= e($payment['transfer_proof']) ?>" target="_blank"
         style="display:inline-flex;align-items:center;gap:.4rem;font-size:.8rem;color:var(--pink-deep);font-weight:600;">
        <i class="bi bi-file-image"></i> Lihat bukti yang dikirim
      </a>
    </div>
    <p style="font-size:.8rem;color:var(--text-muted);text-align:center;margin-bottom:1rem;">
      Kalau ada kesalahan, kamu bisa kirim ulang bukti di bawah.
    </p>
    <!-- Form kirim ulang bukti -->
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="method" value="<?= e($payment['method'] ?? 'transfer_bank') ?>">
      <div class="form-group" style="margin-bottom:.85rem;">
        <label class="form-label">Upload ulang bukti pembayaran</label>
        <input type="file" name="transfer_proof" accept=".jpg,.jpeg,.png,.webp,.pdf"
               class="form-input" style="padding:.5rem;">
        <small style="color:var(--text-muted);font-size:.75rem;">JPG, PNG, WebP, atau PDF. Maks 5 MB.</small>
      </div>
      <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;border-radius:var(--radius-sm);">
        <i class="bi bi-upload"></i> Kirim Ulang Bukti
      </button>
    </form>
    <div style="margin-top:1rem;text-align:center;">
      <a href="<?= site_url('pesanan.php') ?>" style="font-size:.83rem;color:var(--text-muted);">
        <i class="bi bi-arrow-left"></i> Kembali ke Pesanan
      </a>
    </div>

    <?php else: ?>
    <!-- ── Form pilih metode + upload bukti ── -->
    <form method="POST" id="payForm" enctype="multipart/form-data">

      <!-- Pilih Metode -->
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
        <input type="hidden" name="bank_name" id="inp-bank-name" value="">

        <div class="bank-dropdown" id="bankDropdown">
          <div class="bank-dd-trigger" id="bankTrigger" onclick="toggleDropdown()">
            <span class="bank-dd-placeholder" id="bankPlaceholder">
              <span style="color:#aaa;">— Pilih Bank —</span>
            </span>
            <i class="bi bi-chevron-down bank-dd-arrow" id="bankArrow"></i>
          </div>
          <div class="bank-dd-menu" id="bankMenu"></div>
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
                        style="background:none;border:none;cursor:pointer;color:var(--pink-deep);font-size:.85rem;margin-left:.35rem;vertical-align:middle;">
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
            Transfer tepat sesuai jumlah di atas.
          </div>
        </div>

        <!-- Upload bukti transfer -->
        <div class="pay-proof-box" id="proof-transfer" style="margin-top:1rem;">
          <div style="font-weight:600;font-size:.85rem;margin-bottom:.45rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-upload" style="color:var(--pink-deep);"></i> Upload Bukti Transfer <span style="color:#c0392b;">*</span>
          </div>
          <input type="file" name="transfer_proof" id="proof-file-transfer"
                 accept=".jpg,.jpeg,.png,.webp,.pdf"
                 class="form-input" style="padding:.5rem;" required
                 onchange="previewProof(this,'preview-transfer')">
          <small style="color:var(--text-muted);font-size:.75rem;">JPG, PNG, WebP, atau PDF. Maks 5 MB.</small>
          <div id="preview-transfer" style="display:none;margin-top:.65rem;">
            <img id="preview-transfer-img" src="" alt="Preview"
                 style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid var(--border);object-fit:contain;">
          </div>
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
          <div style="margin-top:.75rem;font-size:.78rem;color:#a07000;font-weight:600;">
            <i class="bi bi-info-circle"></i> COD tidak memerlukan upload bukti pembayaran.
          </div>
        </div>
      </div>

      <!-- ── Detail QRIS ── -->
      <div id="detail-qris" class="pay-detail" style="display:none;margin-bottom:1.25rem;">
        <div style="background:#f5f0ff;border:1px solid #c9b8f0;border-radius:var(--radius-sm);
             padding:1.25rem 1.1rem;text-align:center;font-size:.875rem;">
          <div style="font-weight:700;margin-bottom:.75rem;color:#5a3ea0;font-size:.95rem;">
            <i class="bi bi-qr-code-scan"></i> Scan QRIS
          </div>
          <div style="width:220px;height:220px;margin:0 auto .85rem;background:#fff;border-radius:12px;
               display:flex;align-items:center;justify-content:center;
               border:2px solid #c9b8f0;overflow:hidden;">
            <img src="<?= site_url('assets/img/qris.png') ?>" alt="QRIS WireFlower"
                 style="width:100%;height:100%;object-fit:contain;padding:4px;">
          </div>
          <div style="color:#666;font-size:.8rem;line-height:1.65;">
            Scan dengan GoPay, OVO, Dana, ShopeePay, m-Banking, atau aplikasi QRIS lainnya
          </div>
          <div style="margin-top:.75rem;font-weight:700;color:#5a3ea0;font-size:1rem;"><?= rupiah($order['total']) ?></div>
        </div>

        <!-- Upload bukti QRIS -->
        <div class="pay-proof-box" id="proof-qris" style="margin-top:1rem;">
          <div style="font-weight:600;font-size:.85rem;margin-bottom:.45rem;display:flex;align-items:center;gap:.4rem;">
            <i class="bi bi-upload" style="color:var(--pink-deep);"></i> Upload Bukti Pembayaran QRIS <span style="color:#c0392b;">*</span>
          </div>
          <input type="file" name="transfer_proof" id="proof-file-qris"
                 accept=".jpg,.jpeg,.png,.webp,.pdf"
                 class="form-input" style="padding:.5rem;"
                 onchange="previewProof(this,'preview-qris')">
          <small style="color:var(--text-muted);font-size:.75rem;">Screenshot hasil pembayaran QRIS. Maks 5 MB.</small>
          <div id="preview-qris" style="display:none;margin-top:.65rem;">
            <img id="preview-qris-img" src="" alt="Preview"
                 style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid var(--border);object-fit:contain;">
          </div>
        </div>
      </div>

      <!-- Submit button -->
      <button type="submit" class="btn btn-primary" id="btnSubmitPay"
              style="width:100%;justify-content:center;border-radius:var(--radius-sm);">
        <i class="bi bi-upload"></i> <span id="btnSubmitLabel">Kirim Bukti Pembayaran</span>
      </button>
      <p id="pay-hint" style="text-align:center;font-size:.75rem;color:var(--text-muted);margin-top:.65rem;">
        Pembayaran akan dikonfirmasi setelah admin memverifikasi bukti.
      </p>

    </form>
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

.pay-proof-box {
  background:#faf8f5; border:1.5px dashed var(--border);
  border-radius:var(--radius-sm); padding:1rem;
}

/* Bank dropdown */
.bank-dropdown { position:relative; user-select:none; }
.bank-dd-trigger {
  display:flex; align-items:center; justify-content:space-between;
  padding:.65rem .9rem; border:1.5px solid var(--border); border-radius:var(--radius-sm);
  background:#fff; cursor:pointer; transition:border-color .15s; min-height:46px;
}
.bank-dd-trigger:hover { border-color:var(--pink); }
.bank-dd-trigger.open { border-color:var(--pink-deep); border-radius:var(--radius-sm) var(--radius-sm) 0 0; }
.bank-dd-arrow { font-size:.75rem; color:var(--text-muted); transition:transform .2s; }
.bank-dd-trigger.open .bank-dd-arrow { transform:rotate(180deg); }
.bank-dd-menu {
  display:none; position:absolute; left:0; right:0; top:100%; z-index:200;
  background:#fff; border:1.5px solid var(--pink-deep); border-top:none;
  border-radius:0 0 var(--radius-sm) var(--radius-sm);
  max-height:300px; overflow-y:auto; box-shadow:0 8px 24px rgba(0,0,0,.10);
}
.bank-dd-menu.open { display:block; }
.bank-dd-group {
  font-size:.68rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase;
  color:var(--text-muted); padding:.55rem 1rem .3rem; background:#faf8f5;
  border-bottom:1px solid var(--border);
}
.bank-dd-item {
  display:flex; align-items:center; gap:.75rem; padding:.55rem .9rem; cursor:pointer;
  transition:background .12s; border-bottom:1px solid #f5f0f0;
}
.bank-dd-item:last-child { border-bottom:none; }
.bank-dd-item:hover, .bank-dd-item.selected { background:var(--pink-soft); }
.bank-dd-item img {
  width:32px; height:32px; object-fit:contain; flex-shrink:0;
  border-radius:5px; background:#f5f5f5; padding:2px; border:1px solid #eee;
}
.bank-dd-item span { font-size:.875rem; font-weight:500; color:var(--text); }
.bank-dd-selected { display:flex; align-items:center; gap:.65rem; }
.bank-dd-selected img {
  width:26px; height:26px; object-fit:contain; border-radius:4px;
  background:#f5f5f5; padding:1px; border:1px solid #eee; flex-shrink:0;
}
.bank-dd-selected span { font-size:.875rem; font-weight:600; color:var(--text); }
</style>

<script>
var LOGO = function(s){ return '<?= site_url("assets/img/banks/") ?>' + s + '.svg'; };
var BANKS = [
  { group:'Bank Pemerintah', name:'Bank BRI',        short:'BRI',       logo: LOGO('bri') },
  { group:'Bank Pemerintah', name:'Bank BNI',        short:'BNI',       logo: LOGO('bni') },
  { group:'Bank Pemerintah', name:'Bank Mandiri',    short:'Mandiri',   logo: LOGO('mandiri') },
  { group:'Bank Pemerintah', name:'Bank BTN',        short:'BTN',       logo: LOGO('btn') },
  { group:'Bank Pemerintah', name:'Bank BSI',        short:'BSI',       logo: LOGO('bsi') },
  { group:'Bank Swasta',     name:'Bank BCA',        short:'BCA',       logo: LOGO('bca') },
  { group:'Bank Swasta',     name:'Bank CIMB Niaga', short:'CIMB',      logo: LOGO('cimb') },
  { group:'Bank Swasta',     name:'Bank Danamon',    short:'Danamon',   logo: LOGO('danamon') },
  { group:'Bank Swasta',     name:'Bank Permata',    short:'Permata',   logo: LOGO('permata') },
  { group:'Bank Swasta',     name:'Bank Maybank',    short:'Maybank',   logo: LOGO('maybank') },
  { group:'Bank Swasta',     name:'Bank OCBC NISP',  short:'OCBC',      logo: LOGO('ocbc') },
  { group:'Bank Swasta',     name:'Bank Mega',       short:'Mega',      logo: LOGO('mega') },
  { group:'Bank Digital',    name:'Jenius (BTPN)',   short:'Jenius',    logo: LOGO('jenius') },
  { group:'Bank Digital',    name:'Bank Jago',       short:'Jago',      logo: LOGO('jago') },
  { group:'Bank Digital',    name:'Seabank',         short:'Seabank',   logo: LOGO('seabank') },
  { group:'Bank Digital',    name:'Allo Bank',       short:'Allo',      logo: LOGO('allo') },
  { group:'Dompet Digital',  name:'GoPay',           short:'GoPay',     logo: LOGO('gopay') },
  { group:'Dompet Digital',  name:'OVO',             short:'OVO',       logo: LOGO('ovo') },
  { group:'Dompet Digital',  name:'DANA',            short:'DANA',      logo: LOGO('dana') },
  { group:'Dompet Digital',  name:'ShopeePay',       short:'ShopeePay', logo: LOGO('shopeepay') },
  { group:'Dompet Digital',  name:'LinkAja',         short:'LinkAja',   logo: LOGO('linkaja') },
];

var NOREK = '901757779319';
var selectedBank = null;

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
    var img = document.createElement('img');
    img.src = b.logo; img.alt = b.short; img.width = 32; img.height = 32;
    img.style.cssText = 'object-fit:contain;flex-shrink:0;border-radius:5px;background:#f5f5f5;padding:2px;border:1px solid #eee;';
    var lbl = document.createElement('span');
    lbl.textContent = b.name;
    item.appendChild(img); item.appendChild(lbl);
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
  var trigger = document.getElementById('bankTrigger');
  trigger.querySelector('#bankPlaceholder').innerHTML =
    '<div class="bank-dd-selected">' +
      '<img src="' + b.logo + '" alt="' + b.short + '" onerror="this.style.display=\'none\'">' +
      '<span>' + b.name + '</span>' +
    '</div>';
  document.querySelectorAll('.bank-dd-item').forEach(function(el) {
    el.classList.toggle('selected', el.dataset.name === b.name);
  });
  document.getElementById('bankMenu').classList.remove('open');
  trigger.classList.remove('open');
  document.getElementById('rek-logo').src = b.logo;
  document.getElementById('rek-bank-name').textContent = b.name;
  document.getElementById('rekening-info').style.display = 'block';
}

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
  var method = radio.value;
  document.querySelectorAll('.pay-detail').forEach(function(el) { el.style.display = 'none'; });
  var target = radio.closest('.pay-method-label').dataset.target;
  if (target) { var el = document.getElementById(target); if (el) el.style.display = 'block'; }

  // Update tombol submit & hint
  var btnLabel = document.getElementById('btnSubmitLabel');
  var hint     = document.getElementById('pay-hint');
  if (method === 'cod') {
    if (btnLabel) btnLabel.textContent = 'Konfirmasi Pesanan COD';
    if (hint) hint.textContent = 'Pembayaran dilakukan saat barang tiba.';
  } else {
    if (btnLabel) btnLabel.textContent = 'Kirim Bukti Pembayaran';
    if (hint) hint.textContent = 'Pembayaran akan dikonfirmasi setelah admin memverifikasi bukti.';
  }

  // File input required untuk non-COD
  var proofTransfer = document.getElementById('proof-file-transfer');
  var proofQris     = document.getElementById('proof-file-qris');
  if (proofTransfer) proofTransfer.required = (method === 'transfer_bank');
  if (proofQris)     proofQris.required     = (method === 'qris');
}

function previewProof(input, previewId) {
  var previewBox = document.getElementById(previewId);
  var previewImg = document.getElementById(previewId + '-img');
  if (!input.files || !input.files[0]) return;
  var file = input.files[0];
  if (file.type.startsWith('image/')) {
    var reader = new FileReader();
    reader.onload = function(e) {
      if (previewImg) { previewImg.src = e.target.result; previewBox.style.display = 'block'; }
    };
    reader.readAsDataURL(file);
  } else {
    // PDF: tampilkan nama file saja
    if (previewBox) {
      previewBox.style.display = 'block';
      previewBox.innerHTML = '<div style="padding:.5rem;font-size:.8rem;color:var(--text-muted);"><i class="bi bi-file-pdf" style="color:#c0392b;font-size:1.2rem;"></i> ' + file.name + '</div>';
    }
  }
}

// Validasi
var payForm = document.getElementById('payForm');
if (payForm) {
  payForm.addEventListener('submit', function(e) {
    var method = document.querySelector('input[name="method"]:checked');
    if (!method) return;
    if (method.value === 'transfer_bank' && !selectedBank) {
      e.preventDefault();
      alert('Pilih bank tujuan terlebih dahulu.');
      document.getElementById('bankDropdown').scrollIntoView({ behavior:'smooth', block:'center' });
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  var checked = document.querySelector('input[name="method"]:checked');
  if (checked) switchMethod(checked);
});
</script>
