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

        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowed, true)) {
            flash('danger', 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WebP.');
            return null;
        }

        $filename = 'product_' . uniqid('', true) . '.' . $ext;
        $mimeType = 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
        $content  = file_get_contents($_FILES['image']['tmp_name']);

        // Coba Supabase Storage
        $url = supabase_upload('products', $filename, $content, $mimeType);
        if ($url) {
            return $url;
        }

        // Fallback: filesystem lokal (localhost)
        $dest = BASE_PATH . '/assets/img/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            return $filename;
        }

        flash('danger', 'Gagal mengupload gambar. Pastikan Supabase Storage sudah dikonfigurasi.');
        return null;
    }
}
