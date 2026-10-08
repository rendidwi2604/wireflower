<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ReviewModel;

class ProductController extends Controller
{
    public function show(): void
    {
        $product = $this->model(ProductModel::class)->findBySlug($_GET['slug'] ?? '');

        if (!$product) {
            $this->render('product', [
                'page_title' => 'Produk tidak ditemukan',
                'product' => null,
                'reviews' => [],
                'avg_rating' => 0,
            ]);
            return;
        }

        $reviews = $this->model(ReviewModel::class)->forProduct((int) $product['id']);
        $avgRating = $reviews ? array_sum(array_column($reviews, 'rating')) / count($reviews) : 0;

        $this->render('product', [
            'page_title' => $product['name'],
            'product' => $product,
            'reviews' => $reviews,
            'avg_rating' => $avgRating,
        ]);
    }

    public function category(): void
    {
        $slug       = $_GET['slug'] ?? '';
        $sort       = $_GET['sort'] ?? 'terbaru';
        $categories = $this->model(CategoryModel::class);
        $products   = $this->model(ProductModel::class);
        $active     = $slug !== '' ? $categories->findBySlug($slug) : null;

        if ($slug !== '' && !$active) {
            // slug ada tapi tidak ditemukan
            $productList = [];
        } elseif ($active) {
            $productList = $products->byCategory((int) $active['id']);
        } else {
            // slug kosong = Semua
            $productList = $products->all($sort);
        }

        $this->render('category', [
            'page_title'      => 'Kategori Produk',
            'kategori_list'   => $categories->all(),
            'slug'            => $slug,
            'sort'            => $sort,
            'active_category' => $active,
            'products'        => $productList,
        ]);
    }

    public function search(): void
    {
        $q = trim($_GET['q'] ?? '');

        $this->render('search', [
            'page_title' => 'Hasil Pencarian',
            'q' => $q,
            'category_id' => $_GET['category_id'] ?? '',
            'sort' => $_GET['sort'] ?? '',
            'kategori' => $this->model(CategoryModel::class)->all(),
            'products' => $this->model(ProductModel::class)->search(
                $q,
                $_GET['category_id'] ?? '',
                $_GET['sort'] ?? ''
            ),
        ]);
    }
}
