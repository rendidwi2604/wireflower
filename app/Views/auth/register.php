<?php $role_selected = $_POST['role'] ?? 'customer'; ?>
<div class="auth-layout">

  <!-- ===== LEFT PANEL ===== -->
  <div class="auth-panel-left">
    <!-- Full garden photo background -->
    <img
      src="<?= site_url('assets/img/bungatangan.png') ?>"
      alt="Bunga Tangan WireFlower"
      class="auth-garden-bg"
    >
    <!-- Gradient overlay for readability -->
    <div class="auth-garden-overlay"></div>

    <div class="auth-left-inner">
      <!-- Brand -->
      <a href="<?= site_url('index.php') ?>" class="auth-brand" style="color:#fff;">
        <img src="<?= site_url('assets/img/logoWF.png') ?>" alt="WireFlower Logo" class="auth-brand-img">
        WireFlower
      </a>

      <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:1rem;">
        <!-- Floating badge -->
        <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.35rem 1rem;border-radius:50px;
          background:rgba(255,255,255,.18);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.3);
          font-size:.73rem;font-weight:700;color:#fff;letter-spacing:.06em;text-transform:uppercase;">
          <i class="bi bi-flower1"></i> Bergabung Gratis
        </span>

        <!-- Headline -->
        <h2 style="font-family:Georgia,serif;font-size:clamp(1.6rem,3vw,2.2rem);font-weight:700;
          color:#fff;margin:0;line-height:1.2;text-shadow:0 2px 12px rgba(0,0,0,.3);">
          Bergabung &amp;<br>Temukan Koleksimu.
        </h2>
        <p style="font-size:.9rem;color:rgba(255,255,255,.85);max-width:260px;line-height:1.65;
          text-shadow:0 1px 6px rgba(0,0,0,.25);">
          Daftar gratis dan dapatkan akses ke koleksi bunga kawat bulu handmade terbaik.
        </p>

        <!-- Badges -->
        <div class="auth-badges">
          <span class="auth-badge" style="background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.3);color:#fff;backdrop-filter:blur(6px);">
            <i class="bi bi-gift"></i> Hadiah Cantik
          </span>
          <span class="auth-badge" style="background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.3);color:#fff;backdrop-filter:blur(6px);">
            <i class="bi bi-shield-check"></i> Aman &amp; Terpercaya
          </span>
          <span class="auth-badge" style="background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.3);color:#fff;backdrop-filter:blur(6px);">
            <i class="bi bi-infinity"></i> Tak Pernah Layu
          </span>
        </div>
      </div>

      <!-- Bottom: review snippet -->
      <div style="background:rgba(255,255,255,.14);backdrop-filter:blur(10px);
        border:1px solid rgba(255,255,255,.22);border-radius:14px;padding:.85rem 1.1rem;
        display:flex;align-items:center;gap:.75rem;">
        <div style="width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.3);
          flex-shrink:0;display:flex;align-items:center;justify-content:center;
          font-size:1rem;color:#fff;">🌷</div>
        <div>
          <div style="display:flex;gap:.15rem;margin-bottom:.2rem;">
            <?php for($i=0;$i<5;$i++): ?><i class="bi bi-star-fill" style="color:#fbbf24;font-size:.75rem;"></i><?php endfor; ?>
          </div>
          <p style="font-size:.75rem;color:rgba(255,255,255,.9);margin:0;line-height:1.4;">
            "Produknya luar biasa, packaging rapih dan pengiriman cepat!"
          </p>
          <p style="font-size:.68rem;color:rgba(255,255,255,.6);margin:.15rem 0 0;">— Pelanggan WireFlower</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== RIGHT PANEL ===== -->
  <div class="auth-panel-right">
    <div class="auth-form-box">

      <!-- Back link -->
      <a href="<?= site_url('index.php') ?>" class="auth-back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke beranda
      </a>

      <!-- Heading -->
      <span class="auth-eyebrow">Bergabung sekarang</span>
      <h2>Buat Akun<br>WireFlower</h2>
      <p class="subtitle">Isi data di bawah untuk mulai berbelanja. 🌷</p>

      <?php foreach ($errors ?? [] as $err): ?>
        <div class="flash flash-danger" style="margin-bottom:.65rem;">
          <i class="bi bi-exclamation-circle" style="flex-shrink:0;"></i>
          <span><?= e($err) ?></span>
        </div>
      <?php endforeach; ?>

      <form method="POST" autocomplete="on" id="registerForm">

        <!-- Role selector -->
        <div class="form-group">
          <label class="form-label">Daftar sebagai</label>
          <div class="role-tabs">
            <button type="button" id="tabCustomer"
                    class="role-tab <?= $role_selected === 'customer' ? 'active' : '' ?>"
                    onclick="selectRole('customer')">
              <i class="bi bi-bag-heart"></i> Pembeli
            </button>
            <button type="button" id="tabAdmin"
                    class="role-tab <?= $role_selected === 'admin' ? 'active' : '' ?>"
                    onclick="selectRole('admin')">
              <i class="bi bi-shield-lock"></i> Admin
            </button>
          </div>
          <input type="hidden" name="role" id="roleInput" value="<?= e($role_selected) ?>">
        </div>

        <!-- Admin code (hidden by default) -->
        <div class="form-group" id="adminCodeBox"
             style="display:<?= $role_selected === 'admin' ? 'block' : 'none' ?>;">
          <label class="form-label" for="admin_code">Kode Pendaftaran Admin</label>
          <div class="input-icon-wrap">
            <i class="bi bi-key input-icon"></i>
            <input class="form-input" type="text" id="admin_code" name="admin_code"
                   placeholder="Kode rahasia dari pemilik"
                   value="<?= e($_POST['admin_code'] ?? '') ?>">
          </div>
          <small style="font-size:.73rem;color:var(--text-muted);margin-top:.3rem;display:block;">
            <i class="bi bi-info-circle"></i> Kode ini diberikan oleh pemilik website WireFlower.
          </small>
        </div>

        <!-- Name -->
        <div class="form-group">
          <label class="form-label" for="name">Nama Lengkap</label>
          <div class="input-icon-wrap">
            <i class="bi bi-person input-icon"></i>
            <input class="form-input" type="text" id="name" name="name"
                   placeholder="Nama lengkap kamu"
                   value="<?= e($_POST['name'] ?? '') ?>" required autofocus>
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <div class="input-icon-wrap">
            <i class="bi bi-envelope input-icon"></i>
            <input class="form-input" type="email" id="email" name="email"
                   placeholder="nama@email.com"
                   value="<?= e($_POST['email'] ?? '') ?>" required>
          </div>
        </div>

        <!-- Phone -->
        <div class="form-group">
          <label class="form-label" for="phone">
            Nomor Telepon
            <span style="font-weight:400;color:var(--text-muted);">(opsional)</span>
          </label>
          <div class="input-icon-wrap">
            <i class="bi bi-telephone input-icon"></i>
            <input class="form-input" type="text" id="phone" name="phone"
                   placeholder="08xxxxxxxxxx"
                   value="<?= e($_POST['phone'] ?? '') ?>">
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-icon-wrap">
            <i class="bi bi-lock input-icon"></i>
            <input class="form-input" type="password" id="password" name="password"
                   placeholder="Minimal 6 karakter" required
                   oninput="checkPwStrength(this.value)"
                   style="padding-right:2.8rem;">
            <button type="button" class="input-icon-right" onclick="togglePw(this)" tabindex="-1" aria-label="Tampilkan password">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <!-- Strength bar -->
          <div class="pw-strength-bar">
            <div class="pw-strength-fill" id="pwStrengthFill"></div>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label class="form-label" for="confirm_password">Konfirmasi Password</label>
          <div class="input-icon-wrap">
            <i class="bi bi-lock-fill input-icon"></i>
            <input class="form-input" type="password" id="confirm_password" name="confirm_password"
                   placeholder="Ulangi password" required
                   style="padding-right:2.8rem;">
            <button type="button" class="input-icon-right" onclick="togglePw(this)" tabindex="-1" aria-label="Tampilkan password">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="auth-submit-btn">
          <i class="bi bi-person-plus"></i>
          Buat Akun Sekarang
        </button>
      </form>

      <p class="auth-terms">
        Dengan mendaftar, kamu menyetujui
        <a href="#">Syarat &amp; Ketentuan</a> dan
        <a href="#">Kebijakan Privasi</a> WireFlower.
      </p>

      <div class="auth-footer">
        Sudah punya akun?
        <a href="<?= site_url('auth/login.php') ?>">Masuk di sini →</a>
      </div>

    </div>
  </div>

</div>

<script>
function selectRole(role) {
  document.getElementById('roleInput').value = role;
  document.getElementById('adminCodeBox').style.display = role === 'admin' ? 'block' : 'none';
  document.getElementById('tabCustomer').classList.toggle('active', role === 'customer');
  document.getElementById('tabAdmin').classList.toggle('active', role === 'admin');
}

function togglePw(btn) {
  const input = btn.closest('.input-icon-wrap').querySelector('input');
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'bi bi-eye';
  }
}

function checkPwStrength(val) {
  const fill = document.getElementById('pwStrengthFill');
  if (!fill) return;
  let score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const pct   = (score / 5) * 100;
  const color = score <= 1 ? '#e74c3c' : score <= 3 ? '#f39c12' : '#27ae60';
  fill.style.width      = pct + '%';
  fill.style.background = color;
}
</script>
