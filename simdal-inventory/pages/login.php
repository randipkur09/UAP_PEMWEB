<?php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$page_title = 'Login';
include '../includes/header.php';
?>

<div class="container-fluid vh-100">
    <div class="row h-100">
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-primary">
            <div class="text-center text-white">
                <i class="bi bi-shop display-1 mb-4"></i>
                <h1 class="display-4 fw-bold mb-3">SIMDAL</h1>
                <p class="lead">Sistem Informasi Manajemen Sendal</p>
                <p>Kelola inventaris sendal Anda dengan mudah dan efisien</p>
            </div>
        </div>
        
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="card shadow-lg border-0" style="width: 400px;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">Masuk ke SIMDAL</h3>
                        <p class="text-muted">Silakan masuk dengan akun Anda</p>
                    </div>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="../actions/login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            Demo: admin/password atau staff/password
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
