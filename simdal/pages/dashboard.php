<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil statistik
$stats = [];

// Total sendal
$stmt = $pdo->query("SELECT COUNT(*) as total FROM sendal WHERE status = 'aktif'");
$stats['total_sendal'] = $stmt->fetch()['total'];

// Total kategori
$stmt = $pdo->query("SELECT COUNT(*) as total FROM kategori");
$stats['total_kategori'] = $stmt->fetch()['total'];

// Total supplier
$stmt = $pdo->query("SELECT COUNT(*) as total FROM supplier WHERE status = 'aktif'");
$stats['total_supplier'] = $stmt->fetch()['total'];

// Total transaksi bulan ini
$stmt = $pdo->query("SELECT COUNT(*) as total FROM transaksi WHERE MONTH(tanggal_transaksi) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_transaksi) = YEAR(CURRENT_DATE())");
$stats['transaksi_bulan_ini'] = $stmt->fetch()['total'];

// Sendal stok menipis
$stmt = $pdo->query("SELECT COUNT(*) as total FROM sendal WHERE stok_tersedia <= stok_minimal AND status = 'aktif'");
$stats['stok_menipis'] = $stmt->fetch()['total'];

// Transaksi terbaru
$stmt = $pdo->query("
    SELECT t.*, s.nama_sendal, p.nama_lengkap 
    FROM transaksi t 
    JOIN sendal s ON t.id_sendal = s.id_sendal 
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna 
    ORDER BY t.created_at DESC 
    LIMIT 5
");
$transaksi_terbaru = $stmt->fetchAll();

// Sendal stok menipis detail
$stmt = $pdo->query("
    SELECT s.*, k.nama_kategori 
    FROM sendal s 
    JOIN kategori k ON s.id_kategori = k.id_kategori 
    WHERE s.stok_tersedia <= s.stok_minimal AND s.status = 'aktif' 
    ORDER BY s.stok_tersedia ASC 
    LIMIT 5
");
$stok_menipis = $stmt->fetchAll();

$page_title = 'Dashboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-primary">Dashboard</h2>
                        <p class="text-muted">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?>!</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            <?php echo formatTanggal(date('Y-m-d')); ?>
                        </small>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-box display-4 mb-2"></i>
                                <h3><?php echo $stats['total_sendal']; ?></h3>
                                <p class="mb-0">Total Sendal</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-tags display-4 mb-2"></i>
                                <h3><?php echo $stats['total_kategori']; ?></h3>
                                <p class="mb-0">Kategori</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-truck display-4 mb-2"></i>
                                <h3><?php echo $stats['total_supplier']; ?></h3>
                                <p class="mb-0">Supplier</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body text-center">
                                <i class="bi bi-arrow-left-right display-4 mb-2"></i>
                                <h3><?php echo $stats['transaksi_bulan_ini']; ?></h3>
                                <p class="mb-0">Transaksi Bulan Ini</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alert Stok Menipis -->
                <?php if ($stats['stok_menipis'] > 0): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Perhatian!</strong> Ada <?php echo $stats['stok_menipis']; ?> sendal dengan stok menipis.
                    <a href="sendal/" class="alert-link">Lihat detail</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Transaksi Terbaru -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Transaksi Terbaru</h5>
                                <a href="transaksi/" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($transaksi_terbaru)): ?>
                                    <p class="text-muted text-center py-3">Belum ada transaksi</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Jenis</th>
                                                    <th>Sendal</th>
                                                    <th>Jumlah</th>
                                                    <th>Total</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($transaksi_terbaru as $transaksi): ?>
                                                <tr>
                                                    <td><?php echo $transaksi['kode_transaksi']; ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $transaksi['jenis_transaksi'] == 'masuk' ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo ucfirst($transaksi['jenis_transaksi']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $transaksi['nama_sendal']; ?></td>
                                                    <td><?php echo $transaksi['jumlah']; ?></td>
                                                    <td><?php echo formatRupiah($transaksi['total_harga']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($transaksi['tanggal_transaksi'])); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stok Menipis -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning"></i> Stok Menipis</h5>
                                <a href="sendal/" class="btn btn-sm btn-outline-warning">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($stok_menipis)): ?>
                                    <p class="text-muted text-center py-3">Semua stok aman</p>
                                <?php else: ?>
                                    <?php foreach ($stok_menipis as $sendal): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                        <div>
                                            <h6 class="mb-1"><?php echo $sendal['nama_sendal']; ?></h6>
                                            <small class="text-muted"><?php echo $sendal['nama_kategori']; ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning text-dark"><?php echo $sendal['stok_tersedia']; ?></span>
                                            <br>
                                            <small class="text-muted">Min: <?php echo $sendal['stok_minimal']; ?></small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
