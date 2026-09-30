<?php

namespace App\Core;

use App\Models\CartModel;
use App\Models\NotificationModel;

abstract class Controller
{
    private array $models = [];

    protected function model(string $class)
    {
        return $this->models[$class] ??= new $class();
    }

    protected function userId(): int
    {
        return (int) current_user_id();
    }

    protected function render(string $view, array $data = [], string $layout = 'site'): void
    {
        extract($data, EXTR_SKIP);

        $page_title = $page_title ?? '';
        // Dipakai navbar di layout "site"
        $cart_count = $unread_count = 0;
        if ($layout === 'site' && is_logged_in()) {
            $cart_count = (int) $this->model(CartModel::class)->totalQuantity($this->userId());
            $unread_count = $this->model(NotificationModel::class)->unreadCount($this->userId());
        }

        ob_start();
        require VIEW_PATH . '/' . $view . '.php';
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/' . $layout . '.php';
    }
}
