<?php
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<section class="summary-cards">
    <div class="card summary-card">
        <div class="summary-icon icon-success"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Pemasukan</span>
            <span class="summary-value">Rp1.500.000</span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-danger"><i class="fa-solid fa-money-bill-transfer"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Pengeluaran</span>
            <span class="summary-value">Rp850.000</span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-primary"><i class="fa-solid fa-wallet"></i></div>
        <div class="summary-info">
            <span class="summary-label">Saldo Saat Ini</span>
            <span class="summary-value">Rp650.000</span>
        </div>
    </div>

    <div class="card summary-card">
        <div class="summary-icon icon-warning"><i class="fa-solid fa-tags"></i></div>
        <div class="summary-info">
            <span class="summary-label">Total Kategori</span>
            <span class="summary-value">6</span>
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
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>28 Jun 2026</td>
                    <td><span class="badge badge-success">Pemasukan</span></td>
                    <td>Uang saku bulanan</td>
                    <td>Rp1.000.000</td>
                </tr>
                <tr>
                    <td>27 Jun 2026</td>
                    <td><span class="badge badge-danger">Pengeluaran</span></td>
                    <td>Makan siang</td>
                    <td>Rp25.000</td>
                </tr>
                <tr>
                    <td>25 Jun 2026</td>
                    <td><span class="badge badge-danger">Pengeluaran</span></td>
                    <td>Beli buku kuliah</td>
                    <td>Rp150.000</td>
                </tr>
                <tr>
                    <td>20 Jun 2026</td>
                    <td><span class="badge badge-success">Pemasukan</span></td>
                    <td>Freelance desain</td>
                    <td>Rp500.000</td>
                </tr>
                <tr>
                    <td>18 Jun 2026</td>
                    <td><span class="badge badge-danger">Pengeluaran</span></td>
                    <td>Bensin motor</td>
                    <td>Rp50.000</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
