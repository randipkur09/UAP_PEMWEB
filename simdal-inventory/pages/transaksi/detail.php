<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil ID transaksi
$id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_transaksi <= 0) {
    $_SESSION['error'] = 'ID transaksi tidak valid!';
    header('Location: index.php');
    exit();
}

// Ambil data transaksi dengan join
$stmt = $pdo->prepare("
    SELECT t.*, s.nama_sendal, s.kode_sendal, s.ukuran, s.warna, k.nama_kategori, p.nama_lengkap 
    FROM transaksi t 
    JOIN sendal s ON t.id_sendal = s.id_sendal 
    JOIN kategori k ON s.id_kategori = k.id_kategori 
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna 
    WHERE t.id_transaksi = ?
");
$stmt->execute([$id_transaksi]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    $_SESSION['error'] = 'Data transaksi tidak ditemukan!';
    header('Location: index.php');
    exit();
}

$page_title = 'Detail Transaksi';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Detail Transaksi</h2>
                    <div>
                        <button onclick="window.print()" class="btn btn-success me-2">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <span class="badge <?php echo $transaksi['jenis_transaksi'] == 'masuk' ? 'bg-success' : 'bg-danger'; ?> me-2">
                                <?php echo ucfirst($transaksi['jenis_transaksi']); ?>
                            </span>
                            <?php echo $transaksi['kode_transaksi']; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Tanggal Transaksi</strong></td>
                                        <td>: <?php echo formatTanggal($transaksi['tanggal_transaksi']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kode Sendal</strong></td>
                                        <td>: <?php echo $transaksi['kode_sendal']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Sendal</strong></td>
                                        <td>: <?php echo $transaksi['nama_sendal']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kategori</strong></td>
                                        <td>: <?php echo $transaksi['nama_kategori']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ukuran / Warna</strong></td>
                                        <td>: <?php echo $transaksi['ukuran'] . ' / ' . $transaksi['warna']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Jumlah</strong></td>
                                        <td>: <?php echo $transaksi['jumlah']; ?> pcs</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Harga Satuan</strong></td>
                                        <td>: <?php echo formatRupiah($transaksi['harga_satuan']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Harga</strong></td>
                                        <td>: <strong><?php echo formatRupiah($transaksi['total_harga']); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Petugas</strong></td>
                                        <td>: <?php echo $transaksi['nama_lengkap']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Waktu Input</strong></td>
                                        <td>: <?php echo date('d/m/Y H:i', strtotime($transaksi['created_at'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <?php if (!empty($transaksi['keterangan'])): ?>
                        <div class="mt-3">
                            <h6>Keterangan:</h6>
                            <p><?php echo nl2br($transaksi['keterangan']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center d-none d-print-block mt-5">
                    <p>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .btn, form {
        display: none !important;
    }
    
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<?php include '../../includes/footer.php'; ?>
