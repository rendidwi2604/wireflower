<?php
/**
 * API: Validasi kode voucher (dipanggil via AJAX dari checkout)
 * POST /api/voucher.php
 * Body: code, subtotal, shipping
 * Response: JSON
 */

if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
if (!defined('APP_PATH'))  define('APP_PATH',  BASE_PATH . '/app');

require_once APP_PATH . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

// Hanya izinkan POST dari user yang login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed.']);
    exit;
}

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Silakan login terlebih dahulu.']);
    exit;
}

$code     = trim($_POST['code']     ?? '');
$subtotal = (float) ($_POST['subtotal'] ?? 0);
$shipping = (float) ($_POST['shipping'] ?? 0);

if ($code === '') {
    echo json_encode(['ok' => false, 'message' => 'Masukkan kode voucher.']);
    exit;
}

require_once APP_PATH . '/Models/VoucherModel.php';
$model   = new App\Models\VoucherModel();
$voucher = $model->findValid($code);

if (!$voucher) {
    echo json_encode(['ok' => false, 'message' => 'Kode voucher tidak valid, sudah kadaluarsa, atau habis terpakai.']);
    exit;
}

// Cek min. order
if (!empty($voucher['min_order']) && $subtotal < (float) $voucher['min_order']) {
    echo json_encode([
        'ok'      => false,
        'message' => 'Minimum pembelian untuk voucher ini adalah ' . 'Rp ' . number_format($voucher['min_order'], 0, ',', '.') . '.',
    ]);
    exit;
}

// Hitung diskon
$result = $model->calculate($voucher, $subtotal, $shipping);

// Bangun label
$typeLabel = [
    'free_shipping' => 'Gratis Ongkir',
    'percent'       => 'Diskon ' . (int)$voucher['discount_value'] . '%',
    'fixed'         => 'Potongan Rp ' . number_format($voucher['discount_value'], 0, ',', '.'),
][$voucher['type']] ?? $voucher['type'];

echo json_encode([
    'ok'              => true,
    'message'         => 'Voucher berhasil diterapkan!',
    'voucher_id'      => (int) $voucher['id'],
    'voucher_code'    => $voucher['code'],
    'type'            => $voucher['type'],
    'type_label'      => $typeLabel,
    'description'     => $voucher['description'] ?? '',
    'discount_amount' => $result['discount_amount'],
    'shipping_cost'   => $result['shipping_cost'],
    'total'           => $result['total'],
]);
