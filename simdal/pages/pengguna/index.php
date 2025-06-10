<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Cek apakah user adalah admin
if ($_SESSION['role'] != 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini!';
    header('Location: ../dashboard.php');
    exit();
}

// Ambil data pengguna
$stmt = $pdo->query("SELECT * FROM pengguna ORDER BY nama_lengkap ASC");
$pengguna = $stmt->fetchAll();

$page_title = 'Data Pengguna';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Data Pengguna</h2>
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Pengguna
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-data">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Username</th>
                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pengguna as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $item['username']; ?></td>
                                        <td><?php echo $item['nama_lengkap']; ?></td>
                                        <td><?php echo $item['email'] ?: '-'; ?></td>
                                        <td>
                                            <span class="badge <?php echo $item['role'] == 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                                                <?php echo ucfirst($item['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $item['status'] == 'aktif' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="edit.php?id=<?php echo $item['id_pengguna']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <?php if ($item['id_pengguna'] != $_SESSION['user_id']): ?>
                                                <button onclick="confirmDelete('../../pages/pengguna/hapus.php?id=<?php echo $item['id_pengguna']; ?>')" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php endif; ?>
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
