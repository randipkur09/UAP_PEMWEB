<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Cek ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'ID sendal tidak valid!';
    header('Location: index.php');
    exit();
}

$id_sendal = clean_input($_GET['id']);

// Ambil data sendal dengan join
$stmt = $pdo->prepare("
    SELECT s.*, k.nama_kategori, sp.nama_supplier 
    FROM sendal s 
    LEFT JOIN kategori k ON s.id_kategori = k.id_kategori 
    LEFT JOIN supplier sp ON s.id_supplier = sp.id_supplier 
    WHERE s.id_sendal = ?
");
$stmt->execute([$id_sendal]);
$sendal = $stmt->fetch();

if (!$sendal) {
    $_SESSION['error'] = 'Data sendal tidak ditemukan!';
    header('Location: index.php');
    exit();
}

// Ambil riwayat transaksi
$stmt = $pdo->prepare("
    SELECT t.*, p.nama_lengkap 
    FROM transaksi t 
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna 
    WHERE t.id_sendal = ? 
    ORDER BY t.tanggal_transaksi DESC, t.created_at DESC
");
$stmt->execute([$id_sendal]);
$transaksi = $stmt->fetchAll();

$page_title = 'Detail Sendal';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Detail Sendal</h2>
                    <div>
                        <a href="edit.php?id=<?php echo $sendal['id_sendal']; ?>" class="btn btn-warning me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <?php if ($sendal['gambar']): ?>
                                    <img src="../../<?php echo $sendal['gambar']; ?>" alt="<?php echo $sendal['nama_sendal']; ?>" class="img-fluid rounded mb-3" style="max-height: 200px;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <h4 class="fw-bold"><?php echo $sendal['nama_sendal']; ?></h4>
                                <p class="text-muted mb-2"><?php echo $sendal['kode_sendal']; ?></p>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <span class="badge bg-primary"><?php echo $sendal['ukuran']; ?></span>
                                    <span class="badge bg-info"><?php echo $sendal['warna']; ?></span>
                                    <span class="badge <?php echo $sendal['status'] == 'aktif' ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo ucfirst($sendal['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Sendal</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Kategori</strong></td>
                                                <td>: <?php echo $sendal['nama_kategori'] ?: '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Supplier</strong></td>
                                                <td>: <?php echo $sendal['nama_supplier'] ?: '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Harga Beli</strong></td>
                                                <td>: <?php echo formatRupiah($sendal['harga_beli']); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Harga Jual</strong></td>
                                                <td>: <?php echo formatRupiah($sendal['harga_jual']); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Stok Tersedia</strong></td>
                                                <td>: 
                                                    <span class="badge <?php echo $sendal['stok_tersedia'] <= $sendal['stok_minimal'] ? 'bg-danger' : 'bg-success'; ?>">
                                                        <?php echo $sendal['stok_tersedia']; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Stok Minimal</strong></td>
                                                <td>: <?php echo $sendal['stok_minimal']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tanggal Dibuat</strong></td>
                                                <td>: <?php echo date('d/m/Y', strtotime($sendal['created_at'])); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Terakhir Diupdate</strong></td>
                                                <td>: <?php echo date('d/m/Y', strtotime($sendal['updated_at'])); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <?php if ($sendal['deskripsi']): ?>
                                <div class="mt-3">
                                    <h6 class="fw-bold">Deskripsi:</h6>
                                    <p><?php echo nl2br($sendal['deskripsi']); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Riwayat Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($transaksi)): ?>
                            <p class="text-center text-muted py-3">Belum ada riwayat transaksi</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Kode</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Harga Satuan</th>
                                            <th>Total</th>
                                            <th>Keterangan</th>
                                            <th>Petugas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transaksi as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($item['tanggal_transaksi'])); ?></td>
                                            <td><?php echo $item['kode_transaksi']; ?></td>
                                            <td>
                                                <span class="badge <?php echo $item['jenis_transaksi'] == 'masuk' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ucfirst($item['jenis_transaksi']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $item['jumlah']; ?></td>
                                            <td><?php echo formatRupiah($item['harga_satuan']); ?></td>
                                            <td><?php echo formatRupiah($item['total_harga']); ?></td>
                                            <td><?php echo $item['keterangan'] ?: '-'; ?></td>
                                            <td><?php echo $item['nama_lengkap']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
