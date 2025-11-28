<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="keywords" content="Bootstrap, Landing page, Template, Registration, Landing" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="author" content="UIdeck" />
    <title>Detail Perusahaan | LinkUp</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/icon-linkup2.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/line-icons.css" />
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="assets/css/owl.theme.default.css" />
    <link rel="stylesheet" href="assets/css/slicknav.min.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="stylesheet" href="assets/css/responsive.css" />

    <style>
        :root {
            --primary: #3d8eff;
            --primary-light: #5da1ff;
            --primary-dark: #2a6fd1;
            --gray-light: #f8f9fa;
            --gray: #6c757d;
            --gray-dark: #343a40;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .company-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 60px 0 40px;
            margin-top: 70px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .company-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"></polygon></svg>');
            background-size: cover;
        }

        .company-header .inner-header {
            position: relative;
            z-index: 2;
        }

        .company-logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 20px;
        }

        .company-logo {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .company-logo img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
        }

        .company-info h1 {
            margin-bottom: 5px;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .company-info .location {
            margin-bottom: 15px;
            font-size: 1rem;
            opacity: 0.9;
        }

        .company-info .location i {
            margin-right: 5px;
        }

        .company-nav {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .company-nav .nav-link {
            color: #333;
            font-weight: 500;
            padding: 15px 20px;
            border-bottom: 3px solid transparent;
        }

        .company-nav .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
        }

        .company-content {
            padding: 40px 0;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            min-height: 60vh;
        }

        .company-details {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 30px;
        }

        .company-details h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #2d3748;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .info-table {
            width: 100%;
        }

        .info-table tr {
            border-bottom: 1px solid #f5f5f5;
        }

        .info-table td {
            padding: 10px 5px;
            font-size: 0.95rem;
        }

        .info-table td:first-child {
            font-weight: 600;
            width: 40%;
            color: #4a5568;
        }

        .description-section {
            line-height: 1.7;
            color: #4a5568;
            font-size: 0.95rem;
        }

        .stats-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 30px;
        }

        .stats-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .stats-card .label {
            color: #666;
            font-size: 0.9rem;
        }

        .contact-info {
            background: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 30px;
        }

        .contact-info h4 {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #2d3748;
            font-size: 1.1rem;
        }

        .contact-item {
            display: flex;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .contact-item i {
            margin-right: 15px;
            color: var(--primary);
            width: 20px;
            text-align: center;
        }

        /* Job Card Styles */
        .job-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #f0f2f5;
            margin-bottom: 0;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .job-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .job-logo {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 20px;
            border: 1px solid #f0f2f5;
            padding: 5px;
        }

        .job-info h4 {
            margin: 0 0 5px;
            font-size: 16px;
            color: #2d3748;
            font-weight: 600;
        }

        .job-info p {
            margin: 0 0 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .job-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 10px 0;
        }

        .job-meta span {
            display: flex;
            align-items: center;
            color: #6c757d;
            font-size: 0.85rem;
        }

        .job-meta i {
            margin-right: 5px;
            color: #a0aec0;
        }

        .job-description {
            margin: 12px 0;
            color: #4a5568;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-primary {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .badge-success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-info {
            background-color: #e1f5fe;
            color: #0288d1;
        }

        .badge-warning {
            background-color: #fff8e1;
            color: #f57c00;
        }

        .badge-danger {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .badge-secondary {
            background-color: #f5f5f5;
            color: #757575;
        }

        .badge-dark {
            background-color: #e0e0e0;
            color: #424242;
        }

        .time-posted {
            color: #a0aec0;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
        }

        .time-posted i {
            margin-right: 5px;
        }

        .btn-apply {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-apply:hover {
            background-color: var(--primary-dark);
            color: white;
        }

        .no-jobs {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .company-logo-container {
                margin-bottom: 20px;
            }

            .company-info {
                text-align: center;
            }

            .job-header {
                flex-direction: column;
            }

            .job-logo {
                margin-bottom: 15px;
            }

            .job-meta {
                flex-direction: column;
                gap: 8px;
            }

            .job-footer {
                flex-wrap: wrap;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Header Section Start -->
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
                            <span class="lni-menu"></span>
                        </button>
                        <a href="index.php" class="navbar-brand"><img src="assets/img/icon-linkup.png" alt="" /></a>
                    </div>
                    <div class="collapse navbar-collapse" id="main-navbar">
                        <ul class="navbar-nav mr-auto w-100 justify-content-end">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php"> Cari Lowongan </a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="perusahaan.php">
                                    Jelajahi Perusahaan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="contact.php"> Status Lamaran </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Masuk</a>
                            </li>
                            <li class="button-group">
                                <a href="post-job.php" class="button btn btn-common">Untuk Perusahaan</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mobile-menu" data-logo="assets/img/icon-linkup.png"></div>
        </nav>
        <!-- Navbar End -->
    </header>
    <!-- Header Section End -->

    <!-- Company Header Section -->
    <div class="company-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-sm-12">
                    <div class="company-logo-container">
                        <div class="company-logo">
                            <!-- Logo perusahaan -->
                            <img src="assets/img/product/img2.png" alt="Logo Perusahaan">
                        </div>
                        <div class="company-info">
                            <h1>[nama_perusahaan]</h1>
                            <div class="location">
                                <i class="lni-map-marker"></i>
                                <span>[kota_perusahaan], [provinsi_perusahaan]</span>
                            </div>
                            <div class="tags">
                                <span class="badge badge-light">Lebih dari [jumlah_pelamar] pelamar</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Navigation -->
    <div class="company-nav">
        <div class="container">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-tab="about">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-tab="jobs">Pekerjaan</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Company Content Section -->
    <section class="company-content">
        <div class="container">
            <!-- About Tab Content -->
            <div id="about-tab" class="tab-content">
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="company-details">
                            <h3>Sekilas tentang perusahaan</h3>
                            <table class="info-table">
                                <tr>
                                    <td>Industri</td>
                                    <td>Mining, Minerals & Metals</td>
                                </tr>
                                <tr>
                                    <td>Lowongan kerja tersedia saat ini</td>
                                    <td>[jumlah_lowongan] lowongan</td>
                                </tr>
                            </table>
                        </div>

                        <div class="company-details">
                            <h3>Deskripsi Perusahaan</h3>
                            <div class="description-section">
                                <p>[deskripsi_lowongan]</p>

                                <p>[deskripsi_lowongan]</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-5">
                        <div class="stats-card">
                            <div class="number">10,000+</div>
                            <div class="label">Lowongan saat ini</div>
                        </div>

                        <div class="contact-info">
                            <h4>Informasi Kontak</h4>
                            <div class="contact-item">
                                <i class="lni-envelope"></i>
                                <div>
                                    <strong>Email</strong><br>
                                    <span>[email_perusahaan]</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="lni-phone"></i>
                                <div>
                                    <strong>Telepon</strong><br>
                                    <span>[nohp_perusahaan]</span>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="lni-map-marker"></i>
                                <div>
                                    <strong>Alamat</strong><br>
                                    <span>[kota_perusahaan], [provinsi_perusahaan]</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jobs Tab Content -->
            <div id="jobs-tab" class="tab-content" style="display: none;">
                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="mb-4" style="font-size: 1.5rem; font-weight: 600;">Lowongan Aktif saat ini</h3>

                        <div id="jobListings" class="row" style="row-gap: 20px;">
                            <!-- Job cards will be dynamically loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section Start -->
    <footer>
        <!-- Footer Area Start -->
        <section class="footer-Content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-xs-12">
                        <div class="widget">
                            <div class="footer-logo">
                                <img src="assets/img/icon-linkup.png" alt="" />
                            </div>
                            <div class="textwidget">
                                <p>
                                    Platform yang menghubungkan pencari kerja berbakat dengan perusahaan terbaik.
                                    Temukan karier impian
                                    Anda sekarang.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4 col-xs-12">
                        <div class="widget">
                            <h3 class="block-title">Quick Links</h3>
                            <ul class="menu">
                                <li><a href="index.php">Cari Lowongan</a></li>
                                <li><a href="perusahaan.php">Perusahaan</a></li>
                                <li><a href="statuslamaran.php">Status Lamaran</a></li>
                            </ul>
                            <ul class="menu">
                                <li><a href="login.php">Masuk</a></li>
                                <li><a href="register.php">Daftar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer area End -->

        <!-- Copyright Start  -->
        <div id="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="site-info text-center">
                            <p>
                                © 2025 LinkUp. All rights reserved
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->
    </footer>
    <!-- Footer Section End -->

    <!-- Go To Top Link -->
    <a href="#" class="back-to-top">
        <i class="lni-arrow-up"></i>
    </a>

    <!-- Preloader -->
    <div id="preloader">
        <div class="loader" id="loader-1"></div>
    </div>
    <!-- End Preloader -->

    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="assets/js/jquery-min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.slicknav.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/form-validator.min.js"></script>
    <script src="assets/js/contact-form-script.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        // Sample job data for PT Freeport Indonesia
        const jobData = [
            {
                id: 1,
                title: "[judul_lowongan]",
                company: "[nama_perusahaan]",
                location: "[kota_lowongan]",
                type: "[waktukerja]",
                salary: "[gaji]",
                description: "[deskripsi_lowongan]",
                category: "kategori",
                logo: "https://via.placeholder.com/60/4e73df/ffffff",
                posted: "[tanggal_tutup]",
            },
            {
                id: 2,
                title: "[judul_lowongan]",
                company: "[nama_perusahaan]",
                location: "[kota_lowongan]",
                type: "[waktukerja]",
                salary: "[gaji]",
                description: "[deskripsi_lowongan]",
                category: "kategori",
                logo: "https://via.placeholder.com/60/4e73df/ffffff",
                posted: "[tanggal_tutup]",
            },
            {
                id: 3,
                title: "[judul_lowongan]",
                company: "[nama_perusahaan]",
                location: "[kota_lowongan]",
                type: "[waktukerja]",
                salary: "[gaji]",
                description: "[deskripsi_lowongan]",
                category: "kategori",
                logo: "https://via.placeholder.com/60/4e73df/ffffff",
                posted: "[tanggal_tutup]",
            },
            {
                id: 4,
                title: "[judul_lowongan]",
                company: "[nama_perusahaan]",
                location: "[kota_lowongan]",
                type: "[waktukerja]",
                salary: "[gaji]",
                description: "[deskripsi_lowongan]",
                category: "kategori",
                logo: "https://via.placeholder.com/60/4e73df/ffffff",
                posted: "[tanggal_tutup]",
            },
            {
                id: 5,
                title: "[judul_lowongan]",
                company: "[nama_perusahaan]",
                location: "[kota_lowongan]",
                type: "[waktukerja]",
                salary: "[gaji]",
                description: "[deskripsi_lowongan]",
                category: "kategori",
                logo: "https://via.placeholder.com/60/4e73df/ffffff",
                posted: "[tanggal_tutup]",
            },
            {
                id: 6,
                title: "[judul_lowongan]",
                company: "[nama_perusahaan]",
                location: "[kota_lowongan]",
                type: "[waktukerja]",
                salary: "[gaji]",
                description: "[deskripsi_lowongan]",
                category: "kategori",
                logo: "https://via.placeholder.com/60/4e73df/ffffff",
                posted: "[tanggal_tutup]",
            }
        ];

        // Initialize the page
        document.addEventListener("DOMContentLoaded", function () {
            renderJobCards();
            setupEventListeners();
        });

        // Render job cards
        function renderJobCards() {
            const jobListings = document.getElementById("jobListings");
            jobListings.innerHTML = "";

            if (jobData.length === 0) {
                jobListings.innerHTML = `
            <div class="col-12">
              <div class="no-jobs">
                <i class="lni-briefcase" style="font-size: 48px; margin-bottom: 15px;"></i>
                <h4>Tidak ada lowongan aktif saat ini</h4>
                <p>Silakan periksa kembali di lain waktu</p>
              </div>
            </div>
          `;
                return;
            }

            jobData.forEach((job) => {
                const badgeClass = getBadgeClass(job.category);

                const jobCard = document.createElement("div");
                jobCard.className = "col-lg-6 col-md-6 col-12";
                jobCard.innerHTML = `
            <div class="job-card">
              <div class="job-header">
                <img
                  src="assets/img/product/img1.png"
                  alt="Company Logo"
                  class="job-logo"
                  style="width: 50px; height: 50px; object-fit: contain;"
                />
                <div class="job-info">
                  <h4>${job.title}</h4>
                  <p>${job.company}</p>
                  <div class="job-meta">
                    <span><i class="lni-map-marker"></i> ${job.location}</span>
                    <span><i class="lni-briefcase"></i> ${job.type}</span>
                    <span><i class="lni-coin"></i> ${job.salary}</span>
                  </div>
                </div>
              </div>
              <div class="job-description">
                <p>${job.description}</p>
              </div>
              <div class="job-footer">
                <span class="badge ${badgeClass}">${getCategoryName(job.category)}</span>
                <span class="time-posted"><i class="lni-timer"></i> ${job.posted}</span>
                <button class="btn btn-apply">Lamar Sekarang</button>
              </div>
            </div>
          `;
                jobListings.appendChild(jobCard);
            });
        }

        // Setup event listeners
        function setupEventListeners() {
            // Tab navigation
            document.querySelectorAll('.company-nav .nav-link').forEach(tab => {
                tab.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Update active tab
                    document.querySelectorAll('.company-nav .nav-link').forEach(t => {
                        t.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Show corresponding content
                    const tabName = this.getAttribute('data-tab');
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.style.display = 'none';
                    });
                    document.getElementById(`${tabName}-tab`).style.display = 'block';
                });
            });

            // Apply buttons
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-apply')) {
                    alert('Anda akan diarahkan ke halaman lamaran pekerjaan');
                    // In a real implementation, this would redirect to the application page
                }
            });
        }

        // Get badge class based on category
        function getBadgeClass(category) {
            switch (category) {
                case "mining":
                    return "badge-primary";
                case "geology":
                    return "badge-success";
                case "engineering":
                    return "badge-info";
                case "environment":
                    return "badge-warning";
                case "safety":
                    return "badge-danger";
                case "hr":
                    return "badge-secondary";
                default:
                    return "badge-primary";
            }
        }

        // Get category name
        function getCategoryName(category) {
            switch (category) {
                case "mining":
                    return "Pertambangan";
                case "geology":
                    return "Geologi";
                case "engineering":
                    return "Teknik";
                case "environment":
                    return "Lingkungan";
                case "safety":
                    return "K3";
                case "hr":
                    return "SDM";
                default:
                    return "Lainnya";
            }
        }
    </script>
</body>

</html>