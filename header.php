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
        transition: all 0.3s ease;
    }

    .dropdown-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    .dropdown-item:hover {
        background-color: #007bff !important;
        color: #ffffff !important;
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

    /* Button Untuk Perusahaan */
    .button-group {
        display: flex;
        align-items: center;
        margin-left: 15px;
    }

    .button-group .btn-common {
        background: #007bff;
        color: white;
        padding: 8px 20px;
        border-radius: 30px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        font-weight: 500;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        font-size: 14px;
        line-height: 1.5;
    }

    .button-group .btn-common:hover {
        background: #0056b3 !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .button-group .btn-common:active {
        transform: translateY(0);
    }

    /* Perbaikan navbar Bootstrap - reset style bawaan */
    .navbar {
        padding: 0.5rem 1rem;
    }

    .navbar-nav {
        align-items: center;
    }

    .navbar-nav .nav-item {
        display: flex;
        align-items: center;
    }

    .navbar-nav .nav-link {
        padding: 0.5rem 1rem;
        line-height: 1.5;
    }

    /* Pastikan semua item dalam satu baris dan sejajar */
    .navbar-collapse {
        align-items: center;
    }

    /* Perbaikan untuk dropdown toggle */
    .dropdown-toggle::after {
        vertical-align: middle;
    }

    /* Perbaikan untuk tombol agar sejajar sempurna */
    .btn-common {
        vertical-align: middle;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dropdown-menu {
            position: static;
            float: none;
            width: 100%;
            border: none;
            box-shadow: none;
            background: #f8f9fa;
        }

        .dropdown-item {
            padding-left: 30px;
        }

        .dropdown-item:hover {
            background-color: #007bff !important;
            color: #ffffff !important;
        }

        /* Perbaikan button di mobile */
        .button-group {
            display: inline-block;
            width: auto;
            text-align: center;
            margin: 0;
            margin-left: 15px;
        }

        .button-group .btn-common {
            display: inline-block;
            width: auto;
            text-align: center;
            background: #007bff !important;
            color: white !important;
            padding: 6px 16px;
            font-size: 13px;
            white-space: nowrap;
        }

        .button-group .btn-common:hover {
            background: #0056b3 !important;
            color: white !important;
            transform: translateY(-2px);
        }

        /* Perbaikan navbar untuk mobile */
        .navbar-nav {
            flex-direction: row;
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 10px;
            align-items: center;
        }

        .navbar-nav .nav-item {
            flex-shrink: 0;
        }

        /* Perbaikan nav link di mobile */
        .navbar-nav .nav-link {
            padding: 10px 15px;
            display: block;
            white-space: nowrap;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff !important;
            background-color: rgba(0, 123, 255, 0.1);
        }

        /* Perbaikan dropdown toggle di mobile */
        .dropdown-toggle::after {
            float: right;
            margin-top: 8px;
        }
    }

    /* Untuk tablet (768px - 1024px) */
    @media (min-width: 769px) and (max-width: 1024px) {
        .button-group .btn-common {
            padding: 6px 16px;
            font-size: 13px;
            white-space: nowrap;
        }

        .button-group .btn-common:hover {
            background: #0056b3 !important;
            color: white !important;
            transform: translateY(-2px);
        }

        .dropdown-item:hover {
            background-color: #007bff !important;
            color: #ffffff !important;
        }
    }

    /* Untuk layar sangat kecil (max 480px) */
    @media (max-width: 480px) {
        .button-group .btn-common {
            padding: 5px 12px;
            font-size: 12px;
        }

        .navbar-nav .nav-link {
            padding: 8px 12px;
            font-size: 13px;
        }
    }

    /* Untuk laptop dan desktop - perbaikan utama */
    @media (min-width: 992px) {
        .navbar {
            padding-top: 0;
            padding-bottom: 0;
        }

        .navbar-nav {
            align-items: center;
        }

        .navbar-nav .nav-item {
            display: inline-flex;
            align-items: center;
        }

        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
            line-height: 1.5;
        }

        .button-group {
            margin-left: 10px;
            display: inline-flex;
            align-items: center;
        }

        .button-group .btn-common {
            padding: 6px 18px;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        /* Pastikan semua item dalam navbar memiliki tinggi yang sama */
        .navbar .container,
        .navbar .theme-header,
        .navbar-collapse,
        .navbar-nav {
            height: 100%;
        }

        /* Reset margin/padding yang mungkin mengganggu */
        .navbar-header {
            display: flex;
            align-items: center;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            padding: 0;
        }

        .navbar-brand img {
            max-height: 40px;
            width: auto;
        }
    }

    /* Untuk layar sentuh (touch devices) */
    @media (hover: hover) and (pointer: fine) {

        /* Khusus untuk device dengan mouse */
        .button-group .btn-common:hover {
            background: #0056b3 !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
    }

    /* Untuk touch screen, pastikan hover tetap terlihat */
    @media (hover: none) and (pointer: coarse) {
        .button-group .btn-common:active {
            background: #0056b3 !important;
            color: white !important;
            transform: scale(0.98);
        }

        .dropdown-item:active {
            background-color: #007bff !important;
            color: #ffffff !important;
        }
    }
</style>
<header id="home" class="hero-area">
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg fixed-top scrolling-navbar">
        <div class="container">
            <div class="theme-header clearfix w-100">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header d-flex align-items-center">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-navbar"
                        aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        <span class="lni-menu"></span>
                        <span class="lni-menu"></span>
                    </button>
                    <a href="index.php" class="navbar-brand d-flex align-items-center"><img src="assets/img/logo2.png"
                            alt="" /></a>
                </div>
                <div class="collapse navbar-collapse" id="main-navbar">
                    <ul class="navbar-nav ml-auto align-items-center">
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
                            <a href="perusahaan.php" class="button btn-common">Untuk Perusahaan</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mobile-menu" data-logo="assets/img/logo2.png"></div>
    </nav>
    <!-- Navbar End -->
</header>

<!-- Tambahkan jQuery dan Bootstrap JS jika belum ada -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    // Untuk memastikan dropdown bekerja di mobile
    $(document).ready(function () {
        // Inisialisasi dropdown
        $('.dropdown-toggle').dropdown();

        // Menutup dropdown saat klik di luar
        $(document).on('click', function (event) {
            if (!$(event.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });
    });
</script>