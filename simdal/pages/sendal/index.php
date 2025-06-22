<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil data sendal dengan join
$stmt = $pdo->query("
    SELECT s.*, k.nama_kategori, sp.nama_supplier 
    FROM sendal s 
    LEFT JOIN kategori k ON s.id_kategori = k.id_kategori 
    LEFT JOIN supplier sp ON s.id_supplier = sp.id_supplier 
    ORDER BY s.nama_sendal ASC
");
$sendal = $stmt->fetchAll();

$page_title = 'Data Sendal';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Data Sendal</h2>
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Sendal
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-data">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Sendal</th>
                                        <th>Kategori</th>
                                        <th>Supplier</th>
                                        <th>Ukuran</th>
                                        <th>Warna</th>
                                        <th>Harga Jual</th>
                                        <th>Stok</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sendal as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $item['kode_sendal']; ?></td>
                                        <td><?php echo $item['nama_sendal']; ?></td>
                                        <td><?php echo $item['nama_kategori'] ?: '-'; ?></td>
                                        <td><?php echo $item['nama_supplier'] ?: '-'; ?></td>
                                        <td><?php echo $item['ukuran']; ?></td>
                                        <td><?php echo $item['warna']; ?></td>
                                        <td><?php echo formatRupiah($item['harga_jual']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $item['stok_tersedia'] <= $item['stok_minimal'] ? 'bg-danger' : 'bg-success'; ?>">
                                                <?php echo $item['stok_tersedia']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $item['status'] == 'aktif' ? 'bg-success' : 'bg-secondary'; ?>">
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="detail.php?id=<?php echo $item['id_sendal']; ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="edit.php?id=<?php echo $item['id_sendal']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="confirmDelete('../../pages/sendal/hapus.php?id=<?php echo $item['id_sendal']; ?>')" class="btn btn-sm btn-danger">
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
