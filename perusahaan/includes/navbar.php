<?php
// includes/navbar.php
// Session sudah dimulai di file masing-masing

// Base URL untuk akses gambar
$base_url = 'http://localhost/web-linkup-loker/';

// Ambil data perusahaan dari session atau database
$company_id = $_SESSION['company_id'] ?? null;
$company_name = $_SESSION['company_name'] ?? 'Perusahaan';
$company_logo = null;

if ($company_id) {
    // Coba ambil logo dari database
    require_once 'koneksi_perusahaan.php';
    $perusahaan_data = $db->getPerusahaanById($company_id);
    if ($perusahaan_data && !empty($perusahaan_data['logo_perusahaan'])) {
        $company_logo = $perusahaan_data['logo_perusahaan'];
    }
}

// Tentukan path logo
$logo_path = null;
if ($company_logo) {
    // Cek apakah file logo ada di folder adminpanel
    $admin_logo_path = '../adminpanel/src/images/company/' . $company_logo;
    if (file_exists($admin_logo_path)) {
        $logo_path = $base_url . 'adminpanel/src/images/company/' . $company_logo;
    } else {
        // Cek di folder uploads/logo
        $upload_logo_path = 'uploads/logo/' . $company_logo;
        if (file_exists($upload_logo_path)) {
            $logo_path = $upload_logo_path;
        }
    }
}

// Inisial untuk avatar jika tidak ada logo
$initial = strtoupper(substr($company_name, 0, 1));
?>
<style>
    /* ========== NAVBAR STYLES ========== */
    .top-navbar {
        background: white;
        padding: 12px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 99;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #1e293b;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .menu-toggle:hover {
        background: #f1f5f9;
    }

    .company-info {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-left: auto;
        /* INI PENTING: mendorong ke kanan */
    }

    .company-name {
        font-weight: 600;
        color: #0f172a;
        font-size: 0.9rem;
        padding: 8px 0;
    }

    .avatar-dropdown {
        position: relative;
        cursor: pointer;
    }

    .avatar-icon {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #4f46e5;
        transition: all 0.2s;
        overflow: hidden;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .avatar-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-icon:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: 52px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        width: 200px;
        display: none;
        z-index: 200;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        animation: dropdownFadeIn 0.2s ease;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: #1e293b;
        text-decoration: none;
        transition: 0.2s;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .dropdown-menu a:last-child {
        border-bottom: none;
    }

    .dropdown-menu a i {
        width: 20px;
        font-size: 1rem;
        color: #64748b;
    }

    .dropdown-menu a:hover {
        background: #f8fafc;
    }

    .dropdown-menu a:hover i {
        color: #2563eb;
    }

    /* Mobile & Tablet Responsive */
    @media (max-width: 1024px) {
        .top-navbar {
            padding: 10px 20px;
        }

        .menu-toggle {
            display: block;
        }

        .company-name {
            font-size: 0.85rem;
        }

        .avatar-icon {
            width: 38px;
            height: 38px;
        }

        .dropdown-menu {
            width: 180px;
            top: 48px;
        }
    }
</style>

<div class="top-navbar">
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>
    <div class="company-info">
        <span class="company-name">
            <i class="fas fa-building" style="color: #2563eb; margin-right: 6px; font-size: 0.8rem;"></i>
            <?php echo htmlspecialchars($company_name); ?>
        </span>
        <div class="avatar-dropdown">
            <div class="avatar-icon" id="avatarDropdownBtn">
                <?php if ($logo_path): ?>
                    <img src="<?php echo $logo_path; ?>" alt="Logo Perusahaan"
                        onerror="this.parentElement.innerHTML='<i class=\'fas fa-building\'></i>'; this.parentElement.style.background='linear-gradient(135deg, #eef2ff, #e0e7ff)';">
                <?php else: ?>
                    <i class="fas fa-building"></i>
                <?php endif; ?>
            </div>
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profil.php">
                    <i class="fas fa-user-circle"></i> Profil Saya
                </a>
                <a href="#" id="logoutDropdown">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Dropdown toggle
    document.addEventListener('DOMContentLoaded', function () {
        const avatarBtn = document.getElementById('avatarDropdownBtn');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (avatarBtn && dropdownMenu) {
            avatarBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function () {
                dropdownMenu.classList.remove('show');
            });

            dropdownMenu.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        // Menu toggle for sidebar
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.querySelector('.sidebar');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function () {
                sidebar.classList.toggle('active');
            });
        }

        // Logout dropdown handler
        const logoutDropdown = document.getElementById('logoutDropdown');
        if (logoutDropdown) {
            logoutDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Close dropdown
                dropdownMenu.classList.remove('show');
                
                // Trigger logout modal (same as sidebar)
                const logoutModal = document.getElementById('logoutModal');
                if (logoutModal) {
                    logoutModal.style.display = 'flex';
                }
            });
        }
    });
</script>