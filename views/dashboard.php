<?php
require __DIR__ . '/partials/header.php';
$selectedCat = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$viewMode = $_GET['view'] ?? 'all';
$searchQuery = $_GET['q'] ?? '';

// Top 3 popular apps for sidebar leaderboard
$popularApps = array_slice($apps, 0, 3);
?>

<main class="w-full flex-1 bg-surface pt-4">
    <div class="flex flex-col w-full">
        <!-- Top Command / Maritime Telemetry Greeting Banner (Stitch Standard) -->
        <section class="relative w-full bg-primary-container text-on-primary overflow-hidden shadow-md">
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 1200 400" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern height="40" id="nautical-grid" patternUnits="userSpaceOnUse" width="40">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-dasharray="2,2" stroke-width="0.8"></path>
                        </pattern>
                    </defs>
                    <rect fill="url(#nautical-grid)" height="100%" width="100%"></rect>
                    <circle cx="950" cy="180" fill="none" r="140" stroke="currentColor" stroke-dasharray="4,6" stroke-width="1.5"></circle>
                    <circle cx="950" cy="180" fill="none" opacity="0.6" r="240" stroke="currentColor" stroke-width="1"></circle>
                </svg>
            </div>

            <div class="relative w-full max-w-[1440px] mx-auto px-4 sm:px-8 py-8 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
                <div class="flex flex-col gap-2 max-w-3xl">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-secondary-container/20 text-secondary-fixed text-xs font-bold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-secondary-fixed animate-pulse"></span>
                            Node Operasional JKT-PST • KKP Terhubung
                        </span>
                        <span class="text-xs text-on-primary-container font-mono">Pusdatin KKP RI Live</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-on-primary tracking-tight">
                        Selamat datang, <?= e($currentUser['name']) ?>
                    </h1>

                    <p class="text-sm text-on-primary-container leading-relaxed">
                        Portal Hub Operasional Kelautan — Gerbang satu pintu eksekusi modul Google Apps Script terverifikasi, aplikasi analitik TypeScript SPA, dan instrumen kepatuhan perikanan nasional.
                    </p>
                </div>

                <!-- Quick Metrics Counters -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-[300px] xl:min-w-[460px]">
                    <div class="bg-surface-container-lowest/10 backdrop-blur-md rounded-xl p-4 flex flex-col justify-between border border-white/5">
                        <div class="flex items-center justify-between text-xs text-on-primary-container font-semibold">
                            <span>Aplikasi Aktif</span>
                            <span class="material-symbols-outlined text-secondary-fixed text-[18px]">hub</span>
                        </div>
                        <div class="flex items-baseline gap-2 mt-2">
                            <span class="text-2xl font-bold text-on-primary"><?= $stats['approved_apps'] ?></span>
                            <span class="text-[10px] font-bold text-secondary-fixed uppercase">100% Online</span>
                        </div>
                        <div class="w-full bg-surface-container-lowest/20 h-1.5 rounded-full mt-3 overflow-hidden">
                            <div class="bg-secondary-fixed h-full w-full"></div>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest/10 backdrop-blur-md rounded-xl p-4 flex flex-col justify-between border border-white/5">
                        <div class="flex items-center justify-between text-xs text-on-primary-container font-semibold">
                            <span>Menunggu Review</span>
                            <span class="material-symbols-outlined text-secondary-fixed text-[18px]">pending_actions</span>
                        </div>
                        <div class="flex items-baseline gap-2 mt-2">
                            <span class="text-2xl font-bold text-on-primary"><?= $stats['pending_apps'] ?></span>
                            <span class="text-[10px] font-bold text-secondary-container uppercase">Antrean Board</span>
                        </div>
                        <div class="w-full bg-surface-container-lowest/20 h-1.5 rounded-full mt-3 overflow-hidden">
                            <div class="bg-secondary-container h-full w-2/3"></div>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest/10 backdrop-blur-md rounded-xl p-4 flex flex-col justify-between border border-white/5">
                        <div class="flex items-center justify-between text-xs text-on-primary-container font-semibold">
                            <span>Total Hit Peluncuran</span>
                            <span class="material-symbols-outlined text-secondary-fixed text-[18px]">monitoring</span>
                        </div>
                        <div class="flex items-baseline gap-2 mt-2">
                            <span class="text-2xl font-bold text-on-primary"><?= formatHits($stats['total_hits']) ?></span>
                            <span class="text-[10px] font-bold text-secondary-fixed flex items-center">
                                <span class="material-symbols-outlined text-[12px]">trending_up</span> +18%
                            </span>
                        </div>
                        <div class="w-full bg-surface-container-lowest/20 h-1.5 rounded-full mt-3 overflow-hidden">
                            <div class="bg-secondary-fixed h-full w-4/5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Work Surface Container -->
        <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-8 py-8 flex flex-col gap-8">
            <!-- Section 1: Recently Used Applications (Horizontal Micro-Cards) (FR 2.5) -->
            <?php if (!empty($recentlyUsed)): ?>
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-4 bg-secondary rounded-full"></div>
                        <h2 class="font-bold text-base text-on-surface">Terakhir Digunakan</h2>
                        <span class="text-[11px] font-semibold text-on-surface-variant bg-surface-container px-2.5 py-0.5 rounded-full">Sesi Cepat</span>
                    </div>
                    <span class="text-xs text-secondary font-semibold flex items-center gap-1">
                        Riwayat Akses Pribadi
                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach ($recentlyUsed as $recent): ?>
                    <a href="<?= url('/' . e($recent['category_slug']) . '/' . e($recent['slug'])) ?>" class="group bg-surface-container-lowest p-3.5 rounded-xl shadow-xs hover:shadow-md hover:bg-surface-container-low transition-all duration-200 flex items-center justify-between border border-surface-container/60">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-on-secondary transition-colors shrink-0">
                                <span class="material-symbols-outlined text-[22px]">
                                    <?= strpos($recent['category_slug'], 'appscript') !== false ? 'code' : (strpos($recent['category_slug'], 'pos') !== false ? 'point_of_sale' : 'local_shipping') ?>
                                </span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.2 rounded bg-secondary-container/40 text-on-secondary-container">
                                        <?= e($recent['category_name']) ?>
                                    </span>
                                    <span class="text-[11px] text-on-surface-variant"><?= timeAgo($recent['last_opened_at']) ?></span>
                                </div>
                                <h3 class="font-bold text-xs text-on-surface truncate group-hover:text-secondary transition-colors mt-0.5">
                                    <?= e($recent['title']) ?>
                                </h3>
                                <p class="text-[11px] text-on-surface-variant font-mono truncate">apps.kelautan.id/<?= e($recent['category_slug']) ?>/<?= e($recent['slug']) ?></p>
                            </div>
                        </div>
                        <div class="w-7 h-7 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant group-hover:text-primary shrink-0 ml-2">
                            <span class="material-symbols-outlined text-[16px]">launch</span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Section 2: Dual Column Layout (Main Catalog 9 Cols + Sticky Operational Intel 3 Cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Catalog Main Stream -->
                <div class="lg:col-span-8 xl:col-span-9 flex flex-col gap-6">
                    <!-- Search and Filter Bar Tooling -->
                    <div class="bg-surface-container-lowest p-4 rounded-xl shadow-xs border border-surface-container/60 flex flex-col gap-4">
                        <div class="flex flex-col md:flex-row items-center gap-3 justify-between">
                            <!-- Search Field -->
                            <div class="relative w-full md:flex-1">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[20px]">search</span>
                                <input class="w-full bg-surface-container-low pl-10 pr-4 py-2 rounded-lg text-on-surface text-sm placeholder:text-on-surface-variant focus:outline-none focus:ring-2 focus:ring-secondary/40 transition-all border border-transparent focus:border-secondary/30" 
                                       id="search-input" 
                                       placeholder="Cari nama aplikasi, modul, atau deskripsi layanan..." 
                                       type="text"
                                       value="<?= e($searchQuery) ?>"/>
                            </div>

                            <!-- Sort / View Mode Toggle -->
                            <div class="flex items-center gap-2 w-full md:w-auto shrink-0 justify-end">
                                <div class="flex bg-surface-container-low p-1 rounded-lg">
                                    <a href="<?= url('/' . ($selectedCat ? '?cat=' . $selectedCat : '')) ?>" 
                                       class="px-3 py-1 rounded text-xs font-semibold transition-colors <?= $viewMode !== 'favorites' ? 'bg-surface-container-lowest text-on-surface shadow-xs' : 'text-on-surface-variant hover:text-on-surface' ?>">
                                        Semua Aplikasi
                                    </a>
                                    <a href="<?= url('/?view=favorites' . ($selectedCat ? '&cat=' . $selectedCat : '')) ?>" 
                                       class="px-3 py-1 rounded text-xs font-semibold transition-colors flex items-center gap-1.5 <?= $viewMode === 'favorites' ? 'bg-surface-container-lowest text-on-surface shadow-xs' : 'text-on-surface-variant hover:text-on-surface' ?>">
                                        <span class="material-symbols-outlined text-[14px] text-secondary <?= $viewMode === 'favorites' ? 'material-symbols-fill' : '' ?>">star</span>
                                        <span>Favorit Saya</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Filter Tabs -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 border-t border-surface-container pt-3">
                            <a href="<?= url('/' . ($viewMode === 'favorites' ? '?view=favorites' : '')) ?>" 
                               class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors <?= empty($selectedCat) ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>">
                                Semua (<?= count($apps) ?>)
                            </a>
                            <?php foreach ($categories as $cat): ?>
                                <?php if ($cat['approved_count'] > 0 || Auth::isAdmin()): ?>
                                <a href="<?= url('/?cat=' . $cat['id'] . ($viewMode === 'favorites' ? '&view=favorites' : '')) ?>" 
                                   class="filter-tab px-3.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap transition-colors flex items-center gap-1.5 <?= $selectedCat === (int)$cat['id'] ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' ?>">
                                    <span><?= e($cat['name']) ?></span>
                                    <span class="px-1.5 py-0.2 rounded-full text-[10px] <?= $selectedCat === (int)$cat['id'] ? 'bg-white/20' : 'bg-surface-container-high' ?>">
                                        <?= $cat['approved_count'] ?>
                                    </span>
                                </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Catalog Grid (Cards with Stitch Standard Styling) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="catalog-grid">
                        <?php if (empty($apps)): ?>
                            <div class="col-span-2 bg-surface-container-lowest rounded-xl p-12 text-center border border-dashed border-surface-container-high">
                                <span class="material-symbols-outlined text-4xl text-outline mb-2">dashboard_customize</span>
                                <h3 class="text-base font-bold text-on-surface">Tidak ada aplikasi yang ditemukan</h3>
                                <p class="text-xs text-on-surface-variant max-w-sm mx-auto mt-1">
                                    <?= $viewMode === 'favorites' ? 'Anda belum menambahkan aplikasi favorit. Klik ikon bintang pada kartu aplikasi untuk menyimpannya di sini.' : 'Coba sesuaikan kata kunci pencarian atau ubah filter kategori yang dipilih.' ?>
                                </p>
                                <div class="mt-4">
                                    <a href="<?= url('/') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container text-on-surface text-xs font-semibold hover:bg-surface-container-high transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                                        <span>Reset Filter</span>
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($apps as $app): ?>
                            <div class="app-card group bg-surface-container-lowest rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between border border-surface-container/60" 
                                 data-title="<?= e(strtolower($app['title'])) ?>"
                                 data-desc="<?= e(strtolower($app['description'])) ?>"
                                 data-cat="<?= e(strtolower($app['category_name'])) ?>">
                                <div>
                                    <div class="flex items-start justify-between gap-3 mb-2.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-surface-container-low text-secondary flex items-center justify-center shrink-0 border border-surface-container/50">
                                                <?php if ($app['icon_type'] === 'upload' && !empty($app['icon_value'])): ?>
                                                    <img src="<?= url('/' . e($app['icon_value'])) ?>" class="w-full h-full object-cover rounded-xl" alt="icon">
                                                <?php else: ?>
                                                    <span class="material-symbols-outlined text-[26px]">
                                                        <?= strpos($app['category_slug'], 'appscript') !== false ? 'code' : (strpos($app['category_slug'], 'pos') !== false ? 'storefront' : (strpos($app['category_slug'], 'keuangan') !== false ? 'calculate' : (strpos($app['category_slug'], 'operasional') !== false ? 'directions_boat' : 'insights'))) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-secondary-container/40 text-on-secondary-container uppercase tracking-wider">
                                                        <?= e($app['category_slug'] === 'appscript' ? 'GAS' : (strpos($app['category_slug'], 'typescript') !== false ? 'SPA' : strtoupper($app['category_slug']))) ?>
                                                    </span>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-surface-container text-on-surface-variant font-medium">
                                                        Sandbox Passed
                                                    </span>
                                                </div>
                                                <h3 class="font-bold text-sm text-on-surface group-hover:text-secondary transition-colors mt-0.5 line-clamp-1">
                                                    <?= e($app['title']) ?>
                                                </h3>
                                            </div>
                                        </div>

                                        <button aria-label="Simpan Favorit" 
                                                class="bookmark-btn p-1.5 rounded-lg transition-colors <?= !empty($app['is_bookmarked']) ? 'text-secondary' : 'text-on-surface-variant hover:text-secondary' ?>" 
                                                data-app-id="<?= $app['id'] ?>" 
                                                type="button">
                                            <span class="material-symbols-outlined text-[20px] <?= !empty($app['is_bookmarked']) ? 'material-symbols-fill' : '' ?>">star</span>
                                        </button>
                                    </div>

                                    <p class="text-xs text-on-surface-variant line-clamp-2 mb-3 leading-relaxed">
                                        <?= e($app['description']) ?>
                                    </p>

                                    <div class="bg-surface-container-low/70 rounded-lg p-2.5 mb-3 flex flex-col gap-1 border border-surface-container/40">
                                        <div class="flex items-center justify-between text-[11px] font-mono text-on-surface-variant">
                                            <span class="truncate">apps.kelautan.id/<?= e($app['category_slug']) ?>/<?= e($app['slug']) ?></span>
                                            <button class="text-secondary hover:text-on-secondary-container" 
                                                    onclick="navigator.clipboard.writeText('<?= url('/' . e($app['category_slug']) . '/' . e($app['slug'])) ?>'); showStitchToast('Tautan aplikasi berhasil disalin!');" 
                                                    type="button">
                                                <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between pt-1 border-t border-surface-container text-[11px] text-on-surface-variant">
                                            <span>Pemilik: <strong class="text-on-surface font-semibold"><?= e($app['author_name']) ?></strong></span>
                                            <span class="flex items-center gap-1 font-mono text-on-surface font-semibold">
                                                <span class="material-symbols-outlined text-[14px] text-secondary">visibility</span> <?= number_format($app['hit_count']) ?> Hits
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-1">
                                    <span class="inline-flex items-center gap-1 text-[11px] text-secondary font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> iFrame Wrapper Aktif
                                    </span>
                                    <a class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-lg bg-primary-container text-on-primary font-semibold text-xs hover:bg-tertiary-container transition-colors shadow-xs" 
                                       href="<?= url('/' . e($app['category_slug']) . '/' . e($app['slug'])) ?>">
                                        <span>Buka Aplikasi</span>
                                        <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Operational Sidebar Widgets (3-4 cols) (Stitch Standard) -->
                <aside class="lg:col-span-4 xl:col-span-3 flex flex-col gap-5 sticky top-20">
                    <!-- Widget A: Aplikasi Terpopuler Minggu Ini -->
                    <div class="bg-surface-container-lowest rounded-xl p-4 shadow-xs border border-surface-container/60 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-secondary text-[20px]">local_fire_department</span>
                                <h3 class="font-bold text-sm text-on-surface">Paling Populer</h3>
                            </div>
                            <span class="text-[10px] font-bold bg-secondary-container/40 text-on-secondary-container px-2 py-0.5 rounded uppercase">7 Hari</span>
                        </div>

                        <div class="flex flex-col gap-2">
                            <?php foreach ($popularApps as $idx => $pop): ?>
                            <a href="<?= url('/' . e($pop['category_slug']) . '/' . e($pop['slug'])) ?>" class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-sm font-bold w-4 text-center text-secondary"><?= $idx + 1 ?></span>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-xs font-bold text-on-surface truncate"><?= e($pop['title']) ?></span>
                                        <span class="text-[10px] text-on-surface-variant truncate"><?= number_format($pop['hit_count']) ?> eksekusi</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-secondary">TOP</span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Widget B: Tata Cara & Panduan Kontributor -->
                    <div class="bg-gradient-to-br from-surface-container to-surface-container-low rounded-xl p-4 shadow-xs border border-surface-container/60 flex flex-col gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-primary-container text-on-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">developer_mode</span>
                        </div>
                        <h3 class="font-bold text-sm text-on-surface">Punya Script GAS Sendiri?</h3>
                        <p class="text-xs text-on-surface-variant leading-relaxed">
                            Daftarkan skrip automasi internal Anda untuk diintegrasikan ke wrapper iFrame standar KKP dan dapatkan kuota eksekusi Google Workspace Enterprise.
                        </p>
                        <div class="pt-1 flex flex-col gap-1.5">
                            <a class="w-full text-center px-3 py-2 rounded-lg bg-primary text-on-primary font-bold text-xs hover:bg-primary-container transition-colors shadow-xs" 
                               href="<?= url('/submissions/new') ?>">
                                Ajukan Script Baru →
                            </a>
                            <span class="text-[11px] text-center text-on-surface-variant">
                                Standar Keamanan Sandbox (v2.4 KKP)
                            </span>
                        </div>
                    </div>

                    <!-- Widget C: IT Operations & Helpdesk Support -->
                    <div class="bg-surface-container-lowest rounded-xl p-4 shadow-xs border border-surface-container/60 flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-on-surface">
                            <span class="material-symbols-outlined text-[18px] text-secondary">support_agent</span>
                            <h4 class="font-bold text-xs">Dukungan Sistem & Masalah iFrame</h4>
                        </div>
                        <p class="text-[11px] text-on-surface-variant leading-relaxed">
                            Mengalami token expired atau galat CORS saat menjalankan modul? Hubungi petugas siaga Pusdatin KKP.
                        </p>
                        <div class="flex items-center justify-between pt-1 border-t border-surface-container font-mono text-[11px] text-on-surface-variant">
                            <span>NOC Ext: <strong class="text-on-surface">4421</strong></span>
                            <span class="text-secondary font-sans font-semibold">@kkp_itops</span>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</main>

<!-- Live Search JavaScript Handler -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const cards = document.querySelectorAll('.app-card');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            cards.forEach(card => {
                const title = card.getAttribute('data-title') || '';
                const desc = card.getAttribute('data-desc') || '';
                const cat = card.getAttribute('data-cat') || '';

                if (title.includes(query) || desc.includes(query) || cat.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
