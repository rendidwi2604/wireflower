<?php

namespace App\Models;

use App\Core\Model;

class CartModel extends Model
{
    public function items(int $userId): array
    {
        return $this->fetchAll(
            'SELECT ci.*, p.name, p.price, p.image, p.stock, p.slug
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_id = $1
             ORDER BY ci.created_at DESC',
            [$userId]
        );
    }

    public function selected(int $userId, array $cartIds): array
    {
        if (empty($cartIds)) {
            return [];
        }
        // PostgreSQL: ANY(array) untuk IN dengan parameter binding
        return $this->fetchAll(
            'SELECT ci.id AS cart_id, p.id AS product_id, p.name, p.price, p.image, p.stock, ci.quantity
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_id = $1 AND ci.id = ANY($2)',
            [$userId, '{' . implode(',', array_map('intval', $cartIds)) . '}']
        );
    }

    public function totalQuantity(int $userId): int
    {
        return (int) $this->fetchValue(
            'SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = $1',
            [$userId]
        );
    }

    public function add(int $userId, int $productId, int $qty): void
    {
        // PostgreSQL: INSERT … ON CONFLICT (upsert)
        $this->run(
            'INSERT INTO cart_items (user_id, product_id, quantity)
             VALUES ($1, $2, $3)
             ON CONFLICT (user_id, product_id)
             DO UPDATE SET quantity = cart_items.quantity + EXCLUDED.quantity',
            [$userId, $productId, $qty]
        );
    }

    public function updateQty(int $cartId, int $qty, int $userId): void
    {
        $this->run(
            'UPDATE cart_items SET quantity = $1 WHERE id = $2 AND user_id = $3',
            [$qty, $cartId, $userId]
        );
    }

    public function remove(int $cartId, int $userId): void
    {
        $this->run(
            'DELETE FROM cart_items WHERE id = $1 AND user_id = $2',
            [$cartId, $userId]
        );
    }

    public function removeMany(int $userId, array $cartIds): void
    {
        if (empty($cartIds)) {
            return;
        }
        $this->run(
            'DELETE FROM cart_items WHERE user_id = $1 AND id = ANY($2)',
            [$userId, '{' . implode(',', array_map('intval', $cartIds)) . '}']
        );
    }
}
