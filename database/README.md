# WireFlower — Database Migrations (PostgreSQL)

## Struktur Folder

```
database/
├── migrate.php          ← Runner CLI untuk menjalankan migrasi
└── migrations/
    ├── 001_create_tables.sql   ← Buat semua tabel PostgreSQL
    └── 002_seed_data.sql       ← Data awal (categories, products, users, dll)
```

## Prasyarat

1. PostgreSQL sudah terinstall dan berjalan
2. Buat database `wireflower`:
   ```sql
   CREATE DATABASE wireflower;
   ```
3. Isi konfigurasi di `config/app.php`:
   ```php
   'db_host' => 'localhost',
   'db_port' => '5432',
   'db_name' => 'wireflower',
   'db_user' => 'postgres',
   'db_pass' => 'your_password',
   ```

## Cara Menjalankan Migrasi

Dari root project (`c:/php/htdocs/wireflower`):

```bash
# Jalankan semua migrasi yang belum dijalankan
php database/migrate.php

# Lihat status tiap file migrasi
php database/migrate.php --status

# Reset total: drop semua tabel lalu jalankan ulang dari awal
php database/migrate.php --fresh
```

## Perubahan dari MySQL → PostgreSQL

| MySQL                        | PostgreSQL                          |
|------------------------------|-------------------------------------|
| `INT AUTO_INCREMENT`         | `SERIAL`                            |
| `TINYINT(1)`                 | `BOOLEAN`                           |
| `DECIMAL(12,2)`              | `NUMERIC(12,2)`                     |
| `ENUM('a','b')`              | `VARCHAR(n) CHECK (col IN (...))`   |
| `RAND()`                     | `RANDOM()`                          |
| `LIKE '%x%'`                 | `ILIKE '%x%'` (case-insensitive)    |
| `GREATEST(a, b)`             | `GREATEST(a, b)` ✓ (sama)          |
| `NOW()`                      | `CURRENT_TIMESTAMP`                 |
| `DATE(col)`                  | `col::date`                         |
| `lastInsertId()`             | `... RETURNING id`                  |
| `? placeholder`              | `$1, $2, $3 placeholder`            |
| `SHOW COLUMNS FROM tbl`      | `information_schema.columns`        |
| `IN (?,?,?)`                 | `= ANY($n)` dengan array literal    |
| `tinyint DEFAULT 0/1`        | `BOOLEAN DEFAULT TRUE/FALSE`        |
| `ON DUPLICATE KEY UPDATE`    | `ON CONFLICT (...) DO UPDATE`       |
