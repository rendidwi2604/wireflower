<?php
// Isolated integration checks; never connects to the configured application DB.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/helpers.php';
spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'App\\')) require BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
});

use App\Controllers\ChatController;
use App\Core\Controller;
use App\Models\ChatModel;
use App\Models\ProductModel;
use App\Models\UserModel;

$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$pdo->exec('PRAGMA foreign_keys = ON');
$pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, role TEXT)');
$pdo->exec("INSERT INTO users VALUES (1, 'Pelanggan A', 'a@example.test', 'customer'), (2, 'Pelanggan B', 'b@example.test', 'customer'), (3, 'Admin', 'admin@example.test', 'admin')");
$pdo->exec('CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, slug TEXT, price NUMERIC, stock INTEGER, description TEXT, image TEXT, is_active BOOLEAN)');
$pdo->exec("INSERT INTO products VALUES (1, 'Buket Wisuda', 'buket-wisuda', 150000, 12, 'Buket handmade', 'bungatangan.png', TRUE), (2, 'Buket Mawar', 'buket-mawar', 200000, 0, 'Mawar merah', 'header1.png', TRUE)");
// Exercise the migration structure with SQLite's equivalent types/functions.
$schema = file_get_contents(BASE_PATH . '/database/migrations/005_add_chat.sql');
$schema = str_replace(['SERIAL PRIMARY KEY', 'char_length('], ['INTEGER PRIMARY KEY AUTOINCREMENT', 'length('], $schema);
$schema = preg_replace('/ALTER TABLE \w+ ENABLE ROW LEVEL SECURITY;/', '', $schema);
$pdo->exec($schema);
$chat = new ChatModel($pdo);
$controller = new ChatController();
$models = new ReflectionProperty(Controller::class, 'models');
$testModels = [ChatModel::class => $chat, ProductModel::class => new ProductModel($pdo), UserModel::class => new UserModel($pdo)];
$models->setValue($controller, $testModels);
$completed = false;
register_shutdown_function(function () use (&$completed) {
    if (!$completed) { fwrite(STDERR, "FAIL: chat checks did not complete (possible unexpected redirect).\n"); exit(1); }
});
$checks = 0;
function check(bool $condition, string $name): void {
    global $checks;
    if (!$condition) throw new RuntimeException('FAILED: ' . $name);
    $checks++;
}
function api(string $method, array $params, ?int $user = 1, string $role = 'customer'): array {
    global $controller;
    $_SESSION = ['chat_csrf' => 'test-csrf', 'chat_intents' => [str_repeat('a', 48) => 1, str_repeat('b', 48) => 2]];
    if ($user !== null) $_SESSION += ['user_id' => $user, 'role' => $role];
    $_SERVER['REQUEST_METHOD'] = $method;
    $_GET = $method === 'GET' ? $params : [];
    $_POST = $method === 'POST' ? $params + ['csrf' => 'test-csrf'] : [];
    http_response_code(200);
    ob_start();
    $controller->api();
    $result = json_decode(ob_get_clean(), true, 512, JSON_THROW_ON_ERROR);
    return [http_response_code(), $result];
}

