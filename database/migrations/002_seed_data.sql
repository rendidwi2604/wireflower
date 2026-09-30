-- ============================================================
-- WireFlower PostgreSQL Migration
-- 002_seed_data.sql
-- Data awal: categories, products, users, dan sample data
-- ============================================================

-- ============================================================
-- CATEGORIES
-- ============================================================
INSERT INTO categories (id, name, slug) VALUES
(1, 'Buket Mawar',       'buket-mawar'),
(2, 'Buket Lily',        'buket-lily'),
(3, 'Buket Tulip',       'buket-tulip'),
(4, 'Buket Wisuda',      'buket-wisuda'),
(5, 'Buket Ulang Tahun', 'buket-ulang-tahun'),
(6, 'Buket Custom',      'buket-custom')
ON CONFLICT (id) DO NOTHING;

-- Sync sequence setelah insert dengan id eksplisit
SELECT setval('categories_id_seq', (SELECT MAX(id) FROM categories));

-- ============================================================
-- PRODUCTS
-- ============================================================
INSERT INTO products (id, category_id, name, slug, description, price, stock, image, sold_count, is_featured, is_active, created_at) VALUES
(1, 1, 'Buket Mawar Merah Kawat Bulu',         'buket-mawar-merah-kawat-bulu',  'Buket mawar cantik terbuat dari kawat bulu (chenille), tahan lama dan bisa jadi hiasan.',    85000,  17, 'mawar-merah.jpg', 0, TRUE,  TRUE, '2026-09-22 13:43:53'),
(2, 2, 'Buket Lily Putih Kawat Bulu',           'buket-lily-putih-kawat-bulu',   'Buket lily elegan dari kawat bulu, cocok untuk hadiah spesial.',                              95000,  15, 'lily-putih.jpg',  0, TRUE,  TRUE, '2026-09-22 13:43:53'),
(3, 3, 'Buket Tulip Pink Kawat Bulu',           'buket-tulip-pink-kawat-bulu',   'Buket tulip warna pink lembut, unik dan awet.',                                               90000,  18, 'tulip-pink.jpg',  0, FALSE, TRUE, '2026-09-22 13:43:53'),
(4, 4, 'Buket Wisuda Kawat Bulu Mix',           'buket-wisuda-kawat-bulu-mix',   'Buket wisuda kombinasi warna, cocok untuk momen kelulusan.',                                  120000, 10, 'wisuda.jpg',      0, TRUE,  TRUE, '2026-09-22 13:43:53'),
(5, 5, 'Buket Ulang Tahun Kawat Bulu Warna Warni','buket-ultah-kawat-bulu',      'Buket ulang tahun ceria dengan banyak warna.',                                                100000, 12, 'ultah.jpg',       0, FALSE, TRUE, '2026-09-22 13:43:53'),
(6, 6, 'Buket Custom Sesuai Request',           'buket-custom-request',          'Buket custom, warna dan bentuk bisa disesuaikan permintaan.',                                 150000,  5, 'custom.jpg',      0, FALSE, TRUE, '2026-09-22 13:43:53')
ON CONFLICT (id) DO NOTHING;

SELECT setval('products_id_seq', (SELECT MAX(id) FROM products));

-- ============================================================
-- USERS
-- password 'admin12' (plain) untuk admin pertama — GANTI setelah deploy!
-- password user lain sudah di-hash dengan bcrypt
-- ============================================================
INSERT INTO users (id, name, email, password, phone, role, notif_email, notif_push, privacy_public_profile, created_at) VALUES
(1, 'Admin12',      'admin@wireflower.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL,           'admin',    TRUE, TRUE, FALSE, '2026-09-22 13:43:53'),
(2, 'murni murni',  'murniptr8@gmail.com',    '$2y$10$TbDHRN7F79gje3DNFlKGIOE5QfO8LtoJJTLMHVVx/mNtol5b1tDG6', '083164905202', 'customer', TRUE, TRUE, FALSE, '2026-09-22 14:18:44'),
(3, 'rini',         'rachmamiftahj@gmail.com','$2y$10$xsfdnHLql3LCze4UR7eYBuPlwYFaDq8sxPgWSzbEKlOoM/eJ5B5.e', '083164905202', 'admin',    TRUE, TRUE, FALSE, '2026-09-22 15:28:00')
ON CONFLICT (id) DO NOTHING;

SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));

