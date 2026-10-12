<?php
// View: Login Page
$error = getFlash('error');
$info = getFlash('info');
if (isset($_GET['expired'])) {
    $info = "Sesi Anda telah berakhir secara otomatis karena tidak ada aktivitas (Session Timeout 30 menit). Silakan masuk kembali.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Portal Hub Aplikasi Kelautan</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">

    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 30px rgba(6, 182, 212, 0.15);
            position: relative;
            z-index: 2;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-brand-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--ocean-cyan), var(--ocean-blue));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #ffffff;
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.4);
        }

        .demo-box {
            margin-top: 24px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px dashed var(--border-glass);
            border-radius: var(--radius-md);
            text-align: center;
        }

        .demo-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .demo-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="login-brand-icon">
                <i class="fa-solid fa-anchor"></i>
            </div>
            <h1 style="font-family: var(--font-heading); font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 6px;">
                Portal Hub Kelautan
            </h1>
            <p style="font-size: 13px; color: var(--text-secondary);">
                Satu pintu masuk seluruh aplikasi internal & Google Apps Script
            </p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div style="font-size: 13px;"><?= e($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="alert alert-info" style="margin-bottom: 20px;">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <div style="font-size: 13px;"><?= e($info) ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= url('/login') ?>" method="POST" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email Dinas</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="email" id="email" name="email" class="form-control" style="padding-left: 42px;" placeholder="nama@kelautan.id" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" id="password" name="password" class="form-control" style="padding-left: 42px;" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; padding: 12px;">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Portal
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="demo-box">
            <div class="demo-title">Akses Akun Demo Siap Pakai</div>
            <div class="demo-buttons">
                <button type="button" class="btn btn-secondary btn-sm" onclick="fillDemo('admin@kelautan.id', 'admin123')">
                    <i class="fa-solid fa-shield-halved" style="color: #06b6d4;"></i> Admin
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="fillDemo('user@kelautan.id', 'user123')">
                    <i class="fa-solid fa-user" style="color: #10b981;"></i> Pengguna Biasa
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function fillDemo(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
    document.getElementById('loginForm').submit();
}
</script>

</body>
</html>
