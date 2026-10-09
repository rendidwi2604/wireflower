-- ============================================================
-- Migration 004: Tambah kolom transfer_proof di payments
-- Untuk menyimpan URL bukti transfer dari user
-- ============================================================
ALTER TABLE payments
  ADD COLUMN IF NOT EXISTS transfer_proof VARCHAR(500) DEFAULT NULL;
