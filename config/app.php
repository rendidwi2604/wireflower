<?php
// =====================================================
// KONFIGURASI APLIKASI - Wire Flower
// =====================================================

// Baca env var — cek getenv(), $_ENV[], $_SERVER[] (Vercel pakai $_SERVER)
function _env(string $key, string $default = ''): string {
    $v = getenv($key);
    if ($v !== false && $v !== '') return $v;
    if (!empty($_ENV[$key]))    return $_ENV[$key];
    if (!empty($_SERVER[$key])) return $_SERVER[$key];
    // Coba lowercase juga
    $lower = strtolower($key);
    if (!empty($_ENV[$lower]))    return $_ENV[$lower];
    if (!empty($_SERVER[$lower])) return $_SERVER[$lower];
    return $default;
}

return [
    // --- Database PostgreSQL (Supabase Connection Pooler) ---
    'db_driver'  => 'pgsql',
    'db_host'    => _env('DB_HOST',    'aws-0-ap-south-1.pooler.supabase.com'),
    'db_port'    => _env('DB_PORT',    '5432'),
    'db_name'    => _env('DB_NAME',    'postgres'),
    'db_user'    => _env('DB_USER',    'postgres.hkirjjqocbhkxjdifpee'),
    'db_pass'    => _env('DB_PASS',    'Rendidwi26!'),
    'db_sslmode' => _env('DB_SSLMODE', 'require'),

    'admin_register_code' => _env('ADMIN_CODE', 'WIREFLOWER-ADMIN-2026'),

    'google_client_id'     => _env('GOOGLE_CLIENT_ID',     ''),
    'google_client_secret' => _env('GOOGLE_CLIENT_SECRET', ''),
];
