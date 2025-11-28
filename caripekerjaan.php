<!DOCTYPE html>
<html lang="id">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="Bootstrap, Landing page, Template, Registration, Landing" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <meta name="author" content="UIdeck" />
  <title>Cari Pekerjaan | LinkUp</title>

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

    .page-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      padding: 60px 0 40px;
      margin-top: 60px;
      color: white;
      position: relative;
      overflow: hidden;
      margin-bottom: 0px;
    }

    .page-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"></polygon></svg>');
      background-size: cover;
    }

    .page-header .inner-header {
      position: relative;
      z-index: 2;
    }

    .inner-header p {
      color: white;
    }

    .page-header h3 {
      font-weight: 700;
      margin-bottom: 10px;
      font-size: 2.2rem;
    }

    .page-header p {
      opacity: 0.9;
      font-size: 1.1rem;
    }

    .search-wrapper {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: var(--card-shadow);
      margin-top: -30px;
      position: relative;
      z-index: 1;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .search-input {
      position: relative;
    }

    .search-input i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
    }

    .search-input input {
      width: 100%;
      height: 50px;
      border: 1px solid #eee;
      border-radius: 5px;
      padding-left: 45px;
      padding-right: 15px;
      transition: all 0.3s ease;
    }

    .search-input input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(61, 142, 255, 0.25);
    }

    .filter-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 15px;
    }

    .filter-tag {
      padding: 6px 15px;
      background: #f8f9fa;
      border-radius: 20px;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 1px solid #e9ecef;
    }

    .filter-tag:hover,
    .filter-tag.active {
      background: var(--primary);
      color: #fff;
      border-color: var(--primary);
    }

    /* Job Listings Container */
    #jobListings {
      margin: 0 -15px;
    }

    /* Job Card Wrapper */
    #jobListings>[class*="col-"] {
      padding: 15px;
    }

    .job-card {
      background: #fff;
      border-radius: 10px;
      padding: 25px;
      margin: 0;
      box-shadow: var(--card-shadow);
      transition: all 0.3s ease;
      height: 100%;
      border: 1px solid #f0f2f5;
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

    .company-logo {
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
      font-size: 18px;
      color: #2d3748;
    }

    .job-info p {
      margin: 0 0 10px;
      color: #6c757d;
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
      font-size: 14px;
    }

    .job-meta i {
      margin-right: 5px;
      color: #a0aec0;
    }

    .job-description {
      margin: 15px 0;
      color: #4a5568;
      font-size: 14px;
      line-height: 1.6;
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
      font-size: 13px;
      display: flex;
      align-items: center;
    }

    .time-posted i {
      margin-right: 5px;
    }

    .btn-save {
      background: none;
      border: none;
      color: #a0aec0;
      font-size: 18px;
      cursor: pointer;
      transition: all 0.3s ease;
      padding: 5px;
    }

    .btn-save:hover {
      color: #e74c3c;
    }

    /* Save Button Styles */
    .btn-save {
      background: none;
      border: none;
      color: #d1d5db;
      font-size: 20px;
      cursor: pointer;
      padding: 5px;
      transition: all 0.3s ease;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }

    .btn-save:hover {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
    }

    .btn-save.saved,
    .btn-save.saved:hover {
      color: #e74c3c;
    }

    .btn-save i {
      transition: all 0.3s ease;
    }

    /* Notification Styles */
    .notification {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: var(--primary);
      color: white;
      padding: 12px 24px;
      border-radius: 5px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      transform: translateY(100px);
      opacity: 0;
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .notification.show {
      transform: translateY(0);
      opacity: 1;
    }

    .notification.success {
      background: #1cc88a;
    }

    .notification.info {
      background: #36b9cc;
    }

    .pagination {
      margin-top: 30px;
    }

    .page-item {
      margin: 0 3px;
    }

    .page-link {
      color: #4a5568;
      border: 1px solid #e2e8f0;
      padding: 8px 14px;
      border-radius: 4px !important;
      transition: all 0.3s ease;
    }

    .page-item.active .page-link {
      background-color: var(--primary);
      border-color: var(--primary);
    }

    .page-link:hover {
      background-color: #f8f9fa;
      color: #4a5568;
    }

    .no-results {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }

    @media (max-width: 768px) {
      .search-wrapper {
        padding: 20px 15px;
      }

      .job-header {
        flex-direction: column;
      }

      .company-logo {
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
              <li class="nav-item active">
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

  <!-- Page Header Start -->
  <div class="page-header">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="inner-header text-center">
            <h3>Telusuri Pekerjaan Impianmu</h3>
            <p>Temukan lowongan terbaik yang sesuai dengan keahlian dan minat Anda</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Page Header End -->

  <!-- Search and Filter Section -->
  <section class="section">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="search-wrapper">
            <div class="row">
              <div class="col-lg-5 col-md-5 col-xs-12">
                <div class="search-input">
                  <i class="lni-tag"></i>
                  <input type="text" id="keywordInput" placeholder="Kata kunci (contoh: Developer, Designer)" />
                </div>
              </div>
              <div class="col-lg-5 col-md-5 col-xs-12">
                <div class="search-input">
                  <i class="lni-map-marker"></i>
                  <input type="text" id="locationInput" placeholder="Lokasi (contoh: Jakarta, Bandung)" />
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-xs-12">
                <button id="searchButton" class="btn btn-common btn-block">
                  <i class="lni-search"></i> Cari
                </button>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <div class="filter-tags">
                  <span class="filter-tag active" data-category="all">Semua Kategori</span>
                  <span class="filter-tag" data-category="it">IT & Software</span>
                  <span class="filter-tag" data-category="marketing">Marketing</span>
                  <span class="filter-tag" data-category="finance">Finance</span>
                  <span class="filter-tag" data-category="design">Design</span>
                  <span class="filter-tag" data-category="sales">Sales</span>
                  <span class="filter-tag" data-category="education">Pendidikan</span>
                  <span class="filter-tag" data-category="health">Kesehatan</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Job Listings Section -->
  <section class="section bg-gray">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div id="jobListings" class="row">
            <!-- Job cards will be dynamically loaded here -->
          </div>

          <!-- Pagination -->
          <nav aria-label="Page navigation" class="mt-4">
            <ul id="pagination" class="pagination justify-content-center">
              <!-- Pagination will be dynamically generated -->
            </ul>
          </nav>
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
                  Platform yang menghubungkan pencari kerja berbakat dengan perusahaan terbaik. Temukan karier impian
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
    // Sample job data
    const jobData = [
      {
        id: 1,
        title: "Frontend Developer",
        company: "PT Tech Solutions Indonesia",
        location: "Jakarta Selatan",
        type: "Full Time",
        salary: "Rp8.000.000 - 15.000.000",
        description:
          "Kami mencari Frontend Developer yang berpengalaman dengan React.js dan memiliki pemahaman yang baik tentang UI/UX.",
        category: "it",
        logo: "assets/img/product/img2.png",
        posted: "2 jam lalu",
      },
      {
        id: 2,
        title: "Digital Marketing Specialist",
        company: "PT Digital Nusantara",
        location: "Bandung",
        type: "Full Time",
        salary: "Rp6.500.000 - 10.000.000",
        description:
          "Kami mencari Digital Marketing Specialist yang kreatif dan berpengalaman dalam mengelola kampanye digital.",
        category: "marketing",
        logo: "assets/img/product/img2.png",
        posted: "1 hari lalu",
      },
      {
        id: 3,
        title: "UI/UX Designer",
        company: "PT Creative Design Studio",
        location: "Jakarta Pusat",
        type: "Full Time",
        salary: "Rp7.000.000 - 12.000.000",
        description:
          "Kami mencari UI/UX Designer yang kreatif dan berpengalaman dengan Figma dan Adobe XD.",
        category: "design",
        logo: "assets/img/product/img2.png",
        posted: "3 jam lalu",
      },
      {
        id: 4,
        title: "Backend Developer",
        company: "PT Software House Indonesia",
        location: "Surabaya",
        type: "Full Time",
        salary: "Rp9.000.000 - 16.000.000",
        description:
          "Kami mencari Backend Developer yang berpengalaman dengan Node.js dan database SQL/NoSQL.",
        category: "it",
        logo: "assets/img/product/img2.png",
        posted: "5 jam lalu",
      },
      {
        id: 5,
        title: "Account Manager",
        company: "PT Finance Solutions",
        location: "Jakarta Selatan",
        type: "Full Time",
        salary: "Rp10.000.000 - 18.000.000",
        description:
          "Kami mencari Account Manager yang berpengalaman dalam mengelola portofolio klien keuangan.",
        category: "finance",
        logo: "assets/img/product/img2.png",
        posted: "1 hari lalu",
      },
      {
        id: 6,
        title: "Sales Executive",
        company: "PT Retail Indonesia",
        location: "Bandung",
        type: "Full Time",
        salary: "Rp5.000.000 - 8.000.000",
        description:
          "Kami mencari Sales Executive yang dinamis dan memiliki kemampuan komunikasi yang baik.",
        category: "sales",
        logo: "assets/img/product/img2.png",
        posted: "2 hari lalu",
      },
      {
        id: 7,
        title: "Guru Matematika",
        company: "SMA Negeri 1 Jakarta",
        location: "Jakarta Pusat",
        type: "Part Time",
        salary: "Rp4.000.000 - 6.000.000",
        description:
          "Kami mencari guru matematika yang berpengalaman dan memiliki passion dalam mengajar.",
        category: "education",
        logo: "assets/img/product/img2.png",
        posted: "3 hari lalu",
      },
      {
        id: 8,
        title: "Perawat",
        company: "RS Umum Sehat",
        location: "Surabaya",
        type: "Full Time",
        salary: "Rp5.500.000 - 9.000.000",
        description:
          "Kami mencari perawat yang berpengalaman dan memiliki sertifikasi yang sesuai.",
        category: "health",
        logo: "assets/img/product/img2.png",
        posted: "4 jam lalu",
      },
      {
        id: 9,
        title: "Full Stack Developer",
        company: "PT Web Solutions",
        location: "Jakarta Barat",
        type: "Full Time",
        salary: "Rp12.000.000 - 20.000.000",
        description:
          "Kami mencari Full Stack Developer yang berpengalaman dengan React dan Node.js.",
        category: "it",
        logo: "assets/img/product/img2.png",
        posted: "6 jam lalu",
      },
      {
        id: 10,
        title: "Content Marketing Specialist",
        company: "PT Media Kreatif",
        location: "Jakarta Selatan",
        type: "Full Time",
        salary: "Rp6.000.000 - 9.000.000",
        description:
          "Kami mencari Content Marketing Specialist yang kreatif dan berpengalaman dalam membuat konten digital.",
        category: "marketing",
        logo: "assets/img/product/img2.png",
        posted: "1 hari lalu",
      },
      {
        id: 11,
        title: "Graphic Designer",
        company: "PT Desain Kreatif",
        location: "Bandung",
        type: "Full Time",
        salary: "Rp5.500.000 - 8.500.000",
        description:
          "Kami mencari Graphic Designer yang berpengalaman dengan Adobe Creative Suite.",
        category: "design",
        logo: "assets/img/product/img2.png",
        posted: "2 hari lalu",
      },
      {
        id: 12,
        title: "Financial Analyst",
        company: "PT Investasi Indonesia",
        location: "Jakarta Pusat",
        type: "Full Time",
        salary: "Rp11.000.000 - 17.000.000",
        description:
          "Kami mencari Financial Analyst yang berpengalaman dalam analisis pasar keuangan.",
        category: "finance",
        logo: "assets/img/product/img2.png",
        posted: "3 hari lalu",
      },
      {
        id: 13,
        title: "Sales Manager",
        company: "PT Retail Nasional",
        location: "Surabaya",
        type: "Full Time",
        salary: "Rp12.000.000 - 20.000.000",
        description:
          "Kami mencari Sales Manager yang berpengalaman dalam memimpin tim penjualan.",
        category: "sales",
        logo: "assets/img/product/img2.png",
        posted: "4 hari lalu",
      },
      {
        id: 14,
        title: "Dosen Teknik Informatika",
        company: "Universitas Teknologi",
        location: "Jakarta Timur",
        type: "Part Time",
        salary: "Rp7.000.000 - 10.000.000",
        description:
          "Kami mencari dosen Teknik Informatika yang berpengalaman dalam pengajaran dan penelitian.",
        category: "education",
        logo: "assets/img/product/img2.png",
        posted: "5 hari lalu",
      },
      {
        id: 15,
        title: "Dokter Umum",
        company: "Klinik Sehat Sentosa",
        location: "Jakarta Selatan",
        type: "Full Time",
        salary: "Rp15.000.000 - 25.000.000",
        description:
          "Kami mencari dokter umum yang berpengalaman dan memiliki STR yang masih berlaku.",
        category: "health",
        logo: "assets/img/product/img2.png",
        posted: "6 hari lalu",
      },
      {
        id: 16,
        title: "Mobile Developer",
        company: "PT App Solutions",
        location: "Jakarta Barat",
        type: "Full Time",
        salary: "Rp10.000.000 - 16.000.000",
        description:
          "Kami mencari Mobile Developer yang berpengalaman dengan React Native atau Flutter.",
        category: "it",
        logo: "assets/img/product/img2.png",
        posted: "7 hari lalu",
      },
      {
        id: 17,
        title: "Social Media Specialist",
        company: "PT Media Digital",
        location: "Jakarta Pusat",
        type: "Full Time",
        salary: "Rp5.500.000 - 8.000.000",
        description:
          "Kami mencari Social Media Specialist yang berpengalaman dalam mengelola akun media sosial.",
        category: "marketing",
        logo: "assets/img/product/img2.png",
        posted: "8 hari lalu",
      },
      {
        id: 18,
        title: "Product Designer",
        company: "PT Inovasi Digital",
        location: "Bandung",
        type: "Full Time",
        salary: "Rp8.000.000 - 13.000.000",
        description:
          "Kami mencari Product Designer yang berpengalaman dalam desain produk digital.",
        category: "design",
        logo: "assets/img/product/img2.png",
        posted: "9 hari lalu",
      },
      {
        id: 19,
        title: "Investment Banker",
        company: "PT Bank Investasi",
        location: "Jakarta Selatan",
        type: "Full Time",
        salary: "Rp20.000.000 - 35.000.000",
        description:
          "Kami mencari Investment Banker yang berpengalaman dalam transaksi keuangan korporasi.",
        category: "finance",
        logo: "assets/img/product/img2.png",
        posted: "10 hari lalu",
      },
      {
        id: 20,
        title: "Retail Sales Associate",
        company: "PT Retail Indonesia",
        location: "Surabaya",
        type: "Part Time",
        salary: "Rp3.500.000 - 5.000.000",
        description:
          "Kami mencari Retail Sales Associate yang ramah dan memiliki kemampuan komunikasi yang baik.",
        category: "sales",
        logo: "assets/img/product/img2.png",
        posted: "11 hari lalu",
      },
    ];

    // State variables
    let currentPage = 1;
    const jobsPerPage = 10;
    let filteredJobs = [...jobData];
    let currentCategory = "all";
    let savedJobs = JSON.parse(localStorage.getItem("savedJobs")) || [];

    // Initialize the page
    document.addEventListener("DOMContentLoaded", function () {
      // Load saved jobs from localStorage
      savedJobs = JSON.parse(localStorage.getItem("savedJobs")) || [];
      renderJobCards();
      setupEventListeners();
      updatePagination();
    });

    // Render job cards
    function renderJobCards() {
      const jobListings = document.getElementById("jobListings");
      jobListings.innerHTML = "";

      const startIndex = (currentPage - 1) * jobsPerPage;
      const endIndex = startIndex + jobsPerPage;
      const jobsToShow = filteredJobs.slice(startIndex, endIndex);

      if (jobsToShow.length === 0) {
        jobListings.innerHTML = `
            <div class="col-12">
              <div class="no-results">
                <i class="lni-search" style="font-size: 48px; margin-bottom: 15px;"></i>
                <h4>Tidak ada lowongan yang ditemukan</h4>
                <p>Coba ubah kata kunci pencarian atau filter kategori</p>
              </div>
            </div>
          `;
        return;
      }

      jobsToShow.forEach((job) => {
        const isSaved = savedJobs.includes(job.id);
        const badgeClass = getBadgeClass(job.category);

        const jobCard = document.createElement("div");
        jobCard.className = "col-lg-6 col-md-6 col-sm-12";
        jobCard.innerHTML = `
            <div class="job-card">
              <a href="job-detail.php" class="job-card-link">
                <div class="job-header">
                  <img
                    src="${job.logo}"
                    alt="Company Logo"
                    class="company-logo"
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
                  <span class="badge ${badgeClass}">${getCategoryName(
          job.category
        )}</span>
                  <span class="time-posted"><i class="lni-timer"></i> ${job.posted}</span>
                </div>
              </a>
              <button class="btn btn-save ${isSaved ? "saved" : ""}" data-job-id="${job.id}">
                <i class="${isSaved ? "lni-heart-filled" : "lni-heart"}"></i>
              </button>
            </div>
          `;
        jobListings.appendChild(jobCard);
      });

      // Add event listeners to save buttons
      document.querySelectorAll(".btn-save").forEach((button) => {
        button.addEventListener("click", function () {
          const jobId = parseInt(this.getAttribute("data-job-id"));
          toggleSaveJob(jobId, this);
        });
      });
    }

    // Setup event listeners
    function setupEventListeners() {
      // Filter tags
      document.querySelectorAll(".filter-tag").forEach((tag) => {
        tag.addEventListener("click", function () {
          document
            .querySelectorAll(".filter-tag")
            .forEach((t) => t.classList.remove("active"));
          this.classList.add("active");

          currentCategory = this.getAttribute("data-category");
          filterJobs();
        });
      });

      // Search button
      document
        .getElementById("searchButton")
        .addEventListener("click", function () {
          filterJobs();
        });

      // Enter key in search inputs
      document
        .getElementById("keywordInput")
        .addEventListener("keypress", function (e) {
          if (e.key === "Enter") filterJobs();
        });

      document
        .getElementById("locationInput")
        .addEventListener("keypress", function (e) {
          if (e.key === "Enter") filterJobs();
        });
    }

    // Filter jobs based on search criteria and category
    function filterJobs() {
      const keyword = document
        .getElementById("keywordInput")
        .value.toLowerCase();
      const location = document
        .getElementById("locationInput")
        .value.toLowerCase();

      filteredJobs = jobData.filter((job) => {
        const matchesKeyword =
          job.title.toLowerCase().includes(keyword) ||
          job.company.toLowerCase().includes(keyword) ||
          job.description.toLowerCase().includes(keyword);

        const matchesLocation = job.location.toLowerCase().includes(location);

        const matchesCategory =
          currentCategory === "all" || job.category === currentCategory;

        return matchesKeyword && matchesLocation && matchesCategory;
      });

      currentPage = 1;
      renderJobCards();
      updatePagination();
    }

    // Update pagination
    function updatePagination() {
      const totalPages = Math.ceil(filteredJobs.length / jobsPerPage);
      const pagination = document.getElementById("pagination");
      pagination.innerHTML = "";

      // Previous button
      const prevLi = document.createElement("li");
      prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
      prevLi.innerHTML = `
          <a class="page-link" href="#" aria-label="Previous">
            <i class="lni-chevron-left"></i>
          </a>
        `;
      if (currentPage > 1) {
        prevLi.addEventListener("click", function (e) {
          e.preventDefault();
          currentPage--;
          renderJobCards();
          updatePagination();
        });
      }
      pagination.appendChild(prevLi);

      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        const pageLi = document.createElement("li");
        pageLi.className = `page-item ${i === currentPage ? "active" : ""}`;
        pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;

        pageLi.addEventListener("click", function (e) {
          e.preventDefault();
          currentPage = i;
          renderJobCards();
          updatePagination();
        });

        pagination.appendChild(pageLi);
      }

      // Next button
      const nextLi = document.createElement("li");
      nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""
        }`;
      nextLi.innerHTML = `
          <a class="page-link" href="#" aria-label="Next">
            <i class="lni-chevron-right"></i>
          </a>
        `;
      if (currentPage < totalPages) {
        nextLi.addEventListener("click", function (e) {
          e.preventDefault();
          currentPage++;
          renderJobCards();
          updatePagination();
        });
      }
      pagination.appendChild(nextLi);
    }

    // Toggle save job
    function toggleSaveJob(jobId, button) {
      const index = savedJobs.indexOf(jobId);
      const icon = button.querySelector("i");

      if (index === -1) {
        // Save job
        savedJobs.push(jobId);
        button.classList.add("saved");
        icon.classList.remove("lni-heart");
        icon.classList.add("lni-heart-filled");
        showNotification("Lowongan disimpan", "success");
      } else {
        // Unsave job
        savedJobs.splice(index, 1);
        button.classList.remove("saved");
        icon.classList.remove("lni-heart-filled");
        icon.classList.add("lni-heart");
        showNotification("Lowongan dihapus", "info");
      }

      // Save to localStorage
      localStorage.setItem("savedJobs", JSON.stringify(savedJobs));
    }

    // Show notification
    function showNotification(message, type = "info") {
      const notification = document.createElement("div");
      notification.className = `notification ${type}`;
      notification.textContent = message;
      document.body.appendChild(notification);

      // Add show class after a small delay to trigger animation
      setTimeout(() => {
        notification.classList.add("show");
      }, 10);

      // Remove notification after 3 seconds
      setTimeout(() => {
        notification.classList.remove("show");
        setTimeout(() => {
          notification.remove();
        }, 300);
      }, 3000);
    }

    // Get badge class based on category
    function getBadgeClass(category) {
      switch (category) {
        case "it":
          return "badge-primary";
        case "marketing":
          return "badge-success";
        case "finance":
          return "badge-info";
        case "design":
          return "badge-warning";
        case "sales":
          return "badge-danger";
        case "education":
          return "badge-secondary";
        case "health":
          return "badge-dark";
        default:
          return "badge-primary";
      }
    }

    // Get category name
    function getCategoryName(category) {
      switch (category) {
        case "it":
          return "IT & Software";
        case "marketing":
          return "Marketing";
        case "finance":
          return "Finance";
        case "design":
          return "Design";
        case "sales":
          return "Sales";
        case "education":
          return "Pendidikan";
        case "health":
          return "Kesehatan";
        default:
          return "Other";
      }
    }
  </script>
</body>

</html>