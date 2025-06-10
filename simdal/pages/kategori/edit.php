<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Cek ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'ID kategori tidak valid!';
    header('Location: index.php');
    exit();
}

$id_kategori = clean_input($_GET['id']);

// Ambil data kategori
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
$stmt->execute([$id_kategori]);
$kategori = $stmt->fetch();

if (!$kategori) {
    $_SESSION['error'] = 'Data kategori tidak ditemukan!';
    header('Location: index.php');
    exit();
}

$page_title = 'Edit Kategori';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Edit Kategori</h2>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="../../pages/kategori/update.php" method="POST">
                                    <input type="hidden" name="id_kategori" value="<?php echo $kategori['id_kategori']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?php echo $kategori['nama_kategori']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="deskripsi" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?php echo $kategori['deskripsi']; ?></textarea>
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
