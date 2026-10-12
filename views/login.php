<?php
// View: Login Page (Refactored to match Stitch Institutional Maritime Design)
$error = getFlash('error');
$info = getFlash('info');
if (isset($_GET['expired'])) {
    $info = "Sesi Anda telah berakhir secara otomatis karena tidak ada aktivitas (Session Timeout 30 menit). Silakan masuk kembali.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Masuk - Portal Hub Kelautan KKP RI</title>
    
    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#000f22",
                    "primary-container": "#0a2540",
                    "on-primary": "#ffffff",
                    "on-primary-container": "#768dad",
                    "secondary": "#006972",
                    "secondary-container": "#74eefd",
                    "secondary-fixed": "#8cf2ff",
                    "surface": "#f8f9ff",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low": "#eff4ff",
                    "surface-container": "#e5eeff",
                    "on-surface": "#0b1c30",
                    "on-surface-variant": "#43474d",
                    "error": "#ba1a1a",
                    "error-container": "#ffdad6"
                }
            }
        }
    };
    </script>
</head>
<body class="bg-surface font-sans text-on-surface antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Login Container Card -->
    <div class="w-full max-w-md bg-surface-container-lowest rounded-3xl p-8 sm:p-10 shadow-xl border border-surface-container/80 relative overflow-hidden">
        <!-- Top Institutional Accent Bar -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-secondary via-secondary-container to-primary"></div>

        <div class="flex flex-col items-center text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-primary-container text-secondary flex items-center justify-center shadow-md mb-4">
                <span class="material-symbols-outlined text-[32px]">anchor</span>
            </div>
            
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-secondary-container/30 text-on-secondary-container text-[11px] font-bold uppercase tracking-wider mb-2">
                KKP RI • SSO Portal
            </div>

            <h1 class="text-2xl font-bold text-primary tracking-tight">Portal Hub Kelautan</h1>
            <p class="text-xs text-on-surface-variant mt-1 max-w-xs">
                Satu pintu masuk seluruh aplikasi mikro internal, Google Apps Script, dan sistem operasional pelabuhan.
            </p>
        </div>

        <?php if ($error): ?>
            <div class="p-3.5 rounded-xl bg-error-container text-error text-xs font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="p-3.5 rounded-xl bg-surface-container text-on-surface text-xs font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-secondary">info</span>
                <span><?= e($info) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= url('/login') ?>" method="POST" id="loginForm" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1.5" for="email">Alamat Email Dinas</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-3 text-[18px] text-on-surface-variant">mail</span>
                    <input type="email" id="email" name="email" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:ring-2 focus:ring-secondary/40 focus:outline-none border border-surface-container/60 transition-all font-medium" placeholder="nama@kelautan.id" required autofocus>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1.5" for="password">Kata Sandi</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-3 text-[18px] text-on-surface-variant">lock</span>
                    <input type="password" id="password" name="password" class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:ring-2 focus:ring-secondary/40 focus:outline-none border border-surface-container/60 transition-all font-medium" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary font-bold text-xs transition-colors shadow-md flex items-center justify-center gap-2">
                <span>Masuk ke Portal</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </button>
        </form>

        <!-- Quick Demo Switcher (Stitch Standard) -->
        <div class="mt-8 pt-5 border-t border-surface-container text-center">
            <div class="text-[10px] uppercase font-bold tracking-wider text-on-surface-variant mb-2.5">
                Akses Cepat Akun Demo Siap Pakai
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" class="py-2 px-3 rounded-xl bg-surface-container hover:bg-surface-container-high text-xs font-semibold text-on-surface flex items-center justify-center gap-1.5 transition-colors" onclick="fillDemo('admin@kelautan.id', 'admin123')">
                    <span class="material-symbols-outlined text-[16px] text-secondary">shield_person</span>
                    <span>Admin IT</span>
                </button>
                <button type="button" class="py-2 px-3 rounded-xl bg-surface-container hover:bg-surface-container-high text-xs font-semibold text-on-surface flex items-center justify-center gap-1.5 transition-colors" onclick="fillDemo('user@kelautan.id', 'user123')">
                    <span class="material-symbols-outlined text-[16px] text-secondary">person</span>
                    <span>Pengguna Biasa</span>
                </button>
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
