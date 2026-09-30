# Menghubungkan WireFlower ke Supabase

Panduan lengkap migrasi database WireFlower dari PostgreSQL lokal ke Supabase.

---

## Daftar Isi

1. [Buat Project Supabase](#1-buat-project-supabase)
2. [Ambil Kredensial Koneksi](#2-ambil-kredensial-koneksi)
3. [Update config/app.php](#3-update-configappphp)
4. [Jalankan Migrasi Schema](#4-jalankan-migrasi-schema)
5. [Jalankan Seed Data](#5-jalankan-seed-data)
6. [Aktifkan SSL (Wajib di Supabase)](#6-aktifkan-ssl-wajib-di-supabase)
7. [Test Koneksi](#7-test-koneksi)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Buat Project Supabase

1. Buka **[https://supabase.com](https://supabase.com)** → klik **Start your project**
2. Login dengan GitHub / Google
3. Klik **New project**
4. Isi form:
   - **Organization** → pilih organisasi kamu (atau buat baru)
   - **Name** → `wireflower`
   - **Database Password** → buat password yang kuat, **simpan baik-baik** (tidak bisa dilihat lagi)
   - **Region** → pilih yang terdekat, misalnya `Southeast Asia (Singapore)`
5. Klik **Create new project** — tunggu sekitar 1–2 menit hingga selesai provisioning

---

## 2. Ambil Kredensial Koneksi

Setelah project siap:

1. Di sidebar kiri → klik **Settings** (ikon gear)
2. Pilih menu **Database**
3. Scroll ke bagian **Connection parameters** → pilih tab **URI** atau **Connection string**

Kamu akan menemukan data seperti ini:

```
Host:      db.xxxxxxxxxxxxxxxxxxxx.supabase.co
Port:      5432
Database:  postgres
User:      postgres
Password:  [password yang kamu buat tadi]
```

> **Catatan:** Supabase juga menyediakan **Connection Pooling** (port 6543, via PgBouncer).
> Untuk aplikasi PHP tradisional (bukan serverless), gunakan **port 5432 langsung** agar lebih stabil.

---

## 3. Update config/app.php

Buka file `config/app.php` dan ganti bagian database:

```php
return [
    // --- Database Supabase (PostgreSQL) ---
    'db_driver' => 'pgsql',
    'db_host'   => 'db.xxxxxxxxxxxxxxxxxxxx.supabase.co', // ← ganti dengan host kamu
    'db_port'   => '5432',
    'db_name'   => 'postgres',   // ← nama database di Supabase selalu "postgres"
    'db_user'   => 'postgres',
    'db_pass'   => 'your_password_here', // ← password yang dibuat saat setup project

    'admin_register_code'  => 'WIREFLOWER-ADMIN-2026',
    'google_client_id'     => getenv('GOOGLE_CLIENT_ID')     ?: '',
    'google_client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
];
```

> **Jangan commit** `config/app.php` ke Git jika berisi password asli.
> Gunakan environment variable atau tambahkan ke `.gitignore`.

---

## 4. Jalankan Migrasi Schema

Ada **2 cara** menjalankan migrasi ke Supabase:

### Cara A — Melalui SQL Editor Supabase (Direkomendasikan)

1. Di dashboard Supabase → klik **SQL Editor** di sidebar kiri
2. Klik **New query**
3. Buka file `database/migrations/001_create_tables.sql`
4. Copy semua isinya → paste ke SQL Editor
5. Klik **Run** (atau `Ctrl+Enter`)
6. Pastikan muncul pesan **Success** tanpa error

### Cara B — Melalui Migration Runner PHP (CLI)

Pastikan PHP kamu bisa reach Supabase (butuh internet), lalu jalankan:

```bash
# Dari root project
php database/migrate.php
```

Jika koneksi berhasil, output akan seperti:

```
Terhubung ke PostgreSQL: postgres@db.xxxx.supabase.co:5432
Menjalankan migrasi...
-------------------------------------------------------
  [OK]     001_create_tables.sql
  [OK]     002_seed_data.sql
-------------------------------------------------------
Selesai: 2 migrasi dijalankan, 0 di-skip.
```

---

## 5. Jalankan Seed Data

### Cara A — SQL Editor Supabase

1. SQL Editor → **New query**
2. Buka `database/migrations/002_seed_data.sql`
3. Copy → paste → **Run**

### Cara B — CLI (jika sudah update config/app.php)

```bash
php database/migrate.php
```

Runner otomatis menjalankan file yang belum dijalankan, termasuk seed.

---

## 6. Aktifkan SSL (Wajib di Supabase)

Supabase **mewajibkan SSL**. Update `app/helpers.php` bagian fungsi `db()` untuk menambahkan opsi SSL:

```php
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $host   = config('db_host', 'localhost');
            $port   = config('db_port', '5432');
            $dbname = config('db_name', 'postgres');
            $user   = config('db_user', 'postgres');
            $pass   = config('db_pass', '');

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $pdo->exec("SET client_encoding = 'UTF8'");
            $pdo->exec("SET timezone = 'Asia/Jakarta'");

        } catch (PDOException $e) {
            die('Koneksi database gagal: ' . $e->getMessage());
        }
    }
    return $pdo;
}
```

Perubahan kunci: tambahkan **`;sslmode=require`** di akhir DSN string.

---

## 7. Test Koneksi

Buat file sementara `test_db.php` di root project untuk memverifikasi koneksi:

```php
<?php
require_once __DIR__ . '/app/bootstrap.php';

try {
    $pdo = db();
    $row = $pdo->query("SELECT version()")->fetch();
    echo "Koneksi berhasil!\n";
    echo "PostgreSQL: " . $row['version'] . "\n";

    // Cek tabel ada
    $tables = $pdo->query("
        SELECT tablename FROM pg_tables
        WHERE schemaname = 'public'
        ORDER BY tablename
    ")->fetchAll(PDO::FETCH_COLUMN);

    echo "\nTabel ditemukan (" . count($tables) . "):\n";
    foreach ($tables as $t) {
        echo "  - {$t}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
```

Jalankan:

```bash
php test_db.php
```

Output yang diharapkan:

```
Koneksi berhasil!
PostgreSQL: PostgreSQL 15.x on x86_64-pc-linux-gnu ...

Tabel ditemukan (11):
  - addresses
  - cart_items
  - categories
  - migrations
  - notifications
  - order_items
  - orders
  - payments
  - products
  - reviews
  - users
```

Setelah berhasil, **hapus `test_db.php`** agar tidak terekspos ke publik.

---

## 8. Troubleshooting

### Error: `could not connect to server: Connection refused`
- Pastikan `db_host` sudah benar (copy dari dashboard Supabase, bukan localhost)
- Pastikan port `5432` tidak diblokir firewall/ISP kamu

### Error: `SSL connection has been closed unexpectedly`
- Tambahkan `sslmode=require` di DSN (lihat [langkah 6](#6-aktifkan-ssl-wajib-di-supabase))

### Error: `FATAL: password authentication failed`
- Password salah atau ada karakter spesial — coba reset password di Supabase:
  Settings → Database → **Reset database password**

### Error: `relation "users" does not exist`
- Migrasi belum dijalankan → jalankan `001_create_tables.sql` dulu

### Error: `duplicate key value violates unique constraint`
- Seed data sudah dijalankan sebelumnya → seed sudah pakai `ON CONFLICT DO NOTHING`, aman dijalankan ulang

### Koneksi lambat dari lokal
- Wajar karena server di Singapore — untuk development tetap gunakan PostgreSQL lokal,
  ganti ke Supabase hanya untuk production/staging

---

## Catatan Keamanan

| Hal | Rekomendasi |
|-----|-------------|
| Password database | Simpan di environment variable, bukan hardcode di `config/app.php` |
| `config/app.php` | Tambahkan ke `.gitignore` |
| `test_db.php` | Hapus setelah selesai testing |
| Row Level Security | Aktifkan di Supabase dashboard untuk keamanan ekstra |
| SSL | Selalu gunakan `sslmode=require` untuk koneksi ke Supabase |

---

## Referensi

- [Supabase Docs — Database](https://supabase.com/docs/guides/database)
- [Supabase Docs — Connecting to Postgres](https://supabase.com/docs/guides/database/connecting-to-postgres)
- [PHP PDO PostgreSQL](https://www.php.net/manual/en/ref.pdo-pgsql.php)
