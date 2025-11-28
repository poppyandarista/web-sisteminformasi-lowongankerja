<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="Bootstrap, Landing page, Template, Registration, Landing" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <meta name="author" content="UIdeck" />
  <title>LinkUp - Jelajahi Perusahaan Impianmu</title>

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
    }

    /* Header Styles */
    .page-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      padding: 60px 0 40px;
      margin-top: 70px;
      color: white;
      position: relative;
      overflow: hidden;
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

    /* Company Listing Styles */
    .company-listing {
      padding: 60px 0;
    }

    .company-listing .section-header {
      margin-bottom: 40px;
    }

    .company-listing .section-header h2 {
      font-size: 28px;
      color: #0d72ff;
      margin-bottom: 15px;
    }

    .company-listing .section-header p {
      color: #6c757d;
      font-size: 16px;
    }

    /* Search and Filter Bar */
    .search-filter-bar {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }

    .search-box {
      position: relative;
    }

    .search-box i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
    }

    .search-box input {
      padding-left: 40px;
      height: 50px;
      border: 1px solid #eee;
      border-radius: 4px;
      box-shadow: none;
    }

    .filter-dropdown {
      position: relative;
    }

    .filter-dropdown select {
      height: 50px;
      border: 1px solid #eee;
      border-radius: 4px;
      padding: 0 15px;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      width: 100%;
      background: #fff;
    }

    .filter-dropdown i {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
      pointer-events: none;
    }

    /* Company Card */
    .company-card {
      background: #fff;
      border-radius: 12px;
      padding: 25px 20px;
      margin: 10px 0;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid #f0f0f0;
      height: 100%;
      position: relative;
      overflow: hidden;
      cursor: pointer;
      text-decoration: none;
      display: block;
      color: inherit;
    }

    .company-card:hover {
      text-decoration: none;
      color: inherit;
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(13, 114, 255, 0.15);
    }

    /* Add margin to company columns */
    .company-list .row>[class*="col-"] {
      margin: 15px 0;
    }

    .company-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #0d72ff, #7cb9ff);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .company-card:hover::before {
      transform: scaleX(1);
    }

    .company-logo {
      width: 100%;
      height: 120px;
      background: #f8faff;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      overflow: hidden;
      border: 1px solid #e8f0fe;
      padding: 15px;
      transition: all 0.3s ease;
    }

    .company-card:hover .company-logo {
      background: #f0f6ff;
      transform: scale(1.02);
    }

    .company-logo img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    .company-info h4 {
      font-size: 18px;
      margin-bottom: 12px;
      color: #1a237e;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .company-card:hover .company-info h4 {
      color: #0d72ff;
    }

    .company-info .meta {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-top: 15px;
      padding-top: 15px;
      border-top: 1px solid #f0f0f0;
    }

    .company-info .meta span {
      display: flex;
      align-items: center;
      color: #555;
      font-size: 14px;
      transition: color 0.3s ease;
    }

    .company-card:hover .company-info .meta span {
      color: #444;
    }

    .company-info .meta i {
      margin-right: 8px;
      color: #0d72ff;
      width: 20px;
      text-align: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .search-filter-bar .row>div {
        margin-bottom: 15px;
      }

      .company-list .row {
        margin: 0 -8px;
      }

      .company-list .row>[class*="col-"] {
        padding: 0 8px;
        margin: 10px 0;
      }
    }

    /* Section Spacing */
    section {
      padding: 60px 0;
    }

    section+section {
      padding-top: 0;
    }

    /* Company Slider Styles */
    .company-slider {
      padding: 40px 0 60px;
      background: #fff;
      margin-top: 30px;
    }

    .company-slider .section-header {
      margin-bottom: 40px;
      text-align: center;
    }

    .company-slider .section-header h2 {
      font-size: 28px;
      margin-bottom: 15px;
      color: #0d72ff;
    }

    .company-slider .section-header p {
      color: #6c757d;
      font-size: 16px;
    }

    .company-slider-container {
      position: relative;
      padding: 20px 0 40px;
    }

    .company-slider-track {
      display: flex;
      transition: transform 0.5s ease;
      gap: 20px;
      padding: 20px 10px;
      overflow: hidden;
    }

    .slider-company-card {
      min-width: 200px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      padding: 25px 20px;
      text-align: center;
      transition: all 0.3s ease;
      border: 1px solid #eee;
      margin: 0 5px;
      cursor: pointer;
      text-decoration: none;
      display: block;
      color: inherit;
    }

    .slider-company-card:hover {
      text-decoration: none;
      color: inherit;
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(13, 114, 255, 0.15);
    }

    .slider-company-logo {
      width: 80px;
      height: 80px;
      margin: 0 auto 15px;
      background: #f8f9fa;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 15px;
      border: 1px solid #eee;
    }

    .slider-company-logo img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    .slider-company-info h4 {
      font-size: 16px;
      margin-bottom: 5px;
      color: #333;
    }

    .job-count {
      color: #0d72ff;
      font-size: 14px;
      font-weight: 500;
    }

    .slider-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 40px;
      height: 40px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 2;
      transition: all 0.3s ease;
      color: #333;
    }

    .slider-nav:hover {
      background: #0d72ff;
      color: #fff;
      border-color: #0d72ff;
    }

    .slider-nav.prev {
      left: -20px;
    }

    .slider-nav.next {
      right: -20px;
    }

    .slider-dots {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .slider-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #ddd;
      margin: 0 5px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .slider-dot.active {
      background: #0d72ff;
      width: 30px;
      border-radius: 5px;
    }

    .btn-common {
      background-color: #0d72ff;
      color: #fff;
      border: none;
      padding: 12px 30px;
      border-radius: 6px;
      font-size: 15px;
      font-weight: 500;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-transform: none;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 15px rgba(13, 114, 255, 0.2);
    }

    .btn-common:hover {
      background-color: #0a5acf;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(13, 114, 255, 0.3);
    }

    @media (max-width: 768px) {
      .slider-company-card {
        min-width: 160px;
      }

      .slider-nav {
        width: 35px;
        height: 35px;
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
            <h3>Temukan perusahaan yang tepat untuk Anda</h3>
            <p>Jelajahi ribuan perusahaan terbaik dan temukan budaya kerja yang cocok untuk karir Anda</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Page Header End -->

  <!-- Counter Section Start -->
  <section id="counter" class="section bg-gray">
    <div class="container">
      <div class="row">
        <!-- Start counter -->
        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="counter-box">
            <div class="icon"><i class="lni-home"></i></div>
            <div class="fact-count">
              <h3><span class="counter">800</span></h3>
              <p>Jobs Posted</p>
            </div>
          </div>
        </div>
        <!-- End counter -->
        <!-- Start counter -->
        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="counter-box">
            <div class="icon"><i class="lni-briefcase"></i></div>
            <div class="fact-count">
              <h3><span class="counter">80</span></h3>
              <p>All Companies</p>
            </div>
          </div>
        </div>
        <!-- End counter -->
        <!-- Start counter -->
        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="counter-box">
            <div class="icon"><i class="lni-pencil-alt"></i></div>
            <div class="fact-count">
              <h3><span class="counter">900</span></h3>
              <p>Resumes</p>
            </div>
          </div>
        </div>
        <!-- End counter -->
        <!-- Start counter -->
        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="counter-box">
            <div class="icon"><i class="lni-save"></i></div>
            <div class="fact-count">
              <h3><span class="counter">1200</span></h3>
              <p>Applications</p>
            </div>
          </div>
        </div>
        <!-- End counter -->
      </div>
    </div>
  </section>
  <!-- Counter Section End -->

  <!-- Company Listing Section Start -->
  <section class="company-listing section bg-gray">
    <div class="container">
      <div class="section-header text-center">
        <h2 class="section-title">
          Lamar Kerja ke 700+ Perusahaan Terbaik di Indonesia
        </h2>
        <p>
          Temukan perusahaan impian Anda dan mulailah karir yang lebih baik
        </p>
      </div>

      <!-- Search and Filter Bar -->
      <div class="search-filter-bar">
        <div class="row">
          <div class="col-md-8">
            <div class="search-box">
              <i class="lni-search"></i>
              <input type="text" class="form-control" placeholder="Cari perusahaan..." />
            </div>
          </div>
          <div class="col-md-4">
            <div class="filter-dropdown">
              <select class="form-control">
                <option>Terbaru</option>
                <option>Terpopuler</option>
                <option>Terpercaya</option>
                <option>Paling Banyak Lowongan</option>
              </select>
              <i class="lni-chevron-down"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Company List -->
      <div class="company-list">
        <div class="row">
          <!-- Company Card 1 -->
          <div class="col-lg-4 col-md-6 col-xs-12">
            <a href="company-details.php" class="company-card">
              <div class="company-logo">
                <img src="assets/img/product/img1.png" alt="Gojek" />
              </div>
              <div class="company-info">
                <h4>PT Gojek Indonesia</h4>
                <div class="meta">
                  <span><i class="lni-briefcase"></i> 124 Lowongan</span>
                  <span><i class="lni-map-marker"></i> Jakarta</span>
                </div>
              </div>
            </a>
          </div>

          <!-- Company Card 2 -->
          <div class="col-lg-4 col-md-6 col-xs-12">
            <a href="company-details.php" class="company-card">
              <div class="company-logo">
                <img src="assets/img/product/img1.png" alt="Tokopedia" />
              </div>
              <div class="company-info">
                <h4>PT Tokopedia</h4>
                <div class="meta">
                  <span><i class="lni-briefcase"></i> 89 Lowongan</span>
                  <span><i class="lni-map-marker"></i> Jakarta</span>
                </div>
              </div>
            </a>
          </div>

          <!-- Company Card 3 -->
          <div class="col-lg-4 col-md-6 col-xs-12">
            <a href="company-details.php" class="company-card">
              <div class="company-logo">
                <img src="assets/img/product/img1.png" alt="Traveloka" />
              </div>
              <div class="company-info">
                <h4>PT Traveloka Indonesia</h4>
                <div class="meta">
                  <span><i class="lni-briefcase"></i> 76 Lowongan</span>
                  <span><i class="lni-map-marker"></i> Jakarta</span>
                </div>
              </div>
            </a>
          </div>

          <!-- More company cards will be added here by JavaScript -->
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-4">
          <button id="loadMoreBtn" class="btn btn-common">
            Lihat Lainnya
          </button>
        </div>
      </div>
    </div>
  </section>
  <!-- Company Listing Section End -->

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

  <script src="assets/js/owl.carousel.min.js"></script>
  <script src="assets/js/jquery.slicknav.js"></script>
  <script src="assets/js/jquery.counterup.min.js"></script>
  <script src="assets/js/waypoints.min.js"></script>
  <script src="assets/js/form-validator.min.js"></script>
  <script src="assets/js/contact-form-script.js"></script>
  <script src="assets/js/main.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Data perusahaan tambahan
      const moreCompanies = [
        {
          name: "PT Bukalapak",
          logo: "assets/img/product/img1.png",
          jobs: 65,
          location: "Jakarta",
        },
        {
          name: "PT Shopee Indonesia",
          logo: "assets/img/product/img1.png",
          jobs: 112,
          location: "Jakarta",
        },
        {
          name: "PT Grab Indonesia",
          logo: "assets/img/product/img1.png",
          jobs: 92,
          location: "Jakarta",
        },
        {
          name: "PT Blibli",
          logo: "assets/img/product/img1.png",
          jobs: 54,
          location: "Jakarta",
        },
        {
          name: "PT OVO",
          logo: "assets/img/product/img1.png",
          jobs: 43,
          location: "Jakarta",
        },
        {
          name: "PT DANA Indonesia",
          logo: "assets/img/product/img1.png",
          jobs: 37,
          location: "Jakarta",
        },
        {
          name: "PT GoTo Gojek Tokopedia",
          logo: "assets/img/product/img1.png",
          jobs: 128,
          location: "Jakarta",
        },
        {
          name: "PT Bank Jago",
          logo: "assets/img/product/img1.png",
          jobs: 42,
          location: "Jakarta",
        },
        {
          name: "PT Ajaib Sekuritas",
          logo: "assets/img/product/img1.png",
          jobs: 35,
          location: "Jakarta",
        },
      ];

      let visibleCompanies = 3;
      const companyList = document.querySelector(".company-list .row");
      const loadMoreBtn = document.getElementById("loadMoreBtn");
      const companiesPerLoad = 3;
      let currentVisible = 0;

      // Fungsi untuk membuat kartu perusahaan
      function createCompanyCard(company) {
        return `
            <div class="col-lg-4 col-md-6 col-xs-12">
              <a href="company-details.php" class="company-card">
                <div class="company-logo">
                  <img src="${company.logo}" alt="${company.name}" />
                </div>
                <div class="company-info">
                  <h4>${company.name}</h4>
                  <div class="meta">
                    <span><i class="lni-briefcase"></i> ${company.jobs} Lowongan</span>
                    <span><i class="lni-map-marker"></i> ${company.location}</span>
                  </div>
                </div>
              </a>
            </div>
          `;
      }

      // Fungsi untuk menampilkan lebih banyak perusahaan
      function loadMoreCompanies() {
        const startIndex = currentVisible;
        const endIndex = Math.min(
          currentVisible + companiesPerLoad,
          moreCompanies.length
        );

        const nextCompanies = moreCompanies.slice(startIndex, endIndex);

        if (nextCompanies.length === 0) {
          loadMoreBtn.style.display = "none";
          return;
        }

        nextCompanies.forEach((company) => {
          companyList.insertAdjacentHTML(
            "beforeend",
            createCompanyCard(company)
          );
        });

        currentVisible += nextCompanies.length;

        const remainingCompanies = moreCompanies.length - currentVisible;
        if (remainingCompanies > 0) {
          loadMoreBtn.textContent = "Lihat Lainnya";
        } else {
          loadMoreBtn.style.display = "none";
        }
      }

      // Event listener untuk tombol "Lihat Semua Perusahaan"
      loadMoreBtn.addEventListener("click", loadMoreCompanies);

      // Inisialisasi perusahaan yang tampil pertama kali
      document.addEventListener("DOMContentLoaded", function () {
        const allCompanyCards = document.querySelectorAll(".company-card");
        allCompanyCards.forEach((card, index) => {
          if (index >= 3) {
            card.parentElement.style.display = "none";
          } else {
            currentVisible++;
          }
        });

        const remainingCompanies = moreCompanies.length;
        if (remainingCompanies > 0) {
          loadMoreBtn.textContent = "Lihat Lainnya";
        }

        if (allCompanyCards.length <= 3) {
          loadMoreBtn.style.display = "none";
        }
      });

      // Company data - in a real app, this would come from an API
      const companies = [
        {
          name: "PT. Gojek Indonesia",
          logo: "https://via.placeholder.com/80x80?text=GJ",
          jobs: 124,
        },
        {
          name: "Tokopedia",
          logo: "https://via.placeholder.com/80x80?text=TP",
          jobs: 89,
        },
        {
          name: "Traveloka",
          logo: "https://via.placeholder.com/80x80?text=TV",
          jobs: 76,
        },
        {
          name: "Bukalapak",
          logo: "https://via.placeholder.com/80x80?text=BL",
          jobs: 65,
        },
        {
          name: "Shopee",
          logo: "https://via.placeholder.com/80x80?text=SP",
          jobs: 112,
        },
        {
          name: "Grab",
          logo: "https://via.placeholder.com/80x80?text=GB",
          jobs: 92,
        },
        {
          name: "Blibli",
          logo: "https://via.placeholder.com/80x80?text=BB",
          jobs: 54,
        },
        {
          name: "OVO",
          logo: "https://via.placeholder.com/80x80?text=OV",
          jobs: 43,
        },
      ];

      const track = document.querySelector(".company-slider-track");
      const dotsContainer = document.querySelector(".slider-dots");
      const prevBtn = document.querySelector(".slider-nav.prev");
      const nextBtn = document.querySelector(".slider-nav.next");

      let currentIndex = 0;
      const cardsPerView = Math.min(5, companies.length);
      const cardWidth = 220;

      // Initialize slider with company cards
      function initSlider() {
        track.innerHTML = "";
        dotsContainer.innerHTML = "";

        companies.forEach((company, index) => {
          const card = document.createElement("a");
          card.className = "slider-company-card";
          card.href = "company-details.php";
          card.innerHTML = `
              <div class="slider-company-logo">
                <img src="${company.logo}" alt="${company.name}" />
              </div>
              <div class="slider-company-info">
                <h4>${company.name}</h4>
                <div class="job-count">${company.jobs} Lowongan</div>
              </div>
            `;
          track.appendChild(card);

          if (index % cardsPerView === 0) {
            const dot = document.createElement("div");
            dot.className = "slider-dot" + (index === 0 ? " active" : "");
            dot.dataset.slide = index / cardsPerView;
            dot.addEventListener("click", () =>
              goToSlide(index / cardsPerView)
            );
            dotsContainer.appendChild(dot);
          }
        });

        updateSlider();
      }

      // Update slider position
      function updateSlider() {
        const offset = -currentIndex * (cardsPerView * cardWidth);
        track.style.transform = `translateX(${offset}px)`;

        document.querySelectorAll(".slider-dot").forEach((dot, index) => {
          dot.classList.toggle(
            "active",
            index === Math.floor(currentIndex / cardsPerView)
          );
        });
      }

      // Go to specific slide
      function goToSlide(slideIndex) {
        const maxSlide = Math.ceil(companies.length / cardsPerView) - 1;
        currentIndex = Math.min(
          Math.max(slideIndex * cardsPerView, 0),
          maxSlide * cardsPerView
        );
        updateSlider();
      }

      // Event listeners
      prevBtn.addEventListener("click", () => {
        if (currentIndex > 0) {
          currentIndex--;
          updateSlider();
        }
      });

      nextBtn.addEventListener("click", () => {
        if (currentIndex < companies.length - cardsPerView) {
          currentIndex++;
          updateSlider();
        }
      });

      // Handle window resize
      let resizeTimer;
      window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
          initSlider();
        }, 250);
      });

      // Initialize the slider
      initSlider();
    });
  </script>
</body>

</html>