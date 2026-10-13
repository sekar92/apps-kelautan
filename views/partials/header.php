<?php
// Partial: Header & Navbar (Refactored to match Stitch Design System)
$currentUser = Auth::user();
$currentRoute = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$stats = Application::getStats();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= e($pageTitle ?? 'Apps Hub Kelautan.ID') ?></title>
    
    <!-- Google Fonts & Material Symbols (Stitch Standard) -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <!-- Tailwind CSS with Custom Stitch Design Tokens -->
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
                    "on-secondary": "#ffffff",
                    "on-secondary-container": "#006b75",
                    "secondary-fixed": "#8cf2ff",
                    "secondary-fixed-dim": "#5ad7e6",
                    "on-secondary-fixed": "#001f23",
                    "on-secondary-fixed-variant": "#004f56",
                    "surface": "#f8f9ff",
                    "surface-bright": "#f8f9ff",
                    "surface-dim": "#cbdbf5",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low": "#eff4ff",
                    "surface-container": "#e5eeff",
                    "surface-container-high": "#dce9ff",
                    "surface-container-highest": "#d3e4fe",
                    "on-surface": "#0b1c30",
                    "on-surface-variant": "#43474d",
                    "outline": "#74777e",
                    "outline-variant": "#c4c6ce",
                    "error": "#ba1a1a",
                    "error-container": "#ffdad6",
                    "on-error": "#ffffff",
                    "on-error-container": "#93000a",
                    "tertiary": "#000f22",
                    "tertiary-container": "#002546",
                    "on-tertiary-container": "#6b8dba",
                    "on-tertiary-fixed-variant": "#244871"
                },
                borderRadius: {
                    "DEFAULT": "0.125rem",
                    "lg": "0.375rem",
                    "xl": "0.75rem",
                    "full": "9999px"
                },
                spacing: {
                    "space-xs": "0.25rem",
                    "space-sm": "0.5rem",
                    "space-md": "1rem",
                    "space-lg": "1.5rem",
                    "space-xl": "2rem",
                    "space-2xl": "3rem",
                    "margin": "2rem",
                    "gutter": "1.5rem"
                },
                fontFamily: {
                    sans: ["Plus Jakarta Sans", "sans-serif"],
                    heading: ["Plus Jakarta Sans", "sans-serif"],
                    mono: ["ui-monospace", "SFMono-Regular", "Menlo", "Monaco", "Consolas", "monospace"]
                }
            }
        }
    };
    </script>

    <style>
        @layer base {
            html, body { margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
            body { overscroll-behavior: none; }
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .material-symbols-fill {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    </style>

    <script>
        window.PORTAL_BASE_URL = "<?= url('') ?>";
    </script>
</head>
<body class="bg-surface font-sans text-on-surface antialiased min-h-screen flex flex-col justify-between">

<?php if (Auth::check()): ?>
<!-- Fixed Top Institutional Navigation Bar (Stitch Shell Standard) -->
<header class="fixed top-0 left-0 w-full z-50 bg-surface-container-lowest/95 backdrop-blur-md shadow-[0_1px_8px_rgba(10,37,64,0.06)] border-b border-surface-container">
    <div class="h-16 w-full px-4 sm:px-8 flex items-center justify-between gap-4">
        <!-- Brand & Main Nav Links -->
        <div class="flex items-center gap-6">
            <a href="<?= url('/') ?>" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-lg bg-primary-container text-secondary flex items-center justify-center shadow-xs">
                    <span class="material-symbols-outlined text-[24px]">anchor</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-[17px] tracking-tight text-primary leading-tight group-hover:text-secondary transition-colors">
                        Apps Hub Kelautan.ID
                    </span>
                    <span class="text-[10px] font-bold text-secondary tracking-wider uppercase">
                        Application Hub by Kelautan.ID
                    </span>
                </div>
            </a>

            <div class="h-6 w-px bg-surface-container-high hidden lg:block"></div>

            <!-- Nav Tabs -->
            <nav class="hidden lg:flex items-center gap-1">
                <a href="<?= url('/') ?>" 
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors <?= ($currentRoute === '/' || $currentRoute === '/index.php') ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>">
                    Katalog Aplikasi
                </a>

                <a href="<?= url('/submissions') ?>" 
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors <?= strpos($currentRoute, '/submissions') === 0 ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>">
                    Ajukan Aplikasi
                </a>

                <?php if (Auth::isAdmin()): ?>
                <a href="<?= url('/admin/reviews') ?>" 
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors flex items-center gap-1.5 <?= strpos($currentRoute, '/admin/reviews') === 0 ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>">
                    <span>Panel Tata Kelola</span>
                    <?php if (($stats['pending_apps'] ?? 0) > 0): ?>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-error text-on-error">
                        <?= $stats['pending_apps'] ?>
                    </span>
                    <?php endif; ?>
                </a>

                <a href="<?= url('/admin/categories') ?>" 
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors <?= strpos($currentRoute, '/admin/categories') === 0 ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>">
                    Kategori
                </a>

                <a href="<?= url('/admin/users') ?>" 
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors <?= strpos($currentRoute, '/admin/users') === 0 ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>">
                    Pengguna
                </a>

                <a href="<?= url('/admin/logs') ?>" 
                   class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors <?= strpos($currentRoute, '/admin/logs') === 0 ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>">
                    Audit Trail
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Search Bar, Session Telemetry & User Menu -->
        <div class="flex items-center gap-3">
            <!-- Active Session Telemetry Chip (FR 1.2) -->
            <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 bg-surface-container-low rounded-full">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span>
                </span>
                <span class="text-[11px] font-semibold text-on-secondary-fixed-variant">Sesi Aktif Kelautan.ID</span>
            </div>

            <div class="h-6 w-px bg-surface-container-high hidden sm:block"></div>

            <!-- User Menu Dropdown Trigger -->
            <div class="relative" id="userMenuWrapper">
                <button type="button" class="flex items-center gap-2.5 p-1 rounded-lg hover:bg-surface-container transition-colors" id="stitchUserBtn">
                    <div class="hidden md:flex flex-col text-right">
                        <span class="text-xs font-bold text-on-surface leading-tight"><?= e($currentUser['name']) ?></span>
                        <span class="text-[10px] font-semibold text-secondary tracking-tight uppercase"><?= e($currentUser['role'] === 'admin' ? 'IT Ops & Admin' : 'Pengguna Kelautan.ID') ?></span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-xs shadow-xs">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-on-surface-variant">expand_more</span>
                </button>

                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 w-56 bg-surface-container-lowest border border-surface-container rounded-xl shadow-lg py-2 hidden z-50" id="stitchUserDropdown">
                    <div class="px-4 py-2 border-b border-surface-container">
                        <div class="font-bold text-xs text-on-surface"><?= e($currentUser['name']) ?></div>
                        <div class="text-[11px] text-on-surface-variant truncate"><?= e($currentUser['email']) ?></div>
                    </div>
                    <a href="<?= url('/profile') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-secondary">badge</span>
                        <span>Profil & Keamanan</span>
                    </a>
                    <a href="<?= url('/submissions') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-secondary">post_add</span>
                        <span>Aplikasi Saya</span>
                    </a>
                    <?php if (Auth::isAdmin()): ?>
                    <a href="<?= url('/admin/reviews') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-secondary">shield_person</span>
                        <span>Review Board Admin</span>
                    </a>
                    <?php endif; ?>
                    <div class="my-1 border-t border-surface-container"></div>
                    <a href="<?= url('/logout') ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-error hover:bg-error-container/20 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        <span>Keluar Sistem</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<?php endif; ?>

<!-- Flash Message Notifications -->
<?php if (Auth::check()): ?>
<div class="max-w-[1440px] w-full mx-auto px-4 sm:px-8 mt-20">
    <?php if ($successMsg = getFlash('success')): ?>
        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-secondary-container/30 border border-secondary/30 text-on-secondary-container mb-4 text-sm font-medium shadow-xs">
            <span class="material-symbols-outlined text-secondary text-[22px]">check_circle</span>
            <div class="flex-1"><?= e($successMsg) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg = getFlash('error')): ?>
        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-error-container border border-error/30 text-on-error-container mb-4 text-sm font-medium shadow-xs">
            <span class="material-symbols-outlined text-error text-[22px]">error</span>
            <div class="flex-1"><?= e($errorMsg) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($infoMsg = getFlash('info')): ?>
        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-surface-container border border-surface-container-high text-on-surface mb-4 text-sm font-medium shadow-xs">
            <span class="material-symbols-outlined text-secondary text-[22px]">info</span>
            <div class="flex-1"><?= e($infoMsg) ?></div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