-- ============================================================
-- ADDRESSES
-- ============================================================
INSERT INTO addresses (id, user_id, label, recipient_name, phone, full_address, city, province, postal_code, is_default) VALUES
(1, 2, 'rumah', 'murni', '083164905202', 'jakarta barat jl bendi besar', 'jakarta', 'jakarta', '39494', TRUE)
ON CONFLICT (id) DO NOTHING;

SELECT setval('addresses_id_seq', (SELECT MAX(id) FROM addresses));

-- ============================================================
-- ORDERS
-- ============================================================
INSERT INTO orders (id, order_code, user_id, address_id, subtotal, shipping_cost, total, status, note, created_at) VALUES
(1, 'WF202609222E4FF4', 2, 1, 85000,  15000, 100000, 'dikemas', '', '2026-09-22 14:21:38'),
(2, 'WF20260922FC1FF0', 2, 1, 170000, 15000, 185000, 'dikemas', '', '2026-09-22 15:05:51')
ON CONFLICT (id) DO NOTHING;

SELECT setval('orders_id_seq', (SELECT MAX(id) FROM orders));

-- ============================================================
-- ORDER ITEMS
-- ============================================================
INSERT INTO order_items (id, order_id, product_id, product_name, price, quantity, subtotal) VALUES
(1, 1, 1, 'Buket Mawar Merah Kawat Bulu', 85000, 1, 85000),
(2, 2, 1, 'Buket Mawar Merah Kawat Bulu', 85000, 2, 170000)
ON CONFLICT (id) DO NOTHING;

SELECT setval('order_items_id_seq', (SELECT MAX(id) FROM order_items));

-- ============================================================
-- PAYMENTS
-- ============================================================
INSERT INTO payments (id, order_id, method, amount, status, paid_at, created_at) VALUES
(1, 1, 'transfer_bank', 100000, 'success', '2026-09-22 14:21:53', '2026-09-22 14:21:38'),
(2, 2, 'cod',           185000, 'success', '2026-09-22 08:28:28', '2026-09-22 15:05:51')
ON CONFLICT (id) DO NOTHING;

SELECT setval('payments_id_seq', (SELECT MAX(id) FROM payments));

-- ============================================================
-- NOTIFICATIONS
-- ============================================================
INSERT INTO notifications (id, user_id, title, message, type, is_read, created_at) VALUES
(1, 2, 'Pesanan Berhasil Dibuat',   'Pesanan #WF202609222E4FF4 berhasil dibuat. Silakan lakukan pembayaran.',                               'order_created',  TRUE, '2026-09-22 14:21:38'),
(2, 2, 'Pembayaran Berhasil',       'Pembayaran untuk pesanan #WF202609222E4FF4 berhasil dikonfirmasi (simulasi). Pesanan sedang dikemas.',  'payment_success',TRUE, '2026-09-22 14:21:53'),
(3, 2, 'Pesanan Sedang Dikemas',    'Pesanan #WF202609222E4FF4 sedang dikemas oleh Wire Flower.',                                            'order_packing',  TRUE, '2026-09-22 14:21:53'),
(4, 2, 'Pesanan Berhasil Dibuat',   'Pesanan #WF20260922FC1FF0 berhasil dibuat. Silakan lakukan pembayaran.',                               'order_created',  TRUE, '2026-09-22 15:05:51'),
(5, 2, 'Pembayaran Berhasil',       'Pembayaran untuk pesanan #WF20260922FC1FF0 berhasil dikonfirmasi (simulasi). Pesanan sedang dikemas.', 'payment_success',TRUE, '2026-09-22 15:06:04'),
(6, 2, 'Pesanan Sedang Dikemas',    'Pesanan #WF20260922FC1FF0 sedang dikemas oleh Wire Flower.',                                            'order_packing',  TRUE, '2026-09-22 15:06:04')
ON CONFLICT (id) DO NOTHING;

SELECT setval('notifications_id_seq', (SELECT MAX(id) FROM notifications));
