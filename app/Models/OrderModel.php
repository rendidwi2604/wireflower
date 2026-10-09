<?php

namespace App\Models;

use App\Core\Model;

class OrderModel extends Model
{
    public const STATUSES = ['belum_bayar', 'dikemas', 'dikirim', 'selesai', 'dibatalkan'];

    public const STATUS_LABELS = [
        ''            => 'Semua',
        'belum_bayar' => 'Belum Bayar',
        'dikemas'     => 'Dikemas',
        'dikirim'     => 'Dikirim',
        'selesai'     => 'Selesai',
        'dibatalkan'  => 'Dibatalkan',
    ];

    // ---- Checkout (satu transaksi) ----

    public function checkout(int $userId, array $data): int
    {
        $this->db->beginTransaction();
        try {
            $orderCode = generate_order_code();

            // INSERT order → RETURNING id (PostgreSQL, bukan lastInsertId)
            $orderId = (int) $this->fetchValue(
                "INSERT INTO orders (order_code, user_id, address_id, subtotal, shipping_cost, total, status, note)
                 VALUES ($1, $2, $3, $4, $5, $6, 'belum_bayar', $7) RETURNING id",
                [
                    $orderCode,
                    $userId,
                    $data['address_id'],
                    $data['subtotal'],
                    $data['shipping_cost'],
                    $data['total'],
                    $data['note'],
                ]
            );

            foreach ($data['items'] as $it) {
                $this->run(
                    'INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal)
                     VALUES ($1, $2, $3, $4, $5, $6)',
                    [
                        $orderId,
                        $it['product_id'],
                        $it['name'],
                        $it['price'],
                        $it['quantity'],
                        $it['price'] * $it['quantity'],
                    ]
                );
                // GREATEST() tersedia di PostgreSQL
                $this->run(
                    'UPDATE products SET stock = GREATEST(stock - $1, 0) WHERE id = $2',
                    [$it['quantity'], $it['product_id']]
                );
            }

            // Hapus cart items yang dipilih
            if ($data['mode'] === 'cart') {
                $cartIds = array_values(
                    array_filter(array_map(fn($it) => (int) ($it['cart_id'] ?? 0), $data['items']))
                );
                if ($cartIds) {
                    // PostgreSQL: gunakan ANY(array) untuk IN dinamis
                    $this->run(
                        'DELETE FROM cart_items WHERE user_id = $1 AND id = ANY($2)',
                        [$userId, '{' . implode(',', $cartIds) . '}']
                    );
                }
            }

            // Buat record payment awal
            $this->run(
                "INSERT INTO payments (order_id, method, amount, status) VALUES ($1, 'transfer_bank', $2, 'pending')",
                [$orderId, $data['total']]
            );

            $this->db->commit();
            return $orderId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ---- Pembeli ----

    public function findForUser(int $id, int $userId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM orders WHERE id = $1 AND user_id = $2',
            [$id, $userId]
        );
    }

    public function forUser(int $userId, string $status): array
    {
        $params = [$userId];
        $sql    = 'SELECT * FROM orders WHERE user_id = $1';
        if ($status !== '') {
            $sql     .= ' AND status = $2';
            $params[] = $status;
        }
        return $this->fetchAll($sql . ' ORDER BY created_at DESC', $params);
    }

    public function countsByStatus(int $userId): array
    {
        $counts = array_fill_keys(array_keys(self::STATUS_LABELS), 0);
        $rows   = $this->fetchAll(
            'SELECT status, COUNT(*) AS total FROM orders WHERE user_id = $1 GROUP BY status',
            [$userId]
        );
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }
        $counts[''] = array_sum($counts);
        return $counts;
    }

    public function itemsOf(int $orderId): array
    {
        return $this->fetchAll(
            'SELECT * FROM order_items WHERE order_id = $1',
            [$orderId]
        );
    }

    public function complete(int $orderId, int $userId): bool
    {
        $stmt = $this->run(
            "UPDATE orders SET status = 'selesai' WHERE id = $1 AND user_id = $2 AND status = 'dikirim'",
            [$orderId, $userId]
        );
        return $stmt->rowCount() > 0;
    }

