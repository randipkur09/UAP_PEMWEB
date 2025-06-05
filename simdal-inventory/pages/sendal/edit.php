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

// Ambil data sendal
$stmt = $pdo->prepare("SELECT * FROM sendal WHERE id_sendal = ?");
$stmt->execute([$id_sendal]);
$sendal = $stmt->fetch();

if (!$sendal) {
    $_SESSION['error'] = 'Data sendal tidak ditemukan!';
    header('Location: index.php');
    exit();
}

// Ambil data kategori
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kategori = $stmt->fetchAll();

// Ambil data supplier
$stmt = $pdo->query("SELECT * FROM supplier WHERE status = 'aktif' ORDER BY nama_supplier ASC");
$supplier = $stmt->fetchAll();

$page_title = 'Edit Sendal';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Edit Sendal</h2>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <form action="../../actions/sendal/update.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_sendal" value="<?php echo $sendal['id_sendal']; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="kode_sendal" class="form-label">Kode Sendal</label>
                                        <input type="text" class="form-control" id="kode_sendal" name="kode_sendal" value="<?php echo $sendal['kode_sendal']; ?>" readonly>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="nama_sendal" class="form-label">Nama Sendal <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_sendal" name="nama_sendal" value="<?php echo $sendal['nama_sendal']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select" id="id_kategori" name="id_kategori" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <?php foreach ($kategori as $item): ?>
                                            <option value="<?php echo $item['id_kategori']; ?>" <?php echo $sendal['id_kategori'] == $item['id_kategori'] ? 'selected' : ''; ?>>
                                                <?php echo $item['nama_kategori']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="id_supplier" class="form-label">Supplier</label>
                                        <select class="form-select" id="id_supplier" name="id_supplier">
                                            <option value="">-- Pilih Supplier --</option>
                                            <?php foreach ($supplier as $item): ?>
                                            <option value="<?php echo $item['id_supplier']; ?>" <?php echo $sendal['id_supplier'] == $item['id_supplier'] ? 'selected' : ''; ?>>
                                                <?php echo $item['nama_supplier']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="ukuran" class="form-label">Ukuran <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="ukuran" name="ukuran" value="<?php echo $sendal['ukuran']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="warna" class="form-label">Warna <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="warna" name="warna" value="<?php echo $sendal['warna']; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="harga_beli" class="form-label">Harga Beli <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" class="form-control" id="harga_beli" name="harga_beli" min="0" step="0.01" value="<?php echo $sendal['harga_beli']; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="harga_jual" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" class="form-control" id="harga_jual" name="harga_jual" min="0" step="0.01" value="<?php echo $sendal['harga_jual']; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="stok_minimal" class="form-label">Stok Minimal</label>
                                                <input type="number" class="form-control" id="stok_minimal" name="stok_minimal" min="0" value="<?php echo $sendal['stok_minimal']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="stok_tersedia" class="form-label">Stok Tersedia</label>
                                                <input type="number" class="form-control" id="stok_tersedia" name="stok_tersedia" value="<?php echo $sendal['stok_tersedia']; ?>" readonly>
                                                <div class="form-text">Stok diubah melalui transaksi</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="gambar" class="form-label">Gambar</label>
                                        <?php if ($sendal['gambar']): ?>
                                            <div class="mb-2">
                                                <img src="../../<?php echo $sendal['gambar']; ?>" alt="<?php echo $sendal['nama_sendal']; ?>" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                                        <div class="form-text">Format: JPG, PNG, JPEG. Maks: 2MB. Kosongkan jika tidak ingin mengubah gambar.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="deskripsi" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo $sendal['deskripsi']; ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="aktif" <?php echo $sendal['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                            <option value="nonaktif" <?php echo $sendal['status'] == 'nonaktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                <a href="index.php" class="btn btn-secondary me-md-2">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
