<?php

namespace App\Core;

/**
 * DatabaseSessionHandler
 *
 * Menyimpan session di PostgreSQL (Supabase) agar session
 * tidak hilang karena GC file atau server stateless (Vercel).
 */
class DatabaseSessionHandler implements \SessionHandlerInterface
{
    private \PDO $pdo;
    private int  $lifetime;

    public function __construct(\PDO $pdo, int $lifetime = 2592000) // default 30 hari
    {
        $this->pdo      = $pdo;
        $this->lifetime = $lifetime;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT data FROM sessions WHERE id = :id AND last_active > :expire'
            );
            $stmt->execute([
                ':id'     => $id,
                ':expire' => time() - $this->lifetime,
            ]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ? (string) $row['data'] : '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function write(string $id, string $data): bool
    {
        try {
            // UPSERT — insert or update
            $stmt = $this->pdo->prepare(
                'INSERT INTO sessions (id, data, last_active)
                 VALUES (:id, :data, :ts)
                 ON CONFLICT (id) DO UPDATE
                   SET data        = EXCLUDED.data,
                       last_active = EXCLUDED.last_active'
            );
            $stmt->execute([
                ':id'   => $id,
                ':data' => $data,
                ':ts'   => time(),
            ]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function destroy(string $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE id = :id');
            $stmt->execute([':id' => $id]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false
    {
        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM sessions WHERE last_active < :expire'
            );
            $stmt->execute([':expire' => time() - $this->lifetime]);
            return $stmt->rowCount();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
