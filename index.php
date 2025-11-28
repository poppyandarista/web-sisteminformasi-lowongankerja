<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="Lowongan Kerja, Cari Pekerjaan, Portal Karir, Loker Terbaru, Pencarian Kerja" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <meta name="author" content="UIdeck" />
  <title>LinkUp - Temukan Pekerjaan Impianmu</title>

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
    /* Company Slider Styles */
    .company-slider-container {
      position: relative;
      padding: 20px 0 40px;
    }

    .company-slider {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
      padding: 20px 0;
      margin-bottom: 20px;
      scrollbar-width: none;
      /* Firefox */
    }

    .company-slider::-webkit-scrollbar {
      display: none;
      /* Hide scrollbar for Chrome, Safari and Opera */
    }

    .company-card {
      flex: 0 0 280px;
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      scroll-snap-align: start;
      border: 1px solid #eaeef5;
    }

    .company-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .company-logo-container {
      width: 80px;
      height: 80px;
      margin: 0 auto 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fff;
      border-radius: 12px;
      border: 1px solid #f0f2f5;
      padding: 10px;
    }

    .company-logo {
      max-width: 100%;
      max-height: 60px;
      object-fit: contain;
    }

    .company-name {
      font-size: 18px;
      font-weight: 600;
      margin: 10px 0 5px;
      color: #2d3748;
    }

    .job-count {
      color: #718096;
      font-size: 14px;
      margin-bottom: 15px;
    }

    .btn-view-jobs {
      background: #4e73df;
      color: white;
      border: none;
      padding: 8px 20px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-view-jobs:hover {
      background: #2e59d9;
      transform: translateY(-2px);
    }

    .slider-controls {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
      margin-top: 20px;
    }

    .slider-arrow {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: white;
      border: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .slider-arrow:hover {
      background: #4e73df;
      color: white;
      border-color: #4e73df;
    }

    .slider-dots {
      display: flex;
      gap: 8px;
    }

    .slider-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #e2e8f0;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .slider-dot.active {
      background: #4e73df;
      width: 30px;
      border-radius: 5px;
    }

    /* View All Button Styles */
    .btn-common {
      background: #4e73df;
      color: #fff;
      border: none;
      padding: 10px 25px;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 500;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-common:hover {
      background: #2e59d9;
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }

    .btn-common i {
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .btn-common:hover i {
      transform: translateX(3px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .company-card {
        flex: 0 0 240px;
      }

      .slider-controls {
        margin-top: 10px;
      }
    }

    /* Job Card Styles */
    .job-card-link {
      display: block;
      text-decoration: none;
      color: inherit;
      transition: all 0.3s ease;
    }

    .job-card-link:hover {
      transform: translateY(-5px);
      text-decoration: none;
    }

    .job-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 25px;
      transition: all 0.3s ease;
      border: 1px solid #eee;
      height: 100%;
      position: relative;
    }

    .job-card-link:hover .job-card {
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border-color: #0d72ff;
    }

    .job-card:hover {
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      transform: translateY(-5px);
      border-color: #4e73df;
    }

    .company-logo {
      width: 50px;
      height: 50px;
      border-radius: 8px;
      object-fit: cover;
      margin-bottom: 15px;
      border: 1px solid #f0f2f5;
      padding: 5px;
      background: white;
    }

    .job-header {
      display: flex;
      align-items: flex-start;
      margin-bottom: 15px;
    }

    .job-info {
      margin-left: 15px;
      flex: 1;
    }

    .job-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin: 10px 0;
    }

    .job-meta-item {
      display: flex;
      align-items: center;
      color: #64748b;
      font-size: 13px;
    }

    .job-meta-item i {
      margin-right: 5px;
      color: #94a3b8;
    }

    .job-salary {
      font-weight: 600;
      color: #2563eb;
      margin: 10px 0;
      font-size: 15px;
    }

    .job-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
      padding-top: 15px;
      border-top: 1px solid #f1f5f9;
    }

    .posted-time {
      font-size: 12px;
      color: #94a3b8;
    }

    .job-tag {
      position: absolute;
      top: 15px;
      right: 15px;
      padding: 4px 10px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .job-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .company-name {
      color: #666;
      margin-bottom: 15px;
    }

    .job-meta {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 15px;
    }

    .job-meta-item {
      display: flex;
      align-items: center;
      margin-right: 20px;
      margin-bottom: 8px;
      color: #666;
      font-size: 14px;
    }

    .job-meta-item i {
      margin-right: 5px;
    }

    .job-type {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 500;
    }

    .job-type.full-time {
      background-color: #e8f5e9;
      color: #2e7d32;
    }

    .job-type.internship {
      background-color: #e3f2fd;
      color: #1565c0;
    }

    .job-salary {
      font-weight: 600;
      color: #333;
    }

    .posted-time {
      color: #999;
      font-size: 12px;
    }

    .filter-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 25px 0 30px;
      padding-bottom: 10px;
      overflow-x: auto;
      scrollbar-width: none;
      /* Firefox */
      -ms-overflow-style: none;
      /* IE and Edge */
    }

    .filter-tags::-webkit-scrollbar {
      display: none;
      /* Chrome, Safari, Opera */
    }

    .filter-tag {
      padding: 8px 18px;
      border-radius: 8px;
      background: #f8fafc;
      color: #475569;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid #e2e8f0;
      white-space: nowrap;
      display: flex;
      align-items: center;
    }

    .filter-tag:hover,
    .filter-tag.active {
      background: #4e73df;
      color: white;
      border-color: #4e73df;
      box-shadow: 0 2px 8px rgba(78, 115, 223, 0.3);
    }

    .filter-tag i {
      margin-right: 6px;
      font-size: 16px;
    }

    .no-jobs {
      grid-column: 1 / -1;
      text-align: center;
      padding: 40px 20px;
      color: #64748b;
    }

    .job-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-top: 20px;
    }

    /* Tablet view */
    @media (max-width: 992px) {
      .job-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    /* Mobile view */
    @media (max-width: 768px) {
      .job-grid {
        grid-template-columns: 1fr;
      }

      .job-card {
        max-width: 100%;
        margin: 0 auto 20px;
      }
    }

    /* Save Button Styles */
    .save-job-btn {
      position: absolute;
      bottom: 10px;
      right: 15px;
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid #e0e0e0;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      z-index: 2;
    }

    .save-job-btn:hover {
      background: #f8f9fa;
      transform: scale(1.1);
    }

    .save-job-btn.saved {
      color: #ff5a5f;
    }

    .save-job-btn i {
      font-size: 16px;
    }

    .job-card {
      position: relative;
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
                <a href="index.php" class="button btn btn-common">Untuk Perusahaan</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="mobile-menu" data-logo="assets/img/icon-linkup.png"></div>
    </nav>
    <!-- Navbar End -->

    <div class="container">
      <div class="row space-100 justify-content-center">
        <div class="col-lg-10 col-md-12 col-xs-12">
          <div class="contents">
            <h2 class="head-title">Temukan Pekerjaan yang Tepat Untukmu</h2>
            <p>
              Jelajahi ribuan lowongan pekerjaan terbaru dari berbagai
              perusahaan terpercaya. <br />
              Dapatkan karir impian Anda bersama kami sekarang juga!
            </p>
            <div class="job-search-form">
              <form>
                <div class="row">
                  <div class="col-lg-5 col-md-6 col-xs-12">
                    <div class="form-group">
                      <input class="form-control" type="text" placeholder="Posisi atau Nama Perusahaan" />
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 col-xs-12">
                    <div class="form-group">
                      <div class="search-category-container">
                        <label class="styled-select">
                          <select>
                            <option value="none">Lokasi</option>
                            <option value="none">Jakarta</option>
                            <option value="none">Surabaya</option>
                            <option value="none">Bandung</option>
                            <option value="none">Medan</option>
                            <option value="none">Semarang</option>
                            <option value="none">Bali</option>
                          </select>
                        </label>
                      </div>
                      <i class="lni-map-marker"></i>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 col-xs-12">
                    <div class="form-group">
                      <div class="search-category-container">
                        <label class="styled-select">
                          <select>
                            <option>Semua Kategori</option>
                            <option>Keuangan</option>
                            <option>IT & Teknik</option>
                            <option>Pendidikan/Pelatihan</option>
                            <option>Seni/Desain</option>
                            <option>Penjualan/Pemasaran</option>
                            <option>Healthcare</option>
                            <option>Science</option>
                            <option>Food Services</option>
                          </select>
                        </label>
                      </div>
                      <i class="lni-layers"></i>
                    </div>
                  </div>
                  <div class="col-lg-1 col-md-6 col-xs-12">
                    <button type="submit" class="button">
                      <i class="lni-search"></i>
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <!-- Header Section End -->

  <!-- Category Section Start -->
  <section class="category section bg-gray">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Cari Kategori</h2>
        <p>Kategori lowongan kerja terpopuler, diurutkan berdasarkan popularitas</p>
      </div>
      <div class="row">
        <div class="col-lg-3 col-md-6 col-xs-12 f-category">
          <a href="caripekerjaan.php">
            <div class="icon bg-color-1">
              <i class="lni-home"></i>
            </div>
            <h3>Keuangan</h3>
            <p>(4286 pekerjaan)</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-xs-12 f-category">
          <a href="caripekerjaan.php">
            <div class="icon bg-color-2">
              <i class="lni-world"></i>
            </div>
            <h3>Sale/Marketing</h3>
            <p>(2000 pekerjaan)</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-xs-12 f-category">
          <a href="caripekerjaan.php">
            <div class="icon bg-color-3">
              <i class="lni-book"></i>
            </div>
            <h3>Pendidikan/pelatihan</h3>
            <p>(1450 pekerjaan)</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-xs-12 f-category border-right-0">
          <a href="caripekerjaan.php">
            <div class="icon bg-color-4">
              <i class="lni-display"></i>
            </div>
            <h3>Teknologi</h3>
            <p>(5100 pekerjaan)</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-xs-12 f-category border-bottom-0">
          <a href="caripekerjaan.php">
            <div class="icon bg-color-5">
              <i class="lni-brush"></i>
            </div>
            <h3>Seni/Desain</h3>
            <p>(5079 pekerjaan)</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-xs-12 f-category border-bottom-0">
          <a href="caripekerjaan.php">
            <div class="icon bg-color-6">
              <i class="lni-heart"></i>
            </div>
            <h3>Kesehatan</h3>
            <p>(3235 pekerjaan)</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-xs-12 f-category border-bottom-0">
          <a href="caripekerjaan.php">
            <div class="icon bg-color-7">
              <i class="lni-funnel"></i>
            </div>
            <h3>Sains</h3>
            <p>(1800 pekerjaan)</p>
          </a>
        </div>
        <div class="col-lg-3 col-md-6 col-xs-12 f-category border-right-0 border-bottom-0">
          <a href="caripekerjaan.php">
            <div class="icon bg-color-8">
              <i class="lni-cup"></i>
            </div>
            <h3>Layanan Makanan</h3>
            <p>(4286 pekerjaan)</p>
          </a>
        </div>
      </div>
    </div>
  </section>
  <!-- Category Section End -->

  <!-- Listings Section Start -->
  <section id="job-listings" class="section">
    <div class="container">
      <div class="row">
        <!-- Job Listings -->
        <div class="col-12">
          <div class="section-header">
            <h2 class="section-title">Lowongan Terbaru</h2>
            <p>Temukan pekerjaan impian Anda di sini</p>

            <!-- Filter Tags -->
            <div class="filter-tags">
              <div class="filter-tag active" data-category="all">
                <i class="lni-grid-alt"></i> Semua Pekerjaan
              </div>
              <div class="filter-tag" data-category="it">
                <i class="lni-laptop-phone"></i> IT & Software
              </div>
              <div class="filter-tag" data-category="marketing">
                <i class="lni-bullhorn"></i> Marketing
              </div>
              <div class="filter-tag" data-category="finance">
                <i class="lni-pie-chart"></i> Keuangan
              </div>
              <div class="filter-tag" data-category="design">
                <i class="lni-paint-roller"></i> Desain
              </div>
              <div class="filter-tag" data-category="sales">
                <i class="lni-cart"></i> Sales
              </div>
              <div class="filter-tag" data-category="education">
                <i class="lni-graduation"></i> Pendidikan
              </div>
              <div class="filter-tag" data-category="healthcare">
                <i class="lni-heart"></i> Kesehatan
              </div>
            </div>
          </div>

          <div class="job-grid" id="jobGrid">
            <!-- Job Card 1 - IT -->
            <a href="job-detail.php" class="job-card-link">
              <div class="job-card" data-category="it">
                <button class="save-job-btn" data-job-id="1">
                  <i class="lni-heart"></i>
                </button>
                <div class="job-tag full-time">Full Time</div>
                <div class="job-header">
                  <img src="assets/img/product/img1.png" alt="Company Logo" class="company-logo" />
                  <div class="job-info">
                    <h3 class="job-title">Frontend Dev</h3>
                    <p class="company-name">PT Tech Solutions</p>
                  </div>
                </div>
                <div class="job-meta">
                  <div class="job-meta-item">
                    <i class="lni-map-marker"></i> Jakarta Selatan
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-graduation"></i> Min. S1
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-briefcase"></i> 2+ Tahun
                  </div>
                </div>
                <div class="job-salary">Rp8.000.000 - 15.000.000</div>
                <div class="job-footer">
                  <span class="job-type">IT & Software</span>
                  <span class="posted-time"><i class="lni-timer"></i> 2 jam lalu</span>
                </div>
              </div>
            </a>

            <!-- Job Card 2 - Marketing -->
            <a href="job-detail.php" class="job-card-link">
              <div class="job-card" data-category="marketing">
                <button class="save-job-btn" data-job-id="2">
                  <i class="lni-heart"></i>
                </button>
                <div class="job-tag full-time">Full Time</div>
                <div class="job-header">
                  <img src="assets/img/product/img2.png" alt="Company Logo" class="company-logo" />
                  <div class="job-info">
                    <h3 class="job-title">Digital Marketer</h3>
                    <p class="company-name">PT Digital Nusantara</p>
                  </div>
                </div>
                <div class="job-meta">
                  <div class="job-meta-item">
                    <i class="lni-map-marker"></i> Jakarta Pusat
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-graduation"></i> Min. D3
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-briefcase"></i> 1+ Tahun
                  </div>
                </div>
                <div class="job-salary">Rp6.500.000 - 10.000.000</div>
                <div class="job-footer">
                  <span class="job-type">Marketing</span>
                  <span class="posted-time"><i class="lni-timer"></i> 1 hari lalu</span>
                </div>
              </div>
            </a>

            <!-- Job Card 3 - Finance -->
            <a href="job-detail.php" class="job-card-link">
              <div class="job-card" data-category="finance">
                <button class="save-job-btn" data-job-id="3">
                  <i class="lni-heart"></i>
                </button>
                <div class="job-tag full-time">Full Time</div>
                <div class="job-header">
                  <img src="assets/img/product/img3.png" alt="Company Logo" class="company-logo" />
                  <div class="job-info">
                    <h3 class="job-title">Accounting</h3>
                    <p class="company-name">PT BCA Finance</p>
                  </div>
                </div>
                <div class="job-meta">
                  <div class="job-meta-item">
                    <i class="lni-map-marker"></i> Tangerang Selatan
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-graduation"></i> Min. S1 Akuntansi
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-briefcase"></i> 1+ Tahun
                  </div>
                </div>
                <div class="job-salary">Rp7.500.000 - 12.000.000</div>
                <div class="job-footer">
                  <span class="job-type">Finance</span>
                  <span class="posted-time"><i class="lni-timer"></i> 5 jam lalu</span>
                </div>
              </div>
            </a>

            <!-- Job Card 4 - Design -->
            <a href="job-detail.php" class="job-card-link">
              <div class="job-card" data-category="design">
                <button class="save-job-btn" data-job-id="4">
                  <i class="lni-heart"></i>
                </button>
                <div class="job-tag part-time">Part Time</div>
                <div class="job-header">
                  <img src="assets/img/product/img1.png" alt="Company Logo" class="company-logo" />
                  <div class="job-info">
                    <h3 class="job-title">UI/UX Designer</h3>
                    <p class="company-name">PT Kreatif Digital</p>
                  </div>
                </div>
                <div class="job-meta">
                  <div class="job-meta-item">
                    <i class="lni-map-marker"></i> Bandung
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-graduation"></i> Min. D3
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-briefcase"></i> 1+ Tahun
                  </div>
                </div>
                <div class="job-salary">Rp150.000 - 250.000</div>
                <div class="job-footer">
                  <span class="job-type">Design</span>
                  <span class="posted-time"><i class="lni-timer"></i> 2 hari lalu</span>
                </div>
              </div>
            </a>

            <!-- Job Card 5 - Sales -->
            <a href="job-detail.php" class="job-card-link">
              <div class="job-card" data-category="sales">
                <button class="save-job-btn" data-job-id="5">
                  <i class="lni-heart"></i>
                </button>
                <div class="job-tag full-time">Full Time</div>
                <div class="job-header">
                  <img src="assets/img/product/img2.png" alt="Company Logo" class="company-logo" />
                  <div class="job-info">
                    <h3 class="job-title">Sales Executive</h3>
                    <p class="company-name">PT Global Jaya</p>
                  </div>
                </div>
                <div class="job-meta">
                  <div class="job-meta-item">
                    <i class="lni-map-marker"></i> Jakarta Pusat
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-graduation"></i> Min. SMA/SMK
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-briefcase"></i> 1-3 Tahun
                  </div>
                </div>
                <div class="job-salary">Rp4.000.000</div>
                <div class="job-footer">
                  <span class="job-type">Sales</span>
                  <span class="posted-time"><i class="lni-timer"></i> 1 minggu lalu</span>
                </div>
              </div>
            </a>

            <!-- Job Card 6 - Education -->
            <a href="job-detail.php" class="job-card-link">
              <div class="job-card" data-category="education">
                <button class="save-job-btn" data-job-id="6">
                  <i class="lni-heart"></i>
                </button>
                <div class="job-tag full-time">Full Time</div>
                <div class="job-header">
                  <img src="assets/img/product/img3.png" alt="Company Logo" class="company-logo" />
                  <div class="job-info">
                    <h3 class="job-title">Guru Matematika</h3>
                    <p class="company-name">SMA Bina Bangsa</p>
                  </div>
                </div>
                <div class="job-meta">
                  <div class="job-meta-item">
                    <i class="lni-map-marker"></i> Depok
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-graduation"></i> Min. S1 Pendidikan
                  </div>
                  <div class="job-meta-item">
                    <i class="lni-briefcase"></i> 2+ Tahun
                  </div>
                </div>
                <div class="job-salary">Rp5.000.000 - 8.000.000</div>
                <div class="job-footer">
                  <span class="job-type">Education</span>
                  <span class="posted-time"><i class="lni-timer"></i> 3 hari lalu</span>
                </div>
              </div>
            </a>
          </div>
          <!-- End Job Grid -->

          <!-- Load More Button -->
          <div class="col-12 text-center mt-4">
            <a href="login.php" class="btn btn-common">Muat Lebih Banyak</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Listings Section End -->

  <!-- Browse jobs Section Start -->
  <div id="browse-jobs" class="section bg-gray">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
          <div class="text-wrapper">
            <div>
              <h3>1000+ Telusuri Lowongan Kerja</h3>
              <p>
                Telusuri semua lowongan pekerjaan di web. Dapatkan estimasi gaji personalisasi Anda dan informasi
                terkait lowongan kerja
                perusahaan di seluruh dunia. Lowongan yang tepat ada di luar sana.
              </p>
              <a class="btn btn-common" href="login.php">Search jobs</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
          <div class="img-thumb">
            <img class="img-fluid" src="assets/img/search.png" alt="" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Browse jobs Section End -->

  <!-- Top Companies Section -->
  <section class="top-companies section bg-gray">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Temukan Perusahaan Anda Berikutnya</h2>
        <p>Ribuan perusahaan terkemuka memposting lowongan kerja di sini</p>
      </div>

      <div class="company-slider-container">
        <div class="company-slider">
          <!-- Company Card 1 -->
          <div class="company-card">
            <div class="company-logo-container">
              <img src="assets/img/product/img1.png" alt="Gojek" class="company-logo" />
            </div>
            <h3 class="company-name">Gojek</h3>
            <div class="job-count">24 Lowongan Tersedia</div>
            <a href="company-details.php?id=1" class="btn-view-jobs">Lihat Perusahaan</a>
          </div>

          <!-- Company Card 2 -->
          <div class="company-card">
            <div class="company-logo-container">
              <img src="assets/img/product/img2.png" alt="Tokopedia" class="company-logo" />
            </div>
            <h3 class="company-name">Tokopedia</h3>
            <div class="job-count">18 Lowongan Tersedia</div>
            <a href="company-details.php?id=1" class="btn-view-jobs">Lihat Perusahaan</a>
          </div>

          <!-- Company Card 3 -->
          <div class="company-card">
            <div class="company-logo-container">
              <img src="assets/img/product/img3.png" alt="Traveloka" class="company-logo" />
            </div>
            <h3 class="company-name">Traveloka</h3>
            <div class="job-count">15 Lowongan Tersedia</div>
            <a href="company-details.php?id=1" class="btn-view-jobs">Lihat Perusahaan</a>
          </div>

          <!-- Company Card 4 -->
          <div class="company-card">
            <div class="company-logo-container">
              <img src="assets/img/product/img1.png" alt="Shopee" class="company-logo" />
            </div>
            <h3 class="company-name">Shopee</h3>
            <div class="job-count">22 Lowongan Tersedia</div>
            <a href="company-details.php?id=1" class="btn-view-jobs">Lihat Perusahaan</a>
          </div>

          <!-- Company Card 5 -->
          <div class="company-card">
            <div class="company-logo-container">
              <img src="assets/img/product/img2.png" alt="Bukalapak" class="company-logo" />
            </div>
            <h3 class="company-name">Bukalapak</h3>
            <div class="job-count">12 Lowongan Tersedia</div>
            <a href="company-details.php?id=1" class="btn-view-jobs">Lihat Perusahaan</a>
          </div>
        </div>

        <div class="slider-controls">
          <button class="slider-arrow prev">
            <i class="lni-chevron-left"></i>
          </button>
          <div class="slider-dots"></div>
          <button class="slider-arrow next">
            <i class="lni-chevron-right"></i>
          </button>
        </div>

        <!-- View All Companies Button -->
        <div class="row mt-4">
          <div class="col-12 text-center">
            <a href="perusahaan.php" class="btn btn-common">
              <i class="lni-arrow-right-circle"></i> Lihat Semua Perusahaan
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Work Section Start -->
  <section class="how-it-works section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Cara Kerjanya?</h2>
        <p>
          Temukan cara mudah mendapatkan pekerjaan impian Anda <br />
          hanya dalam beberapa langkah sederhana.
        </p>
      </div>
      <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
          <div class="work-process">
            <span class="process-icon">
              <i class="lni-user"></i>
            </span>
            <h4>Buat Akun</h4>
            <p>
              Daftarkan diri Anda dan buat profil profesional untuk memulai
              pencarian kerja. Prosesnya cepat dan mudah, hanya membutuhkan
              beberapa menit saja.
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
          <div class="work-process step-2">
            <span class="process-icon">
              <i class="lni-search"></i>
            </span>
            <h4>Cari Lowongan</h4>
            <p>
              Temukan ribuan lowongan kerja terbaru dari berbagai perusahaan
              terpercaya. Gunakan filter untuk mempersempit pencarian sesuai
              kriteria Anda.
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
          <div class="work-process step-3">
            <span class="process-icon">
              <i class="lni-cup"></i>
            </span>
            <h4>Lamar Sekarang</h4>
            <p>
              Kirim lamaran Anda dengan mudah dan pantau status lamaran
              langsung melalui dashboard pribadi Anda.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- How It Work Section End -->

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
  <script>
    // Save Job Functionality
    document.addEventListener('DOMContentLoaded', function () {
      // Load saved jobs from localStorage
      const savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');

      // Initialize save buttons
      document.querySelectorAll('.save-job-btn').forEach(button => {
        const jobId = button.getAttribute('data-job-id');
        if (savedJobs.includes(jobId)) {
          button.classList.add('saved');
          button.innerHTML = '<i class="lni-heart-filled"></i>';
        }

        // Add click event
        button.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();

          const jobId = this.getAttribute('data-job-id');
          const savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
          const jobIndex = savedJobs.indexOf(jobId);

          if (jobIndex === -1) {
            // Save job
            savedJobs.push(jobId);
            this.classList.add('saved');
            this.innerHTML = '<i class="lni-heart-filled"></i>';
            // Show success message or animation
            this.style.transform = 'scale(1.2)';
            setTimeout(() => {
              this.style.transform = 'scale(1)';
            }, 200);
          } else {
            // Unsave job
            savedJobs.splice(jobIndex, 1);
            this.classList.remove('saved');
            this.innerHTML = '<i class="lni-heart"></i>';
          }

          // Update localStorage
          localStorage.setItem('savedJobs', JSON.stringify(savedJobs));
        });
      });
    });

    // Company Slider Functionality
    document.addEventListener("DOMContentLoaded", function () {
      const slider = document.querySelector(".company-slider");
      const slides = document.querySelectorAll(".company-card");
      const dotsContainer = document.querySelector(".slider-dots");
      const prevBtn = document.querySelector(".slider-arrow.prev");
      const nextBtn = document.querySelector(".slider-arrow.next");
      let currentIndex = 0;
      const visibleSlides = 3; // Number of slides to show at once

      // Create dots
      slides.forEach((_, index) => {
        const dot = document.createElement("div");
        dot.classList.add("slider-dot");
        if (index === 0) dot.classList.add("active");
        dot.addEventListener("click", () => {
          goToSlide(index);
        });
        dotsContainer.appendChild(dot);
      });

      const dots = document.querySelectorAll(".slider-dot");

      function updateDots() {
        dots.forEach((dot, index) => {
          if (index === currentIndex) {
            dot.classList.add("active");
          } else {
            dot.classList.remove("active");
          }
        });
      }

      function goToSlide(index) {
        if (index < 0) index = 0;
        if (index > slides.length - visibleSlides)
          index = slides.length - visibleSlides;

        currentIndex = index;
        const slideWidth = slides[0].offsetWidth + 20; // 20px gap
        slider.scrollTo({
          left: slideWidth * index,
          behavior: "smooth",
        });

        updateDots();
      }

      // Navigation
      prevBtn.addEventListener("click", () => {
        if (currentIndex > 0) {
          goToSlide(currentIndex - 1);
        }
      });

      nextBtn.addEventListener("click", () => {
        if (currentIndex < slides.length - visibleSlides) {
          goToSlide(currentIndex + 1);
        }
      });

      // Auto-scroll to current slide on window resize
      window.addEventListener("resize", () => {
        goToSlide(currentIndex);
      });
    });

    // Job Filtering Functionality
    $(document).ready(function () {
      // Filter jobs when a category is clicked
      $(".filter-tag").on("click", function () {
        const category = $(this).data("category");

        // Update active state
        $(".filter-tag").removeClass("active");
        $(this).addClass("active");

        // Show/hide jobs based on category
        if (category === "all") {
          $(".job-card").fadeIn(300);
        } else {
          $(".job-card").fadeOut(100);
          $(`.job-card[data-category="${category}"]`).fadeIn(300);
        }

        // Show message if no jobs found
        setTimeout(() => {
          const visibleJobs = $(".job-card:visible").length;
          if (visibleJobs === 0) {
            if ($("#noJobsMessage").length === 0) {
              $("#jobGrid").append(
                '<div class="no-jobs" id="noJobsMessage">Tidak ada lowongan yang tersedia untuk kategori ini</div>'
              );
            }
          } else {
            $("#noJobsMessage").remove();
          }
        }, 300);
      });

      // Add hover effect for job cards
      $(".job-card").hover(
        function () {
          $(this).find(".job-title").css("color", "#4e73df");
        },
        function () {
          $(this).find(".job-title").css("color", "");
        }
      );
    });
  </script>
  <script src="assets/js/main.js"></script>
</body>

</html>