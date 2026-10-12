<?php
// View: Edit / Revise Application (Refactored to match Stitch Standard)
require __DIR__ . '/partials/header.php';
?>

<main class="w-full flex-1 bg-surface pt-4">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 py-8">
        <div class="max-w-3xl mx-auto flex flex-col gap-6">
            <div>
                <a href="<?= url('/submissions') ?>" class="inline-flex items-center gap-1.5 text-xs text-secondary font-semibold hover:underline mb-2">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Riwayat Pengajuan</span>
                </a>
                <h1 class="text-2xl font-bold text-primary tracking-tight">Perbarui / Revisi Aplikasi</h1>
                <p class="text-xs text-on-surface-variant">Lakukan perbaikan data atau URL aplikasi sesuai instruksi evaluator.</p>
            </div>

            <!-- Admin Feedback Banner if Revision or Rejected -->
            <?php if (!empty($app['review_notes'])): ?>
            <div class="p-4 rounded-2xl bg-secondary-container/30 border border-secondary/40 text-on-surface">
                <div class="flex items-center gap-2 text-secondary font-bold text-xs mb-1.5">
                    <span class="material-symbols-outlined text-[20px]">feedback</span>
                    <span>Catatan Evaluasi dari Administrator:</span>
                </div>
                <div class="text-xs text-on-surface-variant leading-relaxed"><?= nl2br(e($app['review_notes'])) ?></div>
                <div class="mt-2 text-[10px] font-bold text-secondary uppercase">
                    Status: <?= strtoupper(e($app['status'])) ?> &bull; Silakan simpan dan kirim ulang setelah perbaikan.
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-surface-container-lowest p-6 sm:p-8 rounded-2xl shadow-xs border border-surface-container/60">
                <form action="<?= url('/submissions/edit/' . $app['id']) ?>" method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1.5">Judul Aplikasi <span class="text-error">*</span></label>
                        <input type="text" name="title" value="<?= e($app['title']) ?>" class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:ring-2 focus:ring-secondary/40 focus:outline-none border border-surface-container/60" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1.5">Kategori Layanan <span class="text-error">*</span></label>
                        <select name="category_id" class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:ring-2 focus:ring-secondary/40 focus:outline-none border border-surface-container/60" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $app['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1.5">URL Aplikasi (GAS / Web App) <span class="text-error">*</span></label>
                        <input type="url" name="app_url" value="<?= e($app['app_url']) ?>" class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:ring-2 focus:ring-secondary/40 focus:outline-none border border-surface-container/60 font-mono" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1.5">Deskripsi & Kegunaan</label>
                        <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:ring-2 focus:ring-secondary/40 focus:outline-none border border-surface-container/60"><?= e($app['description']) ?></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-surface-container">
                        <button type="submit" name="save_as_draft" value="1" class="px-4 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-bold transition-colors">
                            Simpan Draf
                        </button>
                        <button type="submit" class="px-6 py-2 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary text-xs font-bold transition-colors shadow-xs">
                            Kirim Ulang untuk Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
