<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class AuthController extends Controller
{
    private const GOOGLE_TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const GOOGLE_USERINFO_URL = 'https://openidconnect.googleapis.com/v1/userinfo';

    public function loginForm(): void
    {
        if (is_logged_in()) {
            redirect('index.php');
        }
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->model(UserModel::class)->findByEmail(trim($_POST['email'] ?? ''));
            if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
                login_user($user);
                redirect($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
            }
            $error = 'Email atau password salah.';
        }

        $this->render('auth/login', ['page_title' => 'Login', 'error' => $error]);
    }

    public function registerForm(): void
    {
        if (is_logged_in()) {
            redirect('index.php');
        }
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = ($_POST['role'] ?? 'customer') === 'admin' ? 'admin' : 'customer';

            if ($name === '' || $email === '' || $password === '') {
                $errors[] = 'Semua field wajib diisi.';
            }
            if ($password !== ($_POST['confirm_password'] ?? '')) {
                $errors[] = 'Konfirmasi password tidak cocok.';
            }
            if (strlen($password) < 6) {
                $errors[] = 'Password minimal 6 karakter.';
            }
            if ($role === 'admin' && trim($_POST['admin_code'] ?? '') !== config('admin_register_code')) {
                $errors[] = 'Kode pendaftaran Admin salah.';
            }

            $users = $this->model(UserModel::class);
            if (!$errors && $users->emailExists($email)) {
                $errors[] = 'Email sudah terdaftar.';
            }
            if (!$errors) {
                $users->create($name, $email, $password, $phone, $role);
                flash('success', 'Registrasi berhasil sebagai ' . ($role === 'admin' ? 'Admin' : 'Pembeli') . '! Silakan login.');
                redirect('auth/login.php');
            }
        }

        $this->render('auth/register', ['page_title' => 'Daftar Akun', 'errors' => $errors]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect('auth/login.php');
    }

    // ---- Google OAuth ----

    public function googleLogin(): void
    {
        if (is_logged_in()) {
            redirect('index.php');
        }
        if (!$this->googleConfigured()) {
            flash('warning', 'Google Login belum dikonfigurasi. Isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di environment server Anda.');
            redirect('auth/login.php');
        }

        $state = bin2hex(random_bytes(32));
        $_SESSION['google_oauth_state'] = $state;

        $query = http_build_query([
            'client_id' => config('google_client_id'),
            'redirect_uri' => google_redirect_uri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => $state,
        ]);
        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $query);
        exit;
    }

    public function googleCallback(): void
    {
        if (is_logged_in()) {
            redirect('index.php');
        }
        if (!$this->googleConfigured()) {
            flash('warning', 'Google Login belum dikonfigurasi.');
            redirect('auth/login.php');
        }
        if (!isset($_GET['code'], $_GET['state'])) {
            flash('danger', 'Login Google dibatalkan.');
            redirect('auth/login.php');
        }
        if (!hash_equals($_SESSION['google_oauth_state'] ?? '', $_GET['state'])) {
            flash('danger', 'State OAuth tidak valid.');
            redirect('auth/login.php');
        }
        unset($_SESSION['google_oauth_state']);

        $profile = $this->fetchGoogleProfile((string) $_GET['code']);
        if ($profile === null) {
            flash('danger', 'Login Google gagal.');
            redirect('auth/login.php');
        }

        $email = trim($profile['email'] ?? '');
        $googleId = $profile['sub'] ?? null;
        if ($email === '' || !$googleId) {
            flash('danger', 'Data Google tidak lengkap.');
            redirect('auth/login.php');
        }

        $users = $this->model(UserModel::class);
        $user = $users->findByGoogle($googleId, $email);

        if ($user) {
            if (empty($user['google_id'])) {
                $users->linkGoogle((int) $user['id'], $googleId);
            }
        } elseif ($users->emailExists($email)) {
            flash('warning', 'Email ini sudah terdaftar pada akun non-Google. Silakan login dengan email/password biasa.');
            redirect('auth/login.php');
        } else {
            $name = trim($profile['name'] ?? ($profile['given_name'] ?? 'Pengguna Google'));
            $user = $users->find($users->createFromGoogle($name, $email, $googleId));
        }

        login_user($user);
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    }

    private function googleConfigured(): bool
    {
        return has_google_oauth_config();
    }

    private function fetchGoogleProfile(string $code): ?array
    {
        $token = $this->httpJson(self::GOOGLE_TOKEN_URL, [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\nAccept: application/json\r\n",
            'content' => http_build_query([
                'code' => $code,
                'client_id' => config('google_client_id'),
                'client_secret' => config('google_client_secret'),
                'redirect_uri' => google_redirect_uri(),
                'grant_type' => 'authorization_code',
            ]),
        ]);
        $accessToken = $token['access_token'] ?? null;
        if (!$accessToken) {
            return null;
        }

        return $this->httpJson(self::GOOGLE_USERINFO_URL, [
            'method' => 'GET',
            'header' => "Authorization: Bearer {$accessToken}\r\nAccept: application/json\r\n",
        ]);
    }

    private function httpJson(string $url, array $options): ?array
    {
        $context = stream_context_create(['http' => $options + ['timeout' => 30]]);
        $raw = @file_get_contents($url, false, $context);
        return $raw === false ? null : json_decode($raw, true);
    }
}
