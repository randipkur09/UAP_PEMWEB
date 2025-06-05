<!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.table-data').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                },
                "pageLength": 10,
                "responsive": true
            });
        });
        
        // Confirm delete
        function confirmDelete(url, message = 'Data ini akan dihapus permanen!') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
        
        // Show success message
        <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>',
            timer: 3000,
            showConfirmButton: false
        });
        <?php endif; ?>
        
        // Show error message
        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',
            timer: 3000,
            showConfirmButton: false
        });
        <?php endif; ?>
    </script>
</body>
</html>
