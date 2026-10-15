(() => {
  'use strict';
  const root = document.getElementById('wfChat');
  if (!root) return;
  const api = root.dataset.api;
  const threadId = Number(root.dataset.thread);
  const role = root.dataset.role;
  const list = document.getElementById('chatMessages');
  const history = root.querySelector('.wf-chat-history');
  const status = document.getElementById('chatStatus');
  const retry = document.getElementById('chatRetry');
  const composer = document.getElementById('chatComposer');
  const body = document.getElementById('chatBody');
  const send = document.getElementById('chatSend');
  const older = document.getElementById('chatOlder');
  let lastId = 0, firstId = 0, loaded = false, polling = false, sending = false;
  let pendingProduct = root.dataset.intent;
  let pendingText = null;
  let retryAction = null;
  let threads = [];
  const shown = new Set();

  const node = (tag, className, text) => {
    const el = document.createElement(tag);
    if (className) el.className = className;
    if (text !== undefined) el.textContent = text;
    return el;
  };
  async function request(params, post = false) {
    const data = new URLSearchParams({thread_id: String(threadId), ...params});
    if (post) data.set('csrf', root.dataset.csrf);
    const response = await fetch(post ? api : `${api}?${data}`, {
      method: post ? 'POST' : 'GET', credentials: 'same-origin', cache: 'no-store',
      ...(post ? {body: data} : {}), signal: AbortSignal.timeout(15000)
    });
    let result;
    try { result = await response.json(); } catch (_) { throw new Error('Chat tidak dapat dimuat. Silakan coba lagi.'); }
    if (!response.ok) throw new Error(result.error || 'Permintaan gagal. Coba lagi.');
    return result;
  }
  function messageStatus(text = '', action = null) {
    if (!status) return;
    status.textContent = text;
    retryAction = action;
    retry.hidden = !action;
  }
  function enableComposer() {
    if (!body) return;
    body.disabled = !loaded || !!pendingProduct || sending;
    send.disabled = body.disabled;
  }
  function renderMessage(message) {
    const el = node('article', 'wf-chat-message' + (message.sender_role === role ? ' is-mine' : ''));
    el.dataset.id = message.id;
    el.setAttribute('aria-label', message.sender_role === 'admin' ? 'Pesan admin' : 'Pesan pelanggan');
    if (message.product) {
      const p = message.product;
      const card = node('a', 'wf-chat-product');
      card.href = p.url;
      const img = node('img');
      img.src = p.image_url;
      img.alt = p.name;
      img.width = img.height = 76;
      const info = node('div');
      info.append(node('strong', '', p.name), node('span', 'wf-chat-price', new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', maximumFractionDigits: 0}).format(Number(p.price))), node('span', '', `Stok: ${p.stock}`), node('small', '', 'Detail saat dikirim · Lihat produk'));
      card.append(img, info);
      el.append(card);
      if (p.description) {
        const details = node('details', 'wf-chat-product-desc');
        details.append(node('summary', '', 'Deskripsi produk'), node('p', '', p.description));
        el.append(details);
      }
    }
    if (message.body) el.append(node('p', '', message.body));
    const date = new Date(message.created_at.replace(' ', 'T') + '+07:00');
    const time = node('time', '', Number.isNaN(date.getTime()) ? message.created_at : date.toLocaleString('id-ID', {dateStyle: 'medium', timeStyle: 'short'}));
    time.dateTime = message.created_at.replace(' ', 'T') + '+07:00';
    el.append(time);
    return el;
  }
  function appendMessages(messages, prepend = false) {
    const nearBottom = history.scrollHeight - history.scrollTop - history.clientHeight < 100;
    const height = history.scrollHeight;
    const fragment = document.createDocumentFragment();
    for (const message of messages) {
      if (shown.has(message.id)) continue;
      shown.add(message.id);
      lastId = Math.max(lastId, message.id);
      firstId = firstId ? Math.min(firstId, message.id) : message.id;
      fragment.append(renderMessage(message));
    }
    if (prepend) list.prepend(fragment); else list.append(fragment);
    const empty = document.getElementById('chatEmpty');
    empty.hidden = shown.size > 0;
    empty.textContent = 'Belum ada pesan. Mulai percakapan dengan mengirim pesan.';
    if (prepend) history.scrollTop += history.scrollHeight - height;
    else if (nearBottom || !loaded) history.scrollTop = history.scrollHeight;
  }
  async function markRead() {
    if (lastId && !document.hidden) await request({action: 'read', through_id: lastId}, true);
  }
  async function loadMessages() {
    if (!threadId || polling) return;
    polling = true;
    try {
      const result = await request(lastId ? {after: lastId} : {});
      appendMessages(result.messages);
      if (!loaded) older.hidden = result.messages.length < 50;
      loaded = true;
      enableComposer();
      await markRead();
      if (!pendingProduct && !pendingText && !sending) messageStatus();
    } catch (error) {
      if (!pendingProduct && !pendingText) messageStatus(error.message, loadMessages);
    } finally { polling = false; }
  }
  async function sendProduct() {
    if (!pendingProduct || sending) return;
    sending = true;
    enableComposer();
    messageStatus('Mengirim foto dan detail produk...');
    try {
      await request({action: 'product', request_key: pendingProduct}, true);
      pendingProduct = '';
      // Refresh/back must not resend the product intent that has already succeeded.
      const url = new URL(window.location.href);
      url.searchParams.delete('intent');
      window.history.replaceState(null, '', url);
      messageStatus();
      await loadMessages();
      history.scrollTop = history.scrollHeight;
    } catch (error) {
      messageStatus(error.message, sendProduct);
    } finally { sending = false; enableComposer(); }
  }
  function newKey() {
    return Array.from(crypto.getRandomValues(new Uint8Array(24)), b => b.toString(16).padStart(2, '0')).join('');
  }
  async function sendText() {
    if (sending || pendingProduct || !loaded) return;
    const text = body.value.trim();
    if (!pendingText && !text) return;
    // Keep the same key and body after a timeout: the server may have saved it already.
    pendingText ??= {body: text, request_key: newKey()};
    const sentText = pendingText.body;
    sending = true;
    enableComposer();
    messageStatus('Mengirim pesan...');
    try {
      await request({action: 'send', ...pendingText}, true);
      pendingText = null;
      if (body.value.trim() === sentText) body.value = '';
      messageStatus();
      await loadMessages();
      history.scrollTop = history.scrollHeight;
    } catch (error) {
      messageStatus(error.message, sendText);
    } finally { sending = false; enableComposer(); body.focus(); }
  }
  function renderInbox() {
    const target = document.getElementById('chatThreads');
    const query = document.getElementById('chatSearch').value.toLocaleLowerCase('id-ID');
    const fragment = document.createDocumentFragment();
    for (const thread of threads.filter(t => `${t.customer_name} ${t.customer_email}`.toLocaleLowerCase('id-ID').includes(query))) {
      const link = node('a', 'wf-chat-thread' + (Number(thread.id) === threadId ? ' is-active' : ''));
      link.href = `${root.dataset.adminUrl}?thread_id=${thread.id}`;
      if (Number(thread.id) === threadId) link.setAttribute('aria-current', 'page');
      link.append(node('strong', '', thread.customer_name), node('small', '', thread.customer_email), node('p', '', thread.last_message || 'Belum ada pesan'));
      if (Number(thread.unread_count)) link.append(node('span', 'wf-chat-badge', thread.unread_count));
      fragment.append(link);
    }
    if (!fragment.childNodes.length) fragment.append(node('p', 'wf-chat-empty', query ? 'Pelanggan tidak ditemukan.' : 'Belum ada percakapan pelanggan.'));
    target.replaceChildren(fragment);
  }
  async function loadInbox() {
    if (role !== 'admin') return;
    try {
      const result = await request({action: 'inbox'});
      threads = result.threads;
      renderInbox();
    } catch (error) {
      const target = document.getElementById('chatThreads');
      target.replaceChildren(node('p', 'wf-chat-empty', error.message));
    }
  }
  if (retry) retry.addEventListener('click', () => retryAction?.());
  if (composer) composer.addEventListener('submit', event => { event.preventDefault(); sendText(); });
  if (older) older.addEventListener('click', async () => {
    older.disabled = true;
    try {
      const result = await request({before: firstId});
      appendMessages(result.messages, true);
      older.hidden = result.messages.length < 50;
    } catch (error) { messageStatus(error.message, () => older.click()); }
    finally { older.disabled = false; }
  });
  document.getElementById('chatSearch')?.addEventListener('input', renderInbox);
  async function start() {
    await loadMessages();
    if (pendingProduct) await sendProduct();
    await loadInbox();
    setTimeout(tick, 4000);
  }
  async function tick() {
    if (!document.hidden) { await loadMessages(); await loadInbox(); }
    setTimeout(tick, 4000);
  }
  // The room is painted before the product is posted into the conversation.
  requestAnimationFrame(() => setTimeout(start, 0));
})();
