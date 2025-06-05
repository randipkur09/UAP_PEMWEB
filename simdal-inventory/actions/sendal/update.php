<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_sendal = clean_input($_POST['id_sendal']);
    $nama_sendal = clean_input($_POST['nama_sendal']);
    $id_kategori = clean_input($_POST['id_kategori']);
    $id_supplier = !empty($_POST['id_supplier']) ? clean_input($_POST['id_supplier']) : null;
    $ukuran = clean_input($_POST['ukuran']);
    $warna = clean_input($_POST['warna']);
    $harga_beli = clean_input($_POST['harga_beli']);
    $harga_jual = clean_input($_POST['harga_jual']);
    $stok_minimal = clean_input($_POST['stok_minimal']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $status = clean_input($_POST['status']);
    
    // Validasi input
    if (empty($nama_sendal) || empty($id_kategori) || empty($ukuran) || empty($warna) || empty($harga_beli) || empty($harga_jual)) {
        $_SESSION['error'] = 'Semua field wajib harus diisi!';
        header('Location: ../../pages/sendal/edit.php?id=' . $id_sendal);
        exit();
    }
    
    try {
        $pdo->beginTransaction();
        
        // Ambil data sendal lama untuk gambar
        $stmt = $pdo->prepare("SELECT gambar FROM sendal WHERE id_sendal = ?");
        $stmt->execute([$id_sendal]);
        $sendal_lama = $stmt->fetch();
        
        // Upload gambar jika ada
        $gambar = $sendal_lama['gambar'];
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($_FILES['gambar']['type'], $allowed_types)) {
                throw new Exception('Format file tidak didukung. Gunakan JPG, PNG, atau JPEG.');
            }
            
            if ($_FILES['gambar']['size'] > $max_size) {
                throw new Exception('Ukuran file terlalu besar. Maksimal 2MB.');
            }
            
            $upload_dir = '../../assets/uploads/sendal/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Hapus gambar lama jika ada
            if ($sendal_lama['gambar'] && file_exists('../../' . $sendal_lama['gambar'])) {
                unlink('../../' . $sendal_lama['gambar']);
            }
            
            // Upload gambar baru
            $stmt = $pdo->prepare("SELECT kode_sendal FROM sendal WHERE id_sendal = ?");
            $stmt->execute([$id_sendal]);
            $kode_sendal = $stmt->fetch()['kode_sendal'];
            
            $file_name = $kode_sendal . '_' . time() . '.' . pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar = 'assets/uploads/sendal/' . $file_name;
            } else {
                throw new Exception('Gagal mengupload gambar.');
            }
        }
        
        // Update data sendal
        $stmt = $pdo->prepare("
            UPDATE sendal SET 
                nama_sendal = ?, 
                id_kategori = ?, 
                id_supplier = ?, 
                ukuran = ?, 
                warna = ?, 
                harga_beli = ?, 
                harga_jual = ?, 
                stok_minimal = ?, 
                deskripsi = ?, 
                gambar = ?, 
                status = ?, 
                updated_at = CURRENT_TIMESTAMP 
            WHERE id_sendal = ?
        ");
        $stmt->execute([
            $nama_sendal, $id_kategori, $id_supplier, $ukuran, $warna, 
            $harga_beli, $harga_jual, $stok_minimal, $deskripsi, $gambar, $status, $id_sendal
        ]);
        
        $pdo->commit();
        
        $_SESSION['success'] = 'Data sendal berhasil diperbarui!';
        header('Location: ../../pages/sendal/index.php');
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/sendal/edit.php?id=' . $id_sendal);
        exit();
    }
} else {
    header('Location: ../../pages/sendal/index.php');
    exit();
}
?>
