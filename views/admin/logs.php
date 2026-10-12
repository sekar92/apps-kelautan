<?php
// View: Audit Trail & Analytics Logs (FR 6.1 & FR 6.2)
require __DIR__ . '/../partials/header.php';
?>

<main class="container" style="padding-top: 24px; padding-bottom: 60px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <a href="<?= url('/admin/reviews') ?>" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Panel Tata Kelola
            </a>
            <h1 style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #fff;">
                Audit Trail & Log Aktivitas Sistem
            </h1>
            <p style="color: var(--text-secondary); font-size: 14px;">
                Rekam jejak seluruh aktivitas CRUD administrator, evaluasi aplikasi, dan riwayat akses pengguna (FR 6.2).
            </p>
        </div>
    </div>

    <!-- Analytics Summary Cards (FR 6.1) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 20px;">
            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Total Peluncuran Aplikasi (Hits)</div>
            <div style="font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: var(--ocean-cyan);">
                <?= number_format($stats['total_hits']) ?>
            </div>
            <div style="font-size: 11px; color: #10b981; margin-top: 4px;">
                <i class="fa-solid fa-chart-line"></i> Hit Counter Terakumulasi
            </div>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 20px;">
            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Aplikasi Disetujui</div>
            <div style="font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: #10b981;">
                <?= $stats['approved_apps'] ?> <span style="font-size: 14px; color: var(--text-secondary); font-weight: 400;">/ <?= $stats['total_apps'] ?></span>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                Aktif di Katalog Publik
            </div>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 20px;">
            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Antrean Review</div>
            <div style="font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: #f59e0b;">
                <?= $stats['pending_apps'] ?>
            </div>
            <div style="font-size: 11px; color: #f59e0b; margin-top: 4px;">
                Menunggu Verifikasi Admin
            </div>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 20px;">
            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Total Pengguna Terdaftar</div>
            <div style="font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: #8b5cf6;">
                <?= $stats['total_users'] ?>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                Akun Pegawai & Administrator
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu (Timestamp)</th>
                    <th>Aktor / Pengguna</th>
                    <th>Aksi (Action Code)</th>
                    <th>Rincian Aktivitas</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td style="white-space: nowrap; font-size: 12px; color: var(--text-secondary);">
                        <i class="fa-regular fa-clock" style="margin-right: 4px;"></i>
                        <?= date('d M Y, H:i:s', strtotime($log['created_at'])) ?>
                    </td>
                    <td>
                        <?php if (!empty($log['user_name'])): ?>
                            <div style="font-weight: 600; color: #fff; font-size: 13px;"><?= e($log['user_name']) ?></div>
                            <div style="font-size: 11px; color: var(--text-muted);"><?= e($log['user_email']) ?></div>
                        <?php else: ?>
                            <span style="color: var(--text-muted); font-size: 12px;">Sistem Otomatis</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $action = $log['action'];
                        $actionColor = '#06b6d4';
                        if (strpos($action, 'APPROVE') !== false) $actionColor = '#10b981';
                        elseif (strpos($action, 'REJECT') !== false) $actionColor = '#ef4444';
                        elseif (strpos($action, 'REVISION') !== false) $actionColor = '#8b5cf6';
                        elseif (strpos($action, 'LOGIN') !== false) $actionColor = '#3b82f6';
                        ?>
                        <code style="background: rgba(255,255,255,0.06); color: <?= $actionColor ?>; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                            <?= e($action) ?>
                        </code>
                    </td>
                    <td>
                        <span style="font-size: 13px; color: var(--text-primary);"><?= e($log['details']) ?></span>
                    </td>
                    <td>
                        <code style="font-size: 11px; color: var(--text-muted);"><?= e($log['ip_address']) ?></code>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
