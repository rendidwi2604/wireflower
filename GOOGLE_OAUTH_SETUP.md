# Setup Google OAuth untuk WireFlower

Kode Google OAuth sudah tersedia di project. Kamu hanya perlu membuat credentials di Google Cloud Console dan memasukkannya ke Vercel.

---

## Step 1 — Buat Project di Google Cloud Console

1. Buka [console.cloud.google.com](https://console.cloud.google.com)
2. Klik dropdown project di atas → **New Project**
3. Isi nama project: `WireFlower` → klik **Create**
4. Pastikan project `WireFlower` sudah terpilih

---

## Step 2 — Aktifkan Google OAuth API

1. Di menu kiri → **APIs & Services** → **Library**
2. Cari **"Google+ API"** atau **"Google Identity"**
3. Klik **Google Identity Toolkit API** → **Enable**

---

## Step 3 — Buat OAuth Consent Screen

1. **APIs & Services** → **OAuth consent screen**
2. Pilih **External** → klik **Create**
3. Isi form:
   - **App name**: `WireFlower`
   - **User support email**: email kamu
   - **App logo**: upload logo WireFlower (opsional)
   - **Application home page**: `https://wireflower.my.id`
   - **Authorized domains**: tambahkan `wireflower.my.id`
   - **Developer contact email**: email kamu
4. Klik **Save and Continue**
5. Di halaman **Scopes** → klik **Add or Remove Scopes** → centang:
   - `openid`
   - `email`
   - `profile`
6. Klik **Update** → **Save and Continue**
7. Di halaman **Test users** → tambahkan email kamu untuk testing → **Save and Continue**
8. Klik **Back to Dashboard**

---

## Step 4 — Buat OAuth 2.0 Credentials

1. **APIs & Services** → **Credentials**
2. Klik **+ Create Credentials** → **OAuth 2.0 Client IDs**
3. Pilih **Application type**: `Web application`
4. Isi **Name**: `WireFlower Web`
5. Di **Authorized JavaScript origins**, tambahkan:
   ```
   https://wireflower.my.id
   https://www.wireflower.my.id
   https://wireflower.vercel.app
   ```
6. Di **Authorized redirect URIs**, tambahkan:
   ```
   https://wireflower.my.id/auth/google-callback.php
   https://www.wireflower.my.id/auth/google-callback.php
   https://wireflower.vercel.app/auth/google-callback.php
   ```
7. Klik **Create**
8. Sebuah popup muncul dengan **Client ID** dan **Client Secret** — **SALIN KEDUANYA**

---

## Step 5 — Masukkan ke Vercel Environment Variables

1. Buka [vercel.com/dashboard](https://vercel.com/dashboard)
2. Pilih project **wireflower**
3. Tab **Settings** → **Environment Variables**
4. Tambahkan dua variable berikut:

| Key | Value |
|-----|-------|
| `GOOGLE_CLIENT_ID` | `xxxxxx.apps.googleusercontent.com` |
| `GOOGLE_CLIENT_SECRET` | `GOCSPX-xxxxxx` |

5. Pastikan environment yang dicentang: ✅ Production ✅ Preview ✅ Development
6. Klik **Save** untuk masing-masing variable

---

## Step 6 — Redeploy Vercel

Setelah menambah environment variables, Vercel perlu di-redeploy agar variable terbaca:

1. Tab **Deployments**
2. Klik titik tiga (**⋯**) di deployment terbaru → **Redeploy**
3. Tunggu ~1 menit

---

## Step 7 — Test Google Login

1. Buka `https://wireflower.my.id/auth/login.php`
2. Klik tombol **"Lanjutkan dengan Google"**
3. Pilih akun Google kamu
4. Seharusnya langsung masuk ke halaman utama

---

## Troubleshooting

**Tombol Google masih disabled / tidak muncul:**
- Environment variables belum terbaca → pastikan sudah redeploy setelah menambah variabel

**Error "redirect_uri_mismatch":**
- Cek kembali Authorized redirect URIs di Google Console — harus persis sama dengan URL callback

**Error "Access blocked: This app's request is invalid":**
- OAuth consent screen belum selesai dikonfigurasi
- Pastikan domain `wireflower.my.id` sudah ditambahkan di Authorized domains

**Akun Google tidak bisa login tapi email sudah terdaftar:**
- Normal — akun yang dibuat via email/password tidak bisa login via Google
- User perlu login dengan email/password biasa

---

## Catatan Teknis

Implementasi sudah ada di:
- `app/Controllers/AuthController.php` — method `googleLogin()` dan `googleCallback()`
- `app/Views/auth/login.php` — tombol Google sudah ada (muncul otomatis jika config terisi)
- `app/Views/auth/register.php` — tombol Google juga tersedia
- `auth/google-login.php` — entry point
- `auth/google-callback.php` — callback handler
- `app/Models/UserModel.php` — method `createFromGoogle()`, `findByGoogle()`, `linkGoogle()`

Tidak perlu mengubah kode apapun — hanya perlu mengisi environment variables di Vercel.
