<?php
// View: Full Frameless iFrame View (Versi Layar Penuh 100% Viewport)
// Menampilkan iframe murni tanpa portal header/footer, sesuai judul & URL inputan pendaftaran
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($app['title']) ?></title>
    <style>
        /* Menghilangkan margin dan padding bawaan browser */
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden; /* Mencegah munculnya scrollbar ganda dari VPS */
            background-color: #ffffff; /* Bisa disesuaikan dengan warna background GAS Anda */
        }

        /* Mengatur iframe agar memenuhi 100% layar */
        iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block; /* Menghilangkan celah putih di bawah iframe */
        }

        /* Navigasi bantuan melayang (Discreet Floating Quick Switcher) */
        .portal-nav-floating {
            position: fixed;
            top: 10px;
            right: 12px;
            z-index: 999999;
            opacity: 0.18;
            transition: opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .portal-nav-floating:hover,
        .portal-nav-floating:focus-within {
            opacity: 1;
            transform: translateY(2px);
        }

        .portal-nav-btn {
            background: rgba(10, 37, 64, 0.90);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 9999px;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .portal-nav-btn:hover {
            background: #000f22;
            border-color: #74eefd;
            color: #74eefd;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35);
        }

        .portal-nav-btn-close {
            background: rgba(40, 40, 40, 0.7);
            padding: 5px 8px;
        }
    </style>
</head>
<body>
    <!-- Tombol bantuan melayang: Memudahkan berpindah kembali ke Mode Wrapper Standar atau Katalog -->
    <div class="portal-nav-floating" id="floatingSwitcher" title="Arahkan kursor ke sini untuk opsi navigasi">
        <a href="<?= url('/' . e($app['category_slug']) . '/' . e($app['slug'])) ?>" class="portal-nav-btn" title="Beralih ke Versi Wrapper Portal Standar">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
            <span>Mode Wrapper</span>
        </a>
        <a href="<?= url('/') ?>" class="portal-nav-btn" title="Kembali ke Katalog Aplikasi">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span>Katalog</span>
        </a>
        <button type="button" class="portal-nav-btn portal-nav-btn-close" onclick="document.getElementById('floatingSwitcher').style.display='none'" title="Sembunyikan tombol navigasi ini">
            ✕
        </button>
    </div>

    <!-- Live iFrame Web App sesuai inputan pendaftaran -->
    <iframe 
        src="<?= e($fullFrameUrl) ?>" 
        allow="fullscreen; geolocation; microphone; camera; display-capture; autoplay; clipboard-write;"
        sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads">
    </iframe>
</body>
</html>
