<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Laporan Keuangan';
$userId    = (int) $_SESSION['user_id'];

// --- Ambil & bersihkan parameter filter dari GET ---
$startDate       = trim($_GET['start_date'] ?? '');
$endDate         = trim($_GET['end_date'] ?? '');
$categoryId      = (int) ($_GET['category_id'] ?? 0);
$transactionType = $_GET['transaction_type'] ?? 'all';

if (!in_array($transactionType, ['all', 'pemasukan', 'pengeluaran'], true)) {
    $transactionType = 'all';
}

$filterError = '';
if ($startDate !== '' && $endDate !== '' && $startDate > $endDate) {
    $filterError = 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.';
    $startDate   = '';
    $endDate     = '';
}

// Sentinel kosong berarti "tidak difilter" pada query di bawah
$startParam    = $startDate;
$endParam      = $endDate;
$categoryParam = $categoryId > 0 ? (string) $categoryId : '';

// --- Kategori milik user, untuk pilihan filter ---
$stmt = mysqli_prepare($conn, 'SELECT id, name FROM expense_categories WHERE user_id = ? ORDER BY name ASC');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result     = mysqli_stmt_get_result($stmt);
$categories = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

// --- Total pemasukan (di-skip jika filter jenis = pengeluaran) ---
$totalPemasukan = 0.0;
if ($transactionType !== 'pengeluaran') {
    $stmt = mysqli_prepare($conn, '
        SELECT COALESCE(SUM(amount), 0) AS total
        FROM incomes
        WHERE user_id = ?
          AND (? = "" OR income_date >= ?)
          AND (? = "" OR income_date <= ?)
    ');
    mysqli_stmt_bind_param($stmt, 'issss', $userId, $startParam, $startParam, $endParam, $endParam);
    mysqli_stmt_execute($stmt);
    $totalPemasukan = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
    mysqli_stmt_close($stmt);
}

// --- Total pengeluaran (di-skip jika filter jenis = pemasukan) ---
$totalPengeluaran = 0.0;
if ($transactionType !== 'pemasukan') {
    $stmt = mysqli_prepare($conn, '
        SELECT COALESCE(SUM(amount), 0) AS total
        FROM expenses
        WHERE user_id = ?
          AND (? = "" OR expense_date >= ?)
          AND (? = "" OR expense_date <= ?)
          AND (? = "" OR category_id = ?)
    ');
    mysqli_stmt_bind_param($stmt, 'issssss', $userId, $startParam, $startParam, $endParam, $endParam, $categoryParam, $categoryParam);
    mysqli_stmt_execute($stmt);
    $totalPengeluaran = (float) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
    mysqli_stmt_close($stmt);
}

$saldoAkhir = $totalPemasukan - $totalPengeluaran;

// --- Tabel laporan transaksi (gabungan sesuai filter jenis transaksi) ---
$incomeSql = '
    SELECT income_date AS tanggal, "Pemasukan" AS jenis, source AS kategori_sumber,
           description AS keterangan, amount AS jumlah
    FROM incomes
    WHERE user_id = ?
      AND (? = "" OR income_date >= ?)
      AND (? = "" OR income_date <= ?)
';
$expenseSql = '
    SELECT e.expense_date AS tanggal, "Pengeluaran" AS jenis,
           COALESCE(ec.name, "Tanpa Kategori") AS kategori_sumber,
           COALESCE(NULLIF(e.title, ""), e.description) AS keterangan, e.amount AS jumlah
    FROM expenses e
    LEFT JOIN expense_categories ec ON e.category_id = ec.id
    WHERE e.user_id = ?
      AND (? = "" OR e.expense_date >= ?)
      AND (? = "" OR e.expense_date <= ?)
      AND (? = "" OR e.category_id = ?)
';

if ($transactionType === 'pemasukan') {
    $stmt = mysqli_prepare($conn, $incomeSql . ' ORDER BY tanggal DESC');
    mysqli_stmt_bind_param($stmt, 'issss', $userId, $startParam, $startParam, $endParam, $endParam);
} elseif ($transactionType === 'pengeluaran') {
    $stmt = mysqli_prepare($conn, $expenseSql . ' ORDER BY tanggal DESC');
    mysqli_stmt_bind_param($stmt, 'issssss', $userId, $startParam, $startParam, $endParam, $endParam, $categoryParam, $categoryParam);
} else {
    $stmt = mysqli_prepare($conn, '
        SELECT tanggal, jenis, kategori_sumber, keterangan, jumlah FROM (
            ' . $incomeSql . '
            UNION ALL
            ' . $expenseSql . '
        ) AS gabungan
        ORDER BY tanggal DESC
    ');
    mysqli_stmt_bind_param(
        $stmt,
        'issssissssss',
        $userId, $startParam, $startParam, $endParam, $endParam,
        $userId, $startParam, $startParam, $endParam, $endParam, $categoryParam, $categoryParam
    );
}
mysqli_stmt_execute($stmt);
$result      = mysqli_stmt_get_result($stmt);
$riwayatList = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

$jumlahTransaksi = count($riwayatList);

// --- Ringkasan pengeluaran per kategori (selalu mengikuti filter tanggal, tidak mengikuti filter kategori) ---
$stmt = mysqli_prepare($conn, '
    SELECT ec.name AS kategori, COUNT(e.id) AS jumlah_transaksi, SUM(e.amount) AS total_pengeluaran
    FROM expenses e
    JOIN expense_categories ec ON e.category_id = ec.id
    WHERE e.user_id = ?
      AND (? = "" OR e.expense_date >= ?)
      AND (? = "" OR e.expense_date <= ?)
    GROUP BY e.category_id, ec.name
    ORDER BY total_pengeluaran DESC
');
mysqli_stmt_bind_param($stmt, 'issss', $userId, $startParam, $startParam, $endParam, $endParam);
mysqli_stmt_execute($stmt);
$result             = mysqli_stmt_get_result($stmt);
$kategoriRingkasan  = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

// --- Teks periode laporan ---
if ($startDate !== '' && $endDate !== '') {
    $periodeLaporan = formatTanggalIndonesia($startDate) . ' - ' . formatTanggalIndonesia($endDate);
} elseif ($startDate !== '') {
    $periodeLaporan = 'Sejak ' . formatTanggalIndonesia($startDate);
} elseif ($endDate !== '') {
    $periodeLaporan = 'Sampai ' . formatTanggalIndonesia($endDate);
} else {
    $periodeLaporan = 'Semua Periode';
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-intro">
    <p class="page-welcome">Lihat rekap pemasukan, pengeluaran, dan saldo berdasarkan periode tertentu.</p>
</div>

<?php if ($filterError): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($filterError); ?></div>
<?php endif; ?>

<section class="card filter-card">
    <form action="laporan.php" method="GET" class="filter-form">
        <div class="form-group">
            <label for="start_date">Tanggal Awal</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>">
        </div>
        <div class="form-group">
            <label for="end_date">Tanggal Akhir</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>">
        </div>
        <div class="form-group">
            <label for="category_id">Kategori Pengeluaran</label>
            <select id="category_id" name="category_id" class="form-control">
                <option value="0">Semua Kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo (int) $category['id']; ?>" <?php echo ($categoryId === (int) $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="transaction_type">Jenis Transaksi</label>
            <select id="transaction_type" name="transaction_type" class="form-control">
                <option value="all" <?php echo $transactionType === 'all' ? 'selected' : ''; ?>>Semua</option>
                <option value="pemasukan" <?php echo $transactionType === 'pemasukan' ? 'selected' : ''; ?>>Pemasukan</option>
                <option value="pengeluaran" <?php echo $transactionType === 'pengeluaran' ? 'selected' : ''; ?>>Pengeluaran</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="laporan.php" class="btn btn-secondary">Reset</a>
    </form>
</section>

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
            <span class="summary-label">Saldo Akhir</span>
            <span class="summary-value"><?php echo formatRupiah($saldoAkhir); ?></span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-primary"><i class="fa-solid fa-receipt"></i></div>
        <div class="summary-info">
            <span class="summary-label">Jumlah Transaksi</span>
            <span class="summary-value"><?php echo $jumlahTransaksi; ?></span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-warning"><i class="fa-solid fa-calendar-days"></i></div>
        <div class="summary-info">
            <span class="summary-label">Periode Laporan</span>
            <span class="summary-value summary-value-text"><?php echo htmlspecialchars($periodeLaporan); ?></span>
        </div>
    </div>
</section>

<section class="card table-card">
    <div class="card-header">
        <h2>Tabel Laporan Transaksi</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori/Sumber</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($riwayatList)): ?>
                    <tr>
                        <td colspan="6" class="empty-state">Tidak ada data laporan pada periode ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($riwayatList as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo formatTanggal($row['tanggal']); ?></td>
                            <td>
                                <?php if ($row['jenis'] === 'Pemasukan'): ?>
                                    <span class="badge badge-success">Pemasukan</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Pengeluaran</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['kategori_sumber']); ?></td>
                            <td><?php echo htmlspecialchars($row['keterangan'] !== null && $row['keterangan'] !== '' ? $row['keterangan'] : '-'); ?></td>
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
        <h2>Ringkasan Pengeluaran per Kategori</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Jumlah Transaksi</th>
                    <th>Total Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kategoriRingkasan)): ?>
                    <tr>
                        <td colspan="4" class="empty-state">Belum ada data pengeluaran pada periode ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($kategoriRingkasan as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                            <td><?php echo (int) $row['jumlah_transaksi']; ?></td>
                            <td><?php echo formatRupiah($row['total_pengeluaran']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
