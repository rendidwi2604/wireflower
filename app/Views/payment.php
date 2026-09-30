<div class="container" style="max-width:540px;margin:0 auto;padding:2rem 1.5rem;">

  <div class="page-title-bar">
    <h1>Pembayaran</h1>
    <p>Pesanan #<?= e($order['order_code']) ?></p>
  </div>

  <div class="card-wf">
    <!-- Amount -->
    <div style="text-align:center;padding:1.5rem 1rem 1.25rem;border-bottom:1px solid var(--border);margin-bottom:1.25rem;">
      <div style="font-size:.82rem;color:var(--text-muted);margin-bottom:.35rem;">Total Pembayaran</div>
      <div style="font-family:Georgia,serif;font-size:2.2rem;font-weight:700;"><?= rupiah($order['total']) ?></div>
      <div style="margin-top:.65rem;">
        <?php if ($payment['status'] === 'success'): ?>
          <span class="status-badge status-delivered"><i class="bi bi-check-circle-fill"></i> Sudah Dibayar</span>
        <?php else: ?>
          <span class="status-badge status-pending"><i class="bi bi-clock"></i> Menunggu Pembayaran</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($payment['status'] === 'pending'): ?>
      <form method="POST">
        <div class="form-group">
          <label class="form-label">Metode Pembayaran</label>
          <div style="display:flex;flex-direction:column;gap:.5rem;">
            <?php
            $methods = [
              'transfer_bank' => ['icon'=>'bi-bank','label'=>'Transfer Bank'],
              'e_wallet'      => ['icon'=>'bi-phone','label'=>'E-Wallet'],
              'cod'           => ['icon'=>'bi-cash','label'=>'COD (Bayar di Tempat)'],
            ];
            foreach ($methods as $val => $m): ?>
            <label style="display:flex;align-items:center;gap:.75rem;padding:.85rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;transition:border-color .15s;"
                   onmouseenter="this.style.borderColor='var(--pink)'" onmouseleave="this.style.borderColor='var(--border)'">
              <input type="radio" name="method" value="<?= $val ?>"
                     <?= $val === 'transfer_bank' ? 'checked' : '' ?>
                     style="accent-color:var(--pink-deep);">
              <i class="bi <?= $m['icon'] ?>" style="font-size:1.15rem;color:var(--pink-deep);"></i>
              <span style="font-weight:500;font-size:.9rem;"><?= $m['label'] ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div style="background:var(--pink-soft);border-radius:var(--radius-sm);padding:.85rem 1rem;margin:.5rem 0 1.25rem;font-size:.82rem;color:var(--pink-deep);">
          <i class="bi bi-info-circle"></i>
          Ini adalah simulasi pembayaran. Klik tombol di bawah untuk menandai pesanan sudah dibayar.
        </div>

        <button type="submit" class="btn btn-primary"
                style="width:100%;justify-content:center;border-radius:var(--radius-sm);">
          <i class="bi bi-check2"></i> Konfirmasi Pembayaran
        </button>
      </form>

    <?php else: ?>
      <div style="text-align:center;padding:1rem 0;">
        <i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#1a6b3a;display:block;margin-bottom:.75rem;"></i>
        <p style="font-size:.9rem;color:var(--text-muted);margin-bottom:1.25rem;">
          Pembayaran dikonfirmasi pada <?= date('d M Y, H:i', strtotime($payment['paid_at'])) ?>.
        </p>
        <a href="<?= site_url('pesanan.php') ?>" class="btn btn-primary"
           style="border-radius:var(--radius-sm);">
          <i class="bi bi-receipt"></i> Lihat Pesanan Saya
        </a>
      </div>
    <?php endif; ?>
  </div>

</div>