    public function cancel(int $orderId, int $userId, string $reason, string $details): ?string
    {
        // Bisa dibatalkan dari status belum_bayar ATAU dikemas
        $order = $this->fetchOne(
            "SELECT order_code, note, status FROM orders
             WHERE id = $1 AND user_id = $2 AND status IN ('belum_bayar','dikemas')",
            [$orderId, $userId]
        );
        if (!$order) {
            return null;
        }

        $cancelNote = 'Alasan pembatalan: ' . $reason . ($details !== '' ? ' - ' . $details : '');
        $note       = trim(($order['note'] ?? '') . ($order['note'] ? "\n" : '') . $cancelNote);

        $this->run(
            "UPDATE orders SET status = 'dibatalkan', note = $1
             WHERE id = $2 AND user_id = $3 AND status IN ('belum_bayar','dikemas')",
            [$note, $orderId, $userId]
        );
        return $order['order_code'];
    }

    public function latestPaymentForOrder(int $orderId): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM payments WHERE order_id = $1 ORDER BY id DESC LIMIT 1',
            [$orderId]
        );
    }

    // ---- Admin ----

    public function find($id): ?array
    {
        return $this->fetchOne('SELECT * FROM orders WHERE id = $1', [$id]);
    }

    public function allWithCustomer(string $status): array
    {
        $params = [];
        $sql    = 'SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON o.user_id = u.id';
        if ($status !== '') {
            $sql     .= ' WHERE o.status = $1';
            $params[] = $status;
        }
        return $this->fetchAll($sql . ' ORDER BY o.created_at DESC', $params);
    }

    public function updateStatus(int $orderId, string $status): ?array
    {
        $this->run('UPDATE orders SET status = $1 WHERE id = $2', [$status, $orderId]);
        return $this->find($orderId);
    }

    public function dashboardStats(): array
    {
        return [
            'produk'     => (int)   $this->fetchValue('SELECT COUNT(*) FROM products'),
            'pesanan'    => (int)   $this->fetchValue('SELECT COUNT(*) FROM orders'),
            'pendapatan' => (float) $this->fetchValue(
                "SELECT COALESCE(SUM(total), 0) FROM orders WHERE status <> 'dibatalkan'"
            ),
        ];
    }

    public function recentOrders(int $limit = 5): array
    {
        return $this->fetchAll(
            'SELECT o.*, u.name AS customer_name
             FROM orders o JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC LIMIT $1',
            [$limit]
        );
    }

    // ---- Laporan ----

    public function reportSummary(string $start, string $end): array
    {
        // PostgreSQL: DATE(col) tetap valid; bisa juga col::date
        return [
            'total'  => (float) $this->fetchValue(
                "SELECT COALESCE(SUM(total), 0) FROM orders
                 WHERE status <> 'dibatalkan' AND created_at::date BETWEEN $1 AND $2",
                [$start, $end]
            ),
            'jumlah' => (int) $this->fetchValue(
                "SELECT COUNT(*) FROM orders
                 WHERE status <> 'dibatalkan' AND created_at::date BETWEEN $1 AND $2",
                [$start, $end]
            ),
        ];
    }

    public function reportTopProducts(string $start, string $end): array
    {
        return $this->fetchAll(
            "SELECT oi.product_name,
                    SUM(oi.quantity) AS total_terjual,
                    SUM(oi.subtotal) AS total_pendapatan
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             WHERE o.status <> 'dibatalkan'
               AND o.created_at::date BETWEEN $1 AND $2
             GROUP BY oi.product_name
             ORDER BY total_terjual DESC",
            [$start, $end]
        );
    }

    public function reportOrders(string $start, string $end): array
    {
        return $this->fetchAll(
            "SELECT * FROM orders
             WHERE status <> 'dibatalkan'
               AND created_at::date BETWEEN $1 AND $2
             ORDER BY created_at DESC",
            [$start, $end]
        );
    }
}
