<?php
// View: Edit / Revise Application (FR 3.3)
require __DIR__ . '/partials/header.php';
?>

<main class="container" style="padding-top: 24px; padding-bottom: 60px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 28px;">
            <a href="<?= url('/submissions') ?>" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pengajuan
            </a>
            <h1 style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 6px;">
                Perbarui / Revisi Aplikasi
            </h1>
            <p style="color: var(--text-secondary); font-size: 14px;">
                Lakukan perbaikan data atau URL aplikasi sesuai instruksi dan ajukan kembali untuk verifikasi.
            </p>
        </div>

        <!-- Admin Feedback Banner if Revision or Rejected -->
        <?php if (!empty($app['review_notes'])): ?>
        <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: var(--radius-md); padding: 18px 20px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; color: #f59e0b; font-weight: 700; margin-bottom: 6px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Catatan Evaluasi dari Administrator
            </div>
            <div style="color: #fde68a; font-size: 14px; line-height: 1.5;">
                <?= nl2br(e($app['review_notes'])) ?>
            </div>
            <div style="font-size: 11px; color: #d97706; margin-top: 8px;">
                Status Saat Ini: <strong><?= strtoupper(e($app['status'])) ?></strong> &bull; Silakan simpan dan kirim ulang setelah perbaikan selesai.
            </div>
        </div>
        <?php endif; ?>

        <div style="background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-card);">
            <form action="<?= url('/submissions/edit/' . $app['id']) ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                <!-- Judul Aplikasi -->
                <div class="form-group">
                    <label class="form-label" for="appTitle">Nama / Judul Aplikasi <span style="color: var(--coral-red);">*</span></label>
                    <input type="text" id="appTitle" name="title" class="form-control" value="<?= e($app['title']) ?>" required>
                </div>

                <!-- Kategori -->
                <div class="form-group">
                    <label class="form-label" for="categorySelect">Kategori Layanan <span style="color: var(--coral-red);">*</span></label>
                    <select id="categorySelect" name="category_id" class="form-control" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $app['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- URL Aplikasi -->
                <div class="form-group">
                    <label class="form-label" for="appUrl">URL Aplikasi (GAS / Web App) <span style="color: var(--coral-red);">*</span></label>
                    <input type="url" id="appUrl" name="app_url" class="form-control" value="<?= e($app['app_url']) ?>" required>
                </div>

                <!-- Deskripsi -->
                <div class="form-group">
                    <label class="form-label" for="appDesc">Deskripsi & Kegunaan Aplikasi</label>
                    <textarea id="appDesc" name="description" class="form-control" rows="4"><?= e($app['description']) ?></textarea>
                </div>

                <!-- Ikon -->
                <div class="form-group">
                    <label class="form-label">Ikon Aplikasi</label>
                    <input type="hidden" name="icon_type" value="preset">
                    <input type="hidden" name="icon_value" value="<?= e($app['icon_value']) ?>" id="selectedIconValue">
                    
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 12px;">
                        <div style="width: 44px; height: 44px; border-radius: 8px; background: rgba(6, 182, 212, 0.15); display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--ocean-cyan);" id="currentIconPreview">
                            <i class="<?= e($app['icon_value']) ?>"></i>
                        </div>
                        <span style="font-size: 13px; color: var(--text-secondary);">Ikon saat ini: <code><?= e($app['icon_value']) ?></code></span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(44px, 1fr)); gap: 8px; max-height: 140px; overflow-y: auto; padding: 10px; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md);">
                        <?php 
                        $presetIcons = [
                            'fa-solid fa-cube', 'fa-solid fa-cash-register', 'fa-solid fa-book-bookmark',
                            'fa-solid fa-water', 'fa-solid fa-id-card-clip', 'fa-solid fa-calculator',
                            'fa-solid fa-compass', 'fa-solid fa-network-wired', 'fa-solid fa-ship',
                            'fa-solid fa-chart-pie', 'fa-solid fa-anchor', 'fa-solid fa-fish'
                        ];
                        foreach ($presetIcons as $ico):
                        ?>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="chooseIcon('<?= $ico ?>')" style="padding: 10px; font-size: 16px;">
                            <i class="<?= $ico ?>"></i>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px; padding-top: 20px; border-top: 1px solid var(--border-glass); flex-wrap: wrap; gap: 12px;">
                    <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Draf
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Ulang untuk Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function chooseIcon(ico) {
    document.getElementById('selectedIconValue').value = ico;
    document.getElementById('currentIconPreview').innerHTML = `<i class="${ico}"></i>`;
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
