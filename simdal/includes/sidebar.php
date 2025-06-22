<div class="col-md-3 col-lg-2 px-0">
    <div class="sidebar d-flex flex-column p-3">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="/simdal/pages/dashboard.php" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/simdal/pages/sendal/" class="nav-link">
                    <i class="bi bi-box"></i> Data Sendal
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/simdal/pages/kategori/" class="nav-link">
                    <i class="bi bi-tags"></i> Kategori
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/simdal/pages/supplier/" class="nav-link">
                    <i class="bi bi-truck"></i> Supplier
                </a>
            </li>
            
            <li class="nav-item">
                <a href="/simdal/pages/transaksi/" class="nav-link">
                    <i class="bi bi-arrow-left-right"></i> Transaksi
                </a>
            </li>
            
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <li class="nav-item">
                <a href="/simdal/pages/pengguna/" class="nav-link">
                    <i class="bi bi-people"></i> Pengguna
                </a>
            </li>
            <?php endif; ?>
            
            <hr class="text-white">
            
            <li class="nav-item">
                <a href="/simdal/pages/laporan/" class="nav-link">
                    <i class="bi bi-file-earmark-text"></i> Laporan
                </a>
            </li>
        </ul>
    </div>
</div>
