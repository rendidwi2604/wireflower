<?php
/**
 * Proxy Wilayah + Kode Pos
 * Param: ?type=wilayah&endpoint=states
 *        ?type=kodepos&q=melawai
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: public, max-age=86400');

$type = $_GET['type'] ?? '';

/* ── Kode Pos ── */
if ($type === 'kodepos') {
    $q   = trim(strip_tags($_GET['q'] ?? ''));
    if ($q === '') { echo json_encode(['data'=>[]]); exit; }

    $url = 'https://kodepos.vercel.app/search/?q=' . urlencode($q);
    $ctx = stream_context_create([
        'http' => ['method'=>'GET','timeout'=>8,
                   'header'=>"Accept: application/json\r\nUser-Agent: WireFlower/1.0\r\n"],
        'ssl'  => ['verify_peer'=>false,'verify_peer_name'=>false],
    ]);
    $body = @file_get_contents($url, false, $ctx);
    echo $body !== false ? $body : json_encode(['data'=>[]]);
    exit;
}

/* ── Wilayah ── */
$endpoint = trim($_GET['endpoint'] ?? '');

$allowed  = ['states'];
$patterns = [
    '/^states\/\d{2}\/cities$/',
    '/^cities\/[\d.]+\/districts$/',
    '/^districts\/[\d.]+\/villages$/',
];

$ok = in_array($endpoint, $allowed, true);
if (!$ok) {
    foreach ($patterns as $p) {
        if (preg_match($p, $endpoint)) { $ok = true; break; }
    }
}
if (!$ok) {
    http_response_code(400);
    echo json_encode(['error'=>'Endpoint tidak diizinkan: '.$endpoint]);
    exit;
}

$url  = 'https://lokaid.gilangpratama.id/' . $endpoint;
$ctx  = stream_context_create([
    'http' => ['method'=>'GET','timeout'=>10,
               'header'=>"Accept: application/json\r\nUser-Agent: WireFlower/1.0\r\n"],
    'ssl'  => ['verify_peer'=>false,'verify_peer_name'=>false],
]);
$body = @file_get_contents($url, false, $ctx);

if ($body === false) {
    http_response_code(502);
    echo json_encode(['error'=>'Gagal fetch '.$url]);
    exit;
}
echo $body;
