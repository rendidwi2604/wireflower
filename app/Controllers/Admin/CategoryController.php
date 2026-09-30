<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\CategoryModel;

class CategoryController extends Controller
{
    public function index(): void
    {
        require_admin();
        $categories = $this->model(CategoryModel::class);

        if (isset($_POST['save_category'])) {
            $id = $_POST['id'] !== '' ? (int) $_POST['id'] : null;
            $categories->save($id, trim($_POST['name']));
            flash('success', 'Kategori disimpan.');
            redirect('admin/kategori.php');
        }

        if (isset($_GET['delete'])) {
            $categories->delete($_GET['delete']);
            flash('info', 'Kategori dihapus.');
            redirect('admin/kategori.php');
        }

        $this->render('admin/categories', [
            'page_title' => 'Kelola Kategori',
            'edit' => isset($_GET['edit']) ? $categories->find($_GET['edit']) : null,
            'categories' => $categories->withProductCount(),
        ], 'admin');
    }
}
