<?php
/**
 * Vercel Entry Point — semua request masuk ke sini
 * Router sederhana berdasarkan REQUEST_URI
 */

// BASE_PATH menunjuk ke root project (satu level di atas api/)
// Definisikan hanya jika belum ada (bootstrap.php juga mendefinisikan ini)
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
if (!defined('APP_PATH'))  define('APP_PATH',  BASE_PATH . '/app');
if (!defined('VIEW_PATH')) define('VIEW_PATH', APP_PATH . '/Views');

// Bootstrap — setup autoload, session handler (DB), helpers
require_once APP_PATH . '/bootstrap.php';

// ── Router ──────────────────────────────────────────────────────────────────
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = '/' . trim($uri, '/');

// Hapus prefix /api jika ada (karena Vercel memanggil via /api/index.php)
// Tidak perlu, karena routes di vercel.json sudah redirect semua ke sini

$routes = [
    '/'                    => ['App\\Controllers\\HomeController',     'index'],
    '/index.php'           => ['App\\Controllers\\HomeController',     'index'],
    '/kategori.php'        => ['App\\Controllers\\ProductController',  'category'],
    '/produk.php'          => ['App\\Controllers\\ProductController',  'show'],
    '/keranjang.php'       => ['App\\Controllers\\CartController',     'index'],
    '/cart_actions.php'    => ['App\\Controllers\\CartController',     'handle'],
    '/checkout.php'        => ['App\\Controllers\\CheckoutController', 'index'],
    '/proses_checkout.php' => ['App\\Controllers\\CheckoutController', 'process'],
    '/pembayaran.php'      => ['App\\Controllers\\PaymentController',  'show'],
    '/pesanan.php'         => ['App\\Controllers\\OrderController',    'index'],
    '/profil.php'          => ['App\\Controllers\\AccountController',  'profile'],
    '/pengaturan.php'      => ['App\\Controllers\\AccountController',  'settings'],
    '/search.php'          => ['App\\Controllers\\ProductController',  'search'],
    '/notifikasi.php'      => ['App\\Controllers\\AccountController',  'notifications'],
    '/ulasan.php'          => ['App\\Controllers\\AccountController',  'review'],

    // Auth
    '/auth/login.php'         => ['App\\Controllers\\AuthController', 'loginForm'],
    '/auth/register.php'      => ['App\\Controllers\\AuthController', 'registerForm'],
    '/auth/logout.php'        => ['App\\Controllers\\AuthController', 'logout'],
    '/auth/google-login.php'  => ['App\\Controllers\\AuthController', 'googleLogin'],
    '/auth/google-callback.php' => ['App\\Controllers\\AuthController', 'googleCallback'],

    // Admin
    '/admin/index.php'       => ['App\\Controllers\\Admin\\DashboardController',  'index'],
    '/admin/produk.php'      => ['App\\Controllers\\Admin\\ProductController',    'index'],
    '/admin/kategori.php'    => ['App\\Controllers\\Admin\\CategoryController',   'index'],
    '/admin/pesanan.php'     => ['App\\Controllers\\Admin\\OrderController',      'index'],
    '/admin/pembayaran.php'  => ['App\\Controllers\\Admin\\PaymentController',    'index'],
    '/admin/pengguna.php'    => ['App\\Controllers\\Admin\\UserController',       'index'],
    '/admin/ulasan.php'      => ['App\\Controllers\\Admin\\ReviewController',     'index'],
    '/admin/laporan.php'     => ['App\\Controllers\\Admin\\ReportController',     'index'],

    // API proxy wilayah
    '/api/wilayah.php'       => null, // handled below
];

// Handle static assets — seharusnya sudah ditangani vercel.json
if (preg_match('#\.(css|js|png|jpg|jpeg|gif|svg|ico|woff2?|ttf)$#i', $uri)) {
    // Biarkan Vercel serve langsung
    http_response_code(404);
    exit;
}

// API wilayah proxy
if ($uri === '/api/wilayah.php') {
    require BASE_PATH . '/api/wilayah.php';
    exit;
}

// Cari route
if (isset($routes[$uri]) && $routes[$uri] !== null) {
    [$class, $method] = $routes[$uri];
    (new $class)->$method();
    exit;
}

// Fallback: coba tanpa trailing slash / dengan slash
$uriSlash = rtrim($uri, '/') ?: '/';
if (isset($routes[$uriSlash]) && $routes[$uriSlash] !== null) {
    [$class, $method] = $routes[$uriSlash];
    (new $class)->$method();
    exit;
}

// 404
http_response_code(404);
echo '<!DOCTYPE html><html><body>';
echo '<h2>404 — Halaman tidak ditemukan</h2>';
echo '<p><a href="/">Kembali ke Beranda</a></p>';
echo '</body></html>';
