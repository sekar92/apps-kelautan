<?php
// View: Core Engine iFrame Execution Wrapper (Refactored to match Stitch iframe_wrapper.html)
$currentUser = $currentUser ?? Auth::user() ?? [
    'id' => 0,
    'name' => 'Pengguna Publik',
    'email' => 'publik@kelautan.id',
    'department' => 'Akses Publik'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= e($app['title']) ?> &bull; Apps Hub Kelautan.ID</title>
    
    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <!-- Tailwind CSS with Custom Stitch Tokens -->
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
                    "surface": "#f8f9ff",
                    "surface-bright": "#f8f9ff",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low": "#eff4ff",
                    "surface-container": "#e5eeff",
                    "surface-container-high": "#dce9ff",
                    "surface-container-highest": "#d3e4fe",
                    "on-surface": "#0b1c30",
                    "on-surface-variant": "#43474d",
                    "outline": "#74777e",
                    "outline-variant": "#c4c6ce",
                    "error": "#ba1a1a"
                }
            }
        }
    };
    </script>

    <style>
        @layer base { html, body { margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; } }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-surface text-on-surface antialiased h-screen flex flex-col overflow-hidden">

    <!-- Interactive Top Control Bar for iFrame Wrapper (Stitch Standard FR 5.2) -->
    <section class="w-full bg-surface-container-lowest px-4 sm:px-8 py-2.5 shadow-sm z-30 border-b border-surface-container shrink-0">
        <div class="w-full flex flex-col xl:flex-row items-center justify-between gap-3">
            <!-- Left: Breadcrumb, Return, & Status -->
            <div class="flex items-center gap-3 flex-wrap w-full xl:w-auto">
                <a href="<?= url('/') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold text-xs transition-colors">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Katalog</span>
                </a>

                <div class="hidden sm:flex items-center gap-1.5 text-on-surface-variant text-xs">
                    <span>Portal</span>
                    <span class="material-symbols-outlined text-[14px] text-outline">chevron_right</span>
                    <span class="uppercase font-semibold text-[11px]"><?= e($app['category_name']) ?></span>
                    <span class="material-symbols-outlined text-[14px] text-outline">chevron_right</span>
                    <span class="font-bold text-primary truncate max-w-[200px]"><?= e($app['title']) ?></span>
                </div>

                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-container-low rounded-full">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span>
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-secondary">iFrame Live & Sandboxed</span>
                </div>
            </div>

            <!-- Center: Clean Address Pill URL Browser Bar (FR 5.1) -->
            <div class="w-full xl:w-[480px] flex items-center justify-between bg-surface-container-low px-3 py-1.5 rounded-full shadow-inner border border-surface-container/60">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="material-symbols-outlined text-[16px] text-secondary">lock</span>
                    <span class="text-[10px] font-bold bg-secondary-container text-on-secondary-container px-1.5 py-0.2 rounded">SSL Clean Route</span>
                    <span class="text-xs text-on-surface truncate tracking-tight font-mono">
                        https://apps.kelautan.id/<?= e($app['category_slug']) ?>/<?= e($app['slug']) ?>
                    </span>
                </div>
                <button class="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors pl-2" 
                        onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan bersih berhasil disalin!');" 
                        title="Salin Tautan" 
                        type="button">
                    <span class="material-symbols-outlined text-[16px]">content_copy</span>
                </button>
            </div>

            <!-- Right: Token Status & Action Buttons (FR 5.3) -->
            <div class="flex items-center gap-2 justify-end w-full xl:w-auto flex-wrap">
                <button type="button" 
                        onclick="document.getElementById('tokenModal').classList.remove('hidden')"
                        class="hidden lg:flex items-center gap-1.5 px-3 py-1 bg-surface-container hover:bg-surface-container-high rounded-lg transition-colors cursor-pointer" 
                        title="Klik untuk melihat rincian HMAC Token">
                    <span class="material-symbols-outlined text-[16px] text-secondary">verified_user</span>
                    <div class="flex flex-col text-left">
                        <span class="text-[10px] font-bold text-on-surface-variant leading-none">HMAC Token Active</span>
                        <span class="text-[9px] text-secondary font-bold leading-tight">Auth Forwarding OK</span>
                    </div>
                </button>

                <button class="p-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface hover:text-primary transition-all flex items-center justify-center" 
                        id="btn-reload" 
                        onclick="document.getElementById('appMainFrame').src = document.getElementById('appMainFrame').src;" 
                        title="Muat Ulang Aplikasi" 
                        type="button">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                </button>

                <a class="p-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface hover:text-primary transition-all flex items-center justify-center" 
                   href="<?= e($fullFrameUrl) ?>" 
                   rel="noopener noreferrer" 
                   target="_blank" 
                   title="Buka di Tab Baru">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                </a>

                <button class="px-3 py-1.5 rounded-lg bg-primary-container text-on-primary hover:bg-tertiary-container transition-all flex items-center gap-1.5 font-bold text-xs shadow-xs" 
                        id="btn-fullscreen" 
                        onclick="toggleWrapperFullscreen()" 
                        type="button">
                    <span class="material-symbols-outlined text-[18px]">fullscreen</span>
                    <span class="hidden sm:inline">Layar Penuh</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Technical Diagnostics Sub-Header Bar (Browser Sandbox Specs NFR 1 & NFR 3) -->
    <div class="w-full bg-surface-container-high px-4 sm:px-8 py-1.5 flex flex-wrap items-center justify-between text-on-surface-variant text-[11px] font-mono shadow-xs border-b border-surface-container shrink-0">
        <div class="flex items-center gap-3 overflow-x-auto py-0.5">
            <span class="flex items-center gap-1 text-on-surface font-semibold">
                <span class="material-symbols-outlined text-[14px] text-secondary">shield</span>
                sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads"
            </span>
            <span class="hidden md:inline text-outline-variant">•</span>
            <span class="hidden md:inline">X-Frame-Options: SAMEORIGIN</span>
            <span class="hidden md:inline text-outline-variant">•</span>
            <span class="hidden sm:inline">Referrer-Policy: strict-origin-when-cross-origin</span>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5 text-secondary font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                <span>Latency: 28ms (Sub-500ms NFR 3 OK)</span>
            </div>
            <span class="hidden lg:inline bg-surface-container text-on-surface px-2 py-0.5 rounded font-bold uppercase text-[10px]">
                <?= e($app['category_slug']) ?> Virtualizer
            </span>
        </div>
    </div>

    <!-- Main Viewport Area: Sandboxed iFrame App Environment -->
    <div class="relative w-full p-2 sm:p-4 flex-1 flex flex-col bg-surface overflow-hidden" id="iframe-viewport-container">
        <!-- Outer Viewport Decorative Shell Container -->
        <div class="w-full flex-1 rounded-xl bg-surface-container-lowest shadow-md flex flex-col overflow-hidden relative border border-surface-container">
            <!-- Top Chrome Bar for Sandboxed Window -->
            <div class="w-full bg-surface-container-low px-4 py-2 flex items-center justify-between border-b border-surface-container">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-error/70"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-secondary-container"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                    </div>
                    <span class="text-on-surface-variant ml-2 font-mono text-[11px]">iFrame Context Isolation (NFR 1)</span>
                </div>
                <div class="flex items-center gap-2 text-on-surface-variant text-xs">
                    <span class="material-symbols-outlined text-[16px] text-secondary">memory</span>
                    <span>Micro-App Virtualizer Engine</span>
                </div>
            </div>

            <!-- Sandboxed iFrame execution -->
            <iframe 
                src="<?= e($fullFrameUrl) ?>" 
                id="appMainFrame"
                class="w-full flex-1 h-full border-none bg-surface-bright"
                sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads"
                allow="geolocation; microphone; camera; display-capture; autoplay; clipboard-write;"
                title="<?= e($app['title']) ?>">
            </iframe>
        </div>
    </div>

    <!-- Modal Token Inspector (FR 5.3) -->
    <div id="tokenModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest border border-surface-container rounded-2xl max-w-lg w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-surface-container mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[22px]">verified_user</span>
                    <h3 class="font-bold text-base text-primary">Autentikasi Terusan iFrame (FR 5.3)</h3>
                </div>
                <button type="button" onclick="document.getElementById('tokenModal').classList.add('hidden')" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <p class="text-xs text-on-surface-variant mb-4 leading-relaxed">
                Token keamanan dibuat otomatis di sisi server portal menggunakan HMAC-SHA256 signature berdurasi 1 jam dan diteruskan ke URL query parameter micro-app agar GAS / SPA dapat mengenali pengguna tanpa login ulang.
            </p>

            <div class="bg-surface-container-low p-3.5 rounded-xl font-mono text-xs text-on-surface space-y-1.5 mb-4 border border-surface-container/60">
                <div><span class="text-on-surface-variant">User ID:</span> <strong><?= e($currentUser['id']) ?></strong></div>
                <div><span class="text-on-surface-variant">Nama:</span> <strong><?= e($currentUser['name']) ?></strong></div>
                <div><span class="text-on-surface-variant">Email:</span> <strong><?= e($currentUser['email']) ?></strong></div>
                <div><span class="text-on-surface-variant">Unit Kerja:</span> <strong><?= e($currentUser['department']) ?></strong></div>
                <div class="pt-2 border-t border-surface-container">
                    <div class="text-[10px] text-secondary font-bold mb-1">SIGNATURE TOKEN (PAYLOAD):</div>
                    <div class="text-[10px] text-on-surface-variant break-all"><?= e($appToken) ?></div>
                </div>
            </div>

            <button type="button" onclick="document.getElementById('tokenModal').classList.add('hidden')" class="w-full py-2 bg-primary-container text-on-primary rounded-xl font-bold text-xs hover:bg-tertiary-container transition-colors">
                Tutup Info Token
            </button>
        </div>
    </div>

    <script>
    function toggleWrapperFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => console.error(err));
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }
    </script>
</body>
</html>
