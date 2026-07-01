<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Data Pemasukan';
$userId    = (int) $_SESSION['user_id'];

$flashSuccess = flash('flash_success');
$flashError   = flash('flash_error');

// --- Data yang sedang diedit (jika ada parameter ?edit=ID milik user ini) ---
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt   = mysqli_prepare($conn, 'SELECT id, income_date, source, amount, description FROM incomes WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $editId, $userId);
    mysqli_stmt_execute($stmt);
    $editData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

// --- Total pemasukan ---
$stmt = mysqli_prepare($conn, 'SELECT COALESCE(SUM(amount), 0) AS total FROM incomes WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$totalPemasukan = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

// --- Daftar pemasukan, terbaru dulu ---
$stmt = mysqli_prepare($conn, 'SELECT id, income_date, source, amount, description FROM incomes WHERE user_id = ? ORDER BY income_date DESC, id DESC');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result        = mysqli_stmt_get_result($stmt);
$pemasukanList = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-intro">
    <p class="page-welcome">Kelola semua data pemasukan keuangan kamu.</p>
</div>

<?php if ($flashSuccess): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess); ?></div>
<?php endif; ?>
<?php if ($flashError): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($flashError); ?></div>
<?php endif; ?>

<section class="summary-cards">
    <div class="card summary-card">
        <div class="summary-icon icon-success"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Pemasukan</span>
            <span class="summary-value"><?php echo formatRupiah($totalPemasukan); ?></span>
        </div>
    </div>
</section>

<?php if (!$editData): ?>
<div class="toolbar-actions">
    <button type="button" class="btn btn-primary" onclick="toggleFormCard('formPemasukanCard')">
        <i class="fa-solid fa-plus"></i> Tambah Pemasukan
    </button>
</div>
<?php endif; ?>

<section class="card form-card" id="formPemasukanCard" <?php echo $editData ? '' : 'style="display:none;"'; ?>>
    <div class="card-header">
        <h2><?php echo $editData ? 'Edit Pemasukan' : 'Tambah Pemasukan'; ?></h2>
        <?php if ($editData): ?>
            <a href="pemasukan.php" class="btn btn-secondary">Batal</a>
        <?php endif; ?>
    </div>

    <?php if ($editData): ?>
        <form action="../proses/edit_pemasukan.php" method="POST" class="grid-form">
            <input type="hidden" name="id" value="<?php echo (int) $editData['id']; ?>">
            <div class="form-group">
                <label for="income_date">Tanggal</label>
                <input type="date" id="income_date" name="income_date" class="form-control" value="<?php echo htmlspecialchars($editData['income_date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="source">Sumber</label>
                <input type="text" id="source" name="source" class="form-control" placeholder="Uang Bulanan, Beasiswa, Freelance" value="<?php echo htmlspecialchars($editData['source']); ?>" required>
            </div>
            <div class="form-group">
                <label for="amount">Jumlah</label>
                <input type="number" id="amount" name="amount" class="form-control" min="1" step="0.01" value="<?php echo htmlspecialchars($editData['amount']); ?>" required>
            </div>
            <div class="form-group form-group-full">
                <label for="description">Keterangan</label>
                <textarea id="description" name="description" class="form-control" rows="2" placeholder="Keterangan tambahan (opsional)"><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    <?php else: ?>
        <form action="../proses/tambah_pemasukan.php" method="POST" class="grid-form">
            <div class="form-group">
                <label for="income_date">Tanggal</label>
                <input type="date" id="income_date" name="income_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="source">Sumber</label>
                <input type="text" id="source" name="source" class="form-control" placeholder="Uang Bulanan, Beasiswa, Freelance" required>
            </div>
            <div class="form-group">
                <label for="amount">Jumlah</label>
                <input type="number" id="amount" name="amount" class="form-control" min="1" step="0.01" placeholder="0" required>
            </div>
            <div class="form-group form-group-full">
                <label for="description">Keterangan</label>
                <textarea id="description" name="description" class="form-control" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Pemasukan</button>
            </div>
        </form>
    <?php endif; ?>
</section>

<section class="card table-card">
    <div class="card-header">
        <h2>Data Pemasukan</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Sumber</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pemasukanList)): ?>
                    <tr>
                        <td colspan="6" class="empty-state">Belum ada data pemasukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pemasukanList as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo date('d M Y', strtotime($row['income_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['source']); ?></td>
                            <td class="amount-positive">+ <?php echo formatRupiah($row['amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['description'] !== null && $row['description'] !== '' ? $row['description'] : '-'); ?></td>
                            <td>
                                <a href="pemasukan.php?edit=<?php echo (int) $row['id']; ?>#formPemasukanCard" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                <a href="../proses/hapus_pemasukan.php?id=<?php echo (int) $row['id']; ?>" class="btn-icon btn-delete" title="Hapus" onclick="return confirmDelete('Yakin ingin menghapus data pemasukan ini?')"><i class="fa-solid fa-trash-can"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
