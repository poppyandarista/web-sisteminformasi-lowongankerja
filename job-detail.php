<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="keywords" content="Lowongan Kerja, Cari Pekerjaan, Portal Karir, Loker Terbaru, Pencarian Kerja" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="author" content="UIdeck" />
    <title>Detail Pekerjaan | LinkUp</title>

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

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            color: #333;
            min-height: 100vh;
        }

        .job-detail-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 50px 0 30px;
            margin-top: 70px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .job-detail-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"></polygon></svg>');
            background-size: cover;
        }

        .job-detail-header .inner-header {
            position: relative;
            z-index: 2;
        }

        .inner-header p {
            color: white;
        }

        /* Ukuran judul lowongan diperkecil */
        .job-detail-header h1 {
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 1.8rem;
            /* Diperkecil dari 2.5rem */
        }

        .job-detail-header p {
            opacity: 0.9;
            font-size: 1rem;
            /* Diperkecil dari 1.1rem */
            margin-bottom: 15px;
        }

        .job-meta-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .job-meta-item {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            font-size: 0.85rem;
            /* Diperkecil */
        }

        .job-meta-item i {
            margin-right: 6px;
            font-size: 0.9rem;
            /* Diperkecil */
        }

        .job-content {
            padding: 30px 0;
            /* Diperkecil padding */
        }

        .job-main-content {
            background: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 25px;
        }

        /* Ukuran heading konten utama diperkecil */
        .job-main-content h3 {
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eee;
            color: #2d3748;
            font-size: 1.3rem;
            /* Diperkecil */
        }

        /* Ukuran deskripsi pekerjaan diperkecil */
        .job-description {
            line-height: 1.6;
            color: #4a5568;
            margin-bottom: 20px;
            font-size: 0.95rem;
            /* Diperkecil */
        }

        .job-requirements,
        .job-responsibilities,
        .job-benefits {
            margin-bottom: 20px;
        }

        /* Ukuran heading sub-section diperkecil */
        .job-requirements h4,
        .job-responsibilities h4,
        .job-benefits h4 {
            font-size: 1.1rem;
            /* Diperkecil */
            margin-bottom: 12px;
            color: #2d3748;
        }

        .job-requirements ul,
        .job-responsibilities ul,
        .job-benefits ul {
            padding-left: 18px;
            color: #4a5568;
        }

        .job-requirements li,
        .job-responsibilities li,
        .job-benefits li {
            margin-bottom: 6px;
            line-height: 1.5;
            font-size: 0.9rem;
            /* Diperkecil */
        }

        .job-sidebar {
            background: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            margin-bottom: 25px;
        }

        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-logo {
            width: 70px;
            /* Diperkecil */
            height: 70px;
            /* Diperkecil */
            border-radius: 8px;
            object-fit: cover;
            margin: 0 auto 12px;
            border: 1px solid #f0f2f5;
            padding: 4px;
            background: white;
        }

        .company-name {
            font-size: 1.1rem;
            /* Diperkecil */
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
        }

        .company-info p {
            font-size: 0.85rem;
            /* Diperkecil */
            color: #6c757d;
            line-height: 1.4;
        }

        .job-actions {
            margin-top: 20px;
        }

        .btn-apply {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            /* Diperkecil */
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 12px;
        }

        .btn-apply:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(61, 142, 255, 0.3);
        }

        .btn-save {
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #e9ecef;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            /* Diperkecil */
            width: 100%;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-save:hover {
            background: #e9ecef;
            color: #495057;
        }

        .btn-save.saved {
            color: #e74c3c;
            border-color: #e74c3c;
        }

        .job-details-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .job-details-list li {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            /* Diperkecil */
        }

        .job-details-list li:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6c757d;
            font-weight: 500;
        }

        .detail-value {
            color: #2d3748;
            font-weight: 600;
        }

        .similar-jobs {
            margin-top: 30px;
        }

        .similar-jobs h3 {
            margin-bottom: 20px;
            color: #2d3748;
            font-size: 1.3rem;
            /* Diperkecil */
        }

        .job-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin: 0;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #f0f2f5;
            margin-bottom: 15px;
        }

        .job-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--hover-shadow);
        }

        .job-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .job-logo {
            width: 45px;
            /* Diperkecil */
            height: 45px;
            /* Diperkecil */
            border-radius: 6px;
            object-fit: cover;
            margin-right: 12px;
            border: 1px solid #f0f2f5;
            padding: 4px;
            background: white;
        }

        .job-info h4 {
            margin: 0 0 4px;
            font-size: 16px;
            /* Diperkecil */
            color: #2d3748;
        }

        .job-info p {
            margin: 0 0 8px;
            color: #6c757d;
            font-size: 0.85rem;
            /* Diperkecil */
        }

        .job-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 8px 0;
        }

        .job-meta span {
            display: flex;
            align-items: center;
            color: #6c757d;
            font-size: 0.8rem;
            /* Diperkecil */
        }

        .job-meta i {
            margin-right: 4px;
            color: #a0aec0;
            font-size: 0.85rem;
            /* Diperkecil */
        }

        .job-salary {
            font-weight: 600;
            color: #2563eb;
            margin: 8px 0;
            font-size: 0.9rem;
            /* Diperkecil */
        }

        .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .posted-time {
            font-size: 0.75rem;
            /* Diperkecil */
            color: #94a3b8;
        }

        .job-tag {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            /* Diperkecil */
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .job-tag.full-time {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .job-tag.part-time {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .job-tag.internship {
            background-color: #fff8e1;
            color: #f57c00;
        }

        @media (max-width: 768px) {
            .job-detail-header h1 {
                font-size: 1.5rem;
                /* Diperkecil untuk mobile */
            }

            .job-meta-info {
                gap: 8px;
            }

            .job-meta-item {
                font-size: 0.8rem;
                /* Diperkecil untuk mobile */
                padding: 5px 10px;
            }

            .job-header {
                flex-direction: column;
            }

            .job-logo {
                margin-bottom: 10px;
            }

            .job-meta {
                flex-direction: column;
                gap: 6px;
            }

            .job-footer {
                flex-wrap: wrap;
                gap: 8px;
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
                            <li class="nav-item">
                                <a class="nav-link" href="perusahaan.php">
                                    Jelajahi Perusahaan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="statuslamaran.php"> Status Lamaran </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Masuk</a>
                            </li>
                            <li class="button-group">
                                <a href="index.php" class="button btn btn-common">Untuk Perusahaan</a>
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

    <div class="job-detail-header">
        <div class="container">
            <div class="inner-header">
                <h1>[judul_lowongan]</h1>
                <p>[nama_perusahaan] - [kota_lowongan]</p>

                <div class="job-meta-info">
                    <div class="job-meta-item">
                        <i class="lni-briefcase"></i> [waktukerja]
                    </div>
                    <div class="job-meta-item">
                        <i class="lni-coin"></i> [gaji]
                    </div>
                    <div class="job-meta-item">
                        <i class="lni-timer"></i> Diposting [tanggal_posting]
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Content Section -->
    <section class="job-content">
        <div class="container">
            <div class="row">
                <!-- Main Job Content -->
                <div class="col-lg-8 col-md-7">
                    <div class="job-main-content">
                        <h3>Deskripsi Pekerjaan</h3>
                        <div class="job-description">
                            <p>[deskripsi_lowongan]</p>
                        </div>

                        <div class="job-requirements">
                            <h4>Kualifikasi yang Dibutuhkan:</h4>
                            <ul>
                                <li>[kualifikasi1]</li>
                                <li>[kualifikasi2]</li>
                                <li>[kualifikasi3]</li>
                                <li>[kualifikasi4]</li>
                                <li>[kualifikasi5]</li>
                                <li>[kualifikasi6]</li>
                            </ul>
                        </div>

                        <div class="job-benefits">
                            <h4>Pertanyaan dari Perusahaan:</h4>
                            <ul>
                                <li>[pertanyaan1]</li>
                                <li>[pertanyaan2]</li>
                                <li>[pertanyaan3]</li>
                                <li>[pertanyaan4]</li>
                                <li>[pertanyaan5]</li>
                                <li>[pertanyaan6]</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Job Sidebar -->
                <div class="col-lg-4 col-md-5">
                    <div class="job-sidebar">
                        <div class="company-info">
                            <img src="assets/img/product/img3.png" alt="Company Logo" class="company-logo" />
                            <h3 class="company-name">[nama_perusahaan]</h3>
                            <p>[deskripsi_perusahaan]</p>
                        </div>

                        <div class="job-actions">
                            <button class="btn-apply">Lamar Sekarang</button>
                            <button class="btn-save" id="saveJobBtn">
                                <i class="lni-heart"></i> Simpan Lowongan
                            </button>
                        </div>

                        <ul class="job-details-list">
                            <li>
                                <span class="detail-label">Lokasi</span>
                                <span class="detail-value">[lokasi]</span>
                            </li>
                            <li>
                                <span class="detail-label">Tipe Pekerjaan</span>
                                <span class="detail-value">[waktukerja]</span>
                            </li>
                            <li>
                                <span class="detail-label">Gaji</span>
                                <span class="detail-value">[gaji]</span>
                            </li>
                            <li>
                                <span class="detail-label">Kategori</span>
                                <span class="detail-value">[kategori]</span>
                            </li>
                            <li>
                                <span class="detail-label">Diposting</span>
                                <span class="detail-value">[tanggal_posting]</span>
                            </li>
                            <li>
                                <span class="detail-label">Berakhir</span>
                                <span class="detail-value">[tanggal_tutup]</span>
                            </li>
                        </ul>
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
        // Save Job Functionality
        document.addEventListener('DOMContentLoaded', function () {
            const saveJobBtn = document.getElementById('saveJobBtn');
            const savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
            const currentJobId = 1; // ID pekerjaan saat ini

            // Check if job is already saved
            if (savedJobs.includes(currentJobId)) {
                saveJobBtn.classList.add('saved');
                saveJobBtn.innerHTML = '<i class="lni-heart-filled"></i> Disimpan';
            }

            // Save job functionality
            saveJobBtn.addEventListener('click', function () {
                const savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
                const jobIndex = savedJobs.indexOf(currentJobId);

                if (jobIndex === -1) {
                    // Save job
                    savedJobs.push(currentJobId);
                    this.classList.add('saved');
                    this.innerHTML = '<i class="lni-heart-filled"></i> Disimpan';
                    showNotification('Lowongan berhasil disimpan', 'success');
                } else {
                    // Unsave job
                    savedJobs.splice(jobIndex, 1);
                    this.classList.remove('saved');
                    this.innerHTML = '<i class="lni-heart"></i> Simpan Lowongan';
                    showNotification('Lowongan dihapus dari daftar simpan', 'info');
                }

                // Update localStorage
                localStorage.setItem('savedJobs', JSON.stringify(savedJobs));
            });

            // Apply button functionality
            document.querySelector('.btn-apply').addEventListener('click', function () {
                // Check if user is logged in
                const isLoggedIn = false; // This should be replaced with actual authentication check

                if (!isLoggedIn) {
                    showNotification('Silakan login terlebih dahulu untuk melamar', 'info');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showNotification('Mengarahkan ke halaman lamaran...', 'success');
                    // In real implementation, redirect to application page
                }
            });

            // Notification function
            function showNotification(message, type = 'info') {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.textContent = message;
                notification.style.cssText = `
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background: ${type === 'success' ? '#1cc88a' : type === 'info' ? '#36b9cc' : '#4e73df'};
                    color: white;
                    padding: 10px 20px;
                    border-radius: 5px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    transform: translateY(100px);
                    opacity: 0;
                    transition: all 0.3s ease;
                    z-index: 1000;
                    font-size: 0.9rem;
                `;

                document.body.appendChild(notification);

                // Show notification
                setTimeout(() => {
                    notification.style.transform = 'translateY(0)';
                    notification.style.opacity = '1';
                }, 10);

                // Hide notification after 3 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateY(100px)';
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>

</html>