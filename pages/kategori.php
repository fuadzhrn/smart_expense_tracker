<?php
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Kategori';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<section class="card table-card">
    <div class="card-header">
        <h2>Kategori Pengeluaran</h2>
        <button type="button" class="btn btn-primary">+ Tambah Kategori</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Makanan</td>
                    <td>Pengeluaran untuk makan dan minum sehari-hari</td>
                    <td>
                        <button type="button" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-delete" onclick="return confirmDelete()" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Transportasi</td>
                    <td>Bensin, ongkos, dan biaya perjalanan</td>
                    <td>
                        <button type="button" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-delete" onclick="return confirmDelete()" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Pendidikan</td>
                    <td>Buku, alat tulis, dan kebutuhan kuliah</td>
                    <td>
                        <button type="button" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button type="button" class="btn-icon btn-delete" onclick="return confirmDelete()" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Hiburan</td>
                    <td>Nonton, langganan streaming, dan rekreasi</td>
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
