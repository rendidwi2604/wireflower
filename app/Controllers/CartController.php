<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CartModel;

class CartController extends Controller
{
    public function index(): void
    {
        require_login();

        $items = $this->model(CartModel::class)->items($this->userId());
        $total = 0;
        $itemCount = 0;
        foreach ($items as $it) {
            $total += $it['price'] * $it['quantity'];
            $itemCount += (int) $it['quantity'];
        }

        $this->render('cart', [
            'page_title' => 'Keranjang',
            'items' => $items,
            'total' => $total,
            'item_count' => $itemCount,
        ]);
    }

    public function handle(): void
    {
        require_login();

        $action = $_POST['action'] ?? '';
        $cart = $this->model(CartModel::class);
        $userId = $this->userId();

        if ($action === 'add') {
            $cart->add($userId, (int) $_POST['product_id'], max(1, (int) $_POST['quantity']));
            flash('success', 'Produk ditambahkan ke keranjang.');
            redirect($this->refererOr('keranjang.php'));
        }

        if ($action === 'update') {
            $cart->updateQty((int) $_POST['cart_id'], max(1, (int) $_POST['quantity']), $userId);
            redirect('keranjang.php');
        }

        if ($action === 'delete') {
            $cart->remove((int) $_POST['cart_id'], $userId);
            flash('info', 'Produk dihapus dari keranjang.');
        }

        redirect('keranjang.php');
    }

    private function refererOr(string $fallback): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer === '') {
            return $fallback;
        }
        $parts = parse_url($referer);
        $target = $parts['path'] ?? '';
        if (!empty($parts['query'])) {
            $target .= '?' . $parts['query'];
        }
        return $target !== '' ? $target : $fallback;
    }
}
