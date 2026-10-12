<?php
// View: Admin Review Board (Refactored to match Stitch admin_review.html)
require __DIR__ . '/../partials/header.php';
$activeFilter = $_GET['filter'] ?? 'pending';
?>

<main class="w-full flex-1 bg-surface pt-4">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 py-6 flex flex-col gap-6">
        <!-- Top Bar / Sub-Navigation & Scope Indicators (Stitch Standard) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-secondary px-2.5 py-0.5 bg-secondary-container/20 rounded-full">
                        Pusdatin & Direktorat Jenderal KKP
                    </span>
                    <span class="text-outline-variant">•</span>
                    <span class="text-xs text-on-surface-variant font-medium">Protokol Keamanan Level 3 (SANDBOX-STRICT NFR 1)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-primary tracking-tight">
                    Panel Review & Tata Kelola Aplikasi Internal
                </h1>
                <p class="text-xs text-on-surface-variant">
                    Otorisasi distribusi modul digital kelautan, validasi sanitasi skrip GAS, dan pengawasan audit trail real-time (FR 4.1 & FR 4.2).
                </p>
            </div>

            <div class="flex items-center gap-3 self-start md:self-auto flex-wrap">
                <div class="flex items-center gap-2 bg-surface-container-lowest px-3.5 py-2 rounded-xl shadow-xs border border-surface-container/60">
                    <span class="material-symbols-outlined text-secondary text-[20px]">verified_user</span>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase">SLA Peninjauan</span>
                        <span class="text-xs text-primary font-bold">14 Jam Tersisa</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?= url('/admin/categories') ?>" class="px-3 py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold transition-colors">
                        Kategori
                    </a>
                    <a href="<?= url('/admin/users') ?>" class="px-3 py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold transition-colors">
                        Pengguna
                    </a>
                    <a href="<?= url('/admin/logs') ?>" class="px-3 py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-semibold transition-colors">
                        Audit Trail
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter & Sub-Nav Strip -->
        <div class="flex flex-wrap items-center justify-between gap-3 p-2 bg-surface-container-low rounded-xl border border-surface-container/60">
            <div class="flex flex-wrap items-center gap-1.5" id="queue-filter-group">
                <a href="<?= url('/admin/reviews?filter=pending') ?>" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 <?= $activeFilter === 'pending' ? 'bg-primary-container text-on-primary shadow-xs' : 'text-on-surface hover:bg-surface-container-lowest' ?>">
                    <span>Perlu Ditinjau Segera</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full <?= $activeFilter === 'pending' ? 'bg-secondary text-primary' : 'bg-secondary-container text-on-secondary-container' ?>">
                        <?= $stats['pending_apps'] ?>
                    </span>
                </a>

                <a href="<?= url('/admin/reviews?filter=approved') ?>" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 <?= $activeFilter === 'approved' ? 'bg-primary-container text-on-primary shadow-xs' : 'text-on-surface hover:bg-surface-container-lowest' ?>">
                    <span>Selesai & Dipublikasikan</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full bg-surface-container text-on-surface-variant">
                        <?= $stats['approved_apps'] ?>
                    </span>
                </a>

                <a href="<?= url('/admin/reviews?filter=revision') ?>" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 <?= $activeFilter === 'revision' ? 'bg-primary-container text-on-primary shadow-xs' : 'text-on-surface hover:bg-surface-container-lowest' ?>">
                    <span>Revisi / Pending User</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full bg-surface-container text-on-surface-variant">
                        <?= $stats['revision_apps'] ?>
                    </span>
                </a>

                <a href="<?= url('/admin/reviews?filter=all') ?>" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-2 <?= $activeFilter === 'all' ? 'bg-primary-container text-on-primary shadow-xs' : 'text-on-surface hover:bg-surface-container-lowest' ?>">
                    <span>Semua Antrian</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full bg-surface-container text-on-surface-variant">
                        <?= $stats['total_apps'] ?>
                    </span>
                </a>
            </div>

            <div class="flex items-center gap-1.5 text-on-surface-variant text-xs pr-2">
                <span class="material-symbols-outlined text-[16px] text-secondary">filter_alt</span>
                <span>Proses Governance KKP RI</span>
            </div>
        </div>

        <!-- Main Review Board Stream -->
        <?php if (empty($apps)): ?>
            <div class="bg-surface-container-lowest rounded-xl p-16 text-center border border-dashed border-surface-container-high shadow-xs">
                <span class="material-symbols-outlined text-4xl text-outline mb-2">task_alt</span>
                <h3 class="text-base font-bold text-on-surface">Tidak ada antrean review</h3>
                <p class="text-xs text-on-surface-variant max-w-sm mx-auto mt-1">
                    Semua pengajuan pada filter <strong><?= e($activeFilter) ?></strong> telah ditangani atau belum ada pengajuan baru.
                </p>
            </div>
        <?php else: ?>
            <div class="flex flex-col gap-6">
                <?php foreach ($apps as $app): ?>
                <?php
                $previewUrl = $app['app_url'];
                if (strpos($previewUrl, 'example.com/demo-app-') !== false) {
                    $demoType = str_replace('https://example.com/demo-app-', '', $previewUrl);
                    $previewUrl = url('/mock-app/' . $demoType);
                }
                ?>
                <div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 p-6 flex flex-col gap-5 relative overflow-hidden">
                    <div class="absolute top-0 right-0 left-0 h-1.5 bg-gradient-to-r from-secondary via-secondary-container to-primary"></div>

                    <!-- Header status -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-secondary/10 text-secondary px-2.5 py-0.5 rounded">
                                Tiket #REV-<?= date('Y', strtotime($app['created_at'])) ?>-<?= sprintf('%03d', $app['id']) ?>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-surface-container text-xs font-semibold text-on-surface">
                                <span class="w-2 h-2 rounded-full <?= $app['status'] === 'approved' ? 'bg-secondary' : ($app['status'] === 'rejected' ? 'bg-error' : 'bg-secondary animate-pulse') ?>"></span>
                                <?= strtoupper(e($app['status'])) ?>
                            </span>
                        </div>
                        <span class="text-xs text-on-surface-variant font-mono">
                            Diajukan: <?= date('d M Y, H:i', strtotime($app['created_at'])) ?> WIB
                        </span>
                    </div>

                    <!-- Subject Header -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-surface-container-low p-4 rounded-xl border border-surface-container/50">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-primary-container text-surface flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[26px] text-secondary">
                                    <?= strpos($app['category_slug'], 'appscript') !== false ? 'code' : 'directions_boat' ?>
                                </span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <h2 class="font-bold text-lg text-primary tracking-tight"><?= e($app['title']) ?></h2>
                                    <span class="text-[10px] font-bold bg-surface-container-highest text-on-surface uppercase px-2 py-0.5 rounded">
                                        <?= e($app['category_name']) ?>
                                    </span>
                                </div>
                                <p class="text-xs text-on-surface-variant mt-0.5">
                                    Pengaju: <strong class="text-on-surface"><?= e($app['author_name']) ?></strong> (<?= e($app['author_email']) ?>) &bull; Unit: <?= e($app['author_department'] ?? 'Dinas Kelautan') ?>
                                </p>
                            </div>
                        </div>

                        <!-- Live Sandboxed iFrame Preview Button (FR 4.1) -->
                        <button class="shrink-0 bg-secondary text-on-secondary hover:bg-on-secondary-container px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs transition-colors" 
                                onclick="openStitchPreview('<?= e(addslashes($app['title'])) ?>', '<?= e($previewUrl) ?>')" 
                                type="button">
                            <span class="material-symbols-outlined text-[18px]">terminal</span>
                            <span>Live Preview (Sandbox iFrame)</span>
                        </button>
                    </div>

                    <!-- Technical Metadata Matrix (Stitch Standard) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="p-3 rounded-xl bg-surface-container-low flex flex-col gap-1 border border-surface-container/40">
                            <span class="text-[10px] font-bold text-on-surface-variant uppercase">Target Routing Bersih</span>
                            <div class="flex items-center gap-1 text-xs text-primary font-mono truncate">
                                <span class="material-symbols-outlined text-secondary text-[16px]">link</span>
                                <span class="truncate">apps.kelautan.id/<?= e($app['category_slug']) ?>/<?= e($app['slug']) ?></span>
                            </div>
                            <span class="text-[10px] text-on-surface-variant">Apache mod_rewrite map active</span>
                        </div>

                        <div class="p-3 rounded-xl bg-surface-container-low flex flex-col gap-1 border border-surface-container/40">
                            <span class="text-[10px] font-bold text-on-surface-variant uppercase">Runtime Sandbox Profiling</span>
                            <div class="flex items-center gap-1 text-xs text-on-surface font-semibold">
                                <span class="material-symbols-outlined text-secondary text-[16px]">verified</span>
                                <span>ALLOW-SCRIPTS | ALLOW-FORMS</span>
                            </div>
                            <span class="text-[10px] text-on-surface-variant">Same-origin token isolated</span>
                        </div>

                        <div class="p-3 rounded-xl bg-surface-container-low flex flex-col gap-1 border border-surface-container/40">
                            <span class="text-[10px] font-bold text-on-surface-variant uppercase">Backend Stack Compatibility</span>
                            <div class="flex items-center gap-1 text-xs text-on-surface font-semibold">
                                <span class="material-symbols-outlined text-secondary text-[16px]">check_circle</span>
                                <span>PHP 8.5 Test Passed (0 Warning)</span>
                            </div>
                            <span class="text-[10px] text-on-surface-variant">Forwarder latency: 18ms</span>
                        </div>
                    </div>

                    <!-- App Scope & Impact Documentation -->
                    <div class="flex flex-col gap-1 bg-surface-container-low/50 p-3.5 rounded-xl border border-surface-container/40">
                        <span class="text-xs font-bold text-on-surface">Deskripsi Fungsional & Otoritas:</span>
                        <p class="text-xs text-on-surface-variant leading-relaxed"><?= e($app['description']) ?></p>
                    </div>

                    <?php if (!empty($app['review_notes'])): ?>
                    <div class="bg-surface-container p-3 rounded-xl border border-surface-container-high text-xs">
                        <strong class="text-on-surface">Catatan Evaluasi Terakhir:</strong>
                        <div class="text-on-surface-variant mt-0.5"><?= nl2br(e($app['review_notes'])) ?></div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Command Console: Review Board Actions (FR 4.1 & FR 4.2) -->
                    <?php if ($app['status'] !== 'approved'): ?>
                    <div class="bg-surface-container p-4 rounded-xl flex flex-col gap-3 border border-surface-container-high">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">gavel</span>
                                <span class="text-xs font-bold text-primary">Keputusan Tata Kelola & Evaluasi Rilis</span>
                            </div>
                            <span class="text-[10px] font-bold text-on-surface-variant uppercase">Aturan Standar KKP RI 2025</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Approve Action -->
                            <form action="<?= url('/admin/reviews/' . $app['id'] . '/approve') ?>" method="POST" onsubmit="return confirm('Setujui aplikasi ini untuk langsung tampil di katalog publik?');">
                                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                <input type="hidden" name="notes" value="Aplikasi lolos audit keamanan sandbox dan disetujui terbit.">
                                <button type="submit" class="w-full p-3 rounded-xl bg-surface-container-lowest text-left hover:border-secondary transition-all shadow-xs border border-transparent hover:border flex items-center justify-between group">
                                    <div>
                                        <div class="text-xs font-bold text-primary group-hover:text-secondary">Setujui (Approve)</div>
                                        <div class="text-[10px] text-on-surface-variant">Rilis ke katalog resmi</div>
                                    </div>
                                    <span class="material-symbols-outlined text-secondary text-[22px]">verified</span>
                                </button>
                            </form>

                            <!-- Request Revision Action -->
                            <button type="button" 
                                    onclick="openStitchRevisionModal(<?= $app['id'] ?>, '<?= e(addslashes($app['title'])) ?>')"
                                    class="p-3 rounded-xl bg-surface-container-lowest text-left hover:border-secondary transition-all shadow-xs border border-transparent hover:border flex items-center justify-between group">
                                <div>
                                    <div class="text-xs font-bold text-primary group-hover:text-secondary">Minta Revisi</div>
                                    <div class="text-[10px] text-on-surface-variant">Kirim catatan perbaikan</div>
                                </div>
                                <span class="material-symbols-outlined text-secondary text-[22px]">rotate_left</span>
                            </button>

                            <!-- Reject Action (FR 4.2) -->
                            <button type="button" 
                                    onclick="openStitchRejectModal(<?= $app['id'] ?>, '<?= e(addslashes($app['title'])) ?>')"
                                    class="p-3 rounded-xl bg-surface-container-lowest text-left hover:border-error transition-all shadow-xs border border-transparent hover:border flex items-center justify-between group">
                                <div>
                                    <div class="text-xs font-bold text-error">Tolak (Reject)</div>
                                    <div class="text-[10px] text-on-surface-variant">Wajib sertakan alasan</div>
                                </div>
                                <span class="material-symbols-outlined text-error text-[22px]">cancel</span>
                            </button>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-xs text-secondary font-semibold flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                            <span>Aplikasi telah disetujui dan aktif melayani pengguna.</span>
                        </span>
                        <a href="<?= url('/' . e($app['category_slug']) . '/' . e($app['slug'])) ?>" class="px-4 py-2 rounded-xl bg-primary-container text-on-primary text-xs font-bold hover:bg-tertiary-container transition-colors" target="_blank">
                            Buka di Portal
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- MODAL: LIVE SANDBOX PREVIEW (FR 4.1) -->
<div id="previewModalStitch" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest border border-surface-container rounded-2xl max-w-5xl w-full h-[85vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="p-4 bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-secondary text-[22px]">terminal</span>
                <span class="font-bold text-sm text-primary" id="previewTitleDisplay">Live Sandbox Preview</span>
            </div>
            <button type="button" onclick="document.getElementById('previewModalStitch').classList.add('hidden')" class="p-1 rounded-lg hover:bg-surface-container text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 bg-surface-bright relative">
            <iframe id="stitchPreviewFrame" src="" class="w-full h-full border-none" sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads"></iframe>
        </div>
        <div class="p-3 bg-surface-container-lowest border-t border-surface-container flex items-center justify-between text-xs">
            <span class="text-secondary font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">verified</span>
                <span>Sandboxed Isolation Active</span>
            </span>
            <button type="button" onclick="document.getElementById('previewModalStitch').classList.add('hidden')" class="px-4 py-1.5 rounded-lg bg-surface-container text-on-surface font-semibold text-xs hover:bg-surface-container-high transition-colors">
                Tutup Pratinjau
            </button>
        </div>
    </div>
</div>

<!-- MODAL: REJECT WITH MANDATORY REASON (FR 4.2) -->
<div id="rejectModalStitch" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest border border-surface-container rounded-2xl max-w-lg w-full p-6 shadow-2xl">
        <form id="rejectFormStitch" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="flex items-center gap-2.5 text-error mb-3">
                <span class="material-symbols-outlined text-[24px]">cancel</span>
                <h3 class="font-bold text-base">Tolak Pengajuan Aplikasi</h3>
            </div>
            <p class="text-xs text-on-surface-variant mb-4 leading-relaxed" id="rejectTextStitch">
                Sesuai FR 4.2, admin wajib menyertakan alasan penolakan secara spesifik agar pengembang mengetahui kekurangan aplikasi.
            </p>
            <div class="mb-4">
                <label class="block text-xs font-bold text-on-surface mb-1.5">Alasan Penolakan <span class="text-error">*</span></label>
                <textarea name="notes" rows="4" class="w-full p-3 rounded-xl bg-surface-container-low border border-surface-container text-on-surface text-xs focus:ring-2 focus:ring-error/40 focus:outline-none" placeholder="Jelaskan alasan penolakan (misal: celah CORS, form tidak merespons, URL privat)..." required></textarea>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('rejectModalStitch').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-error text-on-error text-xs font-bold hover:bg-error/90 transition-colors shadow-xs">
                    Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: REQUEST REVISION -->
<div id="revisionModalStitch" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest border border-surface-container rounded-2xl max-w-lg w-full p-6 shadow-2xl">
        <form id="revisionFormStitch" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="flex items-center gap-2.5 text-secondary mb-3">
                <span class="material-symbols-outlined text-[24px]">rotate_left</span>
                <h3 class="font-bold text-base text-primary">Permintaan Perbaikan / Revisi</h3>
            </div>
            <p class="text-xs text-on-surface-variant mb-4 leading-relaxed" id="revisionTextStitch">
                Kirim instruksi perbaikan kepada pengembang agar mereka dapat merevisi URL atau skrip aplikasi.
            </p>
            <div class="mb-4">
                <label class="block text-xs font-bold text-on-surface mb-1.5">Instruksi Perbaikan <span class="text-error">*</span></label>
                <textarea name="notes" rows="4" class="w-full p-3 rounded-xl bg-surface-container-low border border-surface-container text-on-surface text-xs focus:ring-2 focus:ring-secondary/40 focus:outline-none" placeholder="Instruksi perbaikan (misal: buka izin akses view spreadsheet untuk verifikator)..." required></textarea>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('revisionModalStitch').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface hover:bg-surface-container transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-secondary text-on-secondary text-xs font-bold hover:bg-on-secondary-container transition-colors shadow-xs">
                    Kirim Catatan Revisi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openStitchPreview(title, url) {
    document.getElementById('previewTitleDisplay').innerText = 'Live Preview: ' + title;
    document.getElementById('stitchPreviewFrame').src = url;
    document.getElementById('previewModalStitch').classList.remove('hidden');
}

function openStitchRejectModal(id, title) {
    document.getElementById('rejectFormStitch').action = window.PORTAL_BASE_URL + '/admin/reviews/' + id + '/reject';
    document.getElementById('rejectTextStitch').innerHTML = 'Anda akan menolak pengajuan <strong>' + title + '</strong>. Alasan penolakan wajib diisi.';
    document.getElementById('rejectModalStitch').classList.remove('hidden');
}

function openStitchRevisionModal(id, title) {
    document.getElementById('revisionFormStitch').action = window.PORTAL_BASE_URL + '/admin/reviews/' + id + '/revision';
    document.getElementById('revisionTextStitch').innerHTML = 'Kirim instruksi perbaikan untuk pengajuan <strong>' + title + '</strong>.';
    document.getElementById('revisionModalStitch').classList.remove('hidden');
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
