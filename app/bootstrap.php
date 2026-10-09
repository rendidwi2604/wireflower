<?php
// =====================================================
// BOOTSTRAP APLIKASI - dipanggil setiap entry script
// =====================================================
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');

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
    // Pastikan session stabil di XAMPP Windows
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', '7200'); // 2 jam
    ini_set('session.cookie_lifetime', '0');   // sampai browser ditutup
    session_start();
}

require_once APP_PATH . '/helpers.php';
