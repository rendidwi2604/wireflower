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

          <!-- Transfer Bank -->
          <label class="pay-method-label" data-target="detail-transfer">
            <input type="radio" name="method" value="transfer_bank" checked
                   style="accent-color:var(--pink-deep);" onchange="switchMethod(this)">
            <i class="bi bi-bank" style="font-size:1.15rem;color:var(--pink-deep);"></i>
            <span style="font-weight:500;font-size:.9rem;">Transfer Bank</span>
          </label>

          <!-- COD -->
          <label class="pay-method-label" data-target="detail-cod">
            <input type="radio" name="method" value="cod"
                   style="accent-color:var(--pink-deep);" onchange="switchMethod(this)">
            <i class="bi bi-cash-coin" style="font-size:1.15rem;color:var(--pink-deep);"></i>
            <span style="font-weight:500;font-size:.9rem;">COD (Bayar di Tempat)</span>
          </label>

          <!-- QRIS -->
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

        <div class="form-group" style="margin-bottom:.85rem;">
          <label class="form-label">Pilih Bank Tujuan</label>
          <select name="bank_name" id="sel-bank" class="form-input" style="cursor:pointer;" onchange="updateRekening()">
            <option value="">-- Pilih Bank --</option>
            <!-- Bank Pemerintah -->
            <optgroup label="Bank Pemerintah">
              <option value="Bank BRI">Bank BRI</option>
              <option value="Bank BNI">Bank BNI</option>
              <option value="Bank Mandiri">Bank Mandiri</option>
              <option value="Bank BTN">Bank BTN</option>
              <option value="Bank BSI">Bank BSI (Syariah Indonesia)</option>
            </optgroup>
            <!-- Bank Swasta Nasional -->
            <optgroup label="Bank Swasta Nasional">
              <option value="Bank BCA" selected>Bank BCA</option>
              <option value="Bank CIMB Niaga">Bank CIMB Niaga</option>
              <option value="Bank Danamon">Bank Danamon</option>
              <option value="Bank Permata">Bank Permata</option>
              <option value="Bank Panin">Bank Panin</option>
              <option value="Bank Maybank">Bank Maybank</option>
              <option value="Bank OCBC NISP">Bank OCBC NISP</option>
              <option value="Bank Mega">Bank Mega</option>
              <option value="Bank Sinarmas">Bank Sinarmas</option>
              <option value="Bank Commonwealth">Bank Commonwealth</option>
            </optgroup>
            <!-- Bank Digital -->
            <optgroup label="Bank Digital">
              <option value="Jenius (BTPN)">Jenius (BTPN)</option>
              <option value="Blu by BCA Digital">Blu by BCA Digital</option>
              <option value="Bank Jago">Bank Jago</option>
              <option value="Seabank">Seabank</option>
              <option value="Neobank">Neobank</option>
              <option value="Allo Bank">Allo Bank</option>
            </optgroup>
            <!-- E-Wallet / Dompet Digital -->
            <optgroup label="Dompet Digital">
              <option value="GoPay">GoPay</option>
              <option value="OVO">OVO</option>
              <option value="Dana">Dana</option>
              <option value="ShopeePay">ShopeePay</option>
              <option value="LinkAja">LinkAja</option>
            </optgroup>
          </select>
        </div>

        <!-- Info rekening tujuan -->
        <div id="rekening-info" style="background:#f0faf5;border:1px solid #b7e4cc;border-radius:var(--radius-sm);padding:1rem 1.1rem;font-size:.875rem;">
          <div style="font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#1a6b3a;margin-bottom:.65rem;">
            <i class="bi bi-info-circle"></i> Rekening Tujuan
          </div>
          <table style="width:100%;border-collapse:collapse;">
            <tr>
              <td style="color:#555;padding:.2rem 0;width:40%;">Bank</td>
              <td style="font-weight:700;color:#1e1212;" id="rek-bank">Bank BCA</td>
            </tr>
            <tr>
              <td style="color:#555;padding:.2rem 0;">No. Rekening</td>
              <td>
                <span style="font-weight:700;font-size:1rem;color:#1e1212;letter-spacing:.05em;" id="rek-norek">901757779319</span>
                <button type="button" onclick="copyRek()" title="Salin nomor rekening"
                        style="background:none;border:none;cursor:pointer;color:var(--pink-deep);font-size:.85rem;margin-left:.4rem;vertical-align:middle;">
                  <i class="bi bi-copy" id="copy-icon"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td style="color:#555;padding:.2rem 0;">Atas Nama</td>
              <td style="font-weight:600;color:#1e1212;">WireFlower</td>
            </tr>
            <tr>
              <td style="color:#555;padding:.2rem 0;">Jumlah Transfer</td>
              <td style="font-weight:700;color:var(--pink-deep);"><?= rupiah($order['total']) ?></td>
            </tr>
          </table>
          <div style="margin-top:.75rem;font-size:.75rem;color:#555;line-height:1.5;">
            <i class="bi bi-exclamation-triangle" style="color:#e67e22;"></i>
            Transfer sesuai jumlah di atas. Pesanan dikonfirmasi setelah admin memverifikasi pembayaran.
          </div>
        </div>

        <!-- Input bukti transfer -->
        <div class="form-group" style="margin-top:.85rem;margin-bottom:0;">
          <label class="form-label">Nama Pengirim / Catatan Transfer <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
          <input type="text" name="transfer_note" class="form-input"
                 placeholder="Mis: Budi Santoso / WF-20260930">
        </div>
      </div>

      <!-- ── Detail COD ── -->
      <div id="detail-cod" class="pay-detail" style="display:none;margin-bottom:1.25rem;">
        <div style="background:#fffbea;border:1px solid #f0d070;border-radius:var(--radius-sm);padding:1rem 1.1rem;font-size:.875rem;">
          <div style="font-weight:700;margin-bottom:.4rem;"><i class="bi bi-truck" style="color:#c07a00;"></i> Bayar di Tempat (COD)</div>
          <ul style="margin:.4rem 0 0 1rem;padding:0;color:#555;line-height:1.8;font-size:.83rem;">
            <li>Siapkan uang tunai sesuai total pesanan</li>
            <li>Pembayaran dilakukan kepada kurir saat barang tiba</li>
            <li>Pastikan nomor telepon aktif agar kurir bisa menghubungi</li>
          </ul>
        </div>
      </div>

      <!-- ── Detail QRIS ── -->
      <div id="detail-qris" class="pay-detail" style="display:none;margin-bottom:1.25rem;">
        <div style="background:#f5f0ff;border:1px solid #c9b8f0;border-radius:var(--radius-sm);padding:1rem 1.1rem;text-align:center;font-size:.875rem;">
          <div style="font-weight:700;margin-bottom:.65rem;color:#5a3ea0;"><i class="bi bi-qr-code-scan"></i> Scan QRIS</div>
          <!-- Placeholder QR — ganti src dengan gambar QRIS asli -->
          <div style="width:180px;height:180px;margin:0 auto .75rem;background:#e8e0ff;border-radius:10px;
                      display:flex;align-items:center;justify-content:center;color:#5a3ea0;font-size:3rem;">
            <i class="bi bi-qr-code"></i>
          </div>
          <div style="color:#555;font-size:.8rem;line-height:1.6;">
            Scan dengan aplikasi apapun yang mendukung QRIS<br>
            (GoPay, OVO, Dana, ShopeePay, m-Banking, dll.)
          </div>
          <div style="margin-top:.65rem;font-weight:700;color:#5a3ea0;"><?= rupiah($order['total']) ?></div>
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
</style>

<script>
function switchMethod(radio) {
  // Sembunyikan semua detail
  document.querySelectorAll('.pay-detail').forEach(function(el) {
    el.style.display = 'none';
  });
  // Tampilkan detail yang sesuai
  var target = radio.closest('.pay-method-label').dataset.target;
  if (target) {
    var el = document.getElementById(target);
    if (el) el.style.display = 'block';
  }
}

// Nomor rekening tetap (semua bank pakai no rek yang sama)
var NOREK = '901757779319';

function updateRekening() {
  var sel   = document.getElementById('sel-bank');
  var bank  = sel.value || 'Bank BCA';
  document.getElementById('rek-bank').textContent = bank;
  document.getElementById('rek-norek').textContent = NOREK;
}

function copyRek() {
  navigator.clipboard.writeText(NOREK).then(function() {
    var icon = document.getElementById('copy-icon');
    icon.className = 'bi bi-check2';
    setTimeout(function() { icon.className = 'bi bi-copy'; }, 1800);
  });
}

// Init — tampilkan detail transfer saat load
document.addEventListener('DOMContentLoaded', function() {
  var checked = document.querySelector('input[name="method"]:checked');
  if (checked) switchMethod(checked);
});
</script>
