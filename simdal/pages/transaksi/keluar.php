<?php
require_once '../../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil data sendal yang memiliki stok
$stmt = $pdo->query("SELECT * FROM sendal WHERE status = 'aktif' AND stok_tersedia > 0 ORDER BY nama_sendal ASC");
$sendal = $stmt->fetchAll();

// Generate kode transaksi
$kode_transaksi = generateKode('TK', 'transaksi', 'kode_transaksi');

$page_title = 'Transaksi Barang Keluar';
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-danger">Transaksi Barang Keluar</h2>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="../../actions/transaksi/simpan_keluar.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="kode_transaksi" class="form-label">Kode Transaksi</label>
                                                <input type="text" class="form-control" id="kode_transaksi" name="kode_transaksi" value="<?php echo $kode_transaksi; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tanggal_transaksi" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="id_sendal" class="form-label">Pilih Sendal <span class="text-danger">*</span></label>
                                        <select class="form-select" id="id_sendal" name="id_sendal" required>
                                            <option value="">-- Pilih Sendal --</option>
                                            <?php foreach ($sendal as $item): ?>
                                            <option value="<?php echo $item['id_sendal']; ?>" 
                                                    data-harga="<?php echo $item['harga_jual']; ?>" 
                                                    data-stok="<?php echo $item['stok_tersedia']; ?>">
                                                <?php echo $item['kode_sendal'] . ' - ' . $item['nama_sendal'] . ' (' . $item['ukuran'] . ', ' . $item['warna'] . ') - Stok: ' . $item['stok_tersedia']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" required>
                                                <div class="form-text">Stok tersedia: <span id="stok-info">-</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="harga_satuan" class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" min="0" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="total_harga" class="form-label">Total Harga</label>
                                                <input type="number" class="form-control" id="total_harga" name="total_harga" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="index.php" class="btn btn-secondary me-md-2">Batal</a>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-save"></i> Simpan Transaksi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendalSelect = document.getElementById('id_sendal');
    const jumlahInput = document.getElementById('jumlah');
    const hargaInput = document.getElementById('harga_satuan');
    const totalInput = document.getElementById('total_harga');
    const stokInfo = document.getElementById('stok-info');
    
    let maxStok = 0;
    
    // Auto fill harga dan update stok info saat sendal dipilih
    sendalSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const harga = selectedOption.getAttribute('data-harga');
        const stok = selectedOption.getAttribute('data-stok');
        
        if (harga && stok) {
            hargaInput.value = harga;
            maxStok = parseInt(stok);
            stokInfo.textContent = stok;
            jumlahInput.max = maxStok;
            jumlahInput.value = '';
            totalInput.value = '';
        } else {
            hargaInput.value = '';
            maxStok = 0;
            stokInfo.textContent = '-';
            jumlahInput.max = '';
        }
    });
    
    // Validasi jumlah tidak melebihi stok
    jumlahInput.addEventListener('input', function() {
        const jumlah = parseInt(this.value);
        if (jumlah > maxStok) {
            this.value = maxStok;
            alert('Jumlah tidak boleh melebihi stok tersedia!');
        }
        calculateTotal();
    });
    
    // Hitung total otomatis
    function calculateTotal() {
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;
        totalInput.value = jumlah * harga;
    }
    
    hargaInput.addEventListener('input', calculateTotal);
});
</script>

<?php include '../../includes/footer.php'; ?>
