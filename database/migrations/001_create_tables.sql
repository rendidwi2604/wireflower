-- ============================================================
-- WireFlower PostgreSQL Migration
-- 001_create_tables.sql
-- Schema: semua tabel dengan tipe data PostgreSQL
-- ============================================================

-- Pastikan ekstensi pgcrypto tersedia (untuk gen_random_uuid jika dibutuhkan)
-- CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ============================================================
-- TABEL users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id               SERIAL PRIMARY KEY,
    name             VARCHAR(100)  NOT NULL,
    email            VARCHAR(150)  NOT NULL UNIQUE,
    password         VARCHAR(255)  NOT NULL,
    phone            VARCHAR(20)   DEFAULT NULL,
    role             VARCHAR(10)   NOT NULL DEFAULT 'customer' CHECK (role IN ('customer','admin')),
    photo            VARCHAR(255)  DEFAULT NULL,
    google_id        VARCHAR(255)  DEFAULT NULL UNIQUE,
    notif_email      BOOLEAN       NOT NULL DEFAULT TRUE,
    notif_push       BOOLEAN       NOT NULL DEFAULT TRUE,
    privacy_public_profile BOOLEAN NOT NULL DEFAULT FALSE,
    created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL categories
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id    SERIAL PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    slug  VARCHAR(100) NOT NULL UNIQUE
);

-- ============================================================
-- TABEL products
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id           SERIAL PRIMARY KEY,
    category_id  INT           NOT NULL REFERENCES categories(id),
    name         VARCHAR(150)  NOT NULL,
    slug         VARCHAR(150)  NOT NULL UNIQUE,
    description  TEXT          DEFAULT NULL,
    price        NUMERIC(12,2) NOT NULL,
    stock        INT           NOT NULL DEFAULT 0,
    image        VARCHAR(255)  DEFAULT NULL,
    sold_count   INT           NOT NULL DEFAULT 0,
    is_featured  BOOLEAN       NOT NULL DEFAULT FALSE,
    is_active    BOOLEAN       NOT NULL DEFAULT TRUE,
    created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL addresses
-- ============================================================
CREATE TABLE IF NOT EXISTS addresses (
    id              SERIAL PRIMARY KEY,
    user_id         INT          NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    label           VARCHAR(50)  NOT NULL DEFAULT 'Rumah',
    recipient_name  VARCHAR(100) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    full_address    TEXT         NOT NULL,
    city            VARCHAR(100) NOT NULL,
    province        VARCHAR(100) NOT NULL,
    postal_code     VARCHAR(10)  NOT NULL,
    is_default      BOOLEAN      NOT NULL DEFAULT FALSE
);

-- ============================================================
-- TABEL orders
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id            SERIAL PRIMARY KEY,
    order_code    VARCHAR(30)   NOT NULL UNIQUE,
    user_id       INT           NOT NULL REFERENCES users(id),
    address_id    INT           NOT NULL REFERENCES addresses(id),
    subtotal      NUMERIC(12,2) NOT NULL,
    shipping_cost NUMERIC(12,2) NOT NULL DEFAULT 0,
    total         NUMERIC(12,2) NOT NULL,
    status        VARCHAR(20)   NOT NULL DEFAULT 'belum_bayar'
                      CHECK (status IN ('belum_bayar','dikemas','dikirim','selesai','dibatalkan')),
    note          TEXT          DEFAULT NULL,
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL order_items
-- ============================================================
CREATE TABLE IF NOT EXISTS order_items (
    id            SERIAL PRIMARY KEY,
    order_id      INT           NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id    INT           NOT NULL REFERENCES products(id),
    product_name  VARCHAR(150)  NOT NULL,
    price         NUMERIC(12,2) NOT NULL,
    quantity      INT           NOT NULL,
    subtotal      NUMERIC(12,2) NOT NULL
);

-- ============================================================
-- TABEL payments
-- ============================================================
CREATE TABLE IF NOT EXISTS payments (
    id          SERIAL PRIMARY KEY,
    order_id    INT           NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    method      VARCHAR(20)   NOT NULL DEFAULT 'transfer_bank'
                    CHECK (method IN ('transfer_bank','e_wallet','cod')),
    amount      NUMERIC(12,2) NOT NULL,
    status      VARCHAR(10)   NOT NULL DEFAULT 'pending'
                    CHECK (status IN ('pending','success','failed')),
    paid_at     TIMESTAMP     DEFAULT NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL cart_items
-- ============================================================
CREATE TABLE IF NOT EXISTS cart_items (
    id          SERIAL PRIMARY KEY,
    user_id     INT  NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id  INT  NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity    INT  NOT NULL DEFAULT 1,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, product_id)
);

-- ============================================================
-- TABEL reviews
-- ============================================================
CREATE TABLE IF NOT EXISTS reviews (
    id            SERIAL PRIMARY KEY,
    product_id    INT      NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    user_id       INT      NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    order_item_id INT      DEFAULT NULL REFERENCES order_items(id),
    rating        SMALLINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment       TEXT     DEFAULT NULL,
    photo         VARCHAR(255) DEFAULT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS notifications (
    id         SERIAL PRIMARY KEY,
    user_id    INT          NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title      VARCHAR(150) NOT NULL,
    message    TEXT         NOT NULL,
    type       VARCHAR(50)  NOT NULL DEFAULT 'info',
    is_read    BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABEL migrations (tracker)
-- ============================================================
CREATE TABLE IF NOT EXISTS migrations (
    id         SERIAL PRIMARY KEY,
    filename   VARCHAR(255) NOT NULL UNIQUE,
    ran_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- INDEX tambahan untuk performa
-- ============================================================
CREATE INDEX IF NOT EXISTS idx_products_category  ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_active     ON products(is_active);
CREATE INDEX IF NOT EXISTS idx_orders_user         ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status       ON orders(status);
CREATE INDEX IF NOT EXISTS idx_cart_user           ON cart_items(user_id);
CREATE INDEX IF NOT EXISTS idx_notif_user          ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_reviews_product     ON reviews(product_id);
