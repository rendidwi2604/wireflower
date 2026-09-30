# 🌸 Wire Flower - E-Commerce Buket Kawat Bulu

Website e-commerce untuk menjual buket bunga dari kawat bulu (chenille), dibangun dengan **PHP native (PDO) + MySQL + Bootstrap 5**.

## Fitur
- Login / Register / Logout
- Beranda (produk terbaru, terlaris, rekomendasi/promo)
- Pencarian produk + filter kategori + sorting harga
- Kategori produk (Mawar, Lily, Tulip, Wisuda, Ulang Tahun, Custom)
- Detail produk (foto, harga, deskripsi, stok, tambah ke keranjang, beli sekarang)
- Keranjang belanja (tambah/kurang/hapus, subtotal, total)
- Checkout (alamat, ongkir, ringkasan pesanan)
- Pembayaran **simulasi/dummy** (transfer bank, e-wallet, COD)
- Pesanan saya dengan status: Belum Bayar, Dikemas, Dikirim, Selesai, Dibatalkan
- Notifikasi otomatis di tiap perubahan status
- Penilaian pembeli (rating, ulasan, upload foto)
- Profil & Pengaturan (edit profil, ubah password, alamat, notifikasi, privasi)
- Admin Panel lengkap: dashboard, kelola produk/kategori/pesanan/pembayaran/pengguna/ulasan, laporan penjualan

## Instalasi (menggunakan XAMPP / Laragon)

1. **Salin folder** `wireflower` ke folder `htdocs` (XAMPP) atau `www` (Laragon).
2. **Buat database**: buka phpMyAdmin, lalu import file `database.sql` (ini akan otomatis membuat database `wireflower` beserta tabel dan data contoh).
3. **Atur koneksi database** di `config/app.php` jika username/password MySQL kamu berbeda dari default (`root` tanpa password). Kode pendaftaran admin dan kredensial Google OAuth juga ada di file yang sama.
4. **Buat folder gambar**: pastikan folder `assets/img/` ada dan bisa ditulis (untuk upload gambar produk & ulasan). Sudah dibuatkan folder kosong beserta `.gitkeep`.
5. Akses melalui browser: `http://localhost/wireflower/index.php`

## Login Admin & Pembeli (Satu Halaman Login)
Halaman **Login** (`/auth/login.php`) berlaku untuk **Admin maupun Pembeli** — sistem otomatis mendeteksi role akun dan mengarahkan:
- Role `admin` → diarahkan ke `/admin/index.php` (Admin Panel)
- Role `customer` → diarahkan ke `/index.php` (Beranda)

## Cara Membuat Akun Admin
Saat **Registrasi** (`/auth/register.php`), pilih tombol **"Admin"** lalu masukkan **Kode Pendaftaran Admin**.

- Kode default: `WIREFLOWER-ADMIN-2026`
- Kode ini bisa diganti di file `config/app.php`, cari baris:
  ```php
  'admin_register_code' => 'WIREFLOWER-ADMIN-2026',
  ```
  Ganti nilainya dengan kode rahasia milikmu sendiri sebelum website digunakan secara nyata, agar tidak sembarang orang bisa mendaftar sebagai Admin.

Jika memilih **"Pembeli"** saat daftar, tidak perlu kode apa pun — langsung bisa daftar seperti biasa.

Admin juga bisa mengubah role pengguna lain kapan saja lewat menu **Kelola Pengguna** di Admin Panel (tombol "Jadikan Admin/Customer").

## Arsitektur (MVC)
Pola **MVC** murni tanpa framework atau Composer. Tidak ada `.htaccess`, tidak ada
front controller — setiap file `.php` di root/`auth/`/`admin/` tetap punya URL
yang sama seperti sebelumnya, tetapi sekarang isinya cuma pemanggilan Controller.

```
wireflower/
├── app/
│   ├── bootstrap.php         # Autoloader App\ + session + manggil helpers
│   ├── helpers.php           # config(), db(), guard auth, site_url(), e(), rupiah()
│   ├── Core/
│   │   ├── Model.php         # Base: fetchAll/fetchOne/fetchValue
│   │   └── Controller.php    # Base: render() + layout, model() cache
│   ├── Models/               # M — semua query SQL ada di sini
│   │   ├── ProductModel.php      CategoryModel.php
│   │   ├── UserModel.php         (user + alamat)
│   │   ├── CartModel.php         OrderModel.php
│   │   ├── PaymentModel.php      NotificationModel.php
│   │   └── ReviewModel.php
│   ├── Controllers/          # C — mengatur alur request
│   │   ├── HomeController.php     ProductController.php   (produk, kategori, search)
│   │   ├── CartController.php     CheckoutController.php
│   │   ├── OrderController.php    PaymentController.php
│   │   ├── AccountController.php  (profil, pengaturan, notifikasi, ulasan)
│   │   ├── AuthController.php     (login, register, logout, Google OAuth)
│   │   └── Admin/                 (Dashboard, Product, Category, Order,
│   │                              Payment, User, Review, Report)
│   └── Views/                # V — HTML murni, tanpa query
│       ├── layouts/          # site.php (navbar+footer), admin.php (sidebar)
│       ├── partials/         # product_grid.php
│       ├── *.php             # home, product, category, search, cart, ...
│       ├── auth/             # login, register
│       └── admin/            # dashboard, products, categories, ...
├── config/app.php        # Kredensial DB, kode admin, kredensial Google
├── admin/                # Entry script panel admin
├── auth/                 # Entry script login/register/logout/Google
├── assets/               # css + img (upload)
├── index.php             # Entry script: beranda
├── kategori.php  produk.php  search.php  keranjang.php  cart_actions.php
├── checkout.php  proses_checkout.php  pembayaran.php  pesanan.php
├── ulasan.php  notifikasi.php  profil.php  pengaturan.php
└── wireflower.sql         # Skema database lengkap
```

**Alur satu request:** `produk.php` → `ProductController::show()` → `ProductModel` /
`ReviewModel` (SQL) → `Views/product.php` dibungkus `Views/layouts/site.php`.

Menambah halaman baru cukup 3 langkah: tambah method Model, tambah method
Controller dengan `$this->render(...)`, tambah file View. Tidak perlu daftar
routing, tidak perlu edit file lain.

## Catatan Penting
- **Pembayaran bersifat simulasi/dummy** — tidak terhubung ke payment gateway nyata (Midtrans/Xendit dsb). Untuk produksi, ganti `PaymentController::show()` dengan integrasi payment gateway asli.
- Ongkos kirim masih **flat Rp 15.000** (lihat `SHIPPING_FLAT` di `app/Controllers/CheckoutController.php`), silakan sambungkan ke API ongkir (RajaOngkir dll) bila diperlukan.
- Semua password di-hash menggunakan `password_hash()` PHP (bcrypt) — aman digunakan.
- `app/` dan `config/` sebaiknya tidak bisa diakses langsung lewat browser. Tambahkan `Require all denied` di `.htaccess` di dalam kedua folder tersebut (butuh `AllowOverride All` di konfigurasi Apache).
