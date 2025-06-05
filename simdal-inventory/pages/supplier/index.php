<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil data supplier
$stmt = $pdo->query("SELECT * FROM supplier ORDER BY nama_supplier ASC");
$supplier = $stmt->fetchAll();

$page_title = 'Data Supplier';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Data Supplier</h2>
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Supplier
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-data">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Supplier</th>
                                        <th>Alamat</th>
                                        <th>Telepon</th>
                                        <th>Email</th>
                                        <th>Kontak Person</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($supplier as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $item['nama_supplier']; ?></td>
                                        <td><?php echo $item['alamat'] ?: '-'; ?></td>
                                        <td><?php echo $item['telepon'] ?: '-'; ?></td>
                                        <td><?php echo $item['email'] ?: '-'; ?></td>
                                        <td><?php echo $item['kontak_person'] ?: '-'; ?></td>
                                        <td>
                                            <span class="badge <?php echo $item['status'] == 'aktif' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="edit.php?id=<?php echo $item['id_supplier']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="confirmDelete('../../actions/supplier/hapus.php?id=<?php echo $item['id_supplier']; ?>')" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
