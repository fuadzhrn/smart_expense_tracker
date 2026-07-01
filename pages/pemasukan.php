<?php
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Pemasukan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<section class="card table-card">
    <div class="card-header">
        <h2>Data Pemasukan</h2>
        <button type="button" class="btn btn-primary">+ Tambah Pemasukan</button>
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
                <tr>
                    <td>1</td>
                    <td>28 Jun 2026</td>
                    <td>Uang Saku</td>
                    <td>Rp1.000.000</td>
                    <td>Kiriman bulanan orang tua</td>
                    <td>
                        <button type="button" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-delete" onclick="return confirmDelete()" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>20 Jun 2026</td>
                    <td>Freelance</td>
                    <td>Rp500.000</td>
                    <td>Proyek desain logo</td>
                    <td>
                        <button type="button" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-delete" onclick="return confirmDelete()" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>10 Jun 2026</td>
                    <td>Beasiswa</td>
                    <td>Rp750.000</td>
                    <td>Pencairan beasiswa semester</td>
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
