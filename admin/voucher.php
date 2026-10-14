<?php
/**
 * Admin: Kelola Voucher
 * Entry point — diarahkan ke router Vercel
 */
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/bootstrap.php';
(new App\Controllers\Admin\VoucherController())->index();
