<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\UserModel;

class UserController extends Controller
{
    public function index(): void
    {
        require_admin();
        $users = $this->model(UserModel::class);

        if (isset($_POST['toggle_role'])) {
            $users->toggleRole((int) $_POST['user_id']);
            flash('success', 'Role pengguna diperbarui.');
            redirect('admin/pengguna.php');
        }

        if (isset($_GET['delete'])) {
            $users->deleteExceptSelf($_GET['delete'], $this->userId());
            flash('info', 'Pengguna dihapus.');
            redirect('admin/pengguna.php');
        }

        $this->render('admin/users', [
            'page_title' => 'Kelola Pengguna',
            'users' => $users->all(),
        ], 'admin');
    }
}
