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
    // Session tahan lama — 30 hari
    $lifetime = 60 * 60 * 24 * 30; // 30 hari dalam detik
    ini_set('session.cookie_path',     '/');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime',  (string) $lifetime);
    ini_set('session.gc_probability',  '1');
    ini_set('session.gc_divisor',      '100');
    session_set_cookie_params([
        'lifetime' => $lifetime, // cookie persist 30 hari
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once APP_PATH . '/helpers.php';
