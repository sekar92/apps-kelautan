<?php
// View: 500 Internal Server Error
$pageTitle = "500 Terjadi Kesalahan Server - Apps Hub Kelautan.ID";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Kesalahan Server - Apps Hub Kelautan.ID</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#000f22] text-white min-h-screen flex items-center justify-center p-6 font-['Plus_Jakarta_Sans',sans-serif]">
    <div class="max-w-md w-full bg-[#0a2540]/90 backdrop-blur-md rounded-3xl p-8 border border-white/10 shadow-2xl text-center flex flex-col items-center">
        <div class="w-16 h-16 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center mb-5">
            <span class="material-symbols-outlined text-[36px]">error</span>
        </div>

        <span class="px-3 py-1 rounded-full bg-red-500/20 text-red-300 text-xs font-bold uppercase tracking-wider mb-2">
            HTTP 500 &bull; Internal Server Error
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-white mb-2">
            Terjadi Kesalahan Server
        </h1>

        <p class="text-xs text-slate-300 leading-relaxed mb-6">
            Sistem mendeteksi kendala pada koneksi database atau konfigurasi server. Periksa file log server atau pastikan koneksi database telah aktif.
        </p>

        <?php if (!empty($errorMessage) && (getenv('APP_DEBUG') === 'true' || getenv('APP_DEBUG') === '1')): ?>
        <div class="w-full text-left bg-slate-900/80 p-3 rounded-xl border border-red-500/30 text-[11px] font-mono text-red-300 mb-6 break-all">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <a href="<?= function_exists('url') ? url('/') : '/' ?>" class="w-full py-2.5 px-4 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition-colors shadow-md flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[16px]">refresh</span>
                <span>Muat Ulang Halaman</span>
            </a>
            <a href="javascript:history.back()" class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/15 text-slate-200 font-semibold text-xs transition-colors flex items-center justify-center gap-2">
                <span>Kembali</span>
            </a>
        </div>
    </div>
</body>
</html>
