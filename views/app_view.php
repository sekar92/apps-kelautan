<?php
// View: Core Engine iFrame Execution Wrapper (FR 5.1, 5.2, 5.3, NFR 1)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($app['title']) ?> &bull; Portal Kelautan</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
    <script>
        window.PORTAL_BASE_URL = "<?= url('') ?>";
    </script>
</head>
<body style="overflow: hidden; margin: 0; background: #000;">

<div class="iframe-page-wrapper" id="portalIframeWrapper">
    <!-- Header Wrapper Controls (FR 5.2) -->
    <header class="iframe-header">
        <div class="iframe-header-left">
            <a href="<?= url('/') ?>" class="btn-back-portal" title="Kembali ke Katalog Utama">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>

            <div class="iframe-app-meta">
                <div class="iframe-app-icon">
                    <?php if ($app['icon_type'] === 'upload' && !empty($app['icon_value'])): ?>
                        <img src="<?= url('/' . e($app['icon_value'])) ?>" style="width: 100%; height: 100%; border-radius: 4px;" alt="icon">
                    <?php else: ?>
                        <i class="<?= e($app['icon_value']) ?>"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="iframe-app-title"><?= e($app['title']) ?></span>
                        <span class="iframe-app-badge"><?= e($app['category_name']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="iframe-header-right">
            <!-- App Token Info / Security Badge (FR 5.3) -->
            <button type="button" class="header-tool-btn" onclick="openModal('tokenInfoModal')" title="Lihat Status Token Autentikasi (FR 5.3)">
                <i class="fa-solid fa-shield-halved" style="color: #10b981;"></i>
            </button>

            <!-- Refresh iFrame -->
            <button type="button" class="header-tool-btn" onclick="reloadIframe()" title="Muat Ulang Aplikasi">
                <i class="fa-solid fa-rotate-right"></i>
            </button>

            <!-- Copy URL -->
            <button type="button" class="header-tool-btn" onclick="copyCurrentUrl()" title="Salin Tautan Aplikasi">
                <i class="fa-solid fa-link"></i>
            </button>

            <!-- Open in New Tab -->
            <a href="<?= e($fullFrameUrl) ?>" target="_blank" rel="noopener noreferrer" class="header-tool-btn" title="Buka di Tab Baru">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>

            <!-- Full Screen Toggle Button (FR 5.2) -->
            <button type="button" class="header-tool-btn" onclick="toggleAppFullscreen()" title="Layar Penuh (Full Screen)">
                <i class="fa-solid fa-expand"></i>
            </button>
        </div>
    </header>

    <!-- Sandboxed iFrame Container (NFR 1) -->
    <div class="iframe-container">
        <!-- Loading Spinner -->
        <div class="iframe-loading" id="iframeLoader">
            <div class="loading-spinner"></div>
            <div style="color: var(--text-secondary); font-size: 14px; font-weight: 500;">
                Memuat aplikasi <span style="color: var(--ocean-cyan); font-weight: 600;"><?= e($app['title']) ?></span>...
            </div>
        </div>

        <!-- iFrame with strict sandboxing -->
        <iframe 
            src="<?= e($fullFrameUrl) ?>" 
            id="appMainFrame"
            class="app-iframe"
            sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads"
            onload="document.getElementById('iframeLoader').style.display='none';"
            allow="geolocation; microphone; camera; display-capture; autoplay; clipboard-write;"
            title="<?= e($app['title']) ?>">
        </iframe>
    </div>
</div>

<!-- Modal Token & Security Inspection (FR 5.3) -->
<div class="modal-backdrop" id="tokenInfoModal" onclick="if(event.target===this) closeModal('tokenInfoModal')">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-shield-keyhole" style="color: #10b981;"></i> Info Autentikasi iFrame</h3>
            <button type="button" class="modal-close" onclick="closeModal('tokenInfoModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
                Sesuai <strong>FR 5.3</strong>, portal meneruskan token terenkripsi HMAC-SHA256 ke dalam aplikasi via URL query parameter agar GAS / SPA dapat mengenali identitas pengguna aktif secara aman.
            </p>

            <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-glass); border-radius: 8px; padding: 14px; font-family: monospace; font-size: 12px; margin-bottom: 14px; word-break: break-all;">
                <div style="color: var(--ocean-cyan); margin-bottom: 4px;">// Identitas Sesi Pengguna:</div>
                <div>User ID: <strong><?= e($currentUser['id']) ?></strong></div>
                <div>Nama: <strong><?= e($currentUser['name']) ?></strong></div>
                <div>Email: <strong><?= e($currentUser['email']) ?></strong></div>
                <div>Role: <strong><?= e($currentUser['role']) ?></strong></div>
                <div style="margin-top: 8px; color: #10b981;">// HMAC Token Signature:</div>
                <div style="color: #94a3b8; font-size: 11px;"><?= e($appToken) ?></div>
            </div>

            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 10px 14px; border-radius: 8px; font-size: 12px; color: #a7f3d0;">
                <i class="fa-solid fa-lock"></i> <strong>Sandboxed Isolation:</strong> Atribut sandbox aktif mencegah akses langsung ke parent DOM portal.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('tokenInfoModal')">Tutup</button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<script src="<?= url('/assets/js/main.js') ?>"></script>
</body>
</html>
