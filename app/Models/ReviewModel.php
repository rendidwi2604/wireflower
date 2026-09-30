<?php

namespace App\Models;

use App\Core\Model;

class ReviewModel extends Model
{
    public function forProduct(int $productId): array
    {
        return $this->fetchAll(
            'SELECT r.*, u.name AS user_name
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.product_id = $1
             ORDER BY r.created_at DESC',
            [$productId]
        );
    }

    public function avgRating(int $productId): float
    {
        return (float) $this->fetchValue(
            'SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE product_id = $1',
            [$productId]
        );
    }

    public function create(int $productId, int $userId, ?int $orderItemId, int $rating, string $comment, ?string $photo): void
    {
        $this->run(
            'INSERT INTO reviews (product_id, user_id, order_item_id, rating, comment, photo)
             VALUES ($1, $2, $3, $4, $5, $6)',
            [$productId, $userId, $orderItemId, $rating, $comment, $photo]
        );
    }

    // ---- Admin ----

    public function allWithNames(): array
    {
        return $this->fetchAll(
            'SELECT r.*, u.name AS user_name, p.name AS product_name
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             JOIN products p ON r.product_id = p.id
             ORDER BY r.created_at DESC'
        );
    }

    public function delete($id): void
    {
        $this->run('DELETE FROM reviews WHERE id = $1', [$id]);
    }
}
