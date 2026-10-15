<?php

namespace App\Models;

use App\Core\Model;

class ChatModel extends Model
{
    public function forCustomer(int $customerId): array
    {
        $this->run('INSERT INTO chat_threads (customer_id) VALUES ($1) ON CONFLICT (customer_id) DO NOTHING', [$customerId]);
        return $this->fetchOne('SELECT * FROM chat_threads WHERE customer_id = $1', [$customerId]);
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne('SELECT t.*, u.name AS customer_name, u.email AS customer_email
            FROM chat_threads t JOIN users u ON u.id = t.customer_id WHERE t.id = $1', [$id]);
    }

    public function inbox(): array
    {
        return $this->fetchAll("SELECT t.*, u.name AS customer_name, u.email AS customer_email,
            (SELECT COUNT(*) FROM chat_messages m WHERE m.thread_id = t.id AND m.sender_role = 'customer' AND m.read_at IS NULL) AS unread_count,
            (SELECT COALESCE(NULLIF(m.body, ''), 'Mengirim detail produk') FROM chat_messages m WHERE m.thread_id = t.id ORDER BY m.id DESC LIMIT 1) AS last_message,
            (SELECT MAX(m.id) FROM chat_messages m WHERE m.thread_id = t.id) AS last_message_id
            FROM chat_threads t JOIN users u ON u.id = t.customer_id
            ORDER BY COALESCE((SELECT MAX(m.id) FROM chat_messages m WHERE m.thread_id = t.id), 0) DESC, t.id DESC");
    }

    public function messages(int $threadId, int $after = 0, int $before = 0): array
    {
        if ($after > 0) {
            return $this->fetchAll('SELECT * FROM chat_messages WHERE thread_id = $1 AND id > $2 ORDER BY id LIMIT 100', [$threadId, $after]);
        }
        $rows = $before > 0
            ? $this->fetchAll('SELECT * FROM chat_messages WHERE thread_id = $1 AND id < $2 ORDER BY id DESC LIMIT 50', [$threadId, $before])
            : $this->fetchAll('SELECT * FROM chat_messages WHERE thread_id = $1 ORDER BY id DESC LIMIT 50', [$threadId]);
        return array_reverse($rows);
    }

    public function send(int $threadId, int $senderId, string $role, string $body, ?array $product, string $key): array
    {
        // Retrying the same request must not duplicate a product card or text message.
        $this->run('INSERT INTO chat_messages (thread_id, sender_id, sender_role, body, product_snapshot, request_key)
            VALUES ($1, $2, $3, $4, $5, $6) ON CONFLICT (sender_id, request_key) DO NOTHING',
            [$threadId, $senderId, $role, $body, $product ? json_encode($product, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE) : null, $key]);
        $message = $this->fetchOne('SELECT * FROM chat_messages WHERE sender_id = $1 AND request_key = $2 AND thread_id = $3', [$senderId, $key, $threadId]);
        if (!$message) {
            throw new \InvalidArgumentException('Permintaan pesan tidak valid. Muat ulang chat.');
        }
        return $message;
    }

    public function markRead(int $threadId, string $readerRole, int $throughId): void
    {
        $this->run('UPDATE chat_messages SET read_at = CURRENT_TIMESTAMP
            WHERE thread_id = $1 AND sender_role <> $2 AND id <= $3 AND read_at IS NULL', [$threadId, $readerRole, $throughId]);
    }
}
