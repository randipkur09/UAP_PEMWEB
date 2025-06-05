<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_transaksi = clean_input($_POST['kode_transaksi']);
    $tanggal_transaksi = clean_input($_POST['tanggal_transaksi']);
    $id_sendal = clean_input($_POST['id_sendal']);
    $jumlah = clean_input($_POST['jumlah']);
    $harga_satuan = clean_input($_POST['harga_satuan']);
    $total_harga = clean_input($_POST['total_harga']);
    $keterangan = clean_input($_POST['keterangan']);
    $id_pengguna = $_SESSION['user_id'];
    
    // Validasi input
    if (empty($kode_transaksi) || empty($tanggal_transaksi) || empty($id_sendal) || empty($jumlah) || empty($harga_satuan)) {
        $_SESSION['error'] = 'Semua field wajib harus diisi!';
        header('Location: ../../pages/transaksi/masuk.php');
        exit();
    }
    
    try {
        $pdo->beginTransaction();
        
        // Simpan transaksi
        $stmt = $pdo->prepare("
            INSERT INTO transaksi (kode_transaksi, jenis_transaksi, id_sendal, jumlah, harga_satuan, total_harga, tanggal_transaksi, keterangan, id_pengguna) 
            VALUES (?, 'masuk', ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$kode_transaksi, $id_sendal, $jumlah, $harga_satuan, $total_harga, $tanggal_transaksi, $keterangan, $id_pengguna]);
        
        // Update stok sendal (tambah)
        $stmt = $pdo->prepare("UPDATE sendal SET stok_tersedia = stok_tersedia + ? WHERE id_sendal = ?");
        $stmt->execute([$jumlah, $id_sendal]);
        
        $pdo->commit();
        
        $_SESSION['success'] = 'Transaksi barang masuk berhasil disimpan!';
        header('Location: ../../pages/transaksi/index.php');
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/transaksi/masuk.php');
        exit();
    }
} else {
    header('Location: ../../pages/transaksi/index.php');
    exit();
}
?>
