<?php
// =====================================================
// KONFIGURASI APLIKASI - Wire Flower
// =====================================================
//
// Untuk menghubungkan ke Supabase, ganti bagian
// database di bawah ini. Lihat panduan lengkapnya di:
// database/SUPABASE_SETUP.md
//
// Contoh konfigurasi Supabase:
// ---------------------------------------------------
// 'db_host'    => 'db.xxxxxxxxxxxxxxxxxxxx.supabase.co',
// 'db_port'    => '5432',
// 'db_name'    => 'postgres',
// 'db_user'    => 'postgres',
// 'db_pass'    => 'your_supabase_password',
// 'db_sslmode' => 'require',   // <-- wajib untuk Supabase
// ---------------------------------------------------

return [
    // --- Database PostgreSQL (Supabase Connection Pooler) ---
    'db_driver'  => 'pgsql',
    'db_host'    => getenv('DB_HOST')     ?: 'aws-0-ap-south-1.pooler.supabase.com',
    'db_port'    => getenv('DB_PORT')     ?: '5432',
    'db_name'    => getenv('DB_NAME')     ?: 'postgres',
    'db_user'    => getenv('DB_USER')     ?: 'postgres.hkirjjqocbhkxjdifpee',
    'db_pass'    => getenv('DB_PASS')     ?: '',
    'db_sslmode' => getenv('DB_SSLMODE')  ?: 'require',

    // Kode rahasia agar pendaftaran sebagai Admin tidak bisa sembarang orang.
    // Ganti kode ini sesuai keinginanmu sebelum website dipakai secara nyata.
    'admin_register_code' => getenv('ADMIN_CODE') ?: 'WIREFLOWER-ADMIN-2026',

    // Login Google: isi lewat environment server (opsional).
    'google_client_id'     => getenv('GOOGLE_CLIENT_ID')     ?: '',
    'google_client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
];
