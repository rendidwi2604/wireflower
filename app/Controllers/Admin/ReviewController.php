<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\ReviewModel;

class ReviewController extends Controller
{
    public function index(): void
    {
        require_admin();
        $reviews = $this->model(ReviewModel::class);

        if (isset($_GET['delete'])) {
            $reviews->delete($_GET['delete']);
            flash('info', 'Ulasan dihapus.');
            redirect('admin/ulasan.php');
        }

        $this->render('admin/reviews', [
            'page_title' => 'Kelola Ulasan',
            'reviews' => $reviews->allWithNames(),
        ], 'admin');
    }
}
