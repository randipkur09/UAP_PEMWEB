<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_kategori = clean_input($_GET['id']);
    
    try {
        // Cek apakah kategori digunakan oleh sendal
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM sendal WHERE id_kategori = ?");
        $stmt->execute([$id_kategori]);
        $has_sendal = $stmt->fetch()['total'] > 0;
        
        if ($has_sendal) {
            $_SESSION['error'] = 'Kategori tidak dapat dihapus karena masih digunakan oleh data sendal!';
        } else {
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = ?");
            $stmt->execute([$id_kategori]);
            
            $_SESSION['success'] = 'Kategori berhasil dihapus!';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'ID kategori tidak valid!';
}

header('Location: ../../pages/kategori/index.php');
exit();
?>
