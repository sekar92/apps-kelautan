<?php
// View: Admin Review Board (FR 4.1 & FR 4.2)
require __DIR__ . '/../partials/header.php';
$activeFilter = $_GET['filter'] ?? 'pending';
?>

<main class="container" style="padding-top: 24px; padding-bottom: 60px;">
    <!-- Admin Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; color: var(--ocean-cyan); font-size: 13px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">
                <i class="fa-solid fa-shield-halved"></i> Panel Tata Kelola & Keamanan
            </div>
            <h1 style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #fff;">
                Review Board Aplikasi
            </h1>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="<?= url('/admin/categories') ?>" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-folder-tree"></i> Kelola Kategori
            </a>
            <a href="<?= url('/admin/users') ?>" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-users"></i> Kelola Pengguna
            </a>
            <a href="<?= url('/admin/logs') ?>" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-list-check"></i> Audit Trail
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div style="display: flex; gap: 10px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 6px;">
        <a href="<?= url('/admin/reviews?filter=pending') ?>" class="category-pill <?= $activeFilter === 'pending' ? 'active' : '' ?>">
            <i class="fa-solid fa-clock" style="color: #f59e0b;"></i> Menunggu Verifikasi
            <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-weight: bold;"><?= $stats['pending_apps'] ?></span>
        </a>
        <a href="<?= url('/admin/reviews?filter=approved') ?>" class="category-pill <?= $activeFilter === 'approved' ? 'active' : '' ?>">
            <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Disetujui
            <span class="badge"><?= $stats['approved_apps'] ?></span>
        </a>
        <a href="<?= url('/admin/reviews?filter=revision') ?>" class="category-pill <?= $activeFilter === 'revision' ? 'active' : '' ?>">
            <i class="fa-solid fa-triangle-exclamation" style="color: #8b5cf6;"></i> Perlu Revisi
            <span class="badge"><?= $stats['revision_apps'] ?></span>
        </a>
        <a href="<?= url('/admin/reviews?filter=all') ?>" class="category-pill <?= $activeFilter === 'all' ? 'active' : '' ?>">
            <i class="fa-solid fa-list"></i> Semua Aplikasi
            <span class="badge"><?= $stats['total_apps'] ?></span>
        </a>
    </div>

    <?php if (empty($apps)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fa-solid fa-clipboard-check"></i></div>
            <h3 class="empty-title">Tidak ada antrean review</h3>
            <p class="empty-desc">
                Saat ini tidak ada aplikasi dengan status <strong><?= e($activeFilter) ?></strong>. Semua pengajuan telah ditangani.
            </p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Aplikasi & Pengaju</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal Pengajuan</th>
                        <th style="text-align: right;">Aksi Evaluasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($apps as $app): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: flex-start; gap: 14px;">
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--ocean-cyan); flex-shrink: 0;">
                                    <i class="<?= e($app['icon_value']) ?>"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #fff; font-size: 15px;"><?= e($app['title']) ?></div>
                                    <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">
                                        Oleh: <strong><?= e($app['author_name']) ?></strong> (<?= e($app['author_department'] ?? 'Dinas Kelautan') ?>)
                                    </div>
                                    <div style="font-size: 11px; color: var(--text-muted); font-family: monospace; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e($app['app_url']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 12px; font-weight: 600; color: var(--ocean-cyan);">
                                <?= e($app['category_name']) ?>
                            </span>
                        </td>
                        <td>
                            <?= getStatusBadge($app['status']) ?>
                        </td>
                        <td>
                            <div style="font-size: 12px; color: var(--text-secondary);">
                                <?= date('d M Y, H:i', strtotime($app['created_at'])) ?>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                <!-- FR 4.1: Live Sandboxed Preview Button -->
                                <?php
                                $previewUrl = $app['app_url'];
                                if (strpos($previewUrl, 'example.com/demo-app-') !== false) {
                                    $demoType = str_replace('https://example.com/demo-app-', '', $previewUrl);
                                    $previewUrl = url('/mock-app/' . $demoType);
                                }
                                ?>
                                <button type="button" class="btn btn-secondary btn-sm" 
                                        onclick="openPreviewModal('<?= e($app['title']) ?>', '<?= e($previewUrl) ?>')"
                                        title="Uji coba dalam sandbox iframe sebelum menyetujui">
                                    <i class="fa-solid fa-eye" style="color: #38bdf8;"></i> Preview
                                </button>

                                <?php if ($app['status'] !== 'approved'): ?>
                                    <!-- Approve Form -->
                                    <form action="<?= url('/admin/reviews/' . $app['id'] . '/approve') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Setujui aplikasi ini untuk langsung tampil di katalog publik?');">
                                        <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                        <button type="submit" class="btn btn-success btn-sm" title="Setujui (Approve)">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                    </form>

                                    <!-- Revision Button -->
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            onclick="openRevisionModal(<?= $app['id'] ?>, '<?= e(addslashes($app['title'])) ?>')">
                                        <i class="fa-solid fa-rotate-left"></i> Minta Revisi
                                    </button>

                                    <!-- Reject Button (FR 4.2) -->
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="openRejectModal(<?= $app['id'] ?>, '<?= e(addslashes($app['title'])) ?>')">
                                        <i class="fa-solid fa-xmark"></i> Tolak
                                    </button>
                                <?php else: ?>
                                    <a href="<?= url('/' . e($app['category_slug']) . '/' . e($app['slug'])) ?>" class="btn btn-secondary btn-sm" target="_blank">
                                        <i class="fa-solid fa-up-right-from-square"></i> Buka
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<!-- MODAL 1: PREVIEW IFRAME (FR 4.1) -->
<div class="modal-backdrop" id="previewModal" onclick="if(event.target===this) closeModal('previewModal')">
    <div class="modal-dialog modal-xl">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" id="previewModalTitle"><i class="fa-solid fa-flask"></i> Uji Coba Keamanan iFrame</h3>
                <div style="font-size: 11px; color: var(--text-muted);">Diuji dalam atribut sandbox isolasi ketat</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('previewModal')">&times;</button>
        </div>
        <div class="modal-body" style="padding: 0; position: relative;">
            <iframe id="previewModalFrame" src="" class="app-iframe" style="width: 100%; height: 100%; min-height: 500px;" sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads"></iframe>
        </div>
        <div class="modal-footer">
            <span style="font-size: 12px; color: #10b981; margin-right: auto;">
                <i class="fa-solid fa-shield-check"></i> Sandboxed Environment Active
            </span>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('previewModal')">Tutup Pratinjau</button>
        </div>
    </div>
