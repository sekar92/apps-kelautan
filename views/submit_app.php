<?php
// View: Submit New Application (Refactored to match Stitch pengajuan.html)
require __DIR__ . '/partials/header.php';
?>

<main class="w-full flex-1 bg-surface pt-4">
    <div class="flex flex-col w-full">
        <!-- Ambient Top Header Band (Stitch Standard) -->
        <section class="w-full px-4 sm:px-8 py-8 bg-surface-container-lowest shadow-xs border-b border-surface-container">
            <div class="max-w-[1440px] mx-auto flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="flex flex-col gap-1.5 max-w-3xl">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-tertiary-fixed-variant text-[10px] font-bold uppercase tracking-wider">
                            FR 3.1 • Pendaftaran Modul Baru
                        </span>
                        <span class="text-outline text-xs">•</span>
                        <span class="text-secondary text-xs font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Sandboxing Terenkripsi Kelautan.ID
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-primary tracking-tight">
                        Form Pengajuan Aplikasi Baru
                    </h1>

                    <p class="text-sm text-on-surface-variant leading-relaxed">
                        Daftarkan aplikasi Google Apps Script (GAS) atau web app internal Anda untuk ditinjau oleh Tim IT Kelautan.ID sebelum diterbitkan ke katalog resmi.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?= url('/submissions') ?>" class="px-4 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-bold transition-colors">
                        ← Kembali ke Riwayat Pengajuan
                    </a>
                </div>
            </div>
        </section>

        <!-- Form Area (Stitch 2-Column Split: Form 8 Cols + Sticky Checklist 4 Cols) -->
        <div class="w-full px-4 sm:px-8 py-8">
            <div class="max-w-[1440px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column: Form Fields Section (8 Cols) -->
                <form action="<?= url('/submissions/new') ?>" method="POST" enctype="multipart/form-data" class="lg:col-span-8 flex flex-col gap-6">
                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                    <!-- Module 1: Informasi Dasar -->
                    <div class="p-6 bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 flex flex-col gap-4">
                        <div class="flex items-start justify-between pb-2 border-b border-surface-container">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold flex items-center justify-center">01</span>
                                    <h2 class="font-bold text-base text-primary">Informasi Dasar Aplikasi</h2>
                                </div>
                                <span class="text-xs text-on-surface-variant mt-0.5">Identitas utama aplikasi saat ditampilkan pada katalog portal internal</span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-surface-container text-on-tertiary-fixed-variant uppercase">Wajib Diisi</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <!-- Judul Aplikasi -->
                            <div class="flex flex-col gap-1.5 md:col-span-2">
                                <label class="text-xs font-bold text-on-surface flex items-center justify-between" for="app-title">
                                    <span>Judul Aplikasi Internal <span class="text-error">*</span></span>
                                    <span class="text-[11px] text-outline font-normal">Maks. 80 Karakter</span>
                                </label>
                                <input class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-2 focus:ring-secondary/40 transition-all border border-surface-container/60" 
                                       id="app-title" 
                                       name="title" 
                                       oninput="updateSlugPreview(this)" 
                                       placeholder="cth: POS Kasir Pelelangan Ikan Muara Baru" 
                                       required 
                                       type="text"/>
                            </div>

                            <!-- Kategori Arsitektur -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-on-surface" for="app-category">Kategori Layanan <span class="text-error">*</span></label>
                                <select class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-2 focus:ring-secondary/40 transition-all border border-surface-container/60" 
                                        id="app-category" 
                                        name="category_id" 
                                        required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Slug Routing URL Preview -->
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-on-surface">Target URL Routing Bersih (FR 5.1)</label>
                                <div class="px-3.5 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-mono text-xs truncate border border-surface-container-high">
                                    apps.kelautan.id/<span class="text-secondary font-bold" id="slug-preview-text">slug-aplikasi</span>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="flex flex-col gap-1.5 md:col-span-2">
                                <label class="text-xs font-bold text-on-surface" for="app-desc">Deskripsi & Kegunaan Operasional</label>
                                <textarea class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-2 focus:ring-secondary/40 transition-all border border-surface-container/60" 
                                          id="app-desc" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Jelaskan alur modul, pengguna yang ditargetkan di pelabuhan/kantor dinas..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Module 2: Konfigurasi URL & Sandboxing -->
                    <div class="p-6 bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 flex flex-col gap-4">
                        <div class="flex items-start justify-between pb-2 border-b border-surface-container">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold flex items-center justify-center">02</span>
                                    <h2 class="font-bold text-base text-primary">Konfigurasi URL & Sandboxing</h2>
                                </div>
                                <span class="text-xs text-on-surface-variant mt-0.5">Tautan deployment Google Apps Script / SPA yang akan di-embed ke dalam wrapper</span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-surface-container text-on-tertiary-fixed-variant uppercase">NFR 1 Compliant</span>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface" for="app-url">
                                URL Deployment Web App <span class="text-error">*</span>
                            </label>
                            <input class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs focus:outline-none focus:ring-2 focus:ring-secondary/40 transition-all border border-surface-container/60 font-mono" 
                                   id="app-url" 
                                   name="app_url" 
                                   placeholder="https://script.google.com/macros/s/... atau https://app.kelautan.id" 
                                   required 
                                   type="url"/>
                            <span class="text-[11px] text-on-surface-variant">
                                Catatan: URL akan di-embed dengan atribut <code>sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-downloads"</code>.
                            </span>
                        </div>
                    </div>

                    <!-- Module 3: Ikon & Tampilan Katalog -->
                    <div class="p-6 bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 flex flex-col gap-4">
                        <div class="flex items-start justify-between pb-2 border-b border-surface-container">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold flex items-center justify-center">03</span>
                                    <h2 class="font-bold text-base text-primary">Ikon & Representasi Visual</h2>
                                </div>
                                <span class="text-xs text-on-surface-variant mt-0.5">Pilih ikon instrumen navigasi maritim untuk kartu katalog</span>
                            </div>
                        </div>

                        <input type="hidden" name="icon_type" value="preset">
                        <input type="hidden" name="icon_value" id="selectedIconInput" value="fa-solid fa-cube">

                        <div class="grid grid-cols-6 sm:grid-cols-9 gap-2">
                            <?php 
                            $icons = [
                                'fa-solid fa-cube', 'fa-solid fa-cash-register', 'fa-solid fa-book-bookmark',
                                'fa-solid fa-water', 'fa-solid fa-id-card-clip', 'fa-solid fa-calculator',
                                'fa-solid fa-compass', 'fa-solid fa-network-wired', 'fa-solid fa-ship',
                                'fa-solid fa-chart-pie', 'fa-solid fa-anchor', 'fa-solid fa-fish',
                                'fa-solid fa-clipboard-check', 'fa-solid fa-boxes-stacked', 'fa-solid fa-temperature-arrow-down',
                                'fa-brands fa-google', 'fa-solid fa-file-contract', 'fa-solid fa-scale-balanced'
                            ];
                            foreach ($icons as $idx => $ico):
                            ?>
                            <button type="button" 
                                    onclick="pickIcon('<?= $ico ?>', this)" 
                                    class="h-11 rounded-xl flex items-center justify-center text-lg transition-all <?= $idx === 0 ? 'bg-primary-container text-on-primary ring-2 ring-secondary' : 'bg-surface-container-low text-secondary hover:bg-surface-container' ?>">
                                <i class="<?= $ico ?>"></i>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between pt-2">
                        <button type="submit" name="save_as_draft" value="1" class="px-5 py-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-bold transition-colors">
                            Simpan sebagai Draf
                        </button>

                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary text-xs font-bold transition-colors shadow-xs flex items-center gap-2">
                            <span>Ajukan untuk Ditinjau Tim IT →</span>
                        </button>
                    </div>
                </form>

                <!-- Right Column: Guidelines & Protocols (4 Cols) -->
                <aside class="lg:col-span-4 flex flex-col gap-5 sticky top-24">
                    <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-xs border border-surface-container/60 flex flex-col gap-3">
                        <div class="flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined text-[22px] text-secondary">security</span>
                            <h3 class="font-bold text-sm">Protokol Peninjauan Admin</h3>
                        </div>
                        <ul class="text-xs text-on-surface-variant space-y-2 leading-relaxed">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-secondary text-[16px] shrink-0 mt-0.5">check_circle</span>
                                <span>Aplikasi akan diuji melalui <strong>Live Sandbox iFrame</strong> oleh evaluator.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-secondary text-[16px] shrink-0 mt-0.5">check_circle</span>
                                <span>URL GAS wajib di-deploy dengan akses <em>"Anyone with account"</em> atau <em>"Anyone"</em>.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-secondary text-[16px] shrink-0 mt-0.5">check_circle</span>
                                <span>Admin berhak meminta revisi atau menolak jika ditemukan celah keamanan.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-surface-container-low rounded-2xl p-5 border border-surface-container/60 text-xs text-on-surface-variant space-y-1.5">
                        <div class="font-bold text-on-surface">Estimasi Waktu Verifikasi</div>
                        <div>Sesuai SLA internal, aplikasi Anda akan diverifikasi dalam waktu 1-2 hari kerja.</div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</main>

<script>
function updateSlugPreview(input) {
    const preview = document.getElementById('slug-preview-text');
    const cleanVal = input.value.trim().toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/--+/g, '-');
    preview.innerText = cleanVal.length > 0 ? cleanVal : 'slug-aplikasi';
}

function pickIcon(ico, btn) {
    document.getElementById('selectedIconInput').value = ico;
    btn.parentElement.querySelectorAll('button').forEach(b => {
        b.className = 'h-11 rounded-xl flex items-center justify-center text-lg transition-all bg-surface-container-low text-secondary hover:bg-surface-container';
    });
    btn.className = 'h-11 rounded-xl flex items-center justify-center text-lg transition-all bg-primary-container text-on-primary ring-2 ring-secondary';
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
