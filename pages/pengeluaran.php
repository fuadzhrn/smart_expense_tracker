<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Data Pengeluaran';
$userId    = (int) $_SESSION['user_id'];

$flashSuccess = flash('flash_success');
$flashError   = flash('flash_error');

// --- Kategori milik user, untuk pilihan di form ---
$stmt = mysqli_prepare($conn, 'SELECT id, name FROM expense_categories WHERE user_id = ? ORDER BY name ASC');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result     = mysqli_stmt_get_result($stmt);
$categories = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

// --- Data yang sedang diedit (jika ada parameter ?edit=ID milik user ini) ---
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt   = mysqli_prepare($conn, 'SELECT id, expense_date, category_id, title, amount, description FROM expenses WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $editId, $userId);
    mysqli_stmt_execute($stmt);
    $editData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

// --- Total pengeluaran ---
$stmt = mysqli_prepare($conn, 'SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$totalPengeluaran = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

// --- Total transaksi pengeluaran ---
$stmt = mysqli_prepare($conn, 'SELECT COUNT(id) AS total FROM expenses WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$totalTransaksi = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

// --- Kategori dengan pengeluaran terbesar ---
$stmt = mysqli_prepare($conn, '
    SELECT ec.name AS kategori, SUM(e.amount) AS total
    FROM expenses e
    JOIN expense_categories ec ON e.category_id = ec.id
    WHERE e.user_id = ?
    GROUP BY e.category_id, ec.name
    ORDER BY total DESC
    LIMIT 1
');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$kategoriTerbesarRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$kategoriTerbesar     = $kategoriTerbesarRow ? $kategoriTerbesarRow['kategori'] : '-';
mysqli_stmt_close($stmt);

// --- Pengeluaran bulan ini ---
$stmt = mysqli_prepare($conn, '
    SELECT COALESCE(SUM(amount), 0) AS total
    FROM expenses
    WHERE user_id = ? AND YEAR(expense_date) = YEAR(CURDATE()) AND MONTH(expense_date) = MONTH(CURDATE())
');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$pengeluaranBulanIni = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

// --- Daftar pengeluaran, terbaru dulu ---
$stmt = mysqli_prepare($conn, '
    SELECT e.id, e.expense_date, e.category_id, ec.name AS kategori, e.title, e.amount, e.description
    FROM expenses e
    LEFT JOIN expense_categories ec ON e.category_id = ec.id
    WHERE e.user_id = ?
    ORDER BY e.expense_date DESC, e.id DESC
');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result          = mysqli_stmt_get_result($stmt);
$pengeluaranList = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-intro">
    <p class="page-welcome">Kelola semua data pengeluaran harian kamu berdasarkan kategori.</p>
</div>

<?php if ($flashSuccess): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess); ?></div>
<?php endif; ?>
<?php if ($flashError): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($flashError); ?></div>
<?php endif; ?>

<section class="summary-cards">
    <div class="card summary-card">
        <div class="summary-icon icon-danger"><i class="fa-solid fa-money-bill-transfer"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Pengeluaran</span>
            <span class="summary-value"><?php echo formatRupiah($totalPengeluaran); ?></span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-primary"><i class="fa-solid fa-receipt"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Transaksi</span>
            <span class="summary-value"><?php echo $totalTransaksi; ?></span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-warning"><i class="fa-solid fa-crown"></i></div>
        <div class="summary-info">
            <span class="summary-label">Kategori Terbesar</span>
            <span class="summary-value"><?php echo htmlspecialchars($kategoriTerbesar); ?></span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-danger"><i class="fa-solid fa-calendar-days"></i></div>
        <div class="summary-info">
            <span class="summary-label">Pengeluaran Bulan Ini</span>
            <span class="summary-value"><?php echo formatRupiah($pengeluaranBulanIni); ?></span>
        </div>
    </div>
</section>

<?php if (empty($categories)): ?>
    <div class="alert alert-warning">
        Belum ada kategori. Silakan <a href="kategori.php">tambahkan kategori pengeluaran</a> terlebih dahulu.
    </div>
<?php else: ?>

    <?php if (!$editData): ?>
    <div class="toolbar-actions">
        <button type="button" class="btn btn-primary" onclick="toggleFormCard('formPengeluaranCard')">
            <i class="fa-solid fa-plus"></i> Tambah Pengeluaran
        </button>
    </div>
    <?php endif; ?>

    <section class="card form-card" id="formPengeluaranCard" <?php echo $editData ? '' : 'style="display:none;"'; ?>>
        <div class="card-header">
            <h2><?php echo $editData ? 'Edit Pengeluaran' : 'Tambah Pengeluaran'; ?></h2>
            <?php if ($editData): ?>
                <a href="pengeluaran.php" class="btn btn-secondary">Batal</a>
            <?php endif; ?>
        </div>

        <?php if ($editData): ?>
            <form action="../proses/edit_pengeluaran.php" method="POST" class="grid-form">
                <input type="hidden" name="id" value="<?php echo (int) $editData['id']; ?>">
                <div class="form-group">
                    <label for="expense_date">Tanggal</label>
                    <input type="date" id="expense_date" name="expense_date" class="form-control" value="<?php echo htmlspecialchars($editData['expense_date']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int) $category['id']; ?>" <?php echo ((int) $editData['category_id'] === (int) $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">Nama Pengeluaran</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Makan Siang, Bensin Motor, Print Tugas" value="<?php echo htmlspecialchars($editData['title']); ?>" required>
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
            <form action="../proses/tambah_pengeluaran.php" method="POST" class="grid-form">
                <div class="form-group">
                    <label for="expense_date">Tanggal</label>
                    <input type="date" id="expense_date" name="expense_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int) $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">Nama Pengeluaran</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Makan Siang, Bensin Motor, Print Tugas" required>
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
                    <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
                </div>
            </form>
        <?php endif; ?>
    </section>
<?php endif; ?>

<section class="card table-card">
    <div class="card-header">
        <h2>Data Pengeluaran</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Nama Pengeluaran</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pengeluaranList)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada data pengeluaran.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pengeluaranList as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo formatTanggal($row['expense_date']); ?></td>
                            <td><?php echo $row['kategori'] !== null ? htmlspecialchars($row['kategori']) : 'Tanpa Kategori'; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="amount-negative">- <?php echo formatRupiah($row['amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['description'] !== null && $row['description'] !== '' ? $row['description'] : '-'); ?></td>
                            <td>
                                <a href="pengeluaran.php?edit=<?php echo (int) $row['id']; ?>#formPengeluaranCard" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                <a href="../proses/hapus_pengeluaran.php?id=<?php echo (int) $row['id']; ?>" class="btn-icon btn-delete" title="Hapus" onclick="return confirmDelete('Yakin ingin menghapus data pengeluaran ini?')"><i class="fa-solid fa-trash-can"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
