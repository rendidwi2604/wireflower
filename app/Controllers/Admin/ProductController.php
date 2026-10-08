<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class ProductController extends Controller
{
    public function index(): void
    {
        require_admin();
        $products = $this->model(ProductModel::class);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
            $id = $_POST['id'] !== '' ? (int) $_POST['id'] : null;
            $products->save($id, [
                'name' => trim($_POST['name']),
                'category_id' => $_POST['category_id'],
                'description' => trim($_POST['description']),
                'price' => $_POST['price'],
                'stock' => $_POST['stock'],
                'is_featured' => isset($_POST['is_featured']),
                'is_active' => isset($_POST['is_active']),
            ], $this->saveUploadedImage());
            flash('success', $id ? 'Produk berhasil diperbarui.' : 'Produk baru ditambahkan.');
            redirect('admin/produk.php');
        }

        if (isset($_GET['delete'])) {
            $products->delete($_GET['delete']);
            flash('info', 'Produk dihapus.');
            redirect('admin/produk.php');
        }

        $this->render('admin/products', [
            'page_title' => 'Kelola Produk',
            'edit_product' => isset($_GET['edit']) ? $products->find($_GET['edit']) : null,
            'categories' => $this->model(CategoryModel::class)->all(),
            'products' => $products->allWithCategory(),
        ], 'admin');
    }

    private function saveUploadedImage(): ?string
    {
        if (empty($_FILES['image']['name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext      = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','webp','gif'];
        if (!in_array($ext, $allowed, true)) {
            flash('danger', 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WebP.');
            return null;
        }

        $filename    = 'product_' . uniqid() . '.' . $ext;
        $fileContent = file_get_contents($_FILES['image']['tmp_name']);

        // ── Coba Supabase Storage (untuk Vercel / server read-only) ──
        $supabaseUrl = '';
        $supabaseKey = '';

        // Vercel meletakkan env vars di $_SERVER
        foreach (['SUPABASE_URL', 'supabase_url'] as $k) {
            $v = getenv($k) ?: ($_ENV[$k] ?? '') ?: ($_SERVER[$k] ?? '');
            if ($v) { $supabaseUrl = $v; break; }
        }
        foreach (['SUPABASE_KEY', 'supabase_key'] as $k) {
            $v = getenv($k) ?: ($_ENV[$k] ?? '') ?: ($_SERVER[$k] ?? '');
            if ($v) { $supabaseKey = $v; break; }
        }

        if ($supabaseUrl && $supabaseKey) {
            $bucket   = 'products';
            $endpoint = rtrim($supabaseUrl, '/') . '/storage/v1/object/' . $bucket . '/' . $filename;

            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $fileContent,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $supabaseKey,
                    'Content-Type: image/' . ($ext === 'jpg' ? 'jpeg' : $ext),
                    'x-upsert: true',
                ],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 || $httpCode === 201) {
                // Kembalikan public URL Supabase Storage
                return rtrim($supabaseUrl, '/') . '/storage/v1/object/public/' . $bucket . '/' . $filename;
            }
            // Log error tapi lanjut ke fallback
            error_log('[Supabase Storage] HTTP ' . $httpCode . ': ' . $response);
        }

        // ── Fallback: simpan ke filesystem lokal (localhost XAMPP) ──
        $dest = BASE_PATH . '/assets/img/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            return $filename;
        }

        flash('danger', 'Gagal mengupload gambar. Pastikan Supabase Storage sudah dikonfigurasi.');
        return null;
    }
}
