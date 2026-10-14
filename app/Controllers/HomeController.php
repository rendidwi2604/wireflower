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
            'page_title'         => 'Beranda',
            'page_meta_desc'     => 'WireFlower — Toko bunga kawat bulu handmade premium. Dibuat satu per satu dengan penuh cinta. Tersedia single flower, bouquet, gift set & custom order. Pengiriman ke seluruh Indonesia.',
            'page_meta_keywords' => 'bunga kawat bulu, wire flower, handmade, bouquet, hadiah, kado, bunga artifisial, toko bunga online, WireFlower',
            'page_og_image'      => 'https://wireflower.my.id/assets/img/header1.png',
            'terbaru'            => $products->latest(8),
            'rekomendasi'        => $products->featured(8),
            'kategori'           => $this->model(CategoryModel::class)->all(),
        ]);
    }
}
