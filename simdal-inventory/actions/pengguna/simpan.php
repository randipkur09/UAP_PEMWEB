<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

// Cek apakah user adalah admin
if ($_SESSION['role'] != 'admin') {
    $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini!';
    header('Location: ../../pages/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $nama_lengkap = clean_input($_POST['nama_lengkap']);
    $email = clean_input($_POST['email']);
    $role = clean_input($_POST['role']);
    $status = clean_input($_POST['status']);
    
    // Validasi input
    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($role) || empty($status)) {
        $_SESSION['error'] = 'Semua field wajib harus diisi!';
        header('Location: ../../pages/pengguna/tambah.php');
        exit();
    }
    
    if ($password !== $konfirmasi_password) {
        $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok!';
        header('Location: ../../pages/pengguna/tambah.php');
        exit();
    }
    
    try {
        // Cek duplikasi username
        $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Username sudah digunakan!';
            header('Location: ../../pages/pengguna/tambah.php');
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Simpan data
        $stmt = $pdo->prepare("
            INSERT INTO pengguna (username, password, nama_lengkap, email, role, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$username, $hashed_password, $nama_lengkap, $email, $role, $status]);
        
        $_SESSION['success'] = 'Pengguna berhasil ditambahkan!';
        header('Location: ../../pages/pengguna/index.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/pengguna/tambah.php');
        exit();
    }
} else {
    header('Location: ../../pages/pengguna/index.php');
    exit();
}
?>
