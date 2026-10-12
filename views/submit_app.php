<?php
// View: Submit New Application (FR 3.1)
require __DIR__ . '/partials/header.php';
?>

<main class="container" style="padding-top: 24px; padding-bottom: 60px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 28px;">
            <a href="<?= url('/submissions') ?>" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pengajuan
            </a>
            <h1 style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 6px;">
                Ajukan Aplikasi Baru
            </h1>
            <p style="color: var(--text-secondary); font-size: 14px;">
                Daftarkan aplikasi Google Apps Script (GAS) atau Single Page Application (SPA) ke portal agar dapat ditinjau oleh Admin.
            </p>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-card);">
            <form action="<?= url('/submissions/new') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                <!-- Judul Aplikasi -->
                <div class="form-group">
                    <label class="form-label" for="appTitle">Nama / Judul Aplikasi <span style="color: var(--coral-red);">*</span></label>
                    <input type="text" id="appTitle" name="title" class="form-control" placeholder="Contoh: POS Kasir Pelelangan Ikan Muara Baru" required>
                    <div class="form-hint">Nama yang jelas dan mudah dipahami oleh pengguna di pelabuhan/kantor dinas.</div>
                </div>

                <!-- Kategori & Tipe Aplikasi -->
                <div class="form-group">
                    <label class="form-label" for="categorySelect">Kategori Layanan <span style="color: var(--coral-red);">*</span></label>
                    <select id="categorySelect" name="category_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- URL Aplikasi (GAS / SPA) -->
                <div class="form-group">
                    <label class="form-label" for="appUrl">URL Aplikasi (Google Apps Script / Web App) <span style="color: var(--coral-red);">*</span></label>
                    <input type="url" id="appUrl" name="app_url" class="form-control" placeholder="https://script.google.com/macros/s/... atau https://app.domain.id" required>
                    <div class="form-hint">
                        Pastikan aplikasi mengizinkan embedding via iFrame atau menggunakan deployment Web App Google Apps Script.
                    </div>
                </div>

                <!-- Deskripsi Aplikasi -->
                <div class="form-group">
                    <label class="form-label" for="appDesc">Deskripsi & Kegunaan Aplikasi</label>
                    <textarea id="appDesc" name="description" class="form-control" rows="4" placeholder="Jelaskan fungsi utama aplikasi, alur kerja, dan siapa pengguna yang ditargetkan..."></textarea>
                </div>

                <!-- Ikon Aplikasi -->
                <div class="form-group">
                    <label class="form-label">Pilih Ikon Aplikasi</label>
                    <div style="display: flex; gap: 16px; margin-bottom: 12px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff; font-size: 13px;">
                            <input type="radio" name="icon_type" value="preset" checked onchange="toggleIconType('preset')"> Gunakan Ikon Preset
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff; font-size: 13px;">
                            <input type="radio" name="icon_type" value="upload" onchange="toggleIconType('upload')"> Unggah Berkas Gambar Ikon
                        </label>
                    </div>

                    <!-- Preset Icons Grid -->
                    <div id="presetIconBox">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(48px, 1fr)); gap: 10px; max-height: 160px; overflow-y: auto; padding: 10px; background: var(--bg-surface); border: 1px solid var(--border-glass); border-radius: var(--radius-md);">
                            <?php 
                            $presetIcons = [
                                'fa-solid fa-cube', 'fa-solid fa-cash-register', 'fa-solid fa-book-bookmark',
                                'fa-solid fa-water', 'fa-solid fa-id-card-clip', 'fa-solid fa-calculator',
                                'fa-solid fa-compass', 'fa-solid fa-network-wired', 'fa-solid fa-ship',
                                'fa-solid fa-chart-pie', 'fa-solid fa-anchor', 'fa-solid fa-fish',
                                'fa-solid fa-clipboard-check', 'fa-solid fa-boxes-stacked', 'fa-solid fa-temperature-arrow-down',
                                'fa-brands fa-google', 'fa-solid fa-file-contract', 'fa-solid fa-scale-balanced'
                            ];
                            foreach ($presetIcons as $idx => $ico):
                            ?>
                            <label style="display: flex; align-items: center; justify-content: center; height: 44px; border: 1px solid var(--border-glass); border-radius: 8px; cursor: pointer; font-size: 18px; color: var(--ocean-cyan); transition: var(--transition);">
                                <input type="radio" name="icon_value" value="<?= $ico ?>" <?= $idx === 0 ? 'checked' : '' ?> style="display: none;" onchange="updateSelectedIcon(this)">
                                <i class="<?= $ico ?>"></i>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- File Upload Input -->
                    <div id="uploadIconBox" style="display: none;">
                        <input type="file" name="icon_file" accept="image/png, image/jpeg, image/svg+xml, image/webp" class="form-control">
                        <div class="form-hint">Format yang didukung: PNG, JPG, SVG, WebP (Rasio 1:1, Maksimal 2MB).</div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px; padding-top: 20px; border-top: 1px solid var(--border-glass); flex-wrap: wrap; gap: 12px;">
                    <button type="submit" name="save_as_draft" value="1" class="btn btn-secondary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan sebagai Draf
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Ajukan untuk Ditinjau Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function toggleIconType(type) {
    if (type === 'preset') {
        document.getElementById('presetIconBox').style.display = 'block';
        document.getElementById('uploadIconBox').style.display = 'none';
    } else {
        document.getElementById('presetIconBox').style.display = 'none';
        document.getElementById('uploadIconBox').style.display = 'block';
    }
}

function updateSelectedIcon(radio) {
    document.querySelectorAll('#presetIconBox label').forEach(lbl => {
        lbl.style.borderColor = 'var(--border-glass)';
        lbl.style.background = 'transparent';
    });
    radio.parentElement.style.borderColor = 'var(--ocean-cyan)';
    radio.parentElement.style.background = 'rgba(6, 182, 212, 0.15)';
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
