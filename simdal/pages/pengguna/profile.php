<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil data pengguna
$stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$stmt->execute([$_SESSION['user_id']]);
$pengguna = $stmt->fetch();

if (!$pengguna) {
    $_SESSION['error'] = 'Data pengguna tidak ditemukan!';
    header('Location: ../dashboard.php');
    exit();
}

$page_title = 'Profil Pengguna';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary">Profil Pengguna</h2>
                    <a href="../dashboard.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                        <?php echo strtoupper(substr($pengguna['nama_lengkap'], 0, 1)); ?>
                                    </div>
                                    <h4><?php echo $pengguna['nama_lengkap']; ?></h4>
                                    <p class="text-muted mb-0"><?php echo ucfirst($pengguna['role']); ?></p>
                                </div>
                                
                                <hr>
                                
                                <form action="../../pages/pengguna/update_profile.php" method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $pengguna['username']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo $pengguna['nama_lengkap']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $pengguna['email']; ?>">
                                    </div>
                                    
                                    <hr>
                                    
                                    <h5 class="mb-3">Ubah Password</h5>
                                    
                                    <div class="mb-3">
                                        <label for="password_lama" class="form-label">Password Lama</label>
                                        <input type="password" class="form-control" id="password_lama" name="password_lama">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password_baru" class="form-label">Password Baru</label>
                                                <input type="password" class="form-control" id="password_baru" name="password_baru">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                                                <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const passwordLama = document.getElementById('password_lama');
    const passwordBaru = document.getElementById('password_baru');
    const konfirmasi = document.getElementById('konfirmasi_password');
    
    form.addEventListener('submit', function(e) {
        // Jika password lama diisi, maka password baru dan konfirmasi harus diisi
        if (passwordLama.value !== '' && (passwordBaru.value === '' || konfirmasi.value === '')) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password harus diisi!');
            return;
        }
        
        // Jika password baru diisi, maka password lama dan konfirmasi harus diisi
        if (passwordBaru.value !== '' && (passwordLama.value === '' || konfirmasi.value === '')) {
            e.preventDefault();
            alert('Password lama dan konfirmasi password harus diisi!');
            return;
        }
        
        // Jika konfirmasi diisi, maka password lama dan baru harus diisi
        if (konfirmasi.value !== '' && (passwordLama.value === '' || passwordBaru.value === '')) {
            e.preventDefault();
            alert('Password lama dan password baru harus diisi!');
            return;
        }
        
        // Jika password baru dan konfirmasi tidak cocok
        if (passwordBaru.value !== '' && passwordBaru.value !== konfirmasi.value) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak cocok!');
            return;
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
