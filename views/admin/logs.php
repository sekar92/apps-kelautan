<?php
// View: Audit Trail & Analytics Logs (Refactored to match Stitch Standard)
require __DIR__ . '/../partials/header.php';
?>

<main class="w-full flex-1 bg-surface pt-4">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 py-8 flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="<?= url('/admin/reviews') ?>" class="inline-flex items-center gap-1.5 text-xs text-secondary font-semibold hover:underline mb-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Review Board</span>
                </a>
                <h1 class="text-2xl font-bold text-primary tracking-tight">Audit Trail & Log Aktivitas Sistem</h1>
                <p class="text-xs text-on-surface-variant">Rekam jejak aktivitas operasional, keputusan evaluasi admin, dan peluncuran modul (FR 6.1 & FR 6.2).</p>
            </div>
        </div>

        <!-- Analytics Metric Cards (Stitch Standard FR 6.1) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-container/60 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-semibold text-on-surface-variant">Total Peluncuran (Hits)</span>
                <div class="text-3xl font-bold text-secondary my-1"><?= number_format($stats['total_hits']) ?></div>
                <span class="text-[10px] text-secondary font-bold uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">bolt</span> Realtime Counter
                </span>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-container/60 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-semibold text-on-surface-variant">Aplikasi Disetujui</span>
                <div class="text-3xl font-bold text-primary my-1"><?= $stats['approved_apps'] ?> <span class="text-xs text-on-surface-variant font-normal">/ <?= $stats['total_apps'] ?></span></div>
                <span class="text-[10px] text-secondary font-bold uppercase tracking-wider">Aktif di Katalog Publik</span>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-container/60 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-semibold text-on-surface-variant">Antrean Review</span>
                <div class="text-3xl font-bold text-on-surface my-1"><?= $stats['pending_apps'] ?></div>
                <span class="text-[10px] text-on-surface-variant font-bold uppercase tracking-wider">Menunggu Evaluasi IT</span>
            </div>

            <div class="bg-surface-container-lowest p-5 rounded-2xl border border-surface-container/60 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-semibold text-on-surface-variant">Pengguna Terdaftar</span>
                <div class="text-3xl font-bold text-primary my-1"><?= $stats['total_users'] ?></div>
                <span class="text-[10px] text-on-surface-variant font-bold uppercase tracking-wider">Akun Pegawai Aktif</span>
            </div>
        </div>

        <!-- Audit Trail Table -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 overflow-hidden">
            <div class="p-4 bg-surface-container-low border-b border-surface-container flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[20px]">history</span>
                    <span class="font-bold text-xs text-primary">Rekam Jejak 100 Aktivitas Terkini</span>
                </div>
                <span class="text-[11px] text-on-surface-variant font-mono">Immutable Audit Log</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-container-low text-on-surface-variant uppercase text-[10px] font-bold tracking-wider border-b border-surface-container">
                        <tr>
                            <th class="p-4">Waktu (WIB)</th>
                            <th class="p-4">Aktor Pengguna</th>
                            <th class="p-4">Action Code</th>
                            <th class="p-4">Rincian Peristiwa</th>
                            <th class="p-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="p-4 whitespace-nowrap text-on-surface-variant font-mono text-[11px]">
                                <?= date('d M Y, H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="p-4">
                                <?php if (!empty($log['user_name'])): ?>
                                    <div class="font-bold text-on-surface"><?= e($log['user_name']) ?></div>
                                    <div class="text-[10px] text-on-surface-variant font-mono"><?= e($log['user_email']) ?></div>
                                <?php else: ?>
                                    <span class="text-on-surface-variant font-medium">Sistem Daemon</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-surface-container text-secondary">
                                    <?= e($log['action']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-on-surface max-w-md font-medium">
                                <?= e($log['details']) ?>
                            </td>
                            <td class="p-4 text-on-surface-variant font-mono text-[11px]">
                                <?= e($log['ip_address']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
