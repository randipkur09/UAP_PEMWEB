<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

// Cek apakah ada parameter id
$id_supplier = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_supplier <= 0) {
    $_SESSION['error'] = 'ID supplier tidak valid!';
    header('Location: ../../pages/supplier/index.php');
    exit();
}

try {
    // Cek apakah supplier digunakan oleh sendal
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM sendal WHERE id_supplier = ?");
    $stmt->execute([$id_supplier]);
    $result = $stmt->fetch();
    
    if ($result['total'] > 0) {
        // Jika digunakan, ubah status menjadi nonaktif
        $stmt = $pdo->prepare("UPDATE supplier SET status = 'nonaktif' WHERE id_supplier = ?");
        $stmt->execute([$id_supplier]);
        
        $_SESSION['success'] = 'Supplier dinonaktifkan karena digunakan oleh ' . $result['total'] . ' sendal!';
    } else {
        // Hapus supplier
        $stmt = $pdo->prepare("DELETE FROM supplier WHERE id_supplier = ?");
        $stmt->execute([$id_supplier]);
        
        $_SESSION['success'] = 'Supplier berhasil dihapus!';
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

header('Location: ../../pages/supplier/index.php');
exit();
?>
