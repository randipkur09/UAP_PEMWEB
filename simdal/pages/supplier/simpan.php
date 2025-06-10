<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supplier = clean_input($_POST['nama_supplier']);
    $alamat = clean_input($_POST['alamat']);
    $telepon = clean_input($_POST['telepon']);
    $email = clean_input($_POST['email']);
    $kontak_person = clean_input($_POST['kontak_person']);
    $status = clean_input($_POST['status']);
    
    if (empty($nama_supplier)) {
        $_SESSION['error'] = 'Nama supplier harus diisi!';
        header('Location: ../../pages/supplier/tambah.php');
        exit();
    }
    
    try {
        // Cek duplikasi nama supplier
        $stmt = $pdo->prepare("SELECT id_supplier FROM supplier WHERE nama_supplier = ?");
        $stmt->execute([$nama_supplier]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Nama supplier sudah ada!';
            header('Location: ../../pages/supplier/tambah.php');
            exit();
        }
        
        // Simpan data
        $stmt = $pdo->prepare("
            INSERT INTO supplier (nama_supplier, alamat, telepon, email, kontak_person, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nama_supplier, $alamat, $telepon, $email, $kontak_person, $status]);
        
        $_SESSION['success'] = 'Supplier berhasil ditambahkan!';
        header('Location: ../../pages/supplier/index.php');
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/supplier/tambah.php');
        exit();
    }
} else {
    header('Location: ../../pages/supplier/index.php');
    exit();
}
?>
