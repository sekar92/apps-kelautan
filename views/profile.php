<?php
// View: User Profile (Refactored to match Stitch Standard)
require __DIR__ . '/partials/header.php';
?>

<main class="w-full flex-1 bg-surface pt-4">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 py-8">
        <div class="max-w-2xl mx-auto flex flex-col gap-6">
            <div>
                <a href="<?= url('/') ?>" class="inline-flex items-center gap-1.5 text-xs text-secondary font-semibold hover:underline mb-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Katalog</span>
                </a>
                <h1 class="text-2xl font-bold text-primary tracking-tight">Profil & Keamanan Akun</h1>
                <p class="text-xs text-on-surface-variant">Kelola identitas dinas dan perbarui kata sandi Anda (FR 1.3).</p>
            </div>

            <!-- Profile Info Form -->
            <div class="bg-surface-container-lowest p-6 sm:p-8 rounded-2xl shadow-xs border border-surface-container/60">
                <div class="flex items-center gap-2 pb-3 border-b border-surface-container mb-4">
                    <span class="material-symbols-outlined text-secondary text-[22px]">badge</span>
                    <h2 class="font-bold text-sm text-primary">Data Identitas Pegawai</h2>
                </div>

                <form action="<?= url('/profile') ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1">Alamat Email Dinas</label>
                        <input type="email" value="<?= e($currentUser['email']) ?>" disabled class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container text-on-surface-variant text-xs cursor-not-allowed border border-surface-container font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="<?= e($currentUser['name']) ?>" required class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs border border-surface-container focus:ring-2 focus:ring-secondary/40 focus:outline-none font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1">Instansi / Unit Kerja</label>
                        <input type="text" name="department" value="<?= e($currentUser['department'] ?? 'Dinas Kelautan & Perikanan') ?>" required class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs border border-surface-container focus:ring-2 focus:ring-secondary/40 focus:outline-none font-medium">
                    </div>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary text-xs font-bold transition-colors shadow-xs">
                        Simpan Perubahan Profil
                    </button>
                </form>
            </div>

            <!-- Password Form -->
            <div class="bg-surface-container-lowest p-6 sm:p-8 rounded-2xl shadow-xs border border-surface-container/60">
                <div class="flex items-center gap-2 pb-3 border-b border-surface-container mb-4">
                    <span class="material-symbols-outlined text-secondary text-[22px]">key</span>
                    <h2 class="font-bold text-sm text-primary">Ganti Kata Sandi</h2>
                </div>

                <form action="<?= url('/profile/password') ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" required class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs border border-surface-container focus:ring-2 focus:ring-secondary/40 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1">Kata Sandi Baru</label>
                        <input type="password" name="new_password" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs border border-surface-container focus:ring-2 focus:ring-secondary/40 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="confirm_password" required minlength="6" class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low text-on-surface text-xs border border-surface-container focus:ring-2 focus:ring-secondary/40 focus:outline-none">
                    </div>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-secondary text-on-secondary hover:bg-on-secondary-container text-xs font-bold transition-colors shadow-xs">
                        Perbarui Kata Sandi
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
