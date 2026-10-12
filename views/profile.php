<?php
// View: User Profile (FR 1.3)
require __DIR__ . '/partials/header.php';
?>

<main class="container" style="padding-top: 24px; padding-bottom: 60px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 6px;">
            Profil & Pengaturan Akun
        </h1>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 28px;">
            Kelola data diri, instansi, dan perbarui kata sandi untuk keamanan akun Anda (FR 1.3).
        </p>

        <!-- Informasi Profil -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-card); margin-bottom: 24px;">
            <h2 style="font-family: var(--font-heading); font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-id-badge" style="color: var(--ocean-cyan);"></i> Data Diri & Instansi
            </h2>

            <form action="<?= url('/profile') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                <div class="form-group">
                    <label class="form-label">Alamat Email (Akun)</label>
                    <input type="email" class="form-control" value="<?= e($currentUser['email']) ?>" disabled style="opacity: 0.7; cursor: not-allowed;">
                    <div class="form-hint">Email akun dikelola terpusat oleh Administrator IT.</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="userName">Nama Lengkap</label>
                    <input type="text" id="userName" name="name" class="form-control" value="<?= e($currentUser['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="userDept">Unit Kerja / Instansi</label>
                    <input type="text" id="userDept" name="department" class="form-control" value="<?= e($currentUser['department'] ?? 'Kelautan & Perikanan') ?>" required>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 8px;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Profil
                </button>
            </form>
        </div>

        <!-- Ubah Kata Sandi -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-card);">
            <h2 style="font-family: var(--font-heading); font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-key" style="color: var(--amber-gold);"></i> Ganti Kata Sandi
            </h2>

            <form action="<?= url('/profile/password') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">

                <div class="form-group">
                    <label class="form-label" for="curPass">Kata Sandi Saat Ini</label>
                    <input type="password" id="curPass" name="current_password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="newPass">Kata Sandi Baru</label>
                    <input type="password" id="newPass" name="new_password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                </div>

                <div class="form-group">
                    <label class="form-label" for="confPass">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="confPass" name="confirm_password" class="form-control" placeholder="Ulangi kata sandi baru" required minlength="6">
                </div>

                <button type="submit" class="btn btn-warning" style="margin-top: 8px;">
                    <i class="fa-solid fa-lock"></i> Perbarui Kata Sandi
                </button>
            </form>
        </div>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
