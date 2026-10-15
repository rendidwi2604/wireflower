-- Satu percakapan untuk semua produk milik pelanggan yang sama.
CREATE TABLE IF NOT EXISTS chat_threads (
    id SERIAL PRIMARY KEY,
    customer_id INT NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS chat_messages (
    id SERIAL PRIMARY KEY,
    thread_id INT NOT NULL REFERENCES chat_threads(id) ON DELETE CASCADE,
    sender_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    sender_role VARCHAR(10) NOT NULL CHECK (sender_role IN ('customer', 'admin')),
    body TEXT NOT NULL DEFAULT '',
    product_snapshot TEXT,
    request_key VARCHAR(64) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP,
    UNIQUE (sender_id, request_key),
    CHECK (char_length(body) <= 5000),
    CHECK (body <> '' OR product_snapshot IS NOT NULL)
);

CREATE INDEX IF NOT EXISTS chat_messages_thread_id_idx ON chat_messages(thread_id, id);
CREATE INDEX IF NOT EXISTS chat_messages_unread_idx ON chat_messages(thread_id, sender_role) WHERE read_at IS NULL;

-- Akses melalui backend PHP; jangan expose percakapan lewat API publik Supabase.
ALTER TABLE chat_threads ENABLE ROW LEVEL SECURITY;
ALTER TABLE chat_messages ENABLE ROW LEVEL SECURITY;
