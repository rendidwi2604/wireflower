<?php
$status_class = [
    'belum_bayar' => 'status-pending',
    'dikemas'     => 'status-processing',
    'dikirim'     => 'status-shipped',
    'selesai'     => 'status-delivered',
    'dibatalkan'  => 'status-cancelled',
];
?>
<div class="container" style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem;">

  <!-- Page title -->
  <div class="page-title-bar" style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
    <div>
      <h1>Pesanan Saya</h1>
      <p>Pantau status pesanan dan riwayat belanjamu di sini.</p>
    </div>
    <a href="<?= site_url('kategori.php') ?>" class="btn btn-primary btn-sm">
      <i class="bi bi-bag-heart"></i> Belanja Lagi
    </a>
  </div>

  <!-- Summary cards -->
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.85rem;margin-bottom:2rem;">
    <?php
    $summary = [
      ['icon'=>'bi-receipt',      'val'=>$order_counts[''],                                      'label'=>'Total Pesanan'],
      ['icon'=>'bi-credit-card',  'val'=>$order_counts['belum_bayar'],                           'label'=>'Belum Bayar'],
      ['icon'=>'bi-box-seam',     'val'=>$order_counts['dikemas'] + $order_counts['dikirim'],    'label'=>'Dalam Proses'],
      ['icon'=>'bi-check2-circle','val'=>$order_counts['selesai'],                               'label'=>'Selesai'],
    ];
    foreach ($summary as $s): ?>
    <div style="display:flex;align-items:center;gap:.75rem;padding:1rem;border:1px solid var(--border);border-radius:var(--radius-md);background:var(--white);">
      <div class="benefit-icon"><i class="bi <?= $s['icon'] ?>"></i></div>
      <div>
        <strong style="font-size:1.2rem;display:block;line-height:1;"><?= $s['val'] ?></strong>
        <small style="color:var(--text-muted);font-size:.72rem;"><?= $s['label'] ?></small>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Status tabs -->
  <div class="tab-nav">
    <?php foreach ($status_tabs as $key => $label): ?>
      <a class="tab-btn <?= $active === $key ? 'active' : '' ?>"
         href="<?= site_url('pesanan.php?status=' . $key) ?>">
        <?= $label ?>
        <?php if ($key !== ''): ?>
          <span class="count">(<?= $order_counts[$key] ?>)</span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($orders)): ?>
    <div style="text-align:center;padding:4rem 2rem;color:var(--text-muted);">
      <i class="bi bi-receipt" style="font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.3;"></i>
      <p style="margin:0;font-size:.9rem;">Belum ada pesanan di kategori ini.</p>
    </div>
  <?php endif; ?>

  <?php foreach ($orders as $o):
    $payment    = $o['payment'] ?? null;
    $isTransfer = $payment && str_starts_with(strtolower($payment['method'] ?? ''), 'transfer');
    $hasProof   = $payment && !empty($payment['transfer_proof']);
  ?>
  <div class="card-wf">
    <!-- Header -->
    <div class="card-wf-header">
      <div>
        <strong style="font-size:.9rem;">#<?= e($o['order_code']) ?></strong>
        <span style="display:block;font-size:.78rem;color:var(--text-muted);margin-top:.15rem;">
          <?= date('d M Y, H:i', strtotime($o['created_at'])) ?>
        </span>
      </div>
      <span class="status-badge <?= $status_class[$o['status']] ?? '' ?>">
        <?= $status_tabs[$o['status']] ?>
      </span>
    </div>

    <!-- Items -->
    <?php foreach ($o['order_items'] as $it): ?>
      <div style="display:flex;justify-content:space-between;font-size:.875rem;padding:.2rem 0;color:var(--text-muted);">
        <span><?= e($it['product_name']) ?> <span style="opacity:.7;">×<?= $it['quantity'] ?></span></span>
        <span><?= rupiah($it['subtotal']) ?></span>
      </div>
    <?php endforeach; ?>

    <!-- Breakdown harga -->
    <div style="height:1px;background:var(--border);margin:.75rem 0 .6rem;"></div>
    <div style="font-size:.82rem;color:var(--text-muted);display:flex;flex-direction:column;gap:.2rem;">
      <div style="display:flex;justify-content:space-between;">
        <span>Subtotal produk</span>
        <span><?= rupiah($o['subtotal']) ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;">
        <span>Ongkos kirim</span>
        <span><?= $o['shipping_cost'] > 0 ? rupiah($o['shipping_cost']) : '<span style="color:#1a6b3a;">Gratis</span>' ?></span>
      </div>
    </div>
    <div style="height:1px;background:var(--border);margin:.6rem 0 .75rem;"></div>

    <!-- Total + actions -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
      <div style="font-weight:700;font-size:.95rem;">
        Total: <?= rupiah($o['total']) ?>
      </div>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;">

        <?php if ($o['status'] === 'belum_bayar'): ?>
          <a href="<?= site_url('pembayaran.php?order_id=' . $o['id']) ?>"
             class="btn btn-primary btn-sm">Bayar Sekarang</a>
          <button type="button" class="btn btn-sm"
                  style="border:1px solid #e0a0a0;color:#c0392b;background:transparent;border-radius:50px;"
                  onclick="document.getElementById('cancelModal<?= $o['id'] ?>').style.display='flex'">
            Batalkan
          </button>

        <?php elseif ($o['status'] === 'dikemas'): ?>
          <button type="button" class="btn btn-sm"
                  style="border:1px solid #e0a0a0;color:#c0392b;background:transparent;border-radius:50px;"
                  onclick="document.getElementById('cancelModal<?= $o['id'] ?>').style.display='flex'">
            <i class="bi bi-x-circle"></i> Batalkan Pesanan
          </button>

        <?php elseif ($o['status'] === 'dikirim'): ?>
          <form method="POST">
            <input type="hidden" name="complete_order_id" value="<?= $o['id'] ?>">
            <button class="btn btn-primary btn-sm">
              <i class="bi bi-check2"></i> Pesanan Diterima
            </button>
          </form>

        <?php elseif ($o['status'] === 'selesai'): ?>
          <?php foreach ($o['order_items'] as $it): ?>
            <a href="<?= site_url('ulasan.php?order_item_id=' . $it['id'] . '&product_id=' . $it['product_id']) ?>"
               class="btn btn-sm" style="border:1px solid var(--border);background:var(--white);color:var(--text);border-radius:50px;">
              <i class="bi bi-star"></i> Beri Ulasan
            </a>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>

    <!-- Bukti transfer — hanya untuk pembayaran transfer yang pending -->
    <?php if ($o['status'] === 'belum_bayar' && $isTransfer): ?>
    <div style="margin-top:.85rem;background:#fffbea;border:1px solid #f0d070;border-radius:var(--radius-sm);padding:.9rem 1rem;font-size:.85rem;">
      <?php if ($hasProof): ?>
        <div style="display:flex;align-items:center;gap:.6rem;color:#1a6b3a;">
          <i class="bi bi-check-circle-fill" style="font-size:1.1rem;"></i>
          <span><strong>Bukti transfer sudah dikirim.</strong> Menunggu verifikasi admin.</span>
          <a href="<?= e($payment['transfer_proof']) ?>" target="_blank"
             style="margin-left:auto;font-size:.78rem;color:var(--pink-deep);text-decoration:underline;">
            Lihat bukti
          </a>
        </div>
      <?php else: ?>
        <div style="font-weight:600;margin-bottom:.55rem;display:flex;align-items:center;gap:.4rem;">
          <i class="bi bi-upload" style="color:#c07a00;"></i>
          Upload Bukti Transfer
        </div>
        <p style="color:#666;margin:0 0 .65rem;font-size:.78rem;line-height:1.5;">
          Sudah transfer? Upload bukti pembayaran agar admin dapat memverifikasi lebih cepat.
          Format: JPG, PNG, WebP, atau PDF. Maks 5 MB.
        </p>
        <form method="POST" enctype="multipart/form-data" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
          <input type="hidden" name="proof_order_id" value="<?= $o['id'] ?>">
          <input type="file" name="transfer_proof" accept=".jpg,.jpeg,.png,.webp,.pdf"
                 required
                 style="font-size:.8rem;border:1px solid #ddc;border-radius:6px;padding:.3rem .5rem;background:#fff;flex:1;min-width:0;">
          <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap;">
            <i class="bi bi-upload"></i> Kirim Bukti
          </button>
        </form>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div><!-- end .card-wf -->

  <!-- Cancel modal — untuk belum_bayar DAN dikemas -->
  <?php if (in_array($o['status'], ['belum_bayar', 'dikemas'])): ?>
  <div id="cancelModal<?= $o['id'] ?>"
       style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:var(--white);border-radius:var(--radius-lg);padding:1.75rem;max-width:440px;width:calc(100% - 2rem);box-shadow:0 20px 60px rgba(0,0,0,.18);">
      <h4 style="font-family:Georgia,serif;margin-bottom:.4rem;">Batalkan Pesanan</h4>
      <?php if ($o['status'] === 'dikemas'): ?>
        <p style="font-size:.82rem;color:#c0392b;margin-bottom:1rem;">
          <i class="bi bi-exclamation-triangle"></i>
          Pesanan sudah dalam proses pengemasan. Pembatalan mungkin tidak selalu bisa diproses.
        </p>
      <?php else: ?>
        <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:1rem;">
          Pesanan akan dibatalkan dan tidak dapat dikembalikan ke status sebelumnya.
        </p>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="cancel_order_id" value="<?= $o['id'] ?>">
        <div class="form-group">
          <label class="form-label">Alasan pembatalan</label>
          <select name="cancel_reason" class="form-input" required style="cursor:pointer;">
            <option value="">Pilih alasan...</option>
            <?php foreach ($cancel_reasons ?? [] as $reason): ?>
              <option><?= e($reason) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Catatan tambahan
            <span style="font-weight:400;color:var(--text-muted);">(opsional)</span>
          </label>
          <textarea name="cancel_details" class="form-input" rows="3"
                    style="resize:vertical;" placeholder="Tulis keterangan jika diperlukan..."></textarea>
        </div>
        <div style="display:flex;gap:.5rem;justify-content:flex-end;margin-top:1rem;">
          <button type="button" class="btn btn-sm"
                  style="border:1px solid var(--border);background:var(--white);color:var(--text);border-radius:50px;"
                  onclick="document.getElementById('cancelModal<?= $o['id'] ?>').style.display='none'">
            Kembali
          </button>
          <button type="submit" class="btn btn-sm"
                  style="background:#c0392b;color:#fff;border:none;border-radius:50px;">
            Konfirmasi Pembatalan
          </button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <?php endforeach; ?>

</div>
