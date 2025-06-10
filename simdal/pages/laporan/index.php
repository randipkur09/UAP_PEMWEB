<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Filter tanggal
$tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');
$tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');
$jenis_transaksi = $_GET['jenis_transaksi'] ?? '';

// Query laporan
$where_conditions = ["DATE(t.tanggal_transaksi) BETWEEN ? AND ?"];
$params = [$tanggal_mulai, $tanggal_selesai];

if (!empty($jenis_transaksi)) {
    $where_conditions[] = "t.jenis_transaksi = ?";
    $params[] = $jenis_transaksi;
}

$where_clause = implode(' AND ', $where_conditions);

$stmt = $pdo->prepare("
    SELECT t.*, s.nama_sendal, s.kode_sendal, k.nama_kategori, p.nama_lengkap 
    FROM transaksi t 
    JOIN sendal s ON t.id_sendal = s.id_sendal 
    JOIN kategori k ON s.id_kategori = k.id_kategori 
    JOIN pengguna p ON t.id_pengguna = p.id_pengguna 
    WHERE $where_clause
    ORDER BY t.tanggal_transaksi DESC, t.created_at DESC
");
$stmt->execute($params);
$laporan = $stmt->fetchAll();

// Statistik
$total_masuk = 0;
$total_keluar = 0;
$nilai_masuk = 0;
$nilai_keluar = 0;

foreach ($laporan as $item) {
    if ($item['jenis_transaksi'] == 'masuk') {
        $total_masuk += $item['jumlah'];
        $nilai_masuk += $item['total_harga'];
    } else {
        $total_keluar += $item['jumlah'];
        $nilai_keluar += $item['total_harga'];
    }
}

$page_title = 'Laporan Transaksi';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Laporan Transaksi</h2>
                    <button onclick="window.print()" class="btn btn-success">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </button>
                </div>
                
                <!-- Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo $tanggal_mulai; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?php echo $tanggal_selesai; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                                <select class="form-select" id="jenis_transaksi" name="jenis_transaksi">
                                    <option value="">Semua</option>
                                    <option value="masuk" <?php echo $jenis_transaksi == 'masuk' ? 'selected' : ''; ?>>Barang Masuk</option>
                                    <option value="keluar" <?php echo $jenis_transaksi == 'keluar' ? 'selected' : ''; ?>>Barang Keluar</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Statistik -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4><?php echo $total_masuk; ?></h4>
                                <p class="mb-0">Total Barang Masuk</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4><?php echo $total_keluar; ?></h4>
                                <p class="mb-0">Total Barang Keluar</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4><?php echo formatRupiah($nilai_masuk); ?></h4>
                                <p class="mb-0">Nilai Barang Masuk</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4><?php echo formatRupiah($nilai_keluar); ?></h4>
                                <p class="mb-0">Nilai Barang Keluar</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabel Laporan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Laporan Periode: <?php echo formatTanggal($tanggal_mulai) . ' - ' . formatTanggal($tanggal_selesai); ?>
                            <?php if (!empty($jenis_transaksi)): ?>
                                (<?php echo ucfirst($jenis_transaksi); ?>)
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kode Transaksi</th>
                                        <th>Jenis</th>
                                        <th>Kode Sendal</th>
                                        <th>Nama Sendal</th>
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Total</th>
                                        <th>Petugas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($laporan)): ?>
                                    <tr>
                                        <td colspan="11" class="text-center">Tidak ada data transaksi</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($laporan as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($item['tanggal_transaksi'])); ?></td>
                                            <td><?php echo $item['kode_transaksi']; ?></td>
                                            <td>
                                                <span class="badge <?php echo $item['jenis_transaksi'] == 'masuk' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ucfirst($item['jenis_transaksi']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $item['kode_sendal']; ?></td>
                                            <td><?php echo $item['nama_sendal']; ?></td>
                                            <td><?php echo $item['nama_kategori']; ?></td>
                                            <td><?php echo $item['jumlah']; ?></td>
                                            <td><?php echo formatRupiah($item['harga_satuan']); ?></td>
                                            <td><?php echo formatRupiah($item['total_harga']); ?></td>
                                            <td><?php echo $item['nama_lengkap']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .btn, .card-header .btn {
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
    
    .table {
        font-size: 12px;
    }
    
    .badge {
        color: #000 !important;
        background-color: transparent !important;
        border: 1px solid #000 !important;
    }
}
</style>

<?php include '../../includes/footer.php'; ?>
