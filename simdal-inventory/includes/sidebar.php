<div class="col-md-3 col-lg-2 px-0">
    <div class="sidebar d-flex flex-column p-3">
        <ul class="nav nav-pills flex-column mb-auto">

            <!-- Dashboard -->
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <!-- Data Sendal -->
            <li class="nav-item">
                <a href="sendal/" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/sendal/') !== false ? 'active' : ''; ?>">
                    <i class="bi bi-box"></i> Data Sendal
                </a>
            </li>

            <!-- Kategori -->
            <li class="nav-item">
                <a href="kategori/" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/kategori/') !== false ? 'active' : ''; ?>">
                    <i class="bi bi-tags"></i> Kategori
                </a>
            </li>

            <!-- Supplier -->
            <li class="nav-item">
                <a href="supplier/" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/supplier/') !== false ? 'active' : ''; ?>">
                    <i class="bi bi-truck"></i> Supplier
                </a>
            </li>

            <!-- Transaksi -->
            <li class="nav-item">
                <a href="transaksi/" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/transaksi/') !== false ? 'active' : ''; ?>">
                    <i class="bi bi-arrow-left-right"></i> Transaksi
                </a>
            </li>

            <!-- Pengguna (admin only) -->
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <li class="nav-item">
                <a href="pengguna/" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/pengguna/') !== false ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i> Pengguna
                </a>
            </li>
            <?php endif; ?>

            <hr class="text-white">

            <!-- Laporan -->
            <li class="nav-item">
                <a href="laporan/" class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/laporan/') !== false ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-text"></i> Laporan
                </a>
            </li>
        </ul>
    </div>
</div>
