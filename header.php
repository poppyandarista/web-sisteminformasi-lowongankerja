<?php


// Fungsi untuk mendeteksi halaman aktif
function isPageActive($pageName)
{
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $pageName;
}
?>
<style>
    .dropdown-menu-right {
        right: 0;
        left: auto;
    }

    .dropdown-item {
        padding: 8px 16px;
        font-size: 14px;
        color: #333;
    }

    .dropdown-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }

    .dropdown-divider {
        margin: 8px 0;
        border-top: 1px solid #e9ecef;
    }

    .nav-link.dropdown-toggle {
        cursor: pointer;
    }

    .nav-link.dropdown-toggle:hover {
        color: #007bff !important;
    }

    /* Active state untuk menu items */
    .nav-item.active .nav-link {
        color: #007bff !important;
        font-weight: 600;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dropdown-menu {
            position: static;
            float: none;
            width: 100%;
            border: none;
            box-shadow: none;
        }

        .dropdown-item {
            padding-left: 30px;
        }
    }
</style>
<header id="home" class="hero-area">
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg fixed-top scrolling-navbar">
        <div class="container">
            <div class="theme-header clearfix">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-navbar"
                        aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        <span class="lni-menu"></span>
                        <span class="lni-menu"></span>
                    </button>
                    <a href="index.php" class="navbar-brand"><img src="assets/img/logo2.png" alt="" /></a>
                </div>
                <div class="collapse navbar-collapse" id="main-navbar">
                    <ul class="navbar-nav mr-auto w-100 justify-content-end">
                        <li class="nav-item <?php echo isPageActive('index.php') ? 'active' : ''; ?>">
                            <a class="nav-link" href="index.php"> Cari Lowongan </a>
                        </li>
                        <li class="nav-item <?php echo isPageActive('jelajahi-perusahaan.php') ? 'active' : ''; ?>">
                            <a class="nav-link" href="jelajahi-perusahaan.php">
                                Jelajahi Perusahaan
                            </a>
                        </li>
                        <li class="nav-item <?php echo isPageActive('statuslamaran.php') ? 'active' : ''; ?>">
                            <a class="nav-link" href="statuslamaran.php"> Status Lamaran </a>
                        </li>
                        <li class="nav-item">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="lni-user"></i>
                                        <?php echo htmlspecialchars($_SESSION['username'] ?? $_SESSION['email'] ?? 'User'); ?>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                        <a class="dropdown-item" href="profile.php">
                                            <i class="lni-user"></i> Profil Saya
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="logout.php">
                                            <i class="lni-exit"></i> Keluar
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a class="nav-link <?php echo isPageActive('login.php') ? 'active' : ''; ?>"
                                    href="login.php">
                                    <i class="lni-user"></i> Masuk
                                </a>
                            <?php endif; ?>
                        </li>
                        <li class="button-group">
                            <a href="perusahaan.php" class="button btn btn-common">Untuk Perusahaan</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mobile-menu" data-logo="assets/img/logo2.png"></div>
    </nav>
    <!-- Navbar End -->
</header>