<?php
// View: User Management (FR 4.4)
require __DIR__ . '/../partials/header.php';
?>

<main class="container" style="padding-top: 24px; padding-bottom: 60px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <a href="<?= url('/admin/reviews') ?>" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Panel Tata Kelola
            </a>
            <h1 style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #fff;">
                Manajemen Pengguna & Otorisasi
            </h1>
            <p style="color: var(--text-secondary); font-size: 14px;">
                Kelola hak akses role (Admin/User), reset password, dan status akun pengguna.
            </p>
        </div>

        <button type="button" class="btn btn-primary" onclick="openModal('addUserModal')">
            <i class="fa-solid fa-user-plus"></i> Tambah Pengguna Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama & Email</th>
                    <th>Instansi / Divisi</th>
                    <th>Peran (Role)</th>
                    <th>Status Akun</th>
                    <th>Aplikasi Diajukan</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #3b82f6); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff;">
                                <?= strtoupper(substr($u['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #fff;"><?= e($u['name']) ?></div>
                                <div style="font-size: 12px; color: var(--text-muted);"><?= e($u['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size: 13px; color: var(--text-secondary);"><?= e($u['department'] ?: 'Dinas Kelautan') ?></span>
                    </td>
                    <td>
                        <span class="badge" style="background: <?= $u['role'] === 'admin' ? 'rgba(6, 182, 212, 0.2)' : 'rgba(255, 255, 255, 0.08)' ?>; color: <?= $u['role'] === 'admin' ? '#06b6d4' : '#94a3b8' ?>; font-weight: 700; padding: 4px 10px; border-radius: 99px; font-size: 11px;">
                            <?= strtoupper(e($u['role'])) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($u['is_active']): ?>
                            <span style="color: #10b981; font-weight: 600; font-size: 12px;"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                        <?php else: ?>
                            <span style="color: #ef4444; font-weight: 600; font-size: 12px;"><i class="fa-solid fa-circle-xmark"></i> Ditangguhkan</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: #fff;"><?= $u['app_count'] ?> aplikasi</span>
                    </td>
                    <td style="text-align: right;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                            <i class="fa-solid fa-user-gear"></i> Kelola
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- MODAL ADD USER -->
<div class="modal-backdrop" id="addUserModal" onclick="if(event.target===this) closeModal('addUserModal')">
    <div class="modal-dialog">
        <form action="<?= url('/admin/users') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-user-plus"></i> Tambah Pengguna Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Ahmad Baharuddin" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Email Dinas</label>
                    <input type="email" name="email" class="form-control" placeholder="ahmad@kelautan.id" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Instansi / Unit Kerja</label>
                    <input type="text" name="department" class="form-control" placeholder="Pelabuhan Perikanan Samudera" value="Dinas Kelautan & Perikanan">
                </div>
                <div class="form-group">
                    <label class="form-label">Peran (Role)</label>
                    <select name="role" class="form-control">
                        <option value="user">User Biasa / Kontributor</option>
                        <option value="admin">Administrator Sistem</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kata Sandi Awal</label>
                    <input type="text" name="password" class="form-control" value="user123" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('addUserModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Buat Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT USER -->
<div class="modal-backdrop" id="editUserModal" onclick="if(event.target===this) closeModal('editUserModal')">
    <div class="modal-dialog">
        <form id="editUserForm" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-user-gear"></i> Kelola Hak Akses Pengguna</h3>
                <button type="button" class="modal-close" onclick="closeModal('editUserModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="background: rgba(0,0,0,0.25); border: 1px solid var(--border-glass); border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                    <div style="font-weight: 700; color: #fff;" id="editUserNameDisplay"></div>
                    <div style="font-size: 12px; color: var(--text-muted);" id="editUserEmailDisplay"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Peran (Role)</label>
                    <select id="editUserRole" name="role" class="form-control">
                        <option value="user">User Biasa</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Akun</label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff; font-size: 13px;">
                        <input type="checkbox" id="editUserActive" name="is_active" value="1">
                        <span>Aktif (Pengguna dapat masuk ke portal)</span>
                    </label>
                </div>

                <div class="form-group" style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-glass);">
                    <label class="form-label">Reset Kata Sandi (Opsional)</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah sandi">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('editUserModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(data) {
    document.getElementById('editUserForm').action = window.PORTAL_BASE_URL + '/admin/users/' + data.id + '/update';
    document.getElementById('editUserNameDisplay').innerText = data.name;
    document.getElementById('editUserEmailDisplay').innerText = data.email;
    document.getElementById('editUserRole').value = data.role;
    document.getElementById('editUserActive').checked = (parseInt(data.is_active) === 1);
    openModal('editUserModal');
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
