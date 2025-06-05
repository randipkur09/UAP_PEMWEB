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
    $id_pengguna = clean_input($_POST['id_pengguna']);
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $nama_lengkap = clean_input($_POST['nama_lengkap']);
    $email = clean_input($_POST['email']);
    $role = clean_input($_POST['role']);
    $status = clean_input($_POST['status']);
    
    // Validasi input
    if (empty($id_pengguna) || empty($username) || empty($nama_lengkap) || empty($role) || empty($status)) {
        $_SESSION['error'] = 'Semua field wajib harus diisi!';
        header('Location: ../../pages/pengguna/edit.php?id=' . $id_pengguna);
        exit();
    }
    
    if (!empty($password) && $password !== $konfirmasi_password) {
        $_SESSION['error'] = 'Password dan konfirmasi password tidak cocok!';
        header('Location: ../../pages/pengguna/edit.php?id=' . $id_pengguna);
        exit();
    }
    
    try {
        // Cek duplikasi username
        $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE username = ? AND id_pengguna != ?");
        $stmt->execute([$username, $id_pengguna]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Username sudah digunakan!';
            header('Location: ../../pages/pengguna/edit.php?id=' . $id_pengguna);
            exit();
        }
        
        // Update data
        if (!empty($password)) {
            // Update dengan password baru
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                UPDATE pengguna SET 
                username = ?, 
                password = ?, 
                nama_lengkap = ?, 
                email = ?, 
                role = ?, 
                status = ? 
                WHERE id_pengguna = ?
            ");
            $stmt->execute([$username, $hashed_password, $nama_lengkap, $email, $role, $status, $id_pengguna]);
        } else {
            // Update tanpa mengubah password
            $stmt = $pdo->prepare("
                UPDATE pengguna SET 
                username = ?, 
                nama_lengkap = ?, 
                email = ?, 
                role = ?, 
                status = ? 
                WHERE id_pengguna = ?
            ");
            $stmt->execute([$username, $nama_lengkap, $email, $role, $status, $id_pengguna]);
        }
        
        // Update session jika user mengedit dirinya sendiri
        if ($id_pengguna == $_SESSION['user_id']) {
            $_SESSION['username'] = $username;
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['role'] = $role;
        }
        
        $_SESSION['success'] = 'Pengguna berhasil diperbarui!';
        header('Location: ../../pages/pengguna/index.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/pengguna/edit.php?id=' . $id_pengguna);
        exit();
    }
} else {
    header('Location: ../../pages/pengguna/index.php');
    exit();
}
?>
