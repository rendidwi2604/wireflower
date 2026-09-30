<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\NotificationModel;
use App\Models\ProductModel;
use App\Models\ReviewModel;
use App\Models\UserModel;

// Halaman & aksi yang seluruhnya milik akun sendiri: profil, pengaturan,
// notifikasi, dan pengiriman ulasan.
class AccountController extends Controller
{
    public function profile(): void
    {
        require_login();
        $users = $this->model(UserModel::class);

        $this->render('profile', [
            'page_title' => 'Profil Saya',
            'user' => $users->find($this->userId()),
            'addresses' => $users->addresses($this->userId()),
        ]);
    }

    public function settings(): void
    {
        require_login();
        $userId = $this->userId();
        $users = $this->model(UserModel::class);

        if (isset($_POST['update_profile'])) {
            $name = trim($_POST['name']);
            $users->updateProfile($userId, $name, trim($_POST['phone']));
            $_SESSION['name'] = $name;
            flash('success', 'Profil berhasil diperbarui.');
        }

        if (isset($_POST['change_password'])) {
            $user = $users->find($userId);
            if (password_verify($_POST['old_password'] ?? '', $user['password'])) {
                $users->updatePassword($userId, $_POST['new_password'] ?? '');
                flash('success', 'Password berhasil diubah.');
            } else {
                flash('danger', 'Password lama salah.');
            }
        }

        if (isset($_POST['add_address'])) {
            $users->addAddress($userId, $_POST + ['is_default' => isset($_POST['is_default'])]);
            flash('success', 'Alamat baru ditambahkan.');
        }

        if (isset($_POST['delete_address'])) {
            $users->deleteAddress((int) $_POST['delete_address'], $userId);
            flash('info', 'Alamat dihapus.');
        }

        if (isset($_POST['update_preferences'])) {
            $users->updatePreferences($userId, [
                'notif_email' => isset($_POST['notif_email']),
                'notif_push' => isset($_POST['notif_push']),
                'privacy_public_profile' => isset($_POST['privacy_public_profile']),
            ]);
            flash('success', 'Preferensi berhasil disimpan.');
        }

        $this->render('settings', [
            'page_title' => 'Pengaturan',
            'user' => $users->find($userId),
            'addresses' => $users->addresses($userId),
        ]);
    }

    public function notifications(): void
    {
        require_login();
        $userId = $this->userId();
        $notifications = $this->model(NotificationModel::class);

        $notifications->markAllRead($userId);

        $this->render('notifications', [
            'page_title' => 'Notifikasi',
            'notifs' => $notifications->forUser($userId),
        ]);
    }

    public function review(): void
    {
        require_login();
        $userId = $this->userId();

        $product = $this->model(ProductModel::class)->find((int) ($_GET['product_id'] ?? 0));
        if (!$product) {
            redirect('pesanan.php');
        }
        $orderItemId = (int) ($_GET['order_item_id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $photo = $this->saveUploadedPhoto();
            $this->model(ReviewModel::class)->create(
                (int) $product['id'],
                $userId,
                $orderItemId ?: null,
                (int) $_POST['rating'],
                trim($_POST['comment'] ?? ''),
                $photo
            );
            flash('success', 'Terima kasih atas ulasanmu!');
            redirect('produk.php?slug=' . $product['slug']);
        }

        $this->render('review', [
            'page_title' => 'Beri Ulasan',
            'product' => $product,
        ]);
    }

    private function saveUploadedPhoto(): ?string
    {
        if (empty($_FILES['photo']['name'])) {
            return null;
        }
        $name = 'review_' . uniqid() . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['photo']['tmp_name'], BASE_PATH . '/assets/img/' . $name);
        return $name;
    }
}
