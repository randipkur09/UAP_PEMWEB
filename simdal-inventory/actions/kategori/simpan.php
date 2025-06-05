<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = clean_input($_POST['nama_kategori']);
    $deskripsi = clean_input($_POST['deskripsi']);
    
    if (empty($nama_kategori)) {
        $_SESSION['error'] = 'Nama kategori harus diisi!';
        header('Location: ../../pages/kategori/tambah.php');
        exit();
    }
    
    try {
        // Cek duplikasi nama kategori
        $stmt = $pdo->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ?");
        $stmt->execute([$nama_kategori]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Nama kategori sudah ada!';
            header('Location: ../../pages/kategori/tambah.php');
            exit();
        }
        
        // Simpan data
        $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)");
        $stmt->execute([$nama_kategori, $deskripsi]);
        
        $_SESSION['success'] = 'Kategori berhasil ditambahkan!';
        header('Location: ../../pages/kategori/index.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/kategori/tambah.php');
        exit();
    }
} else {
    header('Location: ../../pages/kategori/index.php');
    exit();
}
?>
