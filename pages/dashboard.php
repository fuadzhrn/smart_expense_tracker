<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Dashboard';
$userId    = (int) $_SESSION['user_id'];

// --- Total pemasukan ---
$stmt = mysqli_prepare($conn, 'SELECT COALESCE(SUM(amount), 0) AS total FROM incomes WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$totalPemasukan = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

// --- Total pengeluaran ---
$stmt = mysqli_prepare($conn, 'SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$totalPengeluaran = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

// --- Saldo ---
$saldo = $totalPemasukan - $totalPengeluaran;

// --- Total kategori ---
$stmt = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM expense_categories WHERE user_id = ?');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$totalKategori = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
mysqli_stmt_close($stmt);

// --- Riwayat transaksi terbaru (gabungan pemasukan + pengeluaran) ---
$riwayatQuery = '
    SELECT tanggal, jenis, keterangan, jumlah FROM (
        SELECT income_date AS tanggal, "Pemasukan" AS jenis, source AS keterangan, amount AS jumlah
        FROM incomes WHERE user_id = ?
        UNION ALL
        SELECT expense_date AS tanggal, "Pengeluaran" AS jenis, title AS keterangan, amount AS jumlah
        FROM expenses WHERE user_id = ?
    ) AS gabungan
    ORDER BY tanggal DESC
    LIMIT 5
';
$stmt = mysqli_prepare($conn, $riwayatQuery);
mysqli_stmt_bind_param($stmt, 'ii', $userId, $userId);
mysqli_stmt_execute($stmt);
$riwayatResult = mysqli_stmt_get_result($stmt);
$riwayat = $riwayatResult ? mysqli_fetch_all($riwayatResult, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

// --- Pengeluaran berdasarkan kategori ---
$stmt = mysqli_prepare($conn, '
    SELECT ec.name AS kategori, SUM(e.amount) AS total
    FROM expenses e
    JOIN expense_categories ec ON e.category_id = ec.id
    WHERE e.user_id = ?
    GROUP BY e.category_id, ec.name
    ORDER BY total DESC
');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$kategoriResult = mysqli_stmt_get_result($stmt);
$kategoriPengeluaran = $kategoriResult ? mysqli_fetch_all($kategoriResult, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-intro">
    <p class="page-welcome">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>. Berikut ringkasan keuangan kamu.</p>
</div>

<section class="summary-cards">
    <div class="card summary-card">
        <div class="summary-icon icon-success"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Pemasukan</span>
            <span class="summary-value"><?php echo formatRupiah($totalPemasukan); ?></span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-danger"><i class="fa-solid fa-money-bill-transfer"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Pengeluaran</span>
            <span class="summary-value"><?php echo formatRupiah($totalPengeluaran); ?></span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-primary"><i class="fa-solid fa-wallet"></i></div>
        <div class="summary-info">
            <span class="summary-label">Saldo Saat Ini</span>
            <span class="summary-value"><?php echo formatRupiah($saldo); ?></span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-warning"><i class="fa-solid fa-tags"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Kategori</span>
            <span class="summary-value"><?php echo $totalKategori; ?></span>
        </div>
    </div>
</section>

<section class="card table-card">
    <div class="card-header">
        <h2>Riwayat Transaksi Terbaru</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($riwayat)): ?>
                    <tr>
                        <td colspan="5" class="empty-state">Belum ada transaksi.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($riwayat as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td>
                                <?php if ($row['jenis'] === 'Pemasukan'): ?>
                                    <span class="badge badge-success">Pemasukan</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Pengeluaran</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                            <td class="<?php echo $row['jenis'] === 'Pemasukan' ? 'amount-positive' : 'amount-negative'; ?>">
                                <?php echo ($row['jenis'] === 'Pemasukan' ? '+ ' : '- ') . formatRupiah($row['jumlah']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card table-card">
    <div class="card-header">
        <h2>Pengeluaran Berdasarkan Kategori</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Total Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kategoriPengeluaran)): ?>
                    <tr>
                        <td colspan="3" class="empty-state">Belum ada data pengeluaran.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($kategoriPengeluaran as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                            <td><?php echo formatRupiah($row['total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
