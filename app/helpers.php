<?php
// =====================================================
// HELPER GLOBAL (view helper, auth guard, koneksi DB)
// =====================================================

function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require BASE_PATH . '/config/app.php';
    }
    return $config[$key] ?? $default;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $host   = config('db_host', 'localhost');
            $port   = config('db_port', '5432');
            $dbname = config('db_name', 'wireflower');
            $user   = config('db_user', 'postgres');
            $pass   = config('db_pass', '');
            $ssl    = config('db_sslmode', 'prefer'); // 'require' untuk Supabase

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$ssl}";

            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true,  // diperlukan untuk PgBouncer Transaction mode
            ]);

            // Set timezone dan encoding
            $pdo->exec("SET client_encoding = 'UTF8'");
            $pdo->exec("SET timezone = 'Asia/Jakarta'");
        } catch (PDOException $e) {
            die('Koneksi database gagal: ' . $e->getMessage());
        }
    }
    return $pdo;
}

// ---------- Auth guard ----------

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function is_admin()
{
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

function current_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

function require_login()
{
    if (!is_logged_in()) {
        redirect('auth/login.php');
    }
}

function require_admin()
{
    if (!is_admin()) {
        redirect('index.php');
    }
}

function login_user(array $user)
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
}

function flash(string $type, string $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// ---------- URL & format ----------

function site_url($path = '')
{
    // Di Vercel (atau domain root), tidak pakai prefix /wireflower
    // Di localhost XAMPP, pakai prefix /wireflower
    $isVercel = !empty($_SERVER['VERCEL']) || !empty(getenv('VERCEL'));
    $isLocal  = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'], true)
                || str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost:');

    $base = (!$isVercel && $isLocal) ? '/wireflower' : '';

    $path = ltrim((string) $path, '/');
    return $base . ($path === '' ? '/' : '/' . $path);
}

function redirect($url)
{
    if (!preg_match('#^https?://#i', $url)) {
        $url = site_url($url);
    }
    header('Location: ' . $url);
    exit;
}

function e($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function rupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function asset_img($filename, string $fallback)
{
    return $filename ? site_url('assets/img/' . $filename) : $fallback;
}

function generate_order_code()
{
    return 'WF' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

function slugify(string $text)
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text), '-'));
}

// ---------- Google OAuth ----------

function google_redirect_uri()
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . site_url('auth/google-callback.php');
}

function has_google_oauth_config()
{
    return config('google_client_id') !== '' && config('google_client_secret') !== '';
}
