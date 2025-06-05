<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_sendal = clean_input($_GET['id']);
    
    try {
        $pdo->beginTransaction();
        
        // Cek apakah sendal memiliki transaksi
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_sendal = ?");
        $stmt->execute([$id_sendal]);
        $has_transaksi = $stmt->fetch()['total'] > 0;
        
        if ($has_transaksi) {
            // Jika ada transaksi, ubah status menjadi nonaktif
            $stmt = $pdo->prepare("UPDATE sendal SET status = 'nonaktif' WHERE id_sendal = ?");
            $stmt->execute([$id_sendal]);
            
            $_SESSION['success'] = 'Sendal berhasil dinonaktifkan karena memiliki riwayat transaksi!';
        } else {
            // Ambil data gambar
            $stmt = $pdo->prepare("SELECT gambar FROM sendal WHERE id_sendal = ?");
            $stmt->execute([$id_sendal]);
            $sendal = $stmt->fetch();
            
            // Hapus gambar jika ada
            if ($sendal['gambar'] && file_exists('../../' . $sendal['gambar'])) {
                unlink('../../' . $sendal['gambar']);
            }
            
            // Hapus data sendal
            $stmt = $pdo->prepare("DELETE FROM sendal WHERE id_sendal = ?");
            $stmt->execute([$id_sendal]);
            
            $_SESSION['success'] = 'Data sendal berhasil dihapus!';
        }
        
        $pdo->commit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'ID sendal tidak valid!';
}

header('Location: ../../pages/sendal/index.php');
exit();
?>
