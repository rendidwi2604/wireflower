-- ============================================================
-- Migration 003: Fix payments.method constraint
-- Hapus CHECK constraint lama yang hanya izinkan 3 nilai,
-- ganti dengan VARCHAR(50) tanpa constraint agar bisa
-- menerima nilai seperti 'Transfer Bank BRI', 'QRIS', 'COD', dll.
-- ============================================================

-- 1. Hapus constraint lama
ALTER TABLE payments
  DROP CONSTRAINT IF EXISTS payments_method_check;

-- 2. Perluas ukuran kolom method dari VARCHAR(20) ke VARCHAR(50)
ALTER TABLE payments
  ALTER COLUMN method TYPE VARCHAR(50);
