<?php

namespace App\Models;

use App\Core\Model;

class CategoryModel extends Model
{
    public function all(): array
    {
        return $this->fetchAll('SELECT * FROM categories ORDER BY id');
    }

    public function withProductCount(): array
    {
        return $this->fetchAll(
            'SELECT c.*,
                    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS jml_produk
             FROM categories c
             ORDER BY c.id'
        );
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->fetchOne('SELECT * FROM categories WHERE slug = $1', [$slug]);
    }

    public function find($id): ?array
    {
        return $this->fetchOne('SELECT * FROM categories WHERE id = $1', [$id]);
    }

    public function save(?int $id, string $name): void
    {
        $slug = slugify($name);
        if ($id) {
            $this->run(
                'UPDATE categories SET name = $1, slug = $2 WHERE id = $3',
                [$name, $slug, $id]
            );
        } else {
            $this->run(
                'INSERT INTO categories (name, slug) VALUES ($1, $2)',
                [$name, $slug]
            );
        }
    }

    public function delete($id): void
    {
        $this->run('DELETE FROM categories WHERE id = $1', [$id]);
    }
}
