<?php
// View: Category Management CRUD (FR 4.3)
require __DIR__ . '/../partials/header.php';
?>

<main class="container" style="padding-top: 24px; padding-bottom: 60px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div>
            <a href="<?= url('/admin/reviews') ?>" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Panel Tata Kelola
            </a>
            <h1 style="font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #fff;">
                Kelola Kategori Aplikasi
            </h1>
            <p style="color: var(--text-secondary); font-size: 14px;">
                Tambah, edit, dan atur taksonomi aplikasi untuk navigasi portal.
            </p>
        </div>

        <button type="button" class="btn btn-primary" onclick="openModal('addCatModal')">
            <i class="fa-solid fa-plus"></i> Tambah Kategori Baru
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Ikon & Nama Kategori</th>
                    <th>Slug URL Routing</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Aplikasi</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 16px; color: <?= e($c['color']) ?>;">
                                <i class="<?= e($c['icon']) ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #fff;"><?= e($c['name']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code style="background: rgba(0,0,0,0.3); padding: 3px 8px; border-radius: 4px; color: var(--ocean-cyan); font-size: 12px;">
                            /<?= e($c['slug']) ?>/*
                        </code>
                    </td>
                    <td>
                        <span style="font-size: 13px; color: var(--text-secondary);"><?= e($c['description'] ?: '—') ?></span>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: #fff;"><?= $c['approved_count'] ?> aktif</span>
                        <span style="font-size: 11px; color: var(--text-muted);"> (<?= $c['total_count'] ?> total)</span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="editCat(<?= htmlspecialchars(json_encode($c)) ?>)">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <form action="<?= url('/admin/categories/' . $c['id'] . '/delete') ?>" method="POST" style="display: inline;" onsubmit="return confirm('Hapus kategori <?= e($c['name']) ?>?');">
                                <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- MODAL ADD CATEGORY -->
<div class="modal-backdrop" id="addCatModal" onclick="if(event.target===this) closeModal('addCatModal')">
    <div class="modal-dialog">
        <form action="<?= url('/admin/categories') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-folder-plus"></i> Tambah Kategori Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('addCatModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Logistik & Bahan Bakar" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Ikon (FontAwesome Class)</label>
                    <input type="text" name="icon" class="form-control" value="fa-solid fa-folder" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Warna Aksen</label>
                    <input type="color" name="color" class="form-control" value="#06b6d4" style="height: 44px; padding: 4px;">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi peruntukan kategori..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('addCatModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT CATEGORY -->
<div class="modal-backdrop" id="editCatModal" onclick="if(event.target===this) closeModal('editCatModal')">
    <div class="modal-dialog">
        <form id="editCatForm" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Edit Kategori</h3>
                <button type="button" class="modal-close" onclick="closeModal('editCatModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" id="editCatName" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Slug URL</label>
                    <input type="text" id="editCatSlug" name="slug" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Ikon (FontAwesome Class)</label>
                    <input type="text" id="editCatIcon" name="icon" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Warna Aksen</label>
                    <input type="color" id="editCatColor" name="color" class="form-control" style="height: 44px; padding: 4px;">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="editCatDesc" name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('editCatModal')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCat(data) {
    document.getElementById('editCatForm').action = window.PORTAL_BASE_URL + '/admin/categories/' + data.id + '/update';
    document.getElementById('editCatName').value = data.name;
    document.getElementById('editCatSlug').value = data.slug;
    document.getElementById('editCatIcon').value = data.icon;
    document.getElementById('editCatColor').value = data.color || '#06b6d4';
    document.getElementById('editCatDesc').value = data.description || '';
    openModal('editCatModal');
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
