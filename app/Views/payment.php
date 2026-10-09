<div class="container" style="max-width:600px;margin:0 auto;padding:2rem 1.5rem;">

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
            <input type="radio" name="method" value="transfer_bank" checked
                   style="accent-color:var(--pink-deep);" onchange="switchMethod(this)">
            <i class="bi bi-bank" style="font-size:1.15rem;color:var(--pink-deep);"></i>
            <span style="font-weight:500;font-size:.9rem;">Transfer Bank</span>
          </label>

          <label class="pay-method-label" data-target="detail-cod">
            <input type="radio" name="method" value="cod"
                   style="accent-color:var(--pink-deep);" onchange="switchMethod(this)">
            <i class="bi bi-cash-coin" style="font-size:1.15rem;color:var(--pink-deep);"></i>
            <span style="font-weight:500;font-size:.9rem;">COD (Bayar di Tempat)</span>
          </label>

          <label class="pay-method-label" data-target="detail-qris">
            <input type="radio" name="method" value="qris"
                   style="accent-color:var(--pink-deep);" onchange="switchMethod(this)">
            <i class="bi bi-qr-code" style="font-size:1.15rem;color:var(--pink-deep);"></i>
            <span style="font-weight:500;font-size:.9rem;">QRIS</span>
          </label>

        </div>
      </div>

      <!-- ── Detail Transfer Bank ── -->
      <div id="detail-transfer" class="pay-detail" style="margin-bottom:1.25rem;">

        <label class="form-label" style="display:block;margin-bottom:.65rem;">Pilih Bank</label>

        <!-- Hidden input — nilai bank terpilih -->
        <input type="hidden" name="bank_name" id="inp-bank-name" value="">

        <!-- Grid bank cards -->
        <div id="bank-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:.55rem;margin-bottom:1rem;">
        </div>

        <!-- Info rekening tujuan — muncul setelah bank dipilih -->
        <div id="rekening-info" style="display:none;background:#f0faf5;border:1px solid #b7e4cc;border-radius:var(--radius-sm);padding:1rem 1.1rem;font-size:.875rem;">
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
            <img id="rek-logo" src="" alt="" style="width:36px;height:36px;object-fit:contain;border-radius:6px;background:#fff;padding:2px;border:1px solid #dde;">
            <div>
              <div style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#1a6b3a;">Rekening Tujuan</div>
              <div style="font-weight:700;font-size:.92rem;color:#1e1212;" id="rek-bank-name">—</div>
            </div>
          </div>
          <table style="width:100%;border-collapse:collapse;">
            <tr>
              <td style="color:#555;padding:.2rem 0;width:38%;font-size:.83rem;">No. Rekening</td>
              <td>
                <span style="font-weight:700;font-size:1rem;color:#1e1212;letter-spacing:.06em;" id="rek-norek">901757779319</span>
                <button type="button" onclick="copyRek()" title="Salin"
                        style="background:none;border:none;cursor:pointer;color:var(--pink-deep);font-size:.85rem;margin-left:.35rem;vertical-align:middle;">
                  <i class="bi bi-copy" id="copy-icon"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td style="color:#555;padding:.2rem 0;font-size:.83rem;">Atas Nama</td>
              <td style="font-weight:600;color:#1e1212;font-size:.875rem;">WireFlower</td>
            </tr>
            <tr>
              <td style="color:#555;padding:.2rem 0;font-size:.83rem;">Jumlah Transfer</td>
              <td style="font-weight:700;color:var(--pink-deep);"><?= rupiah($order['total']) ?></td>
            </tr>
          </table>
          <div style="margin-top:.75rem;font-size:.75rem;color:#555;line-height:1.5;">
            <i class="bi bi-exclamation-triangle" style="color:#e67e22;"></i>
            Transfer tepat sesuai jumlah di atas. Pesanan dikonfirmasi setelah admin memverifikasi.
          </div>
        </div>

        <!-- Catatan transfer -->
        <div class="form-group" style="margin-top:.85rem;margin-bottom:0;">
          <label class="form-label">Nama Pengirim / Catatan <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
          <input type="text" name="transfer_note" class="form-input"
                 placeholder="Mis: Budi Santoso / WF-20261009">
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
        <div style="background:#f5f0ff;border:1px solid #c9b8f0;border-radius:var(--radius-sm);padding:1.25rem 1.1rem;text-align:center;font-size:.875rem;">
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

      <button type="submit" id="btn-confirm" class="btn btn-primary"
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
        <a href="<?= site_url('pesanan.php') ?>" class="btn btn-primary"
           style="border-radius:var(--radius-sm);">
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
.pay-method-label:has(input:checked) {
  border-color:var(--pink-deep);
  background:var(--pink-soft);
}

