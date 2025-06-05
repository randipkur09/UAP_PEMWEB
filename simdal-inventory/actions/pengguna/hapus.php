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

// Cek apakah ada parameter id
$id_pengguna = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pengguna <= 0) {
    $_SESSION['error'] = 'ID pengguna tidak valid!';
    header('Location: ../../pages/pengguna/index.php');
    exit();
}

// Cek apakah user menghapus dirinya sendiri
if ($id_pengguna == $_SESSION['user_id']) {
    $_SESSION['error'] = 'Anda tidak dapat menghapus akun Anda sendiri!';
    header('Location: ../../pages/pengguna/index.php');
    exit();
}

try {
    // Cek apakah pengguna memiliki transaksi
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_pengguna = ?");
    $stmt->execute([$id_pengguna]);
    $result = $stmt->fetch();
    
    if ($result['total'] > 0) {
        // Jika memiliki transaksi, ubah status menjadi nonaktif
        $stmt = $pdo->prepare("UPDATE pengguna SET status = 'nonaktif' WHERE id_pengguna = ?");
        $stmt->execute([$id_pengguna]);
        
        $_SESSION['success'] = 'Pengguna dinonaktifkan karena memiliki riwayat transaksi!';
    } else {
        // Hapus pengguna
        $stmt = $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$id_pengguna]);
        
        $_SESSION['success'] = 'Pengguna berhasil dihapus!';
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

header('Location: ../../pages/pengguna/index.php');
exit();
?>
