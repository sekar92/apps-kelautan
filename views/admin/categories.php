<?php
// View: Category Management (Refactored to match Stitch Standard)
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
                <h1 class="text-2xl font-bold text-primary tracking-tight">Kelola Kategori Layanan</h1>
                <p class="text-xs text-on-surface-variant">Atur taksonomi dan slug URL routing aplikasi kelautan (FR 4.3).</p>
            </div>

            <button type="button" onclick="document.getElementById('addCatModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary text-xs font-bold transition-colors shadow-xs flex items-center gap-2 self-start sm:self-auto">
                <span class="material-symbols-outlined text-[18px]">create_new_folder</span>
                <span>Tambah Kategori Baru</span>
            </button>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-container-low text-on-surface-variant uppercase text-[10px] font-bold tracking-wider border-b border-surface-container">
                        <tr>
                            <th class="p-4">Kategori Layanan</th>
                            <th class="p-4">Routing Clean URL</th>
                            <th class="p-4">Deskripsi</th>
                            <th class="p-4">Jumlah Aplikasi</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        <?php foreach ($categories as $c): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="p-4 font-bold text-on-surface flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-surface-container text-secondary flex items-center justify-center shrink-0">
                                    <i class="<?= e($c['icon']) ?>"></i>
                                </div>
                                <span><?= e($c['name']) ?></span>
                            </td>
                            <td class="p-4 font-mono text-secondary">
                                <span class="bg-surface-container-low px-2 py-1 rounded">/<?= e($c['slug']) ?>/*</span>
                            </td>
                            <td class="p-4 text-on-surface-variant max-w-xs truncate">
                                <?= e($c['description'] ?: '—') ?>
                            </td>
                            <td class="p-4 font-semibold text-on-surface">
                                <?= $c['approved_count'] ?> aktif <span class="text-on-surface-variant text-[11px] font-normal">(<?= $c['total_count'] ?> total)</span>
                            </td>
                            <td class="p-4 text-right">
                                <form action="<?= url('/admin/categories/' . $c['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Hapus kategori <?= e($c['name']) ?>?');" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                    <button type="submit" class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Add Category -->
<div id="addCatModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest border border-surface-container rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <form action="<?= url('/admin/categories') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                <h3 class="font-bold text-sm text-primary">Tambah Kategori Baru</h3>
                <button type="button" onclick="document.getElementById('addCatModal').classList.add('hidden')" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Nama Kategori</label>
                <input type="text" name="name" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container" placeholder="Contoh: Logistik Pelabuhan" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addCatModal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface hover:bg-surface-container">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary-container text-on-primary text-xs font-bold hover:bg-tertiary-container shadow-xs">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
