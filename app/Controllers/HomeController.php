<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class HomeController extends Controller
{
    public function index(): void
    {
        $products = $this->model(ProductModel::class);

        $this->render('home', [
            'page_title' => 'Beranda',
            'terbaru' => $products->latest(8),
            'rekomendasi' => $products->featured(8),
            'kategori' => $this->model(CategoryModel::class)->all(),
        ]);
    }
}
