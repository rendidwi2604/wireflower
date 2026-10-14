# Setup Google OAuth untuk WireFlower

Kode Google OAuth sudah tersedia di project. Kamu hanya perlu membuat credentials di Google Cloud Console dan memasukkannya ke Vercel.

---

## Step 1 — Buat Project di Google Cloud Console

1. Buka [console.cloud.google.com](https://console.cloud.google.com)
2. Klik dropdown project di atas → **New Project**
3. Isi nama project: `WireFlower` → klik **Create**
4. Pastikan project `WireFlower` sudah terpilih di dropdown atas

---

## Step 2 — Buat OAuth Consent Screen (Branding)

1. Di menu kiri → **Google Auth Platform** (atau cari di search bar)
2. Klik **Get Started** jika baru pertama kali
3. Isi form **App Information**:
   - **App name**: `WireFlower`
   - **User support email**: pilih email kamu
4. Scroll ke bawah → **Audience**:
   - Pilih **External** → klik **Next**
5. Isi **Contact Information**:
   - **Email addresses**: masukkan email kamu
6. Centang persetujuan → klik **Create**

---

## Step 3 — Buat OAuth Client ID (Credentials)

1. Di menu kiri → **Clients**
2. Klik **+ Create Client** (atau **Create OAuth client ID**)
3. **Application type**: pilih `Web application`
4. **Name**: isi `WireFlower Web`
5. Di **Authorized JavaScript origins** → klik **+ Add URI**, tambahkan satu per satu:
   ```
   https://wireflower.my.id
   https://www.wireflower.my.id
   https://wireflower.vercel.app
   ```
6. Di **Authorized redirect URIs** → klik **+ Add URI**, tambahkan satu per satu:
   ```
   https://wireflower.my.id/auth/google-callback.php
   https://www.wireflower.my.id/auth/google-callback.php
   https://wireflower.vercel.app/auth/google-callback.php
   ```
7. Klik **Create**
8. Muncul popup **"OAuth client created"** berisi:
   - **Client ID** — copy ini
   - **Client Secret** — copy ini
   - Atau klik **Download JSON** untuk simpan

---

## Step 4 — Masukkan ke Vercel Environment Variables

1. Buka [vercel.com/dashboard](https://vercel.com/dashboard)
2. Pilih project **wireflower**
3. Tab **Settings** → **Environment Variables**
4. Tambahkan dua variable berikut (klik **Add** untuk masing-masing):

| Key | Value |
|-----|-------|
| `GOOGLE_CLIENT_ID` | `xxxxxx.apps.googleusercontent.com` |
| `GOOGLE_CLIENT_SECRET` | `GOCSPX-xxxxxx` |

5. Pastikan environment yang dicentang: ✅ Production ✅ Preview ✅ Development
6. Klik **Save**

---

## Step 5 — Redeploy Vercel

Setelah menambah environment variables, Vercel perlu di-redeploy:

1. Tab **Deployments**
2. Klik titik tiga (**⋯**) di deployment terbaru → **Redeploy**
3. Tunggu ~1 menit sampai status **Ready**

---

## Step 6 — Test Google Login

1. Buka `https://wireflower.my.id/auth/login.php`
2. Tombol **"Lanjutkan dengan Google"** seharusnya sudah aktif (tidak disabled)
3. Klik → pilih akun Google → langsung masuk ke halaman utama ✅

---

## Troubleshooting

**Tombol Google masih disabled / tidak muncul:**
- Environment variables belum terbaca → pastikan sudah redeploy setelah menambah variabel

**Error "redirect_uri_mismatch":**
- Cek Authorized redirect URIs di Google Console — harus persis sama, tidak boleh ada trailing slash

**Error "Access blocked: This app's request is invalid":**
- Ulangi Step 2 — pastikan OAuth consent screen sudah selesai dikonfigurasi

**Akun Google tidak bisa login tapi email sudah terdaftar:**
- Normal — akun yang dibuat via email/password tidak bisa login via Google
- User perlu login dengan email/password biasa

---

## Catatan Teknis

Tidak perlu ubah kode apapun. Implementasi sudah lengkap di:
- `app/Controllers/AuthController.php` — `googleLogin()` dan `googleCallback()`
- `app/Views/auth/login.php` — tombol Google aktif otomatis jika config terisi
- `app/Views/auth/register.php` — tombol Google juga tersedia
- `auth/google-login.php` — entry point
- `auth/google-callback.php` — callback handler
- `app/Models/UserModel.php` — `createFromGoogle()`, `findByGoogle()`, `linkGoogle()`
