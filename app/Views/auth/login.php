<div class="auth-layout">

  <!-- ===== LEFT PANEL ===== -->
  <div class="auth-panel-left">
    <div class="auth-left-inner">

      <!-- Brand -->
      <a href="<?= site_url('index.php') ?>" class="auth-brand">
        <img src="<?= site_url('assets/img/logoWF.png') ?>" alt="WireFlower Logo" class="auth-brand-img">
        WireFlower
      </a>

      <!-- Floral illustration -->
      <div class="auth-floral-wrap">
        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Stem -->
          <path d="M100 170 C100 140 95 120 100 90" stroke="#5a8a5a" stroke-width="3" stroke-linecap="round"/>
          <!-- Leaves -->
          <path d="M100 130 C85 120 75 105 82 95 C90 105 100 115 100 130Z" fill="#7ab87a" opacity=".85"/>
          <path d="M100 115 C115 105 125 90 118 80 C110 90 100 100 100 115Z" fill="#6aac6a" opacity=".75"/>
          <!-- Petals layer 1 (outer) -->
          <ellipse cx="100" cy="68" rx="10" ry="22" fill="#e8799a" opacity=".7" transform="rotate(0 100 90)"/>
          <ellipse cx="100" cy="68" rx="10" ry="22" fill="#e8799a" opacity=".7" transform="rotate(45 100 90)"/>
          <ellipse cx="100" cy="68" rx="10" ry="22" fill="#e8799a" opacity=".7" transform="rotate(90 100 90)"/>
          <ellipse cx="100" cy="68" rx="10" ry="22" fill="#e8799a" opacity=".7" transform="rotate(135 100 90)"/>
          <ellipse cx="100" cy="68" rx="10" ry="22" fill="#e8799a" opacity=".7" transform="rotate(180 100 90)"/>
          <ellipse cx="100" cy="68" rx="10" ry="22" fill="#e8799a" opacity=".7" transform="rotate(225 100 90)"/>
          <ellipse cx="100" cy="68" rx="10" ry="22" fill="#e8799a" opacity=".7" transform="rotate(270 100 90)"/>
          <ellipse cx="100" cy="68" rx="10" ry="22" fill="#e8799a" opacity=".7" transform="rotate(315 100 90)"/>
          <!-- Petals layer 2 (inner) -->
          <ellipse cx="100" cy="74" rx="7" ry="16" fill="#c9516f" opacity=".85" transform="rotate(22.5 100 90)"/>
          <ellipse cx="100" cy="74" rx="7" ry="16" fill="#c9516f" opacity=".85" transform="rotate(67.5 100 90)"/>
          <ellipse cx="100" cy="74" rx="7" ry="16" fill="#c9516f" opacity=".85" transform="rotate(112.5 100 90)"/>
          <ellipse cx="100" cy="74" rx="7" ry="16" fill="#c9516f" opacity=".85" transform="rotate(157.5 100 90)"/>
          <ellipse cx="100" cy="74" rx="7" ry="16" fill="#c9516f" opacity=".85" transform="rotate(202.5 100 90)"/>
          <ellipse cx="100" cy="74" rx="7" ry="16" fill="#c9516f" opacity=".85" transform="rotate(247.5 100 90)"/>
          <ellipse cx="100" cy="74" rx="7" ry="16" fill="#c9516f" opacity=".85" transform="rotate(292.5 100 90)"/>
          <ellipse cx="100" cy="74" rx="7" ry="16" fill="#c9516f" opacity=".85" transform="rotate(337.5 100 90)"/>
          <!-- Center -->
          <circle cx="100" cy="90" r="13" fill="#fff" opacity=".9"/>
          <circle cx="100" cy="90" r="8" fill="#f9d45c"/>
          <circle cx="100" cy="90" r="4" fill="#e5a820"/>
          <!-- Small accent flower top-right -->
          <circle cx="155" cy="45" r="5" fill="#e8799a" opacity=".5"/>
          <circle cx="148" cy="38" r="3.5" fill="#e8799a" opacity=".4"/>
          <circle cx="162" cy="38" r="3.5" fill="#e8799a" opacity=".4"/>
          <circle cx="148" cy="52" r="3.5" fill="#e8799a" opacity=".4"/>
          <circle cx="162" cy="52" r="3.5" fill="#e8799a" opacity=".4"/>
          <!-- Small accent flower bottom-left -->
          <circle cx="45" cy="150" r="4" fill="#c9516f" opacity=".4"/>
          <circle cx="39" cy="144" r="2.8" fill="#c9516f" opacity=".35"/>
          <circle cx="51" cy="144" r="2.8" fill="#c9516f" opacity=".35"/>
          <circle cx="39" cy="156" r="2.8" fill="#c9516f" opacity=".35"/>
          <circle cx="51" cy="156" r="2.8" fill="#c9516f" opacity=".35"/>
          <!-- Dotted sparkles -->
          <circle cx="60" cy="55" r="2.5" fill="#e8799a" opacity=".45"/>
          <circle cx="145" cy="135" r="2" fill="#c9516f" opacity=".4"/>
          <circle cx="165" cy="100" r="1.8" fill="#e8799a" opacity=".35"/>
          <circle cx="35" cy="90" r="2" fill="#c9516f" opacity=".38"/>
        </svg>
      </div>

      <!-- Copy -->
      <h2>Bunga yang<br>Tak Pernah Layu.</h2>
      <p>Handmade wire flower, dibuat satu per satu untuk hadiah yang bisa bertahan lebih lama.</p>

      <!-- Badges -->
      <div class="auth-badges">
        <span class="auth-badge"><i class="bi bi-heart-fill"></i> Handmade</span>
        <span class="auth-badge"><i class="bi bi-truck"></i> Pengiriman Cepat</span>
        <span class="auth-badge"><i class="bi bi-star-fill"></i> Terpercaya</span>
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
      <span class="auth-eyebrow">Selamat datang kembali</span>
      <h2>Masuk ke akun<br>WireFlower</h2>
      <p class="subtitle">Lanjutkan belanja bunga favoritmu. 🌸</p>

      <?php if ($error): ?>
        <div class="flash flash-danger" style="margin-bottom:1.25rem;">
          <i class="bi bi-exclamation-circle" style="flex-shrink:0;"></i>
          <span><?= e($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="on">

        <!-- Email -->
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <div class="input-icon-wrap">
            <i class="bi bi-envelope input-icon"></i>
            <input class="form-input" type="email" id="email" name="email"
                   placeholder="nama@email.com" required autofocus
                   value="<?= e($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-icon-wrap">
            <i class="bi bi-lock input-icon"></i>
            <input class="form-input" type="password" id="password" name="password"
                   placeholder="••••••••" required style="padding-right:2.8rem;">
            <button type="button" class="input-icon-right" onclick="togglePw(this)" tabindex="-1" aria-label="Tampilkan password">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="auth-submit-btn">
          <i class="bi bi-box-arrow-in-right"></i>
          Masuk Sekarang
        </button>
      </form>

      <!-- Google OAuth -->
      <div class="form-divider">atau masuk dengan</div>

      <?php if (has_google_oauth_config()): ?>
        <a href="<?= site_url('auth/google-login.php') ?>" class="btn-google">
          <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          Lanjutkan dengan Google
        </a>
      <?php else: ?>
        <button type="button" class="btn-google" disabled>
          <svg class="google-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          Lanjutkan dengan Google
        </button>
        <p style="text-align:center;font-size:.73rem;color:var(--text-muted);margin-top:.5rem;">
          <i class="bi bi-info-circle"></i> Google Login belum tersedia di lingkungan ini.
        </p>
      <?php endif; ?>

      <div class="auth-footer">
        Belum punya akun?
        <a href="<?= site_url('auth/register.php') ?>">Daftar gratis →</a>
      </div>

    </div>
  </div>

</div>

<script>
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
</script>
