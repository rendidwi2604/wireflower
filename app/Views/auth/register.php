<?php $role_selected = $_POST['role'] ?? 'customer'; ?>
<div class="auth-layout">

  <!-- ===== LEFT PANEL ===== -->
  <div class="auth-panel-left">
    <div class="auth-left-inner">

      <!-- Brand -->
      <a href="<?= site_url('index.php') ?>" class="auth-brand">
        <img src="<?= site_url('assets/img/logoWF.png') ?>" alt="WireFlower Logo" class="auth-brand-img">
        WireFlower
      </a>

      <!-- Floral illustration — bouquet style -->
      <div class="auth-floral-wrap">
        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Main stems -->
          <path d="M100 175 C100 155 96 140 98 115" stroke="#5a8a5a" stroke-width="2.5" stroke-linecap="round"/>
          <path d="M100 175 C92 155 82 145 78 120" stroke="#6aac6a" stroke-width="2" stroke-linecap="round"/>
          <path d="M100 175 C108 155 118 145 122 120" stroke="#6aac6a" stroke-width="2" stroke-linecap="round"/>

          <!-- Left leaf -->
          <path d="M88 138 C72 130 63 115 70 104 C80 116 88 125 88 138Z" fill="#7ab87a" opacity=".8"/>
          <!-- Right leaf -->
          <path d="M112 135 C128 127 137 112 130 101 C120 113 112 122 112 135Z" fill="#6aac6a" opacity=".75"/>

          <!-- Left flower (tulip-ish) -->
          <ellipse cx="78" cy="100" rx="8" ry="18" fill="#e8799a" opacity=".75" transform="rotate(-15 78 110)"/>
          <ellipse cx="78" cy="100" rx="8" ry="18" fill="#e8799a" opacity=".75" transform="rotate(15 78 110)"/>
          <ellipse cx="78" cy="100" rx="8" ry="18" fill="#c9516f" opacity=".6" transform="rotate(0 78 110)"/>
          <ellipse cx="78" cy="100" rx="6" ry="14" fill="#c9516f" opacity=".7" transform="rotate(30 78 110)"/>
          <ellipse cx="78" cy="100" rx="6" ry="14" fill="#c9516f" opacity=".7" transform="rotate(-30 78 110)"/>
          <circle cx="78" cy="97" r="5" fill="#f9d45c" opacity=".9"/>

          <!-- Center flower (rose) -->
          <ellipse cx="100" cy="82" rx="9" ry="20" fill="#e8799a" opacity=".8" transform="rotate(0 100 95)"/>
          <ellipse cx="100" cy="82" rx="9" ry="20" fill="#e8799a" opacity=".8" transform="rotate(45 100 95)"/>
          <ellipse cx="100" cy="82" rx="9" ry="20" fill="#e8799a" opacity=".8" transform="rotate(90 100 95)"/>
          <ellipse cx="100" cy="82" rx="9" ry="20" fill="#e8799a" opacity=".8" transform="rotate(135 100 95)"/>
          <ellipse cx="100" cy="82" rx="9" ry="20" fill="#e8799a" opacity=".8" transform="rotate(180 100 95)"/>
          <ellipse cx="100" cy="82" rx="9" ry="20" fill="#e8799a" opacity=".8" transform="rotate(225 100 95)"/>
          <ellipse cx="100" cy="82" rx="9" ry="20" fill="#e8799a" opacity=".8" transform="rotate(270 100 95)"/>
          <ellipse cx="100" cy="82" rx="9" ry="20" fill="#e8799a" opacity=".8" transform="rotate(315 100 95)"/>
          <!-- inner petals -->
          <ellipse cx="100" cy="87" rx="6" ry="13" fill="#c9516f" opacity=".9" transform="rotate(22.5 100 95)"/>
          <ellipse cx="100" cy="87" rx="6" ry="13" fill="#c9516f" opacity=".9" transform="rotate(67.5 100 95)"/>
          <ellipse cx="100" cy="87" rx="6" ry="13" fill="#c9516f" opacity=".9" transform="rotate(112.5 100 95)"/>
          <ellipse cx="100" cy="87" rx="6" ry="13" fill="#c9516f" opacity=".9" transform="rotate(157.5 100 95)"/>
          <circle cx="100" cy="95" r="10" fill="#fff" opacity=".85"/>
          <circle cx="100" cy="95" r="6" fill="#f9d45c"/>
          <circle cx="100" cy="95" r="3" fill="#e5a820"/>

          <!-- Right flower (daisy-ish) -->
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(0 122 112)"/>
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(40 122 112)"/>
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(80 122 112)"/>
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(120 122 112)"/>
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(160 122 112)"/>
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(200 122 112)"/>
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(240 122 112)"/>
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(280 122 112)"/>
          <ellipse cx="122" cy="100" rx="7" ry="17" fill="#f5c6d5" opacity=".8" transform="rotate(320 122 112)"/>
          <circle cx="122" cy="112" r="7" fill="#f9d45c" opacity=".95"/>
          <circle cx="122" cy="112" r="4" fill="#e5a820"/>

          <!-- Wrap ribbon -->
          <path d="M72 162 Q100 155 128 162 Q118 175 100 178 Q82 175 72 162Z" fill="#fce8ef" stroke="#e8799a" stroke-width="1.2" opacity=".8"/>
          <!-- Ribbon bow -->
          <path d="M95 162 Q88 155 82 158 Q86 163 95 162Z" fill="#e8799a" opacity=".7"/>
          <path d="M105 162 Q112 155 118 158 Q114 163 105 162Z" fill="#e8799a" opacity=".7"/>

          <!-- Sparkles -->
          <circle cx="50" cy="75" r="2.5" fill="#e8799a" opacity=".4"/>
          <circle cx="58" cy="68" r="1.8" fill="#c9516f" opacity=".35"/>
          <circle cx="152" cy="72" r="2.2" fill="#e8799a" opacity=".4"/>
          <circle cx="160" cy="80" r="1.6" fill="#c9516f" opacity=".35"/>
          <circle cx="40" cy="130" r="2" fill="#e8799a" opacity=".35"/>
          <circle cx="162" cy="140" r="1.8" fill="#c9516f" opacity=".32"/>
          <!-- Small floating dots -->
          <circle cx="65" cy="50" r="3" fill="#f9d45c" opacity=".5"/>
          <circle cx="138" cy="48" r="2.5" fill="#f9d45c" opacity=".45"/>
        </svg>
      </div>

      <!-- Copy -->
      <h2>Bergabung &amp;<br>Temukan Koleksimu</h2>
      <p>Daftar gratis dan dapatkan akses ke koleksi bunga kawat bulu handmade terbaik.</p>

      <!-- Badges -->
      <div class="auth-badges">
        <span class="auth-badge"><i class="bi bi-gift"></i> Hadiah Cantik</span>
        <span class="auth-badge"><i class="bi bi-shield-check"></i> Aman &amp; Terpercaya</span>
        <span class="auth-badge"><i class="bi bi-infinity"></i> Tak Pernah Layu</span>
      </div>
    </div>

    <!-- Decorative circles -->
    <div class="auth-deco-circle c1"></div>
    <div class="auth-deco-circle c2"></div>
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
