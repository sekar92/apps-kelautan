<?php
// View: User Management (Refactored to match Stitch Standard)
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
                <h1 class="text-2xl font-bold text-primary tracking-tight">Manajemen Pengguna & Otorisasi</h1>
                <p class="text-xs text-on-surface-variant">Kelola akun pegawai, role administrator, dan status keaktifan (FR 4.4).</p>
            </div>

            <button type="button" onclick="document.getElementById('addUserModalStitch').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-primary-container hover:bg-tertiary-container text-on-primary text-xs font-bold transition-colors shadow-xs flex items-center gap-2 self-start sm:self-auto">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl shadow-xs border border-surface-container/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-container-low text-on-surface-variant uppercase text-[10px] font-bold tracking-wider border-b border-surface-container">
                        <tr>
                            <th class="p-4">Nama & Email</th>
                            <th class="p-4">Instansi / Unit Kerja</th>
                            <th class="p-4">Peran (Role)</th>
                            <th class="p-4">Status Akun</th>
                            <th class="p-4">Aplikasi Diajukan</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary font-bold text-xs flex items-center justify-center shrink-0">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-on-surface"><?= e($u['name']) ?></div>
                                        <div class="text-[11px] text-on-surface-variant font-mono"><?= e($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-on-surface font-medium"><?= e($u['department'] ?: 'Dinas Kelautan') ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $u['role'] === 'admin' ? 'bg-secondary/15 text-secondary' : 'bg-surface-container text-on-surface-variant' ?>">
                                    <?= strtoupper(e($u['role'])) ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <?php if ($u['is_active']): ?>
                                    <span class="text-secondary font-semibold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="text-error font-semibold flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-error"></span> Ditangguhkan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-semibold text-on-surface"><?= $u['app_count'] ?> aplikasi</td>
                            <td class="p-4 text-right">
                                <button type="button" onclick="editUserStitch(<?= htmlspecialchars(json_encode($u)) ?>)" class="px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold text-xs transition-colors">
                                    Kelola Akun
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Add User -->
<div id="addUserModalStitch" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest border border-surface-container rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <form action="<?= url('/admin/users') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                <h3 class="font-bold text-sm text-primary">Tambah Pengguna Baru</h3>
                <button type="button" onclick="document.getElementById('addUserModalStitch').classList.add('hidden')" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Email Dinas</label>
                <input type="email" name="email" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Unit Kerja</label>
                <input type="text" name="department" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container" value="Dinas Kelautan & Perikanan">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Peran (Role)</label>
                <select name="role" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container">
                    <option value="user">User Biasa / Kontributor</option>
                    <option value="admin">Administrator Sistem</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Kata Sandi Awal</label>
                <input type="text" name="password" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container" value="user123" required>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('addUserModalStitch').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface hover:bg-surface-container">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary-container text-on-primary text-xs font-bold hover:bg-tertiary-container shadow-xs">Buat Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="editUserModalStitch" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest border border-surface-container rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <form id="editUserFormStitch" action="" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                <h3 class="font-bold text-sm text-primary">Kelola Hak Akses Pengguna</h3>
                <button type="button" onclick="document.getElementById('editUserModalStitch').classList.add('hidden')" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="bg-surface-container-low p-3 rounded-xl border border-surface-container">
                <div class="font-bold text-xs text-on-surface" id="editUserNameTxt"></div>
                <div class="text-[11px] text-on-surface-variant font-mono" id="editUserEmailTxt"></div>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Peran (Role)</label>
                <select name="role" id="editUserRoleSelect" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container">
                    <option value="user">User Biasa</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="editUserActiveCheck" value="1" class="rounded">
                <label for="editUserActiveCheck" class="text-xs font-semibold text-on-surface">Akun Aktif (Dapat masuk ke portal)</label>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Reset Kata Sandi (Opsional)</label>
                <input type="password" name="new_password" class="w-full px-3 py-2 rounded-xl bg-surface-container-low text-xs border border-surface-container" placeholder="Kosongkan jika tidak diubah">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editUserModalStitch').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-on-surface hover:bg-surface-container">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary-container text-on-primary text-xs font-bold hover:bg-tertiary-container shadow-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUserStitch(u) {
    document.getElementById('editUserFormStitch').action = window.PORTAL_BASE_URL + '/admin/users/' + u.id + '/update';
    document.getElementById('editUserNameTxt').innerText = u.name;
    document.getElementById('editUserEmailTxt').innerText = u.email;
    document.getElementById('editUserRoleSelect').value = u.role;
    document.getElementById('editUserActiveCheck').checked = (parseInt(u.is_active) === 1);
    document.getElementById('editUserModalStitch').classList.remove('hidden');
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
