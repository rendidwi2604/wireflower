<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CartModel;
use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class CheckoutController extends Controller
{
    // Tarif ongkir per zona — harus sinkron dengan JS di checkout.php
    private const SHIPPING_ZONES = [15000, 20000, 25000, 35000, 45000];
    private const SHIPPING_DEFAULT = 15000;

    public function index(): void
    {
        require_login();
        $userId = $this->userId();

        $items = [];
        $mode = 'cart';

        if (!empty($_POST['buy_now_product_id'])) {
            $product = $this->model(ProductModel::class)->find((int) $_POST['buy_now_product_id']);
            if ($product) {
                $product['quantity'] = min(max(1, (int) ($_POST['buy_now_quantity'] ?? 1)), (int) $product['stock']);
                $items[] = $product;
            }
            $mode = 'buy_now';
        } else {
            $selected = array_values(array_filter(array_map('intval', $_POST['selected_items'] ?? [])));
            if (empty($selected)) {
                flash('warning', 'Pilih minimal satu produk untuk checkout.');
                redirect('keranjang.php');
            }
            $items = $this->model(CartModel::class)->selected($userId, $selected);
        }

        if (empty($items)) {
            redirect('keranjang.php');
        }

        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += $it['price'] * $it['quantity'];
        }

        // Simpan sementara di session untuk diproses setelah submit alamat
        $_SESSION['checkout_items']    = $items;
        $_SESSION['checkout_subtotal'] = $subtotal;
        $_SESSION['checkout_mode']     = $mode;

        $this->render('checkout', [
            'page_title'    => 'Checkout',
            'items'         => $items,
            'subtotal'      => $subtotal,
            'shipping_cost' => 0, // ongkir 0 dulu, berubah setelah user pilih provinsi via JS
            'total'         => $subtotal,
            'addresses'     => $this->model(UserModel::class)->addresses($userId),
        ]);
    }

    public function process(): void
    {
        require_login();
        $userId = $this->userId();

        $items = $_SESSION['checkout_items'] ?? [];
        if (empty($items)) {
            redirect('keranjang.php');
        }

        $subtotal = (float) ($_SESSION['checkout_subtotal'] ?? 0);
        $mode     = $_SESSION['checkout_mode'] ?? 'cart';

        // Ambil ongkir dari POST, validasi hanya nilai yang diizinkan per zona
        $shippingRaw = (int) ($_POST['shipping_cost'] ?? 0);
        $shipping = in_array($shippingRaw, self::SHIPPING_ZONES, true)
            ? $shippingRaw
            : self::SHIPPING_DEFAULT;

        $addressId = $_POST['address_id'] ?? null;
        if (!$addressId || $addressId === 'new') {
            $addressId = $this->model(UserModel::class)->addAddress($userId, [
                'label'          => 'Alamat Baru',
                'recipient_name' => $_POST['recipient_name'] ?? '',
                'phone'          => $_POST['phone'] ?? '',
                'full_address'   => $_POST['full_address'] ?? '',
                'city'           => $_POST['city'] ?? '',
                'province'       => $_POST['province'] ?? '',
                'postal_code'    => $_POST['postal_code'] ?? '',
            ]);
        }

        try {
            $orderId = $this->model(OrderModel::class)->checkout($userId, [
                'address_id'    => $addressId,
                'items'         => $items,
                'subtotal'      => $subtotal,
                'shipping_cost' => $shipping,
                'total'         => $subtotal + $shipping,
                'note'          => $_POST['note'] ?? '',
                'mode'          => $mode,
            ]);
        } catch (\Exception $e) {
            die('Gagal memproses pesanan: ' . $e->getMessage());
        }

        $order = $this->model(OrderModel::class)->find($orderId);
        $this->model(NotificationModel::class)->send(
            $userId,
            'Pesanan Berhasil Dibuat',
            "Pesanan #{$order['order_code']} berhasil dibuat. Silakan lakukan pembayaran.",
            'order_created'
        );

        unset(
            $_SESSION['checkout_items'],
            $_SESSION['checkout_subtotal'],
            $_SESSION['checkout_mode']
        );

        redirect('pembayaran.php?order_id=' . $orderId);
    }
}
