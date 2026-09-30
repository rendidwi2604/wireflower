<?php

namespace App\Models;

use App\Core\Model;

class PaymentModel extends Model
{
    public function latestForOrder(int $orderId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM payments WHERE order_id = $1 ORDER BY id DESC LIMIT 1',
            [$orderId]
        );
    }

    public function pay(int $paymentId, string $method): void
    {
        // PostgreSQL: CURRENT_TIMESTAMP bukan NOW() (keduanya valid, tapi ini lebih standar)
        $this->run(
            "UPDATE payments SET method = $1, status = 'success', paid_at = CURRENT_TIMESTAMP WHERE id = $2",
            [$method, $paymentId]
        );
    }

    // ---- Admin ----

    public function allWithOrder(): array
    {
        return $this->fetchAll(
            'SELECT pay.*, o.order_code, u.name AS customer_name
             FROM payments pay
             JOIN orders o ON pay.order_id = o.id
             JOIN users u ON o.user_id = u.id
             ORDER BY pay.created_at DESC'
        );
    }

    public function updateStatus(int $paymentId, string $status): void
    {
        // NULL jika bukan success, CURRENT_TIMESTAMP jika success
        $this->run(
            "UPDATE payments
             SET status = $1,
                 paid_at = CASE WHEN $1 = 'success' THEN CURRENT_TIMESTAMP ELSE NULL END
             WHERE id = $2",
            [$status, $paymentId]
        );
    }
}
