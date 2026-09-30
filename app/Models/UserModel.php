<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
    public function find($id): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE id = $1', [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE email = $1', [$email]);
    }

    public function emailExists(string $email): bool
    {
        return (bool) $this->fetchValue('SELECT id FROM users WHERE email = $1', [$email]);
    }

    public function create(string $name, string $email, string $password, string $phone, string $role): int
    {
        return (int) $this->fetchValue(
            'INSERT INTO users (name, email, password, phone, role) VALUES ($1, $2, $3, $4, $5) RETURNING id',
            [$name, $email, password_hash($password, PASSWORD_DEFAULT), $phone, $role]
        );
    }

    public function updateProfile(int $id, string $name, string $phone): void
    {
        $this->run('UPDATE users SET name = $1, phone = $2 WHERE id = $3', [$name, $phone, $id]);
    }

    public function updatePassword(int $id, string $plainPassword): void
    {
        $this->run(
            'UPDATE users SET password = $1 WHERE id = $2',
            [password_hash($plainPassword, PASSWORD_DEFAULT), $id]
        );
    }

    public function updatePreferences(int $id, array $prefs): void
    {
        $this->run(
            'UPDATE users SET notif_email = $1, notif_push = $2, privacy_public_profile = $3 WHERE id = $4',
            [
                (bool) $prefs['notif_email'],
                (bool) $prefs['notif_push'],
                (bool) $prefs['privacy_public_profile'],
                $id,
            ]
        );
    }

    // ---- Google OAuth ----

    private function ensureGoogleColumn(): void
    {
        // PostgreSQL: cek kolom lewat information_schema, bukan SHOW COLUMNS
        $exists = $this->fetchValue(
            "SELECT column_name FROM information_schema.columns
             WHERE table_name = 'users' AND column_name = 'google_id'"
        );
        if (!$exists) {
            $this->db->exec('ALTER TABLE users ADD COLUMN google_id VARCHAR(255) DEFAULT NULL');
            $this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS users_google_id_unique ON users(google_id) WHERE google_id IS NOT NULL');
        }
    }

    public function findByGoogle(string $googleId, string $email): ?array
    {
        $this->ensureGoogleColumn();
        return $this->fetchOne(
            'SELECT * FROM users WHERE google_id = $1 OR email = $2 LIMIT 1',
            [$googleId, $email]
        );
    }

    public function linkGoogle(int $id, string $googleId): void
    {
        $this->run('UPDATE users SET google_id = $1 WHERE id = $2', [$googleId, $id]);
    }

    public function createFromGoogle(string $name, string $email, string $googleId): int
    {
        return (int) $this->fetchValue(
            "INSERT INTO users (name, email, password, role, google_id) VALUES ($1, $2, $3, 'customer', $4) RETURNING id",
            [$name, $email, password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), $googleId]
        );
    }

    // ---- Admin ----

    public function all(): array
    {
        return $this->fetchAll('SELECT * FROM users ORDER BY created_at DESC');
    }

    public function toggleRole(int $id): void
    {
        $current = $this->fetchValue('SELECT role FROM users WHERE id = $1', [$id]);
        $newRole  = $current === 'admin' ? 'customer' : 'admin';
        $this->run('UPDATE users SET role = $1 WHERE id = $2', [$newRole, $id]);
    }

    public function deleteExceptSelf($id, int $selfId): void
    {
        $this->run('DELETE FROM users WHERE id = $1 AND id <> $2', [$id, $selfId]);
    }

    public function countAll(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM users');
    }

    public function countCustomers(): int
    {
        return (int) $this->fetchValue("SELECT COUNT(*) FROM users WHERE role = 'customer'");
    }

    // ---- Alamat (sub-resource milik user) ----

    public function addresses(int $userId): array
    {
        return $this->fetchAll(
            'SELECT * FROM addresses WHERE user_id = $1 ORDER BY is_default DESC',
            [$userId]
        );
    }

    public function addAddress(int $userId, array $data): int
    {
        $isDefault = !empty($data['is_default']);
        if ($isDefault) {
            $this->run('UPDATE addresses SET is_default = FALSE WHERE user_id = $1', [$userId]);
        }
        return (int) $this->fetchValue(
            'INSERT INTO addresses (user_id, label, recipient_name, phone, full_address, city, province, postal_code, is_default)
             VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9) RETURNING id',
            [
                $userId,
                $data['label'] ?: 'Rumah',
                $data['recipient_name'],
                $data['phone'],
                $data['full_address'],
                $data['city'],
                $data['province'],
                $data['postal_code'],
                $isDefault ? 'TRUE' : 'FALSE',
            ]
        );
    }

    public function deleteAddress(int $id, int $userId): void
    {
        $this->run('DELETE FROM addresses WHERE id = $1 AND user_id = $2', [$id, $userId]);
    }
}
