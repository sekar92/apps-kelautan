<?php
// View: My Submissions Dashboard (FR 3.2)
require __DIR__ . '/partials/header.php';
?>

<main class="container" style="padding-top: 24px; padding-bottom: 48px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 6px;">
                Pengajuan Aplikasi Saya
            </h1>
            <p style="color: var(--text-secondary); font-size: 14px;">
                Pantau status persetujuan, tanggapan evaluasi tim IT, dan kelola aplikasi yang Anda daftarkan ke portal.
            </p>
        </div>

        <a href="<?= url('/submissions/new') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Ajukan Aplikasi Baru
        </a>
    </div>

    <?php if (empty($myApps)): ?>
        <div class="empty-state">
            <div class="empty-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
            <h3 class="empty-title">Belum ada aplikasi yang diajukan</h3>
            <p class="empty-desc">
                Anda belum pernah mengajukan aplikasi Google Apps Script atau Web SPA. Klik tombol di bawah untuk mendaftarkan aplikasi pertama Anda.
            </p>
            <div style="margin-top: 20px;">
                <a href="<?= url('/submissions/new') ?>" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Ajukan Aplikasi Sekarang
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Aplikasi</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Kunjungan (Hits)</th>
                        <th>Catatan Evaluasi Admin</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myApps as $app): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 18px; color: <?= e($app['category_color'] ?? '#06b6d4') ?>;">
                                    <?php if ($app['icon_type'] === 'upload' && !empty($app['icon_value'])): ?>
                                        <img src="<?= url('/' . e($app['icon_value'])) ?>" style="width: 100%; height: 100%; border-radius: 8px;" alt="icon">
                                    <?php else: ?>
                                        <i class="<?= e($app['icon_value']) ?>"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #fff;"><?= e($app['title']) ?></div>
                                    <div style="font-size: 11px; color: var(--text-muted); max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e($app['app_url']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 12px; font-weight: 600; color: <?= e($app['category_color'] ?? 'var(--ocean-cyan)') ?>;">
                                <?= e($app['category_name']) ?>
                            </span>
                        </td>
                        <td>
                            <?= getStatusBadge($app['status']) ?>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #fff;"><?= number_format($app['hit_count']) ?></span>
                        </td>
                        <td style="max-width: 250px;">
                            <?php if (!empty($app['review_notes'])): ?>
                                <div style="font-size: 12px; background: rgba(0,0,0,0.25); border-left: 3px solid <?= $app['status'] === 'rejected' ? '#ef4444' : ($app['status'] === 'revision' ? '#8b5cf6' : '#10b981') ?>; padding: 6px 10px; border-radius: 4px;">
                                    <strong><?= e($app['reviewer_name'] ?? 'Admin') ?>:</strong><br>
                                    <?= e($app['review_notes']) ?>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 12px; color: var(--text-muted);">&mdash;</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <?php if ($app['status'] === 'approved'): ?>
                                    <a href="<?= url('/' . e($app['category_slug']) . '/' . e($app['slug'])) ?>" class="btn btn-secondary btn-sm" title="Buka di Portal">
                                        <i class="fa-solid fa-play"></i> Buka
                                    </a>
                                <?php endif; ?>

                                <?php if ($app['status'] === 'revision' || $app['status'] === 'rejected' || $app['status'] === 'draft'): ?>
                                    <a href="<?= url('/submissions/edit/' . $app['id']) ?>" class="btn btn-warning btn-sm" title="Perbaiki & Ajukan Ulang">
                                        <i class="fa-solid fa-pen-to-square"></i> Revisi
                                    </a>
                                <?php else: ?>
                                    <a href="<?= url('/submissions/edit/' . $app['id']) ?>" class="btn btn-secondary btn-sm" title="Edit Rincian">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                <?php endif; ?>

                                <form action="<?= url('/submissions/delete/' . $app['id']) ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan aplikasi ini?');" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
