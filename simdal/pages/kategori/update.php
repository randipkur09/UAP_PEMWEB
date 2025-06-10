<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kategori = clean_input($_POST['id_kategori']);
    $nama_kategori = clean_input($_POST['nama_kategori']);
    $deskripsi = clean_input($_POST['deskripsi']);
    
    if (empty($nama_kategori)) {
        $_SESSION['error'] = 'Nama kategori harus diisi!';
        header('Location: ../../pages/kategori/edit.php?id=' . $id_kategori);
        exit();
    }
    
    try {
        // Cek duplikasi nama kategori
        $stmt = $pdo->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_kategori != ?");
        $stmt->execute([$nama_kategori, $id_kategori]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Nama kategori sudah ada!';
            header('Location: ../../pages/kategori/edit.php?id=' . $id_kategori);
            exit();
        }
        
        // Update data
        $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ?, deskripsi = ?, updated_at = CURRENT_TIMESTAMP WHERE id_kategori = ?");
        $stmt->execute([$nama_kategori, $deskripsi, $id_kategori]);
        
        $_SESSION['success'] = 'Kategori berhasil diperbarui!';
        header('Location: ../../pages/kategori/index.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/kategori/edit.php?id=' . $id_kategori);
        exit();
    }
} else {
    header('Location: ../../pages/kategori/index.php');
    exit();
}
?>
