<!-- Footer -->
    <footer class="mt-5 py-4 text-center text-muted">
        <hr>
        <p class="mb-0">
            &copy; <?= date('Y') ?> Sistem Inventaris. 
            Dibuat dengan <i class="fas fa-heart text-danger"></i> 
            untuk Tugas Akhir
        </p>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Sweet Alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    <script>
        // Inisialisasi DataTables
        $(document).ready(function() {
            $('.table-datatable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                "pageLength": 10,
                "ordering": true,
                "searching": true
            });
        });
        
        // Hamburger Menu Toggle
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        if (hamburgerBtn) {
            // Toggle sidebar when hamburger button clicked
            hamburgerBtn.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                hamburgerBtn.classList.toggle('active');
                
                // Prevent body scroll when sidebar is open on mobile
                if (sidebar.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
            
            // Close sidebar when overlay clicked
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                hamburgerBtn.classList.remove('active');
                document.body.style.overflow = '';
            });
            
            // Close sidebar when navigation link clicked (on mobile)
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Only auto-close on mobile/tablet
                    if (window.innerWidth <= 1024) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                        hamburgerBtn.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1024) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    hamburgerBtn.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }
        
        // Konfirmasi hapus dengan SweetAlert
        function confirmDelete(url, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus ${nama}?`,
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
        
        // Auto hide alert setelah 5 detik
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>

</body>
</html>