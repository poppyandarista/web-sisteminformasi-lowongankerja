<?php
session_start();
include 'config/database.php';
$db = new database();

// Get statistics data
$statistics = $db->get_statistics();

// Get all companies data
$all_companies = $db->tampil_semua_perusahaan();
?>

<style>
  .company-logo img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #f0f2f5;
    padding: 5px;
    background: white;
  }

  .company-logo {
    text-align: center;
    margin-bottom: 15px;
  }
</style>
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
  <link rel="icon" type="image/png" href="assets/img/favicon.png">

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
  <!-- ===== Header Start ===== -->
  <?php include("header.php") ?>

  <!-- ===== Header End -->

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
              <h3><span class="counter"><?php echo number_format($statistics['total_jobs']); ?></span></h3>
              <p>Lowongan Aktif</p>
            </div>
          </div>
        </div>
        <!-- End counter -->
        <!-- Start counter -->
        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="counter-box">
            <div class="icon"><i class="lni-briefcase"></i></div>
            <div class="fact-count">
              <h3><span class="counter"><?php echo number_format($statistics['total_companies']); ?></span></h3>
              <p>Perusahaan</p>
            </div>
          </div>
        </div>
        <!-- End counter -->
        <!-- Start counter -->
        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="counter-box">
            <div class="icon"><i class="lni-pencil-alt"></i></div>
            <div class="fact-count">
              <h3><span class="counter"><?php echo number_format($statistics['total_users']); ?></span></h3>
              <p>Pelamar</p>
            </div>
          </div>
        </div>
        <!-- End counter -->
        <!-- Start counter -->
        <div class="col-lg-3 col-md-6 col-xs-12">
          <div class="counter-box">
            <div class="icon"><i class="lni-save"></i></div>
            <div class="fact-count">
              <h3><span class="counter"><?php echo number_format($statistics['total_applications']); ?></span></h3>
              <p>Lamaran</p>
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
          Lamar Kerja ke <?php echo $statistics['total_companies']; ?>+ Perusahaan Terbaik di Indonesia
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
          <?php if (!empty($all_companies)): ?>
            <?php foreach ($all_companies as $company): ?>
              <div class="col-lg-4 col-md-6 col-xs-12">
                <a href="company-details.php?id=<?php echo $company['id_perusahaan']; ?>" class="company-card">
                  <div class="company-logo">
                    <img src="adminpanel/src/images/company/<?php echo htmlspecialchars($company['logo_display']); ?>"
                      alt="<?php echo htmlspecialchars($company['nama_perusahaan']); ?>" />
                  </div>
                  <div class="company-info">
                    <h4><?php echo htmlspecialchars($company['nama_perusahaan']); ?></h4>
                    <div class="meta">
                      <span><i class="lni-briefcase"></i>
                        <?php if ($company['jumlah_lowongan'] > 0): ?>
                          <?php echo $company['jumlah_lowongan']; ?> Lowongan
                        <?php else: ?>
                          Belum ada lowongan
                        <?php endif; ?>
                      </span>
                      <span><i class="lni-map-marker"></i>
                        <?php echo htmlspecialchars($company['kota_perusahaan'] ?: 'Indonesia'); ?>
                      </span>
                    </div>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center">
              <p>Belum ada perusahaan yang terdaftar.</p>
            </div>
          <?php endif; ?>
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
                <li><a href="jelajahi-perusahaan.php">Perusahaan</a></li>
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

</body>

</html>