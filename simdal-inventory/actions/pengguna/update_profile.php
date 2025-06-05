<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pengguna = $_SESSION['user_id'];
    $username = clean_input($_POST['username']);
    $nama_lengkap = clean_input($_POST['nama_lengkap']);
    $email = clean_input($_POST['email']);
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    // Validasi input
    if (empty($username) || empty($nama_lengkap)) {
        $_SESSION['error'] = 'Username dan nama lengkap harus diisi!';
        header('Location: ../../pages/pengguna/profile.php');
        exit();
    }
    
    try {
        // Cek duplikasi username
        $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE username = ? AND id_pengguna != ?");
        $stmt->execute([$username, $id_pengguna]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Username sudah digunakan!';
            header('Location: ../../pages/pengguna/profile.php');
            exit();
        }
        
        // Jika ingin mengubah password
        if (!empty($password_lama) && !empty($password_baru) && !empty($konfirmasi_password)) {
            // Verifikasi password lama
            $stmt = $pdo->prepare("SELECT password FROM pengguna WHERE id_pengguna = ?");
            $stmt->execute([$id_pengguna]);
            $user = $stmt->fetch();
            
            if (!password_verify($password_lama, $user['password'])) {
                $_SESSION['error'] = 'Password lama tidak sesuai!';
                header('Location: ../../pages/pengguna/profile.php');
                exit();
            }
            
            // Cek konfirmasi password
            if ($password_baru !== $konfirmasi_password) {
                $_SESSION['error'] = 'Password baru dan konfirmasi password tidak cocok!';
                header('Location: ../../pages/pengguna/profile.php');
                exit();
            }
            
            // Update dengan password baru
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                UPDATE pengguna SET 
                username = ?, 
                password = ?, 
                nama_lengkap = ?, 
                email = ? 
                WHERE id_pengguna = ?
            ");
            $stmt->execute([$username, $hashed_password, $nama_lengkap, $email, $id_pengguna]);
        } else {
            // Update tanpa mengubah password
            $stmt = $pdo->prepare("
                UPDATE pengguna SET 
                username = ?, 
                nama_lengkap = ?, 
                email = ? 
                WHERE id_pengguna = ?
            ");
            $stmt->execute([$username, $nama_lengkap, $email, $id_pengguna]);
        }
        
        // Update session
        $_SESSION['username'] = $username;
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        
        $_SESSION['success'] = 'Profil berhasil diperbarui!';
        header('Location: ../../pages/pengguna/profile.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/pengguna/profile.php');
        exit();
    }
} else {
    header('Location: ../../pages/pengguna/profile.php');
    exit();
}
?>
