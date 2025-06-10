<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil data transaksi dengan join
$stmt = $pdo->query("
    SELECT t.*, s.nama_sendal, p.nama_lengkap 
    FROM transaksi t 
    JOIN sendal s ON t.id_sendal = s.id_sendal 
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna 
    ORDER BY t.tanggal_transaksi DESC, t.created_at DESC
");
$transaksi = $stmt->fetchAll();

$page_title = 'Data Transaksi';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Data Transaksi</h2>
                    <div>
                        <a href="masuk.php" class="btn btn-success me-2">
                            <i class="bi bi-plus-circle"></i> Barang Masuk
                        </a>
                        <a href="keluar.php" class="btn btn-danger">
                            <i class="bi bi-dash-circle"></i> Barang Keluar
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-data">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Transaksi</th>
                                        <th>Jenis</th>
                                        <th>Sendal</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Total</th>
                                        <th>Tanggal</th>
                                        <th>Petugas</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transaksi as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $item['kode_transaksi']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $item['jenis_transaksi'] == 'masuk' ? 'bg-success' : 'bg-danger'; ?>">
                                                <i class="bi bi-arrow-<?php echo $item['jenis_transaksi'] == 'masuk' ? 'down' : 'up'; ?>"></i>
                                                <?php echo ucfirst($item['jenis_transaksi']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $item['nama_sendal']; ?></td>
                                        <td><?php echo $item['jumlah']; ?></td>
                                        <td><?php echo formatRupiah($item['harga_satuan']); ?></td>
                                        <td><?php echo formatRupiah($item['total_harga']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($item['tanggal_transaksi'])); ?></td>
                                        <td><?php echo $item['nama_lengkap']; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="detail.php?id=<?php echo $item['id_transaksi']; ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button onclick="confirmDelete('../../pages/transaksi/hapus.php?id=<?php echo $item['id_transaksi']; ?>')" class="btn btn-sm btn-danger">
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
