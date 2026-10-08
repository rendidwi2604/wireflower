<?php

namespace App\Models;

use App\Core\Model;

class ProductModel extends Model
{
    private const SORTS = [
        'terbaru'    => 'created_at DESC',
        'terlaris'   => 'sold_count DESC',
        'harga_asc'  => 'price ASC',
        'harga_desc' => 'price DESC',
    ];

    public function latest(int $limit = 8): array
    {
        return $this->fetchAll(
            'SELECT p.*,
                    COALESCE(AVG(r.rating), 0)  AS avg_rating,
                    COUNT(r.id)                  AS review_count
             FROM products p
             LEFT JOIN reviews r ON r.product_id = p.id
             WHERE p.is_active = TRUE
             GROUP BY p.id
             ORDER BY p.created_at DESC
             LIMIT $1',
            [$limit]
        );
    }

    public function featured(int $limit = 8): array
    {
        return $this->fetchAll(
            'SELECT p.*,
                    COALESCE(AVG(r.rating), 0)  AS avg_rating,
                    COUNT(r.id)                  AS review_count
             FROM products p
             LEFT JOIN reviews r ON r.product_id = p.id
             WHERE p.is_active = TRUE AND p.is_featured = TRUE
             GROUP BY p.id
             ORDER BY RANDOM()
             LIMIT $1',
            [$limit]
        );
    }

    public function byCategory(int $categoryId): array
    {
        return $this->fetchAll(
            'SELECT p.*,
                    COALESCE(AVG(r.rating), 0)  AS avg_rating,
                    COUNT(r.id)                  AS review_count
             FROM products p
             LEFT JOIN reviews r ON r.product_id = p.id
             WHERE p.category_id = $1 AND p.is_active = TRUE
             GROUP BY p.id
             ORDER BY p.created_at DESC',
            [$categoryId]
        );
    }

    public function all(string $sort = 'terbaru'): array
    {
        $orderBy = self::SORTS[$sort] ?? 'p.created_at DESC';
        // prefix kolom sort dengan alias p. jika diperlukan
        $orderBy = preg_replace('/^(created_at|sold_count|price)/', 'p.$1', $orderBy);
        return $this->fetchAll(
            "SELECT p.*,
                    COALESCE(AVG(r.rating), 0)  AS avg_rating,
                    COUNT(r.id)                  AS review_count
             FROM products p
             LEFT JOIN reviews r ON r.product_id = p.id
             WHERE p.is_active = TRUE
             GROUP BY p.id
             ORDER BY {$orderBy}"
        );
    }

    public function search(string $q, $categoryId, string $sort): array
    {
        // PostgreSQL: gunakan ILIKE untuk case-insensitive search
        $conditions = ['is_active = TRUE'];
        $params     = [];
        $i          = 1;

        if ($q !== '') {
            $conditions[] = "name ILIKE \${$i}";
            $params[]     = "%{$q}%";
            $i++;
        }
        if ($categoryId !== '' && $categoryId !== null) {
            $conditions[] = "category_id = \${$i}";
            $params[]     = $categoryId;
            $i++;
        }

        $where    = implode(' AND ', $conditions);
        $orderBy  = self::SORTS[$sort] ?? 'created_at DESC';

        return $this->fetchAll("SELECT * FROM products WHERE {$where} ORDER BY {$orderBy}", $params);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->fetchOne(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE p.slug = $1',
            [$slug]
        );
    }

    public function find($id): ?array
    {
        return $this->fetchOne('SELECT * FROM products WHERE id = $1', [$id]);
    }

    // ---- Admin ----

    public function allWithCategory(): array
    {
        return $this->fetchAll(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON p.category_id = c.id
             ORDER BY p.created_at DESC'
        );
    }

    public function save(?int $id, array $data, ?string $image): int
    {
        // Cast boolean dengan benar untuk PostgreSQL + PDO emulate prepares
        $fields = [
            'category_id' => (int)   $data['category_id'],
            'name'        =>          $data['name'],
            'slug'        => slugify($data['name']),
            'description' =>          $data['description'],
            'price'       => (float)  $data['price'],
            'stock'       => (int)    $data['stock'],
            'is_featured' => $data['is_featured'] ? 'TRUE' : 'FALSE',
            'is_active'   => $data['is_active']   ? 'TRUE' : 'FALSE',
        ];

        if ($image) {
            $fields['image'] = $image;
        }

        if ($id) {
            // UPDATE — build $1, $2, ... placeholders
            $i    = 1;
            $sets = [];
            foreach (array_keys($fields) as $col) {
                $sets[] = "{$col} = \${$i}";
                $i++;
            }
            $params   = array_values($fields);
            $params[] = $id;
            $this->run(
                'UPDATE products SET ' . implode(', ', $sets) . " WHERE id = \${$i}",
                $params
            );
            return $id;
        }

        // INSERT … RETURNING id
        $cols        = implode(', ', array_keys($fields));
        $placeholders = implode(', ', array_map(fn($n) => "\${$n}", range(1, count($fields))));
        return (int) $this->fetchValue(
            "INSERT INTO products ({$cols}) VALUES ({$placeholders}) RETURNING id",
            array_values($fields)
        );
    }

    public function delete($id): void
    {
        $this->run('DELETE FROM products WHERE id = $1', [$id]);
    }

    // ---- Stok ----

    public function decrementStock(int $productId, int $qty): void
    {
        // PostgreSQL: GREATEST() tersedia, tapi bisa juga pakai CASE
        $this->run(
            'UPDATE products SET stock = GREATEST(stock - $1, 0) WHERE id = $2',
            [$qty, $productId]
        );
    }

    public function incrementSold(int $productId, int $qty): void
    {
        $this->run(
            'UPDATE products SET sold_count = sold_count + $1 WHERE id = $2',
            [$qty, $productId]
        );
    }
}
