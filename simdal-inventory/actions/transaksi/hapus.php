<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

// Cek apakah ada parameter id
$id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_transaksi <= 0) {
    $_SESSION['error'] = 'ID transaksi tidak valid!';
    header('Location: ../../pages/transaksi/index.php');
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Ambil data transaksi
    $stmt = $pdo->prepare("SELECT * FROM transaksi WHERE id_transaksi = ?");
    $stmt->execute([$id_transaksi]);
    $transaksi = $stmt->fetch();
    
    if (!$transaksi) {
        throw new Exception('Data transaksi tidak ditemukan!');
    }
    
    // Kembalikan stok
    if ($transaksi['jenis_transaksi'] == 'masuk') {
        // Jika transaksi masuk, kurangi stok
        $stmt = $pdo->prepare("UPDATE sendal SET stok_tersedia = stok_tersedia - ? WHERE id_sendal = ?");
        $stmt->execute([$transaksi['jumlah'], $transaksi['id_sendal']]);
    } else {
        // Jika transaksi keluar, tambah stok
        $stmt = $pdo->prepare("UPDATE sendal SET stok_tersedia = stok_tersedia + ? WHERE id_sendal = ?");
        $stmt->execute([$transaksi['jumlah'], $transaksi['id_sendal']]);
    }
    
    // Hapus transaksi
    $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
    $stmt->execute([$id_transaksi]);
    
    $pdo->commit();
    
    $_SESSION['success'] = 'Transaksi berhasil dihapus dan stok telah disesuaikan!';
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

header('Location: ../../pages/transaksi/index.php');
exit();
?>
