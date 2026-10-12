<?php
require __DIR__ . '/partials/header.php';
$stats = Application::getStats();
$selectedCat = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$viewMode = $_GET['view'] ?? 'all';
$searchQuery = $_GET['q'] ?? '';
?>

<main class="container">
    <!-- Hero Banner -->
    <section class="hero-section">
        <div class="hero-banner">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fa-solid fa-water"></i> Portal Aplikasi Resmi KKP
                </div>
                <h1 class="hero-title">
                    Pusat Akses <span>Aplikasi Maritim</span> & Operasional
                </h1>
                <p class="hero-desc">
                    Akses terpadu seluruh aplikasi Google Apps Script, Single Page Application (SPA), dan sistem pelabuhan perikanan tanpa berpindah domain.
                </p>

                <div class="hero-stats">
                    <div class="stat-pill">
                        <i class="fa-solid fa-layer-group" style="color: var(--ocean-cyan); font-size: 18px;"></i>
                        <div>
                            <div class="stat-num"><?= $stats['approved_apps'] ?></div>
                            <div class="stat-label">Aplikasi Aktif</div>
                        </div>
                    </div>
                    <div class="stat-pill">
                        <i class="fa-solid fa-chart-line" style="color: var(--marine-emerald); font-size: 18px;"></i>
                        <div>
                            <div class="stat-num"><?= formatHits($stats['total_hits']) ?></div>
                            <div class="stat-label">Total Peluncuran</div>
                        </div>
                    </div>
                    <div class="stat-pill">
                        <i class="fa-solid fa-folder" style="color: var(--amber-gold); font-size: 18px;"></i>
                        <div>
                            <div class="stat-num"><?= $stats['total_categories'] ?></div>
                            <div class="stat-label">Kategori Layanan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recently Used Section (FR 2.5) -->
    <?php if (!empty($recentlyUsed) && $viewMode !== 'favorites' && empty($searchQuery) && empty($selectedCat)): ?>
    <section class="recent-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--ocean-cyan);"></i> Terakhir Digunakan
            </h2>
        </div>
        <div class="recent-grid">
            <?php foreach ($recentlyUsed as $recent): ?>
            <a href="<?= url('/' . e($recent['category_slug']) . '/' . e($recent['slug'])) ?>" class="recent-card">
                <div class="recent-icon">
                    <i class="<?= e($recent['icon_value']) ?>"></i>
                </div>
                <div class="recent-info">
                    <div class="recent-title"><?= e($recent['title']) ?></div>
                    <div class="recent-time">Dibuka <?= timeAgo($recent['last_opened_at']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Filter & Search Toolbar (FR 2.2, 2.3, 2.4) -->
    <section class="filter-bar">
        <div class="search-row">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="appSearchInput" class="search-input" placeholder="Cari nama aplikasi, modul, atau deskripsi..." value="<?= e($searchQuery) ?>">
            </div>

            <div class="view-toggle">
                <a href="<?= url('/' . ($selectedCat ? '?cat=' . $selectedCat : '')) ?>" class="toggle-btn <?= $viewMode !== 'favorites' ? 'active' : '' ?>">
                    <i class="fa-solid fa-grid-2"></i> Semua Aplikasi
                </a>
                <a href="<?= url('/?view=favorites' . ($selectedCat ? '&cat=' . $selectedCat : '')) ?>" class="toggle-btn <?= $viewMode === 'favorites' ? 'active' : '' ?>">
                    <i class="fa-solid fa-star" style="color: <?= $viewMode === 'favorites' ? '#fff' : '#f59e0b' ?>;"></i> Favorit Saya
                </a>
            </div>
        </div>

        <!-- Category Pills (FR 2.3) -->
        <div class="category-pills">
            <a href="<?= url('/' . ($viewMode === 'favorites' ? '?view=favorites' : '')) ?>" class="category-pill <?= empty($selectedCat) ? 'active' : '' ?>">
                <i class="fa-solid fa-border-all"></i> Semua Kategori
            </a>
            <?php foreach ($categories as $cat): ?>
                <?php if ($cat['approved_count'] > 0 || Auth::isAdmin()): ?>
                <a href="<?= url('/?cat=' . $cat['id'] . ($viewMode === 'favorites' ? '&view=favorites' : '')) ?>" class="category-pill <?= $selectedCat === (int)$cat['id'] ? 'active' : '' ?>">
                    <i class="<?= e($cat['icon']) ?>" style="color: <?= e($cat['color']) ?>;"></i>
                    <?= e($cat['name']) ?>
                    <span class="badge"><?= $cat['approved_count'] ?></span>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Application Catalog Grid (FR 2.1) -->
    <section class="app-grid" id="appsCatalogGrid">
        <?php if (empty($apps)): ?>
            <div class="empty-state" id="catalogEmptyState">
                <div class="empty-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="empty-title">Tidak ada aplikasi yang ditemukan</h3>
                <p class="empty-desc">
                    <?= $viewMode === 'favorites' 
                        ? 'Anda belum menambahkan aplikasi ke daftar favorit. Klik ikon bintang pada kartu aplikasi untuk menyimpannya di sini.' 
                        : 'Tidak ada aplikasi dengan kata kunci atau filter kategori yang dipilih.' ?>
                </p>
                <?php if ($viewMode === 'favorites' || $selectedCat || !empty($searchQuery)): ?>
                <div style="margin-top: 16px;">
                    <a href="<?= url('/') ?>" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-rotate-left"></i> Reset Filter
                    </a>
                </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($apps as $app): ?>
            <div class="app-card app-card-item" 
                 data-title="<?= e($app['title']) ?>" 
                 data-desc="<?= e($app['description']) ?>" 
                 data-cat="<?= e($app['category_name']) ?>"
                 style="--app-color: <?= e($app['category_color'] ?? '#06b6d4') ?>;">
                <div>
                    <div class="app-card-top">
                        <div class="app-icon-wrap">
                            <?php if ($app['icon_type'] === 'upload' && !empty($app['icon_value'])): ?>
                                <img src="<?= url('/' . e($app['icon_value'])) ?>" alt="<?= e($app['title']) ?>">
                            <?php else: ?>
                                <i class="<?= e($app['icon_value']) ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="app-actions-top">
                            <button type="button" class="bookmark-btn <?= !empty($app['is_bookmarked']) ? 'active' : '' ?>" 
                                    data-app-id="<?= $app['id'] ?>" 
                                    title="Simpan ke Favorit">
                                <i class="<?= !empty($app['is_bookmarked']) ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                            </button>
                        </div>
                    </div>

                    <span class="app-category-badge" style="color: <?= e($app['category_color'] ?? 'var(--ocean-cyan)') ?>;">
                        <?= e($app['category_name']) ?>
                    </span>

                    <h3 class="app-title"><?= e($app['title']) ?></h3>
                    <p class="app-desc"><?= e($app['description']) ?></p>
                </div>

                <div class="app-card-footer">
                    <div class="app-metrics">
                        <div class="metric-item" title="Jumlah peluncuran aplikasi">
                            <i class="fa-solid fa-fire" style="color: #f59e0b;"></i>
                            <span><?= formatHits($app['hit_count']) ?> hits</span>
                        </div>
                    </div>

                    <!-- Clean Dynamic Route URL (FR 5.1) -->
                    <a href="<?= url('/' . e($app['category_slug']) . '/' . e($app['slug'])) ?>" class="btn-open">
                        <span>Buka</span>
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
