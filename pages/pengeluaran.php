<?php
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Pengeluaran';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<section class="card table-card">
    <div class="card-header">
        <h2>Data Pengeluaran</h2>
        <button type="button" class="btn btn-primary">+ Tambah Pengeluaran</button>
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
                <tr>
                    <td>1</td>
                    <td>27 Jun 2026</td>
                    <td>Makanan</td>
                    <td>Makan siang</td>
                    <td>Rp25.000</td>
                    <td>Warteg dekat kampus</td>
                    <td>
                        <button type="button" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-delete" onclick="return confirmDelete()" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>25 Jun 2026</td>
                    <td>Pendidikan</td>
                    <td>Beli buku kuliah</td>
                    <td>Rp150.000</td>
                    <td>Buku mata kuliah statistik</td>
                    <td>
                        <button type="button" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-delete" onclick="return confirmDelete()" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>18 Jun 2026</td>
                    <td>Transportasi</td>
                    <td>Bensin motor</td>
                    <td>Rp50.000</td>
                    <td>Isi bensin full tank</td>
                    <td>
                        <button type="button" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-delete" onclick="return confirmDelete()" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
