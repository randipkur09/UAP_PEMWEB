<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Cek apakah user adalah admin
if ($_SESSION['role'] != 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini!';
    header('Location: ../dashboard.php');
    exit();
}

$page_title = 'Tambah Pengguna';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Tambah Pengguna</h2>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="../../pages/pengguna/simpan.php" method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="konfirmasi_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                                <select class="form-select" id="role" name="role" required>
                                                    <option value="staff">Staff</option>
                                                    <option value="admin">Admin</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="aktif">Aktif</option>
                                                    <option value="nonaktif">Non-Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="index.php" class="btn btn-secondary me-md-2">Batal</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Simpan
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const konfirmasi = document.getElementById('konfirmasi_password');
    
    form.addEventListener('submit', function(e) {
        if (password.value !== konfirmasi.value) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
