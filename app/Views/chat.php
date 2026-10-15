<link rel="stylesheet" href="<?= site_url('assets/css/chat.css?v=1') ?>">
<section class="wf-chat-page <?= $chat_admin ? 'wf-chat-page--admin' : 'container' ?>">
  <h1><?= $chat_admin ? 'Chat Pelanggan' : 'Chat Admin' ?></h1>
  <p class="wf-chat-intro"><?= $chat_admin ? 'Semua produk dalam satu percakapan per pelanggan.' : 'Tanyakan produk dan lanjutkan percakapan dengan admin di sini.' ?></p>
  <?php if ($chat_error): ?>
    <p class="wf-chat-error" role="alert"><?= e($chat_error) ?></p>
  <?php else: ?>
  <div class="wf-chat-shell <?= $chat_admin ? 'wf-chat-shell--admin' : '' ?> <?= $thread ? 'has-room' : '' ?>"
       id="wfChat" data-api="<?= site_url('api/chat.php') ?>" data-csrf="<?= e($csrf) ?>"
       data-thread="<?= (int) ($thread['id'] ?? 0) ?>" data-role="<?= $chat_admin ? 'admin' : 'customer' ?>"
       data-intent="<?= e($pending_intent) ?>" data-admin-url="<?= site_url('admin/chat.php') ?>">
    <?php if ($chat_admin): ?>
    <aside class="wf-chat-inbox" aria-label="Daftar pelanggan">
      <h2>Percakapan</h2>
      <label for="chatSearch">Cari pelanggan</label>
      <input type="search" id="chatSearch" placeholder="Nama atau email" autocomplete="off">
      <div id="chatThreads" aria-live="polite"><p class="wf-chat-empty">Memuat percakapan...</p></div>
    </aside>
    <?php endif; ?>
    <div class="wf-chat-room">
      <?php if ($thread): ?>
      <header class="wf-chat-header">
        <?php if ($chat_admin): ?><a class="wf-chat-back" href="<?= site_url('admin/chat.php') ?>" aria-label="Kembali ke daftar pelanggan">&larr;</a><?php endif; ?>
        <div><h2><?= $chat_admin ? e($thread['customer_name']) : 'Admin WireFlower' ?></h2>
        <p><?= $chat_admin ? e($thread['customer_email']) : 'Riwayat chat untuk semua produk Anda' ?></p></div>
      </header>
      <div class="wf-chat-history">
        <button type="button" id="chatOlder" class="wf-chat-secondary" hidden>Muat pesan sebelumnya</button>
        <div id="chatMessages" role="log" aria-label="Pesan percakapan" aria-live="polite" aria-relevant="additions"></div>
        <p id="chatEmpty" class="wf-chat-empty">Memuat pesan...</p>
      </div>
      <div class="wf-chat-status" role="status">
        <span id="chatStatus"><?= $pending_intent ? 'Mengirim foto dan detail produk...' : '' ?></span>
        <button type="button" id="chatRetry" class="wf-chat-secondary" hidden>Coba lagi</button>
      </div>
      <form id="chatComposer" class="wf-chat-composer">
        <label for="chatBody" class="wf-chat-sr-only">Pesan untuk <?= $chat_admin ? 'pelanggan' : 'admin' ?></label>
        <textarea id="chatBody" name="body" rows="2" maxlength="5000" placeholder="Tulis pesan..." required disabled></textarea>
        <button id="chatSend" type="submit" disabled>Kirim</button>
      </form>
      <?php else: ?>
      <p class="wf-chat-empty">Pilih pelanggan untuk membaca dan membalas chat.</p>
      <?php endif; ?>
    </div>
  </div>
  <noscript><p>Aktifkan JavaScript untuk mengirim dan menerima chat.</p></noscript>
  <script src="<?= site_url('assets/js/chat.js?v=1') ?>" defer></script>
  <?php endif; ?>
</section>
