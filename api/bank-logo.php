<?php
/**
 * Serve bank logo SVG dengan MIME type yang benar
 * Usage: /api/bank-logo.php?name=bca
 */
$name = preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['name'] ?? ''));
if ($name === '') {
    http_response_code(400);
    exit;
}

$file = dirname(__DIR__) . '/assets/img/banks/' . $name . '.svg';

if (!is_file($file)) {
    http_response_code(404);
    exit;
}

header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=31536000');
header('X-Content-Type-Options: nosniff');
readfile($file);
