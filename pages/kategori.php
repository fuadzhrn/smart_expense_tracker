<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kategori Pengeluaran';
$userId    = (int) $_SESSION['user_id'];

$flashSuccess = flash('flash_success');
$flashError   = flash('flash_error');

// --- Data yang sedang diedit (jika ada parameter ?edit=ID milik user ini) ---
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt   = mysqli_prepare($conn, 'SELECT id, name, description FROM expense_categories WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $editId, $userId);
    mysqli_stmt_execute($stmt);
    $editData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

// --- Daftar kategori + jumlah & total pengeluaran per kategori ---
$stmt = mysqli_prepare($conn, '
    SELECT ec.id, ec.name, ec.description,
           COUNT(e.id) AS jumlah_pengeluaran,
           COALESCE(SUM(e.amount), 0) AS total_pengeluaran
    FROM expense_categories ec
    LEFT JOIN expenses e ON e.category_id = ec.id AND e.user_id = ec.user_id
    WHERE ec.user_id = ?
    GROUP BY ec.id, ec.name, ec.description
    ORDER BY ec.name ASC
');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result       = mysqli_stmt_get_result($stmt);
$kategoriList = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

$totalKategori = count($kategoriList);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-intro">
    <p class="page-welcome">Kelola kategori agar data pengeluaran lebih rapi.</p>
</div>

<?php if ($flashSuccess): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess); ?></div>
<?php endif; ?>
<?php if ($flashError): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($flashError); ?></div>
<?php endif; ?>

<section class="summary-cards">
    <div class="card summary-card">
        <div class="summary-icon icon-warning"><i class="fa-solid fa-tags"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Kategori</span>
            <span class="summary-value"><?php echo $totalKategori; ?></span>
        </div>
    </div>
</section>

<?php if (!$editData): ?>
<div class="toolbar-actions">
    <button type="button" class="btn btn-primary" onclick="toggleFormCard('formKategoriCard')">
        <i class="fa-solid fa-plus"></i> Tambah Kategori
    </button>
</div>
<?php endif; ?>

<section class="card form-card" id="formKategoriCard" <?php echo $editData ? '' : 'style="display:none;"'; ?>>
    <div class="card-header">
        <h2><?php echo $editData ? 'Edit Kategori' : 'Tambah Kategori'; ?></h2>
        <?php if ($editData): ?>
            <a href="kategori.php" class="btn btn-secondary">Batal</a>
        <?php endif; ?>
    </div>

    <?php if ($editData): ?>
        <form action="../proses/edit_kategori.php" method="POST" class="grid-form grid-form-2">
            <input type="hidden" name="id" value="<?php echo (int) $editData['id']; ?>">
            <div class="form-group">
                <label for="name">Nama Kategori</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Makanan, Transportasi, Pendidikan" value="<?php echo htmlspecialchars($editData['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" rows="2" placeholder="Deskripsi singkat (opsional)"><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    <?php else: ?>
        <form action="../proses/tambah_kategori.php" method="POST" class="grid-form grid-form-2">
            <div class="form-group">
                <label for="name">Nama Kategori</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Makanan, Transportasi, Pendidikan" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" rows="2" placeholder="Deskripsi singkat (opsional)"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    <?php endif; ?>
</section>

<section class="card table-card">
    <div class="card-header">
        <h2>Daftar Kategori</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Pengeluaran</th>
                    <th>Total Pengeluaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kategoriList)): ?>
                    <tr>
                        <td colspan="6" class="empty-state">Belum ada kategori pengeluaran.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($kategoriList as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description'] !== null && $row['description'] !== '' ? $row['description'] : '-'); ?></td>
                            <td><?php echo (int) $row['jumlah_pengeluaran']; ?></td>
                            <td><?php echo formatRupiah($row['total_pengeluaran']); ?></td>
                            <td>
                                <a href="kategori.php?edit=<?php echo (int) $row['id']; ?>#formKategoriCard" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                <a href="../proses/hapus_kategori.php?id=<?php echo (int) $row['id']; ?>" class="btn-icon btn-delete" title="Hapus" onclick="return confirmDelete('Yakin ingin menghapus kategori ini?')"><i class="fa-solid fa-trash-can"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
