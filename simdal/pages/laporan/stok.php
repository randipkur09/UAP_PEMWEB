<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Filter kategori
$id_kategori = isset($_GET['id_kategori']) ? intval($_GET['id_kategori']) : 0;
$status = isset($_GET['status']) ? clean_input($_GET['status']) : '';

// Query laporan stok
$where_conditions = [];
$params = [];

if ($id_kategori > 0) {
    $where_conditions[] = "s.id_kategori = ?";
    $params[] = $id_kategori;
}

if (!empty($status)) {
    $where_conditions[] = "s.status = ?";
    $params[] = $status;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(' AND ', $where_conditions) : "";

$stmt = $pdo->prepare("
    SELECT s.*, k.nama_kategori, sp.nama_supplier 
    FROM sendal s 
    LEFT JOIN kategori k ON s.id_kategori = k.id_kategori 
    LEFT JOIN supplier sp ON s.id_supplier = sp.id_supplier 
    $where_clause
    ORDER BY s.nama_sendal ASC
");
$stmt->execute($params);
$sendal = $stmt->fetchAll();

// Ambil data kategori untuk filter
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kategori = $stmt->fetchAll();

$page_title = 'Laporan Stok';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Laporan Stok</h2>
                    <button onclick="window.print()" class="btn btn-success">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </button>
                </div>
                
                <!-- Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="id_kategori" class="form-label">Kategori</label>
                                <select class="form-select" id="id_kategori" name="id_kategori">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($kategori as $item): ?>
                                    <option value="<?php echo $item['id_kategori']; ?>" <?php echo $id_kategori == $item['id_kategori'] ? 'selected' : ''; ?>>
                                        <?php echo $item['nama_kategori']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" <?php echo $status == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="nonaktif" <?php echo $status == 'nonaktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-4">
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
                
                <!-- Tabel Laporan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Laporan Stok Sendal</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Sendal</th>
                                        <th>Kategori</th>
                                        <th>Supplier</th>
                                        <th>Ukuran</th>
                                        <th>Warna</th>
                                        <th>Stok Tersedia</th>
                                        <th>Stok Minimal</th>
                                        <th>Status Stok</th>
                                        <th>Harga Jual</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($sendal)): ?>
                                    <tr>
                                        <td colspan="12" class="text-center">Tidak ada data sendal</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($sendal as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $item['kode_sendal']; ?></td>
                                            <td><?php echo $item['nama_sendal']; ?></td>
                                            <td><?php echo $item['nama_kategori'] ?: '-'; ?></td>
                                            <td><?php echo $item['nama_supplier'] ?: '-'; ?></td>
                                            <td><?php echo $item['ukuran']; ?></td>
                                            <td><?php echo $item['warna']; ?></td>
                                            <td><?php echo $item['stok_tersedia']; ?></td>
                                            <td><?php echo $item['stok_minimal']; ?></td>
                                            <td>
                                                <?php if ($item['stok_tersedia'] <= $item['stok_minimal']): ?>
                                                    <span class="badge bg-danger">Menipis</span>
                                                <?php elseif ($item['stok_tersedia'] <= ($item['stok_minimal'] * 2)): ?>
                                                    <span class="badge bg-warning">Perhatian</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Aman</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo formatRupiah($item['harga_jual']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $item['status'] == 'aktif' ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo ucfirst($item['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="text-center d-none d-print-block mt-5">
                    <p>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