/* Bank card grid */
.bank-card {
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  gap:.4rem; padding:.6rem .4rem;
  border:1.5px solid var(--border); border-radius:10px;
  cursor:pointer; background:#fff;
  transition:border-color .15s, box-shadow .15s, background .15s;
  min-height:72px;
}
.bank-card:hover { border-color:var(--pink); box-shadow:0 2px 8px rgba(201,81,111,.12); }
.bank-card.selected {
  border-color:var(--pink-deep);
  background:var(--pink-soft);
  box-shadow:0 2px 10px rgba(201,81,111,.18);
}
.bank-card img {
  width:38px; height:38px; object-fit:contain;
  border-radius:6px;
}
.bank-card span {
  font-size:.65rem; font-weight:600; color:var(--text-muted);
  text-align:center; line-height:1.3;
}

/* Group label */
.bank-group-label {
  font-size:.7rem; font-weight:700; letter-spacing:.1em;
  text-transform:uppercase; color:var(--text-muted);
  margin:.85rem 0 .4rem;
}
</style>

<script>
/* ══ DATA BANK ══ */
var BANKS = [
  /* Bank Pemerintah */
  { group:'Bank Pemerintah', name:'BRI',     label:'BRI',        logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/BANK_BRI_logo.svg/320px-BANK_BRI_logo.svg.png' },
  { group:'Bank Pemerintah', name:'BNI',     label:'BNI',        logo:'https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/320px-BNI_logo.svg.png' },
  { group:'Bank Pemerintah', name:'Mandiri', label:'Mandiri',    logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/320px-Bank_Mandiri_logo_2016.svg.png' },
  { group:'Bank Pemerintah', name:'BTN',     label:'BTN',        logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Bank_tabungan_negara_logo.svg/320px-Bank_tabungan_negara_logo.svg.png' },
  { group:'Bank Pemerintah', name:'BSI',     label:'BSI',        logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/Bank_Syariah_Indonesia.svg/320px-Bank_Syariah_Indonesia.svg.png' },
  /* Bank Swasta */
  { group:'Bank Swasta',     name:'BCA',     label:'BCA',        logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/320px-Bank_Central_Asia.svg.png' },
  { group:'Bank Swasta',     name:'CIMB',    label:'CIMB Niaga', logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/CIMB_Niaga.svg/320px-CIMB_Niaga.svg.png' },
  { group:'Bank Swasta',     name:'Danamon', label:'Danamon',    logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/2/28/Bank_Danamon.svg/320px-Bank_Danamon.svg.png' },
  { group:'Bank Swasta',     name:'Permata', label:'Permata',    logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Bank_Permata_logo.svg/320px-Bank_Permata_logo.svg.png' },
  { group:'Bank Swasta',     name:'Maybank', label:'Maybank',    logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Maybank_logo.svg/320px-Maybank_logo.svg.png' },
  { group:'Bank Swasta',     name:'OCBC',    label:'OCBC NISP',  logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/5/58/OCBC_NISP.svg/320px-OCBC_NISP.svg.png' },
  { group:'Bank Swasta',     name:'Mega',    label:'Mega',       logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/6/62/Bank_Mega_logo.svg/320px-Bank_Mega_logo.svg.png' },
  /* Bank Digital */
  { group:'Bank Digital',    name:'Jenius',  label:'Jenius',     logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Jenius_logo.svg/320px-Jenius_logo.svg.png' },
  { group:'Bank Digital',    name:'Jago',    label:'Bank Jago',  logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Bank_Jago_Logo.svg/320px-Bank_Jago_Logo.svg.png' },
  { group:'Bank Digital',    name:'Seabank', label:'Seabank',    logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/SeaBank_Logo.svg/320px-SeaBank_Logo.svg.png' },
  /* Dompet Digital */
  { group:'Dompet Digital',  name:'GoPay',      label:'GoPay',      logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Gopay_logo.svg/320px-Gopay_logo.svg.png' },
  { group:'Dompet Digital',  name:'OVO',        label:'OVO',        logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Logo_ovo_purple.svg/320px-Logo_ovo_purple.svg.png' },
  { group:'Dompet Digital',  name:'Dana',       label:'DANA',       logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Logo_dana_blue.svg/320px-Logo_dana_blue.svg.png' },
  { group:'Dompet Digital',  name:'ShopeePay',  label:'ShopeePay',  logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/ShopeePay_logo.svg/320px-ShopeePay_logo.svg.png' },
  { group:'Dompet Digital',  name:'LinkAja',    label:'LinkAja',    logo:'https://upload.wikimedia.org/wikipedia/commons/thumb/8/85/LinkAja.svg/320px-LinkAja.svg.png' },
];

var NOREK        = '901757779319';
var selectedBank = null;

/* ── Build bank grid ── */
(function buildGrid() {
  var grid = document.getElementById('bank-grid');
  if (!grid) return;

  var lastGroup = null;
  var wrapper   = null;
  var groupEl   = null;

  // Clear grid, buat struktur per group
  grid.innerHTML = '';

  BANKS.forEach(function(b) {
    if (b.group !== lastGroup) {
      // Label grup
      var lbl = document.createElement('div');
      lbl.className = 'bank-group-label';
      lbl.textContent = b.group;
      lbl.style.gridColumn = '1 / -1';
      grid.appendChild(lbl);
      lastGroup = b.group;
    }

    var card = document.createElement('div');
    card.className   = 'bank-card';
    card.dataset.name = b.name;
    card.dataset.logo = b.logo;

    var img = document.createElement('img');
    img.src   = b.logo;
    img.alt   = b.label;
    img.onerror = function() {
      // Fallback jika logo tidak load
      this.style.display = 'none';
      card.style.justifyContent = 'center';
    };

    var span = document.createElement('span');
    span.textContent = b.label;

    card.appendChild(img);
    card.appendChild(span);
    card.addEventListener('click', function() { selectBank(b); });
    grid.appendChild(card);
  });
})();

function selectBank(b) {
  // Highlight card terpilih
  document.querySelectorAll('.bank-card').forEach(function(c) {
    c.classList.remove('selected');
    if (c.dataset.name === b.name) c.classList.add('selected');
  });

  selectedBank = b;
  document.getElementById('inp-bank-name').value = 'Bank ' + b.name;

  // Update info rekening
  document.getElementById('rek-bank-name').textContent = 'Bank ' + b.name;
  document.getElementById('rek-logo').src = b.logo;
  document.getElementById('rek-norek').textContent = NOREK;
  document.getElementById('rekening-info').style.display = 'block';
}

function copyRek() {
  navigator.clipboard.writeText(NOREK).then(function() {
    var icon = document.getElementById('copy-icon');
    icon.className = 'bi bi-check2';
    setTimeout(function() { icon.className = 'bi bi-copy'; }, 1800);
  });
}

/* ── Switch metode ── */
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

/* ── Validasi: wajib pilih bank jika Transfer ── */
document.getElementById('payForm') && document.getElementById('payForm').addEventListener('submit', function(e) {
  var method = document.querySelector('input[name="method"]:checked');
  if (method && method.value === 'transfer_bank' && !selectedBank) {
    e.preventDefault();
    alert('Pilih bank tujuan terlebih dahulu.');
    document.getElementById('bank-grid').scrollIntoView({ behavior:'smooth', block:'center' });
  }
});

/* ── Init ── */
document.addEventListener('DOMContentLoaded', function() {
  var checked = document.querySelector('input[name="method"]:checked');
  if (checked) switchMethod(checked);
});
</script>
