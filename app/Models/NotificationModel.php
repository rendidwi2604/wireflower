<?php

namespace App\Models;

use App\Core\Model;

class NotificationModel extends Model
{
    public function send(int $userId, string $title, string $message, string $type = 'info'): void
    {
        $this->run(
            'INSERT INTO notifications (user_id, title, message, type) VALUES ($1, $2, $3, $4)',
            [$userId, $title, $message, $type]
        );
    }

    public function unreadCount(int $userId): int
    {
        return (int) $this->fetchValue(
            'SELECT COUNT(*) FROM notifications WHERE user_id = $1 AND is_read = FALSE',
            [$userId]
        );
    }

    public function forUser(int $userId): array
    {
        return $this->fetchAll(
            'SELECT * FROM notifications WHERE user_id = $1 ORDER BY created_at DESC',
            [$userId]
        );
    }

    public function markAllRead(int $userId): void
    {
        $this->run(
            'UPDATE notifications SET is_read = TRUE WHERE user_id = $1',
            [$userId]
        );
    }
}