$a = $chat->forCustomer(1);
$b = $chat->forCustomer(2);
check($a['id'] === $chat->forCustomer(1)['id'], 'one thread per customer');
check($a['id'] !== $b['id'], 'different customers have separate threads');
$first = ['thread_id' => $a['id'], 'action' => 'product', 'request_key' => str_repeat('a', 48), 'price' => 1, 'sender_role' => 'admin'];
[$status, $result] = api('POST', $first);
check($status === 200 && $result['message']['product']['price'] == 150000, 'product snapshot uses server price');
check($result['message']['sender_role'] === 'customer', 'sender role cannot be spoofed');
check(str_contains($result['message']['product']['image_url'], 'bungatangan.png'), 'product photo included');
$messageId = $result['message']['id'];
[$status, $result] = api('POST', $first);
check($result['message']['id'] === $messageId && count($chat->messages($a['id'])) === 1, 'product retry deduplicated');
api('POST', ['thread_id' => $a['id'], 'action' => 'product', 'request_key' => str_repeat('b', 48)]);
check(count($chat->messages($a['id'])) === 2, 'different products share one thread including sold-out products');
[$status] = api('GET', ['thread_id' => $a['id']], 2);
check($status === 404, 'other customer cannot read conversation');
[$status] = api('POST', ['thread_id' => $a['id'], 'action' => 'send', 'body' => 'intrusion', 'request_key' => str_repeat('c', 48)], 2);
check($status === 404, 'other customer cannot write conversation');
[$status] = api('POST', ['thread_id' => $a['id'], 'action' => 'read', 'through_id' => 999], 2);
check($status === 404, 'other customer cannot mark conversation read');
[$status] = api('GET', ['action' => 'inbox']);
check($status === 403, 'customers cannot list inbox');
[$status] = api('GET', ['thread_id' => $a['id']], null);
check($status === 401, 'guest API authentication');
[$status] = api('POST', $first + ['csrf' => 'bad']);
check($status === 403, 'CSRF enforced');
[$status] = api('DELETE', ['thread_id' => $a['id']]);
check($status === 405, 'unsupported methods rejected');
[$status] = api('POST', ['thread_id' => $a['id'], 'action' => 'send', 'body' => ' ', 'request_key' => str_repeat('c', 48)]);
check($status === 422, 'empty text rejected');
[$status] = api('POST', ['thread_id' => $a['id'], 'action' => 'send', 'body' => str_repeat('x', 5001), 'request_key' => str_repeat('c', 48)]);
check($status === 422, 'oversized text rejected');
[$status] = api('POST', ['thread_id' => $a['id'], 'action' => 'product', 'request_key' => str_repeat('f', 48)]);
check($status === 422, 'unknown product intent rejected');
$reply = ['thread_id' => $a['id'], 'context' => 'admin', 'action' => 'send', 'body' => 'Halo, ada yang bisa dibantu?', 'request_key' => str_repeat('c', 48)];
[$status, $result] = api('POST', $reply, 3, 'admin');
check($status === 200 && $result['message']['sender_role'] === 'admin', 'admin replies in customer thread');
api('POST', $reply, 3, 'admin');
check(count($chat->messages($a['id'])) === 3, 'text retry deduplicated');
[$status, $result] = api('GET', ['action' => 'inbox', 'context' => 'admin'], 3, 'admin');
check(count($result['threads']) === 2 && $result['threads'][0]['unread_count'] == 2, 'inbox groups products by customer with unread count');
api('POST', ['thread_id' => $a['id'], 'context' => 'admin', 'action' => 'read', 'through_id' => $messageId], 3, 'admin');
check($chat->inbox()[0]['unread_count'] == 1, 'read receipt does not clear newer messages');
$pdo->exec("UPDATE products SET price = 1, name = 'Changed' WHERE id = 1");
$pdo->exec('DELETE FROM products WHERE id = 1');
[$status, $result] = api('GET', ['thread_id' => $a['id']]);
check($result['messages'][0]['product']['price'] == 150000 && $result['messages'][0]['product']['name'] === 'Buket Wisuda', 'product history survives updates and deletion');
check(count($chat->messages($b['id'])) === 0, 'customer B history remains isolated');
for ($i = 0; $i < 110; $i++) $chat->send($a['id'], 1, 'customer', 'Pesan ' . $i, null, hash('sha256', 'page-' . $i));
$latest = $chat->messages($a['id']);
$previous = $chat->messages($a['id'], 0, $latest[0]['id']);
$oldest = $chat->messages($a['id'], 0, $previous[0]['id']);
check(count($latest) === 50 && count($previous) === 50 && count($oldest) === 13, 'history pagination retains every message');
check(end($previous)['id'] < $latest[0]['id'], 'older pages are ordered and non-overlapping');
$new = $chat->messages($a['id'], $messageId);
check(count($new) === 100 && $new[0]['id'] > $messageId, 'incremental polling is ordered and bounded');
// Regression: account role must not change the storefront room into an inbox.
class ChatPageProbe extends ChatController
{
    public array $rendered = [];
    protected function render(string $view, array $data = [], string $layout = 'site'): void
    {
        $this->rendered = ['view' => $view, 'data' => $data, 'layout' => $layout];
    }
}
foreach ([1 => 'customer', 3 => 'admin'] as $id => $role) {
    $_SESSION = ['user_id' => $id, 'role' => $role, 'chat_intents' => [str_repeat('b', 48) => 2]];
    $_GET = ['intent' => str_repeat('b', 48)];
    $page = new ChatPageProbe();
    $models->setValue($page, $testModels);
    $page->index();
    check($page->rendered['layout'] === 'site' && $page->rendered['data']['chat_admin'] === false, $role . ' browsing storefront remains in customer chat');
    check((int) $page->rendered['data']['thread']['customer_id'] === $id, $role . ' storefront room belongs to current user');
    check($page->rendered['data']['pending_intent'] === str_repeat('b', 48), $role . ' product context survives navigation');
}
$ownAdminThread = $chat->forCustomer(3);
[$status, $result] = api('POST', ['thread_id' => $ownAdminThread['id'], 'context' => 'customer', 'action' => 'product', 'request_key' => str_repeat('b', 48)], 3, 'admin');
check($status === 200 && $result['message']['sender_role'] === 'customer' && $result['message']['product']['id'] == 2, 'admin browsing store can send product as shopper');
[$status, $result] = api('POST', ['thread_id' => $ownAdminThread['id'], 'context' => 'customer', 'action' => 'send', 'body' => 'Pertanyaan produk', 'request_key' => str_repeat('d', 48)], 3, 'admin');
check($status === 200 && $result['message']['sender_role'] === 'customer', 'admin browsing store can continue shopper conversation');
[$status] = api('GET', ['thread_id' => $a['id'], 'context' => 'customer'], 3, 'admin');
check($status === 404, 'storefront context cannot access another customer even for admin account');
[$status] = api('GET', ['action' => 'inbox'], 3, 'admin');
check($status === 403, 'storefront cannot load admin inbox');
[$status] = api('GET', ['action' => 'inbox', 'context' => 'admin'], 1, 'customer');
check($status === 403, 'customer cannot select admin context');
[$status] = api('GET', ['action' => 'inbox', 'context' => 'admin'], 1, 'admin');
check($status === 403 && $_SESSION['role'] === 'customer', 'stale admin session cannot bypass database role');
[$status] = api('POST', $reply + ['csrf' => 'test-csrf'], 1, 'admin');
check($status === 403, 'stale admin session cannot send admin reply');
[$status] = api('GET', ['thread_id' => $a['id'], 'context' => 'invalid']);
check($status === 422, 'unknown chat context rejected');
$_SESSION = ['user_id' => 3, 'role' => 'admin'];
$_GET = ['thread_id' => $a['id']];
$adminPage = new ChatPageProbe();
$models->setValue($adminPage, $testModels);
$adminPage->admin();
check($adminPage->rendered['layout'] === 'admin' && $adminPage->rendered['data']['chat_admin'], 'explicit admin route retains customer inbox');
$completed = true;
echo "PASS: {$checks} chat integration checks (isolated SQLite database).\n";
