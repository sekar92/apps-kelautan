<?php
// Partial: Header & Navbar
$currentUser = Auth::user();
$currentRoute = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Portal Hub Aplikasi Kelautan') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
    
    <script>
        window.PORTAL_BASE_URL = "<?= url('') ?>";
    </script>
</head>
<body>

<?php if (Auth::check()): ?>
<!-- Sticky Navigation Bar -->
<header class="navbar">
    <div class="container navbar-inner">
        <!-- Brand -->
        <a href="<?= url('/') ?>" class="brand">
            <div class="brand-icon">
                <i class="fa-solid fa-anchor"></i>
            </div>
            <div class="brand-text">
                <span class="brand-title">Portal Kelautan</span>
                <span class="brand-subtitle">Hub Aplikasi Operasional</span>
            </div>
        </a>

        <!-- Main Navigation Links -->
        <ul class="nav-links">
            <li class="nav-item <?= ($currentRoute === '/' || $currentRoute === '/index.php') ? 'active' : '' ?>">
                <a href="<?= url('/') ?>"><i class="fa-solid fa-shapes"></i> Katalog</a>
            </li>
            <li class="nav-item <?= strpos($currentRoute, '/submissions') === 0 ? 'active' : '' ?>">
                <a href="<?= url('/submissions') ?>"><i class="fa-solid fa-paper-plane"></i> Pengajuan Saya</a>
            </li>
            
            <?php if (Auth::isAdmin()): ?>
            <li class="nav-item <?= strpos($currentRoute, '/admin/reviews') === 0 ? 'active' : '' ?>">
                <a href="<?= url('/admin/reviews') ?>">
                    <i class="fa-solid fa-clipboard-check"></i> Review Board
                    <?php 
                    $pendingCount = (new Application())->getStats()['pending_apps'] ?? 0;
                    if ($pendingCount > 0): 
                    ?>
                    <span class="badge" style="background: #f59e0b; color: #000; font-weight: bold; border-radius: 99px; padding: 2px 6px; font-size: 10px;"><?= $pendingCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item <?= strpos($currentRoute, '/admin/categories') === 0 ? 'active' : '' ?>">
                <a href="<?= url('/admin/categories') ?>"><i class="fa-solid fa-folder-tree"></i> Kategori</a>
            </li>
            <li class="nav-item <?= strpos($currentRoute, '/admin/users') === 0 ? 'active' : '' ?>">
                <a href="<?= url('/admin/users') ?>"><i class="fa-solid fa-users-gear"></i> Pengguna</a>
            </li>
            <li class="nav-item <?= strpos($currentRoute, '/admin/logs') === 0 ? 'active' : '' ?>">
                <a href="<?= url('/admin/logs') ?>"><i class="fa-solid fa-list-check"></i> Log</a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Action & User Menu -->
        <div class="nav-actions">
            <a href="<?= url('/submissions/new') ?>" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Ajukan Aplikasi
            </a>

            <div class="user-menu">
                <button type="button" class="user-btn" id="userMenuBtn">
                    <div class="user-avatar">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= e(explode(' ', $currentUser['name'])[0]) ?></div>
                        <div class="user-role-badge"><?= strtoupper(e($currentUser['role'])) ?></div>
                    </div>
                    <i class="fa-solid fa-chevron-down" style="font-size: 11px; color: var(--text-muted); margin-left: 4px;"></i>
                </button>

                <div class="user-dropdown" id="userDropdown">
                    <div style="padding: 8px 12px; border-bottom: 1px solid var(--border-glass); margin-bottom: 4px;">
                        <div style="font-weight: 600; font-size: 13px; color: #fff;"><?= e($currentUser['name']) ?></div>
                        <div style="font-size: 11px; color: var(--text-muted);"><?= e($currentUser['email']) ?></div>
                    </div>
                    <a href="<?= url('/profile') ?>" class="dropdown-item">
                        <i class="fa-solid fa-id-badge"></i> Profil & Sandi
                    </a>
                    <a href="<?= url('/submissions') ?>" class="dropdown-item">
                        <i class="fa-solid fa-layer-group"></i> Kelola Aplikasi Saya
                    </a>
                    <?php if (Auth::isAdmin()): ?>
                    <div class="dropdown-divider"></div>
                    <a href="<?= url('/admin/reviews') ?>" class="dropdown-item">
                        <i class="fa-solid fa-shield-halved"></i> Panel Tata Kelola
                    </a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a href="<?= url('/logout') ?>" class="dropdown-item" style="color: #ef4444;">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<?php endif; ?>

<!-- Flash Message Notifications -->
<?php if (Auth::check()): ?>
<div class="container" style="margin-top: 20px;">
    <?php if ($successMsg = getFlash('success')): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <div><?= e($successMsg) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg = getFlash('error')): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div><?= e($errorMsg) ?></div>
        </div>
    <?php endif; ?>

    <?php if ($infoMsg = getFlash('info')): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <div><?= e($infoMsg) ?></div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
