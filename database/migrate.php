#!/usr/bin/env php
<?php
/**
 * WireFlower — PostgreSQL Migration Runner
 * =========================================
 * Cara pakai (dari root project):
 *
 *   php database/migrate.php             → jalankan semua migrasi yang belum dijalankan
 *   php database/migrate.php --fresh     → DROP semua tabel lalu jalankan ulang dari awal
 *   php database/migrate.php --status    → tampilkan status tiap file migrasi
 *
 * Pastikan konfigurasi PostgreSQL sudah diisi di config/app.php sebelum menjalankan.
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/app.php';

// ---- Ambil konfigurasi ----
$config = require BASE_PATH . '/config/app.php';

$host   = $config['db_host']   ?? 'localhost';
$port   = $config['db_port']   ?? '5432';
$dbname = $config['db_name']   ?? 'wireflower';
$user   = $config['db_user']   ?? 'postgres';
$pass   = $config['db_pass']   ?? '';

// ---- Koneksi PDO ke PostgreSQL ----
try {
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    out('green', "Terhubung ke PostgreSQL: {$dbname}@{$host}:{$port}");
} catch (PDOException $e) {
    out('red', "Koneksi gagal: " . $e->getMessage());
    exit(1);
}

// ---- Parse argumen ----
$args  = array_slice($argv ?? [], 1);
$fresh  = in_array('--fresh',  $args);
$status = in_array('--status', $args);

// ---- Mode: --fresh (reset semua) ----
if ($fresh) {
    out('yellow', "Mode FRESH: menghapus semua tabel...");
    dropAllTables($pdo);
    out('green', "Semua tabel berhasil dihapus.");
}

// ---- Buat tabel migrations jika belum ada ----
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id       SERIAL PRIMARY KEY,
        filename VARCHAR(255) NOT NULL UNIQUE,
        ran_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
");

// ---- Ambil file migrasi ----
$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

if (empty($files)) {
    out('yellow', "Tidak ada file migrasi di: {$migrationsDir}");
    exit(0);
}

// ---- Mode: --status ----
if ($status) {
    out('bold', "\nStatus Migrasi:");
    out('bold', str_repeat('-', 55));
    foreach ($files as $file) {
        $name = basename($file);
        $ran  = $pdo->prepare("SELECT ran_at FROM migrations WHERE filename = ?");
        $ran->execute([$name]);
        $row  = $ran->fetch();
        if ($row) {
            out('green', "  [SUDAH]  {$name}  (ran: {$row['ran_at']})");
        } else {
            out('yellow', "  [BELUM]  {$name}");
        }
    }
    echo "\n";
    exit(0);
}

// ---- Jalankan migrasi yang belum dijalankan ----
$ran   = 0;
$skip  = 0;

out('bold', "\nMenjalankan migrasi...");
out('bold', str_repeat('-', 55));

foreach ($files as $file) {
    $name = basename($file);

    // Cek sudah dijalankan?
    if (!$fresh) {
        $check = $pdo->prepare("SELECT id FROM migrations WHERE filename = ?");
        $check->execute([$name]);
        if ($check->fetch()) {
            out('gray', "  [SKIP]   {$name}");
            $skip++;
            continue;
        }
    }

    // Jalankan SQL
    $sql = file_get_contents($file);
    try {
        $pdo->beginTransaction();
        $pdo->exec($sql);
        $pdo->exec("INSERT INTO migrations (filename) VALUES ('{$name}') ON CONFLICT (filename) DO UPDATE SET ran_at = CURRENT_TIMESTAMP");
        $pdo->commit();
        out('green', "  [OK]     {$name}");
        $ran++;
    } catch (PDOException $e) {
        $pdo->rollBack();
        out('red', "  [ERROR]  {$name}");
        out('red', "           " . $e->getMessage());
        exit(1);
    }
}

out('bold', str_repeat('-', 55));
out('green', "Selesai: {$ran} migrasi dijalankan, {$skip} di-skip.\n");
exit(0);


// ============================================================
// Helper: drop semua tabel (urutan terbalik foreign key)
// ============================================================
function dropAllTables(PDO $pdo): void
{
    $tables = [
        'migrations', 'notifications', 'reviews', 'cart_items',
        'payments', 'order_items', 'orders', 'addresses',
        'products', 'categories', 'users',
    ];
    // DROP CASCADE agar tidak terganggu foreign key
    foreach ($tables as $t) {
        $pdo->exec("DROP TABLE IF EXISTS {$t} CASCADE");
    }
}

// ============================================================
// Helper: output berwarna ke terminal
// ============================================================
function out(string $color, string $text): void
{
    $colors = [
        'red'    => "\033[31m",
        'green'  => "\033[32m",
        'yellow' => "\033[33m",
        'blue'   => "\033[34m",
        'gray'   => "\033[90m",
        'bold'   => "\033[1m",
    ];
    $reset = "\033[0m";
    $prefix = $colors[$color] ?? '';
    echo $prefix . $text . $reset . PHP_EOL;
}
