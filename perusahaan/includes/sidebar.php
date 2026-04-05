<?php
// includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-logo" style="text-align: center;">
        <img src="../assets/img/logo3.png" alt="LinkUp" style="width: 100%; max-width: 100px; height: auto;">
    </div>
    <ul class="sidebar-nav">
        <li class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li class="<?php echo $current_page == 'lowongan.php' ? 'active' : ''; ?>">
            <a href="lowongan.php"><i class="fas fa-list-ul"></i> Lowongan Saya</a>
        </li>
        <li class="<?php echo $current_page == 'pelamar.php' ? 'active' : ''; ?>">
            <a href="pelamar.php"><i class="fas fa-users"></i> Data Pelamar</a>
        </li>
        <li class="<?php echo $current_page == 'lamaran.php' ? 'active' : ''; ?>">
            <a href="lamaran.php"><i class="fas fa-envelope-open-text"></i> Lamaran Masuk</a>
        </li>
        <li class="<?php echo $current_page == 'profil.php' ? 'active' : ''; ?>">
            <a href="profil.php"><i class="fas fa-building"></i> Profil Perusahaan</a>
        </li>
        <li><a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- Custom Logout Confirmation Modal -->
<div id="logoutModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header" style="background: #fef2f2; border-bottom: 1px solid #fecaca;">
            <h3 style="color: #dc2626; margin: 0;">
                <i class="fas fa-sign-out-alt"></i> Konfirmasi Logout
            </h3>
            <button class="modal-close" style="color: #dc2626;">&times;</button>
        </div>
        <div class="modal-body" style="padding: 24px; text-align: center;">
            <div
                style="width: 60px; height: 60px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-sign-out-alt" style="color: #dc2626; font-size: 1.5rem;"></i>
            </div>
            <h4 style="color: #1f2937; margin-bottom: 8px;">Apakah Anda yakin?</h4>
            <p style="color: #6b7280; margin: 0;">Anda akan keluar dari sistem LinkUp. Semua data yang belum tersimpan
                akan hilang.</p>
        </div>
        <div class="modal-footer"
            style="padding: 20px 24px; border-top: 1px solid #f3f4f6; display: flex; gap: 12px; justify-content: flex-end;">
            <button class="btn-secondary modal-cancel"
                style="background: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db;">
                <i class="fas fa-times"></i> Batal
            </button>
            <button class="btn-primary modal-logout" style="background: #dc2626; border-color: #dc2626;">
                <i class="fas fa-sign-out-alt"></i> Ya, Keluar
            </button>
        </div>
    </div>
</div>

<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modal-header {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #fecaca;
    }

    .btn-primary,
    .btn-secondary {
        padding: 10px 16px;
        border-radius: 10px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: 'Poppins', sans-serif;
    }

    .btn-primary:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }
</style>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('logoutModal').style.display = 'flex';
    });

    document.querySelector('.modal-cancel').addEventListener('click', function () {
        document.getElementById('logoutModal').style.display = 'none';
    });

    document.querySelector('.modal-close').addEventListener('click', function () {
        document.getElementById('logoutModal').style.display = 'none';
    });

    document.querySelector('.modal-logout').addEventListener('click', function () {
        // Tampilkan loading pada tombol
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Keluar...';
        this.disabled = true;
        document.querySelector('.modal-cancel').disabled = true;

        // Redirect ke logout setelah delay
        setTimeout(() => {
            window.location.href = 'logout.php';
        }, 1000);
    });

    // Close modal saat klik di luar
    document.getElementById('logoutModal').addEventListener('click', function (e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });

    // Mencegah back button setelah logout
    window.addEventListener('pageshow', function(event) {
        // Check if page is loaded from cache (back button)
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            // Force page reload to check session properly
            window.location.reload();
        }
    });

    // Additional check on page load
    window.addEventListener('load', function() {
        // Check session immediately on load
        fetch('check_session.php')
            .then(response => response.json())
            .then(data => {
                if (!data.logged_in) {
                    window.location.href = 'login.php?message=Silakan login terlebih dahulu';
                }
            })
            .catch(error => console.log('Session check error:', error));
        
        // Check session status periodically
        setInterval(function() {
            fetch('check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.logged_in) {
                        window.location.href = 'login.php?message=Sesi telah berakhir, silakan login kembali';
                    }
                })
                .catch(error => console.log('Session check error:', error));
        }, 30000); // Check every 30 seconds
    });
</script>