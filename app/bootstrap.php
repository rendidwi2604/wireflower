<?php
// =====================================================
// BOOTSTRAP APLIKASI - dipanggil setiap entry script
// =====================================================
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');

// Aktifkan output buffering agar header/session selalu bisa dikirim
if (!ob_get_level()) {
    ob_start();
}

spl_autoload_register(function (string $class) {
    if (strncmp($class, 'App\\', 4) !== 0) {
        return;
    }
    $file = APP_PATH . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    $lifetime = 60 * 60 * 24 * 30; // 30 hari dalam detik

    // Deteksi environment
    $isVercel = !empty($_SERVER['VERCEL']) || !empty(getenv('VERCEL'));
    $isHttps  = $isVercel
                || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    // Konfigurasi session
    ini_set('session.cookie_path',     '/');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime',  (string) $lifetime);
    ini_set('session.gc_probability',  '1');
    ini_set('session.gc_divisor',      '100');

    // Di Vercel (HTTPS) pakai SameSite=None;Secure agar cookie tidak di-block
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => $isHttps ? 'None' : 'Lax',
    ]);

    // Database session handler — session tersimpan di PostgreSQL Supabase
    require_once APP_PATH . '/helpers.php';
    try {
        $handler = new App\Core\DatabaseSessionHandler(db(), $lifetime);
        session_set_save_handler($handler, true);
    } catch (\Throwable $e) {
        // Fallback ke file session jika DB belum siap
    }

    session_start();

    // Perpanjang cookie setiap request agar tidak expired
    if (!empty($_COOKIE[session_name()])) {
        setcookie(
            session_name(),
            session_id(),
            [
                'expires'  => time() + $lifetime,
                'path'     => '/',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => $isHttps ? 'None' : 'Lax',
            ]
        );
    }
}
