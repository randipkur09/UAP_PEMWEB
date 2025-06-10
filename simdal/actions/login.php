<?php
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
        header('Location: ../pages/login.php');
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE username = ? AND status = 'aktif'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_pengguna'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            
            $_SESSION['success'] = 'Login berhasil! Selamat datang ' . $user['nama_lengkap'];
            header('Location: ../pages/dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = 'Username atau password salah!';
            header('Location: ../pages/login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan sistem!';
        header('Location: ../pages/login.php');
        exit();
    }
} else {
    header('Location: ../pages/login.php');
    exit();
}
?>
