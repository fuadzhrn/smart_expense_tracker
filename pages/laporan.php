<?php
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Laporan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<section class="card filter-card">
    <form action="" method="GET" class="filter-form">
        <div class="form-group">
            <label for="tanggal_awal">Tanggal Awal</label>
            <input type="date" id="tanggal_awal" name="tanggal_awal">
        </div>
        <div class="form-group">
            <label for="tanggal_akhir">Tanggal Akhir</label>
            <input type="date" id="tanggal_akhir" name="tanggal_akhir">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</section>

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
            <span class="summary-label">Saldo</span>
            <span class="summary-value">Rp650.000</span>
        </div>
    </div>
</section>

<section class="card table-card">
    <div class="card-header">
        <h2>Detail Laporan</h2>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori / Sumber</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>28 Jun 2026</td>
                    <td><span class="badge badge-success">Pemasukan</span></td>
                    <td>Uang Saku</td>
                    <td>Kiriman bulanan orang tua</td>
                    <td>Rp1.000.000</td>
                </tr>
                <tr>
                    <td>27 Jun 2026</td>
                    <td><span class="badge badge-danger">Pengeluaran</span></td>
                    <td>Makanan</td>
                    <td>Makan siang</td>
                    <td>Rp25.000</td>
                </tr>
                <tr>
                    <td>25 Jun 2026</td>
                    <td><span class="badge badge-danger">Pengeluaran</span></td>
                    <td>Pendidikan</td>
                    <td>Beli buku kuliah</td>
                    <td>Rp150.000</td>
                </tr>
                <tr>
                    <td>20 Jun 2026</td>
                    <td><span class="badge badge-success">Pemasukan</span></td>
                    <td>Freelance</td>
                    <td>Proyek desain logo</td>
                    <td>Rp500.000</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
