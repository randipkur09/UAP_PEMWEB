<?php
require_once 'config/koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit();
}

// Jika belum login, redirect ke halaman login
header('Location: pages/login.php');
exit();
?>
