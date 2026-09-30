<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? db();
    }

    protected function run(string $sql, array $params = []): \PDOStatement
    {
        // Konversi $1, $2, ... ke ? agar kompatibel dengan EMULATE_PREPARES
        // yang diperlukan untuk PgBouncer (Supabase Connection Pooler)
        $converted = preg_replace('/\$\d+/', '?', $sql);
        $stmt = $this->db->prepare($converted);
        $stmt->execute(array_values($params));
        return $stmt;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        return $this->run($sql, $params)->fetch() ?: null;
    }

    protected function fetchValue(string $sql, array $params = [])
    {
        return $this->run($sql, $params)->fetchColumn();
    }
}