</div>

<!-- MODAL 2: REJECT APP WITH MANDATORY REASON (FR 4.2) -->
<div class="modal-backdrop" id="rejectModal" onclick="if(event.target===this) closeModal('rejectModal')">
    <div class="modal-dialog">
        <form id="rejectForm" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="modal-header">
                <h3 class="modal-title" style="color: var(--coral-red);">
                    <i class="fa-solid fa-triangle-exclamation"></i> Tolak Pengajuan Aplikasi
                </h3>
                <button type="button" class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;" id="rejectModalText">
                    Anda akan menolak pengajuan aplikasi. Sesuai <strong>FR 4.2</strong>, admin wajib menyertakan alasan penolakan secara spesifik.
                </p>

                <div class="form-group">
                    <label class="form-label" for="rejectNotes">Alasan Penolakan <span style="color: var(--coral-red);">*</span></label>
                    <textarea name="notes" id="rejectNotes" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan (misal: celah keamanan, URL tidak dapat diakses, konten tidak relevan)..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('rejectModal')">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fa-solid fa-ban"></i> Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: REQUEST REVISION -->
<div class="modal-backdrop" id="revisionModal" onclick="if(event.target===this) closeModal('revisionModal')">
    <div class="modal-dialog">
        <form id="revisionForm" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="modal-header">
                <h3 class="modal-title" style="color: var(--purple-deep);">
                    <i class="fa-solid fa-rotate-left"></i> Permintaan Perbaikan / Revisi
                </h3>
                <button type="button" class="modal-close" onclick="closeModal('revisionModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;" id="revisionModalText">
                    Kirim instruksi perbaikan kepada pengembang agar mereka dapat merevisi URL atau data aplikasi.
                </p>

                <div class="form-group">
                    <label class="form-label" for="revisionNotes">Instruksi Revisi <span style="color: var(--coral-red);">*</span></label>
                    <textarea name="notes" id="revisionNotes" class="form-control" rows="4" placeholder="Instruksi perbaikan (misal: perbarui izin akses spreadsheet, tambahkan validasi token)..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('revisionModal')">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Permintaan Revisi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPreviewModal(title, url) {
    document.getElementById('previewModalTitle').innerText = 'Pratinjau: ' + title;
    document.getElementById('previewModalFrame').src = url;
    openModal('previewModal');
}

function openRejectModal(id, title) {
    document.getElementById('rejectForm').action = window.PORTAL_BASE_URL + '/admin/reviews/' + id + '/reject';
    document.getElementById('rejectModalText').innerHTML = 'Anda akan menolak pengajuan <strong>' + title + '</strong>. Alasan penolakan wajib diisi.';
    document.getElementById('rejectNotes').value = '';
    openModal('rejectModal');
}

function openRevisionModal(id, title) {
    document.getElementById('revisionForm').action = window.PORTAL_BASE_URL + '/admin/reviews/' + id + '/revision';
    document.getElementById('revisionModalText').innerHTML = 'Kirim instruksi revisi untuk aplikasi <strong>' + title + '</strong>.';
    document.getElementById('revisionNotes').value = '';
    openModal('revisionModal');
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
