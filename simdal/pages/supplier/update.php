<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_supplier = clean_input($_POST['id_supplier']);
    $nama_supplier = clean_input($_POST['nama_supplier']);
    $alamat = clean_input($_POST['alamat']);
    $telepon = clean_input($_POST['telepon']);
    $email = clean_input($_POST['email']);
    $kontak_person = clean_input($_POST['kontak_person']);
    $status = clean_input($_POST['status']);
    
    if (empty($id_supplier) || empty($nama_supplier)) {
        $_SESSION['error'] = 'ID dan nama supplier harus diisi!';
        header('Location: ../../pages/supplier/edit.php?id=' . $id_supplier);
        exit();
    }
    
    try {
        // Cek duplikasi nama supplier
        $stmt = $pdo->prepare("SELECT id_supplier FROM supplier WHERE nama_supplier = ? AND id_supplier != ?");
        $stmt->execute([$nama_supplier, $id_supplier]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Nama supplier sudah ada!';
            header('Location: ../../pages/supplier/edit.php?id=' . $id_supplier);
            exit();
        }
        
        // Update data
        $stmt = $pdo->prepare("
            UPDATE supplier SET 
            nama_supplier = ?, 
            alamat = ?, 
            telepon = ?, 
            email = ?, 
            kontak_person = ?, 
            status = ? 
            WHERE id_supplier = ?
        ");
        $stmt->execute([$nama_supplier, $alamat, $telepon, $email, $kontak_person, $status, $id_supplier]);
        
        $_SESSION['success'] = 'Supplier berhasil diperbarui!';
        header('Location: ../../pages/supplier/index.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/supplier/edit.php?id=' . $id_supplier);
        exit();
    }
} else {
    header('Location: ../../pages/supplier/index.php');
    exit();
}
?>
