<?php
// View: My Submissions & Applications Tracker (Refactored to match Stitch pengajuan.html)
require __DIR__ . '/partials/header.php';
$activeTab = $_GET['tab'] ?? 'tracker';

$totalSubmissions = count($myApps);
$approvedCount = count(array_filter($myApps, fn($a) => $a['status'] === 'approved'));
$revisionCount = count(array_filter($myApps, fn($a) => $a['status'] === 'revision' || $a['status'] === 'rejected'));
?>

<main class="w-full flex-1 bg-surface pt-4">
    <div class="flex flex-col w-full">
        <!-- Ambient Top Visual Header Band (Stitch Standard) -->
        <section class="w-full px-4 sm:px-8 py-8 bg-surface-container-lowest shadow-xs border-b border-surface-container">
            <div class="max-w-[1440px] mx-auto flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="flex flex-col gap-1.5 max-w-3xl">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-tertiary-fixed-variant text-[10px] font-bold uppercase tracking-wider">
                            FR 3.0 • E-Governance Portal
                        </span>
                        <span class="text-outline text-xs">•</span>
                        <span class="text-secondary text-xs font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Sandboxing Terenkripsi KKP
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-primary tracking-tight">
                        Pengajuan & Manajemen Aplikasi Saya
                    </h1>

                    <p class="text-sm text-on-surface-variant leading-relaxed">
                        Daftarkan aplikasi Google Apps Script (GAS) atau TypeScript SPA internal Anda untuk ditinjau oleh Tim IT Kementerian Kelautan dan Perikanan sebelum diterbitkan ke katalog resmi.
                    </p>
                </div>

                <!-- Quick Metrics Counter Ribbon -->
                <div class="flex items-center gap-2 bg-surface-container-low p-2 rounded-2xl border border-surface-container/60">
                    <div class="px-4 py-2 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col items-center min-w-[70px]">
                        <span class="text-xl font-bold text-primary"><?= $totalSubmissions ?></span>
                        <span class="text-[9px] font-bold text-on-surface-variant uppercase">Terdaftar</span>
                    </div>
                    <div class="px-4 py-2 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col items-center min-w-[70px]">
                        <span class="text-xl font-bold text-secondary"><?= $approvedCount ?></span>
                        <span class="text-[9px] font-bold text-on-surface-variant uppercase">Aktif Live</span>
                    </div>
                    <div class="px-4 py-2 bg-surface-container-lowest rounded-xl shadow-xs flex flex-col items-center min-w-[70px]">
                        <span class="text-xl font-bold text-error"><?= $revisionCount ?></span>
                        <span class="text-[9px] font-bold text-on-surface-variant uppercase">Revisi</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Segment Tab Control Hub (Stitch Standard) -->
        <div class="w-full px-4 sm:px-8 pt-6 pb-2">
            <div class="max-w-[1440px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center p-1 bg-surface-container-low rounded-xl gap-1 w-full sm:w-auto shadow-inner border border-surface-container/60">
                    <a href="<?= url('/submissions/new') ?>" 
                       class="flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-bold text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-all">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Form Pengajuan Baru</span>
                    </a>

                    <a href="<?= url('/submissions') ?>" 
                       class="flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-primary-container text-on-primary transition-all shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                        <span>Status & Riwayat Pengajuan (<?= $totalSubmissions ?>)</span>
                    </a>
                </div>

                <!-- Compliance Protocol Info Chip -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-high text-on-surface text-xs font-medium">
                    <span class="material-symbols-outlined text-[16px] text-secondary">verified_user</span>
                    <span>Sandbox Isolation: ISO/IEC 27001 KKP Standard compliant</span>
                </div>
            </div>
        </div>

        <!-- TRACKER VIEW (FR 3.2) -->
        <div class="w-full px-4 sm:px-8 py-4">
            <div class="max-w-[1440px] mx-auto">
                <?php if (empty($myApps)): ?>
                    <div class="bg-surface-container-lowest rounded-2xl p-16 text-center border border-dashed border-surface-container-high shadow-xs">
                        <span class="material-symbols-outlined text-4xl text-outline mb-2">post_add</span>
                        <h3 class="text-base font-bold text-on-surface">Belum ada pengajuan aplikasi</h3>
                        <p class="text-xs text-on-surface-variant max-w-sm mx-auto mt-1">
                            Anda belum pernah mendaftarkan aplikasi Google Apps Script atau TypeScript SPA.
                        </p>
                        <div class="mt-4">
                            <a href="<?= url('/submissions/new') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-container text-on-primary text-xs font-bold hover:bg-tertiary-container transition-colors shadow-xs">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                <span>Ajukan Aplikasi Pertama Anda</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($myApps as $app): ?>
                        <div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-surface-container-low text-secondary flex items-center justify-center shrink-0 border border-surface-container/60">
                                    <span class="material-symbols-outlined text-[26px]">
                                        <?= strpos($app['category_slug'], 'appscript') !== false ? 'code' : 'dataset' ?>
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-surface-container-highest text-on-surface">
                                            <?= e($app['category_name']) ?>
                                        </span>
                                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded-full <?= $app['status'] === 'approved' ? 'bg-secondary/15 text-secondary' : ($app['status'] === 'rejected' ? 'bg-error/15 text-error' : 'bg-surface-container text-on-surface') ?>">
                                            <?= strtoupper(e($app['status'])) ?>
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-base text-primary mt-1"><?= e($app['title']) ?></h3>
                                    <p class="text-xs text-on-surface-variant font-mono truncate max-w-md">
                                        <?= e($app['app_url']) ?>
                                    </p>
                                    
                                    <?php if (!empty($app['review_notes'])): ?>
                                    <div class="mt-2.5 p-3 rounded-xl bg-surface-container-low text-xs border border-surface-container/60">
                                        <strong class="text-on-surface">Catatan Evaluator:</strong>
                                        <div class="text-on-surface-variant mt-0.5"><?= e($app['review_notes']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                                <?php if ($app['status'] === 'approved'): ?>
                                    <a href="<?= url('/' . e($app['category_slug']) . '/' . e($app['slug'])) ?>" class="px-4 py-2 rounded-xl bg-primary-container text-on-primary text-xs font-bold hover:bg-tertiary-container transition-colors shadow-xs">
                                        Buka di Portal
                                    </a>
                                <?php endif; ?>

                                <?php if ($app['status'] === 'revision' || $app['status'] === 'rejected' || $app['status'] === 'draft'): ?>
                                    <a href="<?= url('/submissions/edit/' . $app['id']) ?>" class="px-4 py-2 rounded-xl bg-secondary text-on-secondary text-xs font-bold hover:bg-on-secondary-container transition-colors shadow-xs">
                                        Perbaiki & Ajukan Ulang
                                    </a>
                                <?php else: ?>
                                    <a href="<?= url('/submissions/edit/' . $app['id']) ?>" class="px-3 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold transition-colors">
                                        Edit Rincian
                                    </a>
                                <?php endif; ?>

                                <form action="<?= url('/submissions/delete/' . $app['id']) ?>" method="POST" onsubmit="return confirm('Hapus pengajuan aplikasi ini?');" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                    <button type="submit" class="p-2 rounded-xl text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
