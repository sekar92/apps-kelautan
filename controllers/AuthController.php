<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public function showLogin(): void {
        if (Auth::check()) {
            redirect('/');
        }
        $pageTitle = "Masuk ke Portal - Hub Aplikasi Kelautan";
        require __DIR__ . '/../views/login.php';
    }

    public function processLogin(): void {
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/login', null, 'Token sesi tidak valid. Silakan coba lagi.');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            redirect('/login', null, 'Email dan kata sandi wajib diisi.');
        }

        $user = User::findByEmail($email);
        if (!$user) {
            redirect('/login', null, 'Email atau kata sandi tidak cocok.');
        }

        if (empty($user['is_active'])) {
            redirect('/login', null, 'Akun Anda sedang dinonaktifkan. Hubungi Administrator.');
        }

        if (!password_verify($password, $user['password'])) {
            redirect('/login', null, 'Email atau kata sandi tidak cocok.');
        }

        Auth::login($user);

        $target = $_SESSION['redirect_after_login'] ?? '/';
        unset($_SESSION['redirect_after_login']);

        redirect($target, 'Selamat datang kembali, ' . htmlspecialchars($user['name']) . '!');
    }

    public function logout(): void {
        Auth::logout('Anda telah berhasil keluar.');
        redirect('/login');
    }

    public function showProfile(): void {
        Auth::requireLogin();
        $currentUser = User::findById(Auth::id());
        $pageTitle = "Profil Akun - Portal Hub Kelautan";
        require __DIR__ . '/../views/profile.php';
    }

    public function updateProfile(): void {
        Auth::requireLogin();
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/profile', null, 'Token sesi tidak valid.');
        }

        $name = trim($_POST['name'] ?? '');
        $department = trim($_POST['department'] ?? '');

        if (empty($name)) {
            redirect('/profile', null, 'Nama lengkap tidak boleh kosong.');
        }

        User::update(Auth::id(), [
            'name' => $name,
            'department' => $department
        ]);

        $_SESSION['user_name'] = $name;
        $_SESSION['user_department'] = $department;
        Auth::logActivity(Auth::id(), 'PROFILE_UPDATE', 'Pengguna memperbarui informasi profil.');

        redirect('/profile', 'Informasi profil berhasil diperbarui.');
    }

    public function updatePassword(): void {
        Auth::requireLogin();
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            redirect('/profile', null, 'Token sesi tidak valid.');
        }

        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        $user = User::findById(Auth::id());

        if (!password_verify($currentPass, $user['password'])) {
            redirect('/profile', null, 'Kata sandi saat ini tidak sesuai.');
        }

        if (strlen($newPass) < 6) {
            redirect('/profile', null, 'Kata sandi baru minimal 6 karakter.');
        }

        if ($newPass !== $confirmPass) {
            redirect('/profile', null, 'Konfirmasi kata sandi baru tidak cocok.');
        }

        User::updatePassword(Auth::id(), $newPass);
        Auth::logActivity(Auth::id(), 'PASSWORD_CHANGE', 'Pengguna berhasil mengganti kata sandi.');

        redirect('/profile', 'Kata sandi Anda berhasil diperbarui.');
    }
}
