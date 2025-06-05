<?php
// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'simdal_db');

// Membuat koneksi
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Fungsi untuk mengamankan input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk generate kode otomatis
function generateKode($prefix, $table, $field) {
    global $pdo;
    
    $query = "SELECT MAX(CAST(SUBSTRING($field, " . (strlen($prefix) + 1) . ") AS UNSIGNED)) as max_num FROM $table WHERE $field LIKE '$prefix%'";
    $stmt = $pdo->query($query);
    $result = $stmt->fetch();
    
    $next_num = ($result['max_num'] ?? 0) + 1;
    return $prefix . str_pad($next_num, 4, '0', STR_PAD_LEFT);
}

// Fungsi untuk format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi untuk format tanggal Indonesia
function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Start session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
