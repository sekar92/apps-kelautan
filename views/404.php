<?php
// View: 404 Not Found
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Halaman Tidak Ditemukan - Portal Kelautan</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; padding: 20px;">
    <div style="max-width: 480px;">
        <div style="font-size: 80px; color: var(--ocean-cyan); margin-bottom: 10px;">
            <i class="fa-solid fa-compass"></i>
        </div>
        <h1 style="font-family: var(--font-heading); font-size: 36px; font-weight: 800; color: #fff; margin-bottom: 10px;">404</h1>
        <h2 style="font-size: 18px; color: #fff; margin-bottom: 14px;">Aplikasi atau Halaman Tidak Ditemukan</h2>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px;">
            Tautan routing aplikasi yang Anda tuju mungkin belum disetujui, dipindahkan, atau belum terdaftar di portal kelautan.
        </p>
        <a href="<?= url('/') ?>" class="btn btn-primary">
            <i class="fa-solid fa-house"></i> Kembali ke Katalog Utama
        </a>
    </div>
</body>
</html>
