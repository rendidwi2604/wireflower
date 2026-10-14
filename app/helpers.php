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
    // Jangan regenerate ID di Vercel stateless — menyebabkan session loss
    // session_regenerate_id(false);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['logged_at'] = time();
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

function asset_img($filename, string $fallback = '')
{
    if (!$filename) return $fallback;
    // Jika sudah URL penuh (Supabase Storage), langsung return
    if (str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
        return $filename;
    }
    // File lokal
    return site_url('assets/img/' . $filename);
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
    // Di Vercel selalu HTTPS. Cek juga X-Forwarded-Proto dari proxy.
    $proto  = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
    $https  = $_SERVER['HTTPS'] ?? '';
    $isVercel = !empty($_SERVER['VERCEL']) || !empty(getenv('VERCEL'));

    if ($isVercel || $proto === 'https' || ($https !== '' && $https !== 'off')) {
        $scheme = 'https';
    } else {
        $scheme = 'http';
    }

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . site_url('auth/google-callback.php');
}

function has_google_oauth_config()
{
    return config('google_client_id') !== '' && config('google_client_secret') !== '';
}

// ---------- Supabase Storage ----------

/**
 * Upload file ke Supabase Storage.
 * Mengembalikan public URL jika berhasil, null jika gagal.
 *
 * @param string $bucket   Nama bucket (e.g. 'transfer-proofs', 'products')
 * @param string $filename Nama file tujuan di bucket
 * @param string $content  Isi file (binary)
 * @param string $mimeType MIME type file
 */
function supabase_upload(string $bucket, string $filename, string $content, string $mimeType): ?string
{
    $url = config('supabase_url', '');
    $key = config('supabase_service_key', '');

    // Jika tidak ada di config, coba env langsung
    if (!$url) $url = _env('SUPABASE_URL', '');
    if (!$key) $key = _env('SUPABASE_SERVICE_KEY', _env('SUPABASE_KEY', _env('SUPABASE_ANON_KEY', '')));

    if (!$url || !$key) {
        error_log('[supabase_upload] SUPABASE_URL atau SUPABASE_KEY tidak terkonfigurasi.');
        return null;
    }

    // Supabase Storage REST API menggunakan PUT untuk upload
    $endpoint = rtrim($url, '/') . '/storage/v1/object/' . $bucket . '/' . $filename;

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'PUT',
        CURLOPT_POSTFIELDS     => $content,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $key,
            'Content-Type: ' . $mimeType,
            'x-upsert: true',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 201) {
        return rtrim($url, '/') . '/storage/v1/object/public/' . $bucket . '/' . $filename;
    }

    error_log('[supabase_upload] HTTP ' . $httpCode . ' bucket=' . $bucket . ' file=' . $filename
        . ' curl_err=' . $curlErr . ' response=' . substr($response, 0, 300));
    return null;
}
