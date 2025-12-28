<!-- Logout Modal -->
<div class="logout-modal-overlay" id="logoutModal">
    <div class="logout-modal">
        <div class="logout-modal-header">
            <i class="fas fa-sign-out-alt"></i>
            <h5>Konfirmasi Logout</h5>
        </div>
        <div class="logout-modal-body">
            <p>Apakah Anda yakin ingin keluar dari sistem?</p>
        </div>
        <div class="logout-modal-footer">
            <button class="btn btn-logout-cancel" onclick="hideLogoutModal()">
                <i class="fas fa-times me-2"></i>Batal
            </button>
            <a href="../auth/logout.php" class="btn btn-logout-confirm">
                <i class="fas fa-check me-2"></i>Ya, Logout
            </a>
        </div>
    </div>
</div>

<!-- Footer -->
    <footer class="mt-5 py-4 text-center text-muted">
        <hr>
        <p class="mb-0">
            &copy; <?= date('Y') ?> Sistem Inventaris. 
            Dibuat dengan <i class="fas fa-heart text-danger"></i> 
            untuk Tugas Akhir
        </p>
    </footer>

    <!-- jQuery (Local) -->
    <script src="../assets/js/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS (Local) -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS (Local) -->
    <script src="../assets/js/dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Sweet Alert 2 (Local) -->
    <script src="../assets/js/sweetalert2.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Disable DataTables error alerts (suppress annoying popups)
        $.fn.dataTable.ext.errMode = 'none';
        
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
        
        // Logout Modal Functions
        function showLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function hideLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        // Close modal when clicking overlay
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('logoutModal');
            if (e.target === modal) {
                hideLogoutModal();
            }
        });
        
        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideLogoutModal();
            }
        });
        
        // Auto hide alert setelah 5 detik
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>

</body>
</html>