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
        $this->run(
            "UPDATE payments SET method = $1, status = 'success', paid_at = CURRENT_TIMESTAMP WHERE id = $2",
            [$method, $paymentId]
        );
    }

    public function setMethod(int $paymentId, string $method): void
    {
        $this->run(
            'UPDATE payments SET method = $1 WHERE id = $2',
            [$method, $paymentId]
        );
    }

    public function saveTransferProof(int $paymentId, string $proofUrl): void
    {
        $this->run(
            'UPDATE payments SET transfer_proof = $1 WHERE id = $2',
            [$proofUrl, $paymentId]
        );
    }

    public function find(int $paymentId): ?array
    {
        return $this->fetchOne('SELECT * FROM payments WHERE id = $1', [$paymentId]);
    }

    // ---- Admin ----

    public function allWithOrder(): array
    {
        return $this->fetchAll(
            'SELECT pay.*, o.order_code, o.user_id, u.name AS customer_name
             FROM payments pay
             JOIN orders o ON pay.order_id = o.id
             JOIN users u ON o.user_id = u.id
             ORDER BY pay.created_at DESC'
        );
    }

    public function updateStatus(int $paymentId, string $status): void
    {
        // Use a parameter for status and a separate literal check to avoid
        // repeating the same placeholder (which would create 3 ? after conversion)
        if ($status === 'success') {
            $this->run(
                "UPDATE payments SET status = $1, paid_at = CURRENT_TIMESTAMP WHERE id = $2",
                [$status, $paymentId]
            );
        } else {
            $this->run(
                "UPDATE payments SET status = $1, paid_at = NULL WHERE id = $2",
                [$status, $paymentId]
            );
        }
    }
}
