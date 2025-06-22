-- Database: simdal_db
CREATE DATABASE IF NOT EXISTS simdal_db;
USE simdal_db;

-- Tabel Pengguna
CREATE TABLE pengguna (
    id_pengguna INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'staff') DEFAULT 'staff',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori
CREATE TABLE kategori (
    id_kategori INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Supplier
CREATE TABLE supplier (
    id_supplier INT PRIMARY KEY AUTO_INCREMENT,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    kontak_person VARCHAR(100),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Sendal
CREATE TABLE sendal (
    id_sendal INT PRIMARY KEY AUTO_INCREMENT,
    kode_sendal VARCHAR(20) UNIQUE NOT NULL,
    nama_sendal VARCHAR(100) NOT NULL,
    id_kategori INT,
    id_supplier INT,
    ukuran VARCHAR(10),
    warna VARCHAR(30),
    harga_beli DECIMAL(10,2),
    harga_jual DECIMAL(10,2),
    stok_minimal INT DEFAULT 5,
    stok_tersedia INT DEFAULT 0,
    deskripsi TEXT,
    gambar VARCHAR(255),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori),
    FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier)
);

-- Tabel Transaksi
CREATE TABLE transaksi (
    id_transaksi INT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(20) UNIQUE NOT NULL,
    jenis_transaksi ENUM('masuk', 'keluar') NOT NULL,
    id_sendal INT,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10,2),
    total_harga DECIMAL(10,2),
    tanggal_transaksi DATE NOT NULL,
    keterangan TEXT,
    id_pengguna INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sendal) REFERENCES sendal(id_sendal),
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna)
);

-- Insert data default
INSERT INTO pengguna (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@simdal.com', 'admin'),
('staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Gudang', 'staff@simdal.com', 'staff');

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Sendal Pria', 'Koleksi sendal untuk pria'),
('Sendal Wanita', 'Koleksi sendal untuk wanita'),
('Sendal Anak', 'Koleksi sendal untuk anak-anak'),
('Sendal Kasual', 'Sendal untuk penggunaan sehari-hari'),
('Sendal Gunung', 'Sendal untuk aktivitas outdoor dan hiking');

INSERT INTO supplier (nama_supplier, alamat, telepon, email, kontak_person) VALUES
('CV Sandal Jaya', 'Jl. Industri No. 123, Bandung', '022-1234567', 'info@sandaljaya.com', 'Budi Santoso'),
('PT Alas Kaki Nusantara', 'Jl. Raya Bogor No. 456, Bogor', '0251-987654', 'sales@alaskaki.co.id', 'Siti Rahayu'),
('Toko Sendal Berkah', 'Jl. Pasar Baru No. 789, Jakarta', '021-5555666', 'berkah@gmail.com', 'Ahmad Wijaya');
