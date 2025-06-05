<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil ID supplier
$id_supplier = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_supplier <= 0) {
    $_SESSION['error'] = 'ID supplier tidak valid!';
    header('Location: index.php');
    exit();
}

// Ambil data supplier
$stmt = $pdo->prepare("SELECT * FROM supplier WHERE id_supplier = ?");
$stmt->execute([$id_supplier]);
$supplier = $stmt->fetch();

if (!$supplier) {
    $_SESSION['error'] = 'Data supplier tidak ditemukan!';
    header('Location: index.php');
    exit();
}

$page_title = 'Edit Supplier';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Edit Supplier</h2>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="../../actions/supplier/update.php" method="POST">
                                    <input type="hidden" name="id_supplier" value="<?php echo $supplier['id_supplier']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="nama_supplier" class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" value="<?php echo $supplier['nama_supplier']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo $supplier['alamat']; ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="telepon" class="form-label">Telepon</label>
                                                <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo $supplier['telepon']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $supplier['email']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="kontak_person" class="form-label">Kontak Person</label>
                                        <input type="text" class="form-control" id="kontak_person" name="kontak_person" value="<?php echo $supplier['kontak_person']; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="aktif" <?php echo $supplier['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                            <option value="nonaktif" <?php echo $supplier['status'] == 'nonaktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
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
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
