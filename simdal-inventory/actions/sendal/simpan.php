<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_sendal = clean_input($_POST['kode_sendal']);
    $nama_sendal = clean_input($_POST['nama_sendal']);
    $id_kategori = clean_input($_POST['id_kategori']);
    $id_supplier = !empty($_POST['id_supplier']) ? clean_input($_POST['id_supplier']) : null;
    $ukuran = clean_input($_POST['ukuran']);
    $warna = clean_input($_POST['warna']);
    $harga_beli = clean_input($_POST['harga_beli']);
    $harga_jual = clean_input($_POST['harga_jual']);
    $stok_minimal = clean_input($_POST['stok_minimal']);
    $stok_tersedia = clean_input($_POST['stok_tersedia']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $status = clean_input($_POST['status']);
    
    // Validasi input
    if (empty($nama_sendal) || empty($id_kategori) || empty($ukuran) || empty($warna) || empty($harga_beli) || empty($harga_jual)) {
        $_SESSION['error'] = 'Semua field wajib harus diisi!';
        header('Location: ../../pages/sendal/tambah.php');
        exit();
    }
    
    try {
        $pdo->beginTransaction();
        
        // Upload gambar jika ada
        $gambar = null;
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
            
            $file_name = $kode_sendal . '_' . time() . '.' . pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar = 'assets/uploads/sendal/' . $file_name;
            } else {
                throw new Exception('Gagal mengupload gambar.');
            }
        }
        
        // Simpan data sendal
        $stmt = $pdo->prepare("
            INSERT INTO sendal (kode_sendal, nama_sendal, id_kategori, id_supplier, ukuran, warna, 
                               harga_beli, harga_jual, stok_minimal, stok_tersedia, deskripsi, gambar, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $kode_sendal, $nama_sendal, $id_kategori, $id_supplier, $ukuran, $warna, 
            $harga_beli, $harga_jual, $stok_minimal, $stok_tersedia, $deskripsi, $gambar, $status
        ]);
        
        // Jika ada stok awal, buat transaksi masuk
        if ($stok_tersedia > 0) {
            $id_sendal = $pdo->lastInsertId();
            $kode_transaksi = generateKode('TM', 'transaksi', 'kode_transaksi');
            $total_harga = $harga_beli * $stok_tersedia;
            $tanggal = date('Y-m-d');
            $keterangan = 'Stok awal';
            $id_pengguna = $_SESSION['user_id'];
            
            $stmt = $pdo->prepare("
                INSERT INTO transaksi (kode_transaksi, jenis_transaksi, id_sendal, jumlah, harga_satuan, 
                                      total_harga, tanggal_transaksi, keterangan, id_pengguna) 
                VALUES (?, 'masuk', ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $kode_transaksi, $id_sendal, $stok_tersedia, $harga_beli, $total_harga, $tanggal, $keterangan, $id_pengguna
            ]);
        }
        
        $pdo->commit();
        
        $_SESSION['success'] = 'Data sendal berhasil ditambahkan!';
        header('Location: ../../pages/sendal/index.php');
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: ../../pages/sendal/tambah.php');
        exit();
    }
} else {
    header('Location: ../../pages/sendal/index.php');
    exit();
}
?>
