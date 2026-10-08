<div class="auth-layout">

  <!-- ===== LEFT PANEL ===== -->
  <div class="auth-panel-left">
    <!-- Full garden photo background -->
    <img
      src="https://images.unsplash.com/photo-1523741543316-beb7fc7023d8?auto=format&fit=crop&w=900&q=85"
      alt="Flower garden"
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
          <i class="bi bi-flower1"></i> Handmade Wire Flower
        </span>

        <!-- Headline -->
        <h2 style="font-family:Georgia,serif;font-size:clamp(1.6rem,3vw,2.2rem);font-weight:700;
          color:#fff;margin:0;line-height:1.2;text-shadow:0 2px 12px rgba(0,0,0,.3);">
          Bunga yang<br>Tak Pernah Layu.
        </h2>
        <p style="font-size:.9rem;color:rgba(255,255,255,.85);max-width:260px;line-height:1.65;
          text-shadow:0 1px 6px rgba(0,0,0,.25);">
          Dibuat satu per satu dengan kawat bulu premium — hadiah abadi yang selalu memesona.
        </p>

        <!-- Badges -->
        <div class="auth-badges">
          <span class="auth-badge" style="background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.3);color:#fff;backdrop-filter:blur(6px);">
            <i class="bi bi-heart-fill"></i> Handmade
          </span>
          <span class="auth-badge" style="background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.3);color:#fff;backdrop-filter:blur(6px);">
            <i class="bi bi-truck"></i> Pengiriman Cepat
          </span>
          <span class="auth-badge" style="background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.3);color:#fff;backdrop-filter:blur(6px);">
            <i class="bi bi-star-fill"></i> Terpercaya
          </span>
        </div>
      </div>

      <!-- Bottom: review snippet -->
      <div style="background:rgba(255,255,255,.14);backdrop-filter:blur(10px);
        border:1px solid rgba(255,255,255,.22);border-radius:14px;padding:.85rem 1.1rem;
        display:flex;align-items:center;gap:.75rem;">
        <div style="width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.3);
          flex-shrink:0;display:flex;align-items:center;justify-content:center;
          font-size:1rem;color:#fff;">🌸</div>
        <div>
          <div style="display:flex;gap:.15rem;margin-bottom:.2rem;">
            <?php for($i=0;$i<5;$i++): ?><i class="bi bi-star-fill" style="color:#fbbf24;font-size:.75rem;"></i><?php endfor; ?>
          </div>
          <p style="font-size:.75rem;color:rgba(255,255,255,.9);margin:0;line-height:1.4;">
            "Bunganya cantik banget, awet dan cocok buat hadiah!"
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
