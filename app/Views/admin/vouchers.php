<?php
$typeLabels = [
    'free_shipping' => ['label'=>'Gratis Ongkir', 'icon'=>'bi-truck',        'color'=>'#0d6efd', 'bg'=>'#e7f0ff'],
    'percent'       => ['label'=>'Diskon %',       'icon'=>'bi-percent',      'color'=>'#6f42c1', 'bg'=>'#f3eeff'],
    'fixed'         => ['label'=>'Potongan Rp',    'icon'=>'bi-cash-coin',    'color'=>'#c9516f', 'bg'=>'#fce8ef'],
];
?>

<style>
.vc-table-wrap { overflow-x:auto; border-radius:10px; }
.vc-table { width:100%; border-collapse:collapse; min-width:720px; }
.vc-table thead th {
  font-size:.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;
  color:#8a7070; padding:.65rem 1rem; border-bottom:2px solid #f0e8e8;
  white-space:nowrap; background:#fdfbfb;
}
.vc-table tbody td {
  padding:.8rem 1rem; border-bottom:1px solid #f5eded;
  font-size:.83rem; vertical-align:middle;
}
.vc-table tbody tr:last-child td { border-bottom:none; }
.vc-table tbody tr:hover td { background:#fdfbfb; }
.vc-code {
  font-family:monospace; font-weight:800; font-size:.88rem; letter-spacing:.06em;
  color:#c9516f; background:#fce8ef; padding:.2rem .6rem; border-radius:6px;
}
.vc-type-badge {
  display:inline-flex; align-items:center; gap:.3rem;
  padding:.22rem .7rem; border-radius:50px; font-size:.73rem; font-weight:700;
}
.vc-empty { text-align:center; padding:3rem 2rem; color:#8a7070; }
.vc-empty i { font-size:2.5rem; color:#eeddd9; display:block; margin-bottom:.75rem; }

/* Modal form */
.vc-modal-backdrop {
  display:none; position:fixed; inset:0; z-index:1000;
  background:rgba(0,0,0,.5); align-items:center; justify-content:center; padding:1rem;
}
.vc-modal-backdrop.open { display:flex; }
.vc-modal {
  background:#fff; border-radius:14px; width:100%; max-width:560px;
  max-height:90vh; overflow-y:auto;
  box-shadow:0 8px 40px rgba(0,0,0,.25);
}
.vc-modal-header {
  display:flex; align-items:center; justify-content:space-between;
  padding:1.1rem 1.4rem; border-bottom:1px solid #f0e8e8;
}
.vc-modal-title { font-family:Georgia,serif; font-size:1rem; font-weight:700; color:#2c1a1e; }
.vc-modal-close {
  background:none; border:none; font-size:1.3rem; cursor:pointer;
  color:#8a7070; line-height:1; padding:0;
}
.vc-modal-close:hover { color:#c9516f; }
.vc-modal-body { padding:1.25rem 1.4rem; }
.vc-modal-footer { padding:.9rem 1.4rem; border-top:1px solid #f0e8e8; display:flex; gap:.6rem; justify-content:flex-end; }

.vc-form-row { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
@media(max-width:480px) { .vc-form-row { grid-template-columns:1fr; } }

/* Hint muncul sesuai tipe */
.vc-hint { display:none; font-size:.75rem; color:#8a7070; margin-top:.25rem; }
</style>

<!-- Header -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
  <div>
    <div style="font-family:Georgia,serif;font-size:1.1rem;font-weight:700;">Kelola Voucher</div>
    <div style="font-size:.8rem;color:#8a7070;margin-top:.15rem;">Buat dan kelola kode diskon untuk pelanggan</div>
  </div>
  <button class="btn-adm btn-adm-primary" onclick="openModal()">
    <i class="bi bi-plus-lg"></i> Tambah Voucher
  </button>
</div>

<!-- Tabel voucher -->
<div class="adm-card" style="padding:0;overflow:hidden;">
  <?php if (empty($vouchers)): ?>
    <div class="vc-empty">
      <i class="bi bi-ticket-perforated"></i>
      <h5 style="font-family:Georgia,serif;color:#2c1a1e;margin-bottom:.35rem;">Belum ada voucher</h5>
      <p style="font-size:.84rem;">Tambahkan voucher baru untuk memberikan diskon kepada pelanggan.</p>
    </div>
  <?php else: ?>
  <div class="vc-table-wrap">
    <table class="vc-table">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Tipe</th>
          <th>Nilai</th>
          <th>Min. Order</th>
          <th>Pemakaian</th>
          <th>Berlaku</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vouchers as $v):
          $tc = $typeLabels[$v['type']] ?? ['label'=>$v['type'],'icon'=>'bi-tag','color'=>'#888','bg'=>'#f5f5f5'];
        ?>
        <tr>
          <td><span class="vc-code"><?= e($v['code']) ?></span></td>
          <td>
            <span class="vc-type-badge" style="background:<?= $tc['bg'] ?>;color:<?= $tc['color'] ?>;">
              <i class="bi <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
            </span>
          </td>
          <td>
            <?php if ($v['type'] === 'free_shipping'): ?>
              <span style="color:#0d6efd;font-weight:700;">Gratis</span>
            <?php elseif ($v['type'] === 'percent'): ?>
              <strong><?= (int)$v['discount_value'] ?>%</strong>
              <?php if (!empty($v['max_discount'])): ?>
                <div style="font-size:.72rem;color:#8a7070;">maks <?= rupiah($v['max_discount']) ?></div>
              <?php endif; ?>
            <?php else: ?>
              <strong><?= rupiah($v['discount_value']) ?></strong>
            <?php endif; ?>
          </td>
          <td>
            <?= !empty($v['min_order']) ? rupiah($v['min_order']) : '<span style="color:#ccc;">—</span>' ?>
          </td>
          <td>
            <span style="font-weight:600;"><?= (int)$v['used_count'] ?></span>
            <?php if (!empty($v['usage_limit'])): ?>
              <span style="color:#8a7070;font-size:.78rem;">/ <?= (int)$v['usage_limit'] ?></span>
            <?php else: ?>
              <span style="color:#ccc;font-size:.78rem;">/ ∞</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($v['valid_from']) || !empty($v['valid_until'])): ?>
              <div style="font-size:.78rem;color:#5a4040;">
                <?= !empty($v['valid_from'])  ? date('d M Y', strtotime($v['valid_from']))  : '—' ?>
                →
                <?= !empty($v['valid_until']) ? date('d M Y', strtotime($v['valid_until'])) : '∞' ?>
              </div>
            <?php else: ?>
              <span style="color:#ccc;font-size:.78rem;">Selamanya</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($v['is_active']): ?>
              <span class="badge-active"><i class="bi bi-check-circle-fill"></i> Aktif</span>
            <?php else: ?>
              <span class="badge-inactive"><i class="bi bi-pause-circle"></i> Nonaktif</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
              <button class="btn-adm btn-adm-outline btn-adm-sm"
                      onclick="editVoucher(<?= htmlspecialchars(json_encode($v), ENT_QUOTES) ?>)">
                <i class="bi bi-pencil"></i>
              </button>
              <form method="POST" style="margin:0;" onsubmit="return confirm('Toggle status voucher ini?')">
                <input type="hidden" name="voucher_id" value="<?= $v['id'] ?>">
                <button type="submit" name="toggle_voucher" class="btn-adm btn-adm-outline btn-adm-sm"
                        title="<?= $v['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                  <i class="bi bi-<?= $v['is_active'] ? 'pause' : 'play' ?>-fill"></i>
                </button>
              </form>
              <form method="POST" style="margin:0;" onsubmit="return confirm('Hapus voucher <?= e($v['code']) ?>?')">
                <input type="hidden" name="voucher_id" value="<?= $v['id'] ?>">
                <button type="submit" name="delete_voucher" class="btn-adm btn-adm-danger btn-adm-sm">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- ── Modal form tambah/edit ── -->
<div class="vc-modal-backdrop" id="vcModalBackdrop" onclick="closeModalOutside(event)">
  <div class="vc-modal">
    <div class="vc-modal-header">
      <span class="vc-modal-title" id="vcModalTitle">Tambah Voucher</span>
      <button class="vc-modal-close" onclick="closeModal()">×</button>
    </div>
    <form method="POST" action="<?= site_url('admin/voucher.php') ?>">
      <input type="hidden" name="save_voucher" value="1">
      <input type="hidden" name="voucher_id" id="vcId" value="0">

      <div class="vc-modal-body">

        <div class="vc-form-row">
          <!-- Kode -->
          <div class="adm-form-group">
            <label class="adm-form-label">Kode Voucher <span style="color:#c0392b;">*</span></label>
            <input type="text" name="code" id="vcCode" class="adm-form-control"
                   placeholder="Mis: GRATIS10" style="text-transform:uppercase;" required>
            <div class="vc-hint" id="hint-code">Kode akan diubah menjadi huruf kapital otomatis.</div>
          </div>

          <!-- Tipe -->
          <div class="adm-form-group">
            <label class="adm-form-label">Tipe Voucher <span style="color:#c0392b;">*</span></label>
            <select name="type" id="vcType" class="adm-form-control" onchange="onTypeChange(this.value)" required>
              <?php foreach ($types as $key => $label): ?>
                <option value="<?= $key ?>"><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Nilai diskon -->
        <div class="adm-form-group" id="row-discount-value">
          <label class="adm-form-label" id="lbl-discount-value">Nilai Diskon <span style="color:#c0392b;">*</span></label>
          <input type="number" name="discount_value" id="vcDiscountValue"
                 class="adm-form-control" placeholder="0" min="0" step="any">
          <div class="vc-hint" id="hint-discount-value"></div>
        </div>

        <div class="vc-form-row">
          <!-- Maks diskon (khusus persen) -->
          <div class="adm-form-group" id="row-max-discount">
            <label class="adm-form-label">Maks. Diskon (Rp)</label>
            <input type="number" name="max_discount" id="vcMaxDiscount"
                   class="adm-form-control" placeholder="Kosongkan = tidak dibatasi" min="0">
          </div>

          <!-- Min. order -->
          <div class="adm-form-group">
            <label class="adm-form-label">Min. Order (Rp)</label>
            <input type="number" name="min_order" id="vcMinOrder"
                   class="adm-form-control" placeholder="Kosongkan = tidak ada minimum" min="0">
          </div>
        </div>

        <div class="vc-form-row">
          <!-- Batas pemakaian -->
          <div class="adm-form-group">
            <label class="adm-form-label">Batas Pemakaian</label>
            <input type="number" name="usage_limit" id="vcUsageLimit"
                   class="adm-form-control" placeholder="Kosongkan = tidak terbatas" min="1">
          </div>

          <!-- Placeholder kosong agar grid tetap rapi -->
          <div></div>
        </div>

        <div class="vc-form-row">
          <div class="adm-form-group">
            <label class="adm-form-label">Berlaku Dari</label>
            <input type="date" name="valid_from" id="vcValidFrom" class="adm-form-control">
          </div>
          <div class="adm-form-group">
            <label class="adm-form-label">Berlaku Sampai</label>
            <input type="date" name="valid_until" id="vcValidUntil" class="adm-form-control">
          </div>
        </div>

        <!-- Deskripsi -->
        <div class="adm-form-group">
          <label class="adm-form-label">Deskripsi (opsional)</label>
          <input type="text" name="description" id="vcDescription" class="adm-form-control"
                 placeholder="Mis: Voucher spesial Lebaran">
        </div>

        <!-- Status aktif -->
        <div class="adm-form-group" style="display:flex;align-items:center;gap:.6rem;">
          <input type="checkbox" name="is_active" id="vcIsActive" value="1"
                 checked style="width:16px;height:16px;accent-color:#c9516f;cursor:pointer;">
          <label for="vcIsActive" class="adm-form-label" style="margin:0;cursor:pointer;">Aktifkan voucher ini</label>
        </div>

      </div><!-- .vc-modal-body -->

      <div class="vc-modal-footer">
        <button type="button" class="btn-adm btn-adm-outline" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-adm btn-adm-primary">
          <i class="bi bi-check2"></i> <span id="vcSaveLabel">Simpan Voucher</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// ── Modal open/close ──
function openModal(v) {
  document.getElementById('vcModalTitle').textContent = 'Tambah Voucher';
  document.getElementById('vcSaveLabel').textContent  = 'Simpan Voucher';
  document.getElementById('vcId').value            = '0';
  document.getElementById('vcCode').value          = '';
  document.getElementById('vcType').value          = 'fixed';
  document.getElementById('vcDiscountValue').value = '';
  document.getElementById('vcMaxDiscount').value   = '';
  document.getElementById('vcMinOrder').value      = '';
  document.getElementById('vcUsageLimit').value    = '';
  document.getElementById('vcValidFrom').value     = '';
  document.getElementById('vcValidUntil').value    = '';
  document.getElementById('vcDescription').value   = '';
  document.getElementById('vcIsActive').checked    = true;
  onTypeChange('fixed');
  document.getElementById('vcModalBackdrop').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function editVoucher(v) {
  document.getElementById('vcModalTitle').textContent = 'Edit Voucher';
  document.getElementById('vcSaveLabel').textContent  = 'Update Voucher';
  document.getElementById('vcId').value            = v.id;
  document.getElementById('vcCode').value          = v.code;
  document.getElementById('vcType').value          = v.type;
  document.getElementById('vcDiscountValue').value = v.discount_value;
  document.getElementById('vcMaxDiscount').value   = v.max_discount || '';
  document.getElementById('vcMinOrder').value      = v.min_order    || '';
  document.getElementById('vcUsageLimit').value    = v.usage_limit  || '';
  document.getElementById('vcValidFrom').value     = v.valid_from   ? v.valid_from.substring(0,10)  : '';
  document.getElementById('vcValidUntil').value    = v.valid_until  ? v.valid_until.substring(0,10) : '';
  document.getElementById('vcDescription').value   = v.description  || '';
  document.getElementById('vcIsActive').checked    = v.is_active == true || v.is_active === 't' || v.is_active === '1';
  onTypeChange(v.type);
  document.getElementById('vcModalBackdrop').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('vcModalBackdrop').classList.remove('open');
  document.body.style.overflow = '';
}

function closeModalOutside(e) {
  if (e.target === document.getElementById('vcModalBackdrop')) closeModal();
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeModal();
});

// ── Tampilkan/sembunyikan field sesuai tipe ──
function onTypeChange(type) {
  var rowDiscount = document.getElementById('row-discount-value');
  var rowMax      = document.getElementById('row-max-discount');
  var lbl         = document.getElementById('lbl-discount-value');
  var hint        = document.getElementById('hint-discount-value');
  var inp         = document.getElementById('vcDiscountValue');

  if (type === 'free_shipping') {
    rowDiscount.style.display = 'none';
    rowMax.style.display      = 'none';
    inp.required              = false;
  } else if (type === 'percent') {
    rowDiscount.style.display = '';
    rowMax.style.display      = '';
    lbl.innerHTML  = 'Persen Diskon (%) <span style="color:#c0392b;">*</span>';
    hint.textContent = 'Masukkan angka 1–100. Contoh: 10 berarti diskon 10%.';
    hint.style.display = 'block';
    inp.placeholder    = 'Contoh: 10';
    inp.max            = '100';
    inp.required       = true;
  } else {
    // fixed
    rowDiscount.style.display = '';
    rowMax.style.display      = 'none';
    lbl.innerHTML  = 'Potongan Harga (Rp) <span style="color:#c0392b;">*</span>';
    hint.textContent = 'Jumlah potongan dalam Rupiah. Contoh: 20000';
    hint.style.display = 'block';
    inp.placeholder    = 'Contoh: 20000';
    inp.removeAttribute('max');
    inp.required       = true;
  }
}

// Otomatis uppercase kode voucher
document.getElementById('vcCode').addEventListener('input', function() {
  var pos = this.selectionStart;
  this.value = this.value.toUpperCase();
  this.setSelectionRange(pos, pos);
});

// Jika ada voucher yang sedang di-edit dari URL (server side)
<?php if ($editVoucher): ?>
editVoucher(<?= json_encode($editVoucher) ?>);
<?php endif; ?>
</script>
