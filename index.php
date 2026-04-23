<?php
session_start();
include 'config/database.php';
$db = new database();

// Get filter parameters from URL
$search_keyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : '';
$selected_lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : 'all';
$selected_perusahaan = isset($_GET['perusahaan']) ? $_GET['perusahaan'] : 'all';
$selected_kategori = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : 'all';

// Get filter data from database
$categories = $db->tampil_data_kategori();
$filter_categories = $db->tampil_kategori_filter();
$lokasi_options = $db->get_lokasi_filter();
$perusahaan_options = $db->get_perusahaan_filter();

// Get jobs based on filters
$latest_jobs = $db->filter_lowongan($search_keyword, $selected_lokasi, $selected_kategori);

// Get companies for slider
$companies = $db->tampil_data_perusahaan();

// Icon mapping untuk kategori
$icon_mapping = [
  'Keuangan' => 'lni-home',
  'Sale/Marketing' => 'lni-world',
  'Pendidikan/Pelatihan' => 'lni-book',
  'Teknologi' => 'lni-display',
  'Seni/Desain' => 'lni-brush',
  'Kesehatan' => 'lni-heart',
  'Sains' => 'lni-funnel',
  'Layanan Makanan' => 'lni-cup'
];

// Color mapping untuk kategori
$color_mapping = [
  0 => 'bg-color-1',
  1 => 'bg-color-2',
  2 => 'bg-color-3',
  3 => 'bg-color-4',
  4 => 'bg-color-5',
  5 => 'bg-color-6',
  6 => 'bg-color-7',
  7 => 'bg-color-8'
];

// Function to calculate time ago
function timeAgo($date)
{
  $timestamp = strtotime($date);
  $current_time = time();
  $time_diff = $current_time - $timestamp;

  if ($time_diff < 3600) {
    $minutes = floor($time_diff / 60);
    return $minutes <= 1 ? "Baru saja" : "$minutes menit lalu";
  } elseif ($time_diff < 86400) {
    $hours = floor($time_diff / 3600);
    return $hours <= 1 ? "1 jam lalu" : "$hours jam lalu";
  } elseif ($time_diff < 2592000) {
    $days = floor($time_diff / 86400);
    return $days <= 1 ? "1 hari lalu" : "$days hari lalu";
  } else {
    return date("d M Y", $timestamp);
  }
}

// Function to format salary
function formatSalary($salary)
{
  if ($salary && $salary > 0) {
    return "Rp" . number_format($salary, 0, ',', '.');
  }
  return "Negosiasi";
}

// Function to generate filter URL
function generateFilterUrl($params = []) {
    $current_params = $_GET;
    foreach ($params as $key => $value) {
        if ($value === 'all' || $value === '') {
            unset($current_params[$key]);
        } else {
            $current_params[$key] = $value;
        }
    }
    return 'index.php' . (empty($current_params) ? '' : '?' . http_build_query($current_params));
}

// Function to keep search parameters in URL
function keepParam($paramName, $default = '') {
    return isset($_GET[$paramName]) ? htmlspecialchars($_GET[$paramName]) : $default;
}
?>
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
      width: 160px;
      height: 160px;
      border-radius: 12px;
      object-fit: contain;
      margin: 0 auto 20px;
      display: block;
      background: transparent;
      padding: 0;
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

    .job-count-badge {
      margin-left: 6px;
      font-size: 11px;
      opacity: 0.8;
      font-weight: 400;
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

    /* Active Filters Styles */
.active-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 15px;
}

.filter-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.filter-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 5px 12px;
    background: #e7f1ff;
    color: #0d6efd;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

.filter-badge .remove-filter {
    color: #0d6efd;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    margin-left: 5px;
}

.filter-badge .remove-filter:hover {
    color: #dc3545;
}

.clear-all-filters {
    margin-left: auto;
    color: #dc3545;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.clear-all-filters:hover {
    background: #dc3545;
    color: white;
    text-decoration: none;
}

.job-count-info {
    padding: 10px 0;
    color: #eef1f3ff;
    font-size: 14px;
    border-bottom: 1px solid #e9ecef;
}

.job-count-info i {
    margin-right: 5px;
    color: #eaecefff;
}
  </style>
</head>

<body>
  <!-- ===== Header Start ===== -->
  <?php include("header.php") ?>
  <!-- ===== Header End -->

  <!-- Hero Section Start -->
  <header id="home" class="hero-area">
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
    <form method="GET" action="index.php">
        <div class="row">
            <div class="col-lg-5 col-md-6 col-xs-12">
                <div class="form-group">
                    <input class="form-control" type="text" 
                           name="search_keyword" 
                           placeholder="Posisi atau Nama Perusahaan"
                           value="<?php echo htmlspecialchars($search_keyword); ?>" />
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-xs-12">
                <div class="form-group">
                    <div class="search-category-container">
                        <label class="styled-select">
                            <select name="lokasi">
                                <option value="all" <?php echo $selected_lokasi == 'all' ? 'selected' : ''; ?>>Semua Lokasi</option>
                                <?php foreach ($lokasi_options as $lokasi): ?>
                                    <option value="<?php echo htmlspecialchars($lokasi['nama_lokasi']); ?>" 
                                        <?php echo $selected_lokasi == $lokasi['nama_lokasi'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($lokasi['nama_lokasi']); ?>
                                    </option>
                                <?php endforeach; ?>
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
                            <select name="filter_kategori">
                                <option value="all" <?php echo $selected_kategori == 'all' ? 'selected' : ''; ?>>Semua Kategori</option>
                                <?php foreach ($filter_categories as $kategori): ?>
                                    <option value="<?php echo $kategori['id_kategori']; ?>"
                                        <?php echo $selected_kategori == $kategori['id_kategori'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                        (<?php echo $kategori['jumlah_lowongan']; ?>)
                                    </option>
                                <?php endforeach; ?>
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
        
<!-- Setelah filter tags, tambahkan -->
<div class="row mb-3">
    <div class="col-12">
        <div class="job-count-info">
            <i class="lni-briefcase"></i> 
            Menampilkan <strong><?php echo count($latest_jobs); ?></strong> lowongan pekerjaan
            <?php if ($search_keyword || $selected_lokasi != 'all' || $selected_kategori != 'all'): ?>
                dari hasil pencarian
            <?php endif; ?>
        </div>
    </div>
</div>

        <!-- Tampilkan filter aktif -->
        <?php if ($search_keyword || $selected_lokasi != 'all' || $selected_perusahaan != 'all' || $selected_kategori != 'all'): ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="active-filters">
                    <span class="filter-label">Filter Aktif:</span>
                    <?php if ($search_keyword): ?>
                        <span class="filter-badge">
                            Pencarian: <?php echo htmlspecialchars($search_keyword); ?>
                            <a href="<?php echo generateFilterUrl(['search_keyword' => 'all']); ?>" class="remove-filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($selected_lokasi != 'all'): ?>
                        <span class="filter-badge">
                            Lokasi: <?php echo htmlspecialchars($selected_lokasi); ?>
                            <a href="<?php echo generateFilterUrl(['lokasi' => 'all']); ?>" class="remove-filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($selected_kategori != 'all'): ?>
                        <span class="filter-badge">
                            Kategori: <?php 
                                $kat_nama = '';
                                foreach ($filter_categories as $kat) {
                                    if ($kat['id_kategori'] == $selected_kategori) {
                                        $kat_nama = $kat['nama_kategori'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($kat_nama);
                            ?>
                            <a href="<?php echo generateFilterUrl(['filter_kategori' => 'all']); ?>" class="remove-filter">×</a>
                        </span>
                    <?php endif; ?>
                    <a href="index.php" class="clear-all-filters">Reset Semua Filter</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </form>
</div>
          </div>
        </div>
      </div>
    </div>
  </header>
  <!-- Hero Section End -->

  

  <!-- Category Section Start -->
  <section class="category section bg-gray">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Cari Kategori</h2>
        <p>Kategori lowongan kerja terpopuler, diurutkan berdasarkan popularitas</p>
      </div>
      <div class="row" id="categoryGrid">
        <?php
        $index = 0;
        $visible_count = 8; // 2 baris x 4 kolom
        foreach ($categories as $category):
          $icon_class = $icon_mapping[$category['nama_kategori']] ?? 'lni-folder';
          $color_class = $color_mapping[$index % 8];
          $border_classes = '';
          $hidden_class = ($index >= $visible_count) ? 'category-hidden' : '';

          // Add border classes untuk layout grid
          if ($index % 4 == 3)
            $border_classes .= ' border-right-0';
          if ($index >= 4)
            $border_classes .= ' border-bottom-0';
          ?>
          <div class="col-lg-3 col-md-6 col-xs-12 f-category<?php echo $border_classes . ' ' . $hidden_class; ?>"
            data-category-index="<?php echo $index; ?>">
            <a
              href="caripekerjaan.php?kategori=<?php echo urlencode($category['nama_kategori']); ?>&id=<?php echo $category['id_kategori']; ?>">
              <div class="icon <?php echo $color_class; ?>">
                <i class="<?php echo $icon_class; ?>"></i>
              </div>
              <h3><?php echo htmlspecialchars($category['nama_kategori']); ?></h3>
              <p>(<?php echo number_format($category['jumlah_lowongan']); ?> pekerjaan)</p>
            </a>
          </div>
          <?php
          $index++;
        endforeach;
        ?>
      </div>

      <?php if (count($categories) > $visible_count): ?>
        <div class="text-center mt-4">
          <button id="loadMoreCategories" class="btn btn-common">
            <i class="lni-plus"></i> Lihat Lainnya
          </button>
          <button id="showLessCategories" class="btn btn-common" style="display: none;">
            <i class="lni-minus"></i> Tampilkan Sedikit
          </button>
        </div>
      <?php endif; ?>
    </div>
  </section>
  <!-- Category Section End -->

  <style>
    .category-hidden {
      display: none;
    }

    .category-visible {
      display: block;
      animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Job Tag Positioning */
    .job-card {
      position: relative;
    }

    .job-tag-wrapper {
      position: absolute;
      bottom: 52px;
      /* diturunkan sedikit */
      right: 40px;
      z-index: 2;
    }

    .job-tag {
      background: #4e73df;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px rgba(78, 115, 223, 0.3);
      white-space: nowrap;
      transition: all 0.3s ease;
    }

    .job-tag.fulltime,
    .job-tag.full-time {
      background: #28a745;
      box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .job-tag.parttime,
    .job-tag.part-time {
      background: #ffc107;
      color: #212529;
      box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
    }

    .job-tag.freelance {
      background: #6f42c1;
      box-shadow: 0 2px 8px rgba(111, 66, 193, 0.3);
    }

    .job-tag:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(78, 115, 223, 0.4);
    }

    .job-tag.fulltime:hover,
    .job-tag.full-time:hover {
      box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }

    .job-tag.parttime:hover,
    .job-tag.part-time:hover {
      box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
    }

    .job-tag.freelance:hover {
      box-shadow: 0 4px 12px rgba(111, 66, 193, 0.4);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .job-tag-wrapper {
        top: 10px;
        right: 50px;
      }

      .job-tag {
        font-size: 10px;
        padding: 3px 10px;
      }
    }

    @media (max-width: 576px) {
      .job-tag-wrapper {
        top: 8px;
        right: 45px;
      }

      .job-tag {
        font-size: 9px;
        padding: 2px 8px;
      }
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const loadMoreBtn = document.getElementById('loadMoreCategories');
      const showLessBtn = document.getElementById('showLessCategories');
      const categoryGrid = document.getElementById('categoryGrid');
      const hiddenCategories = document.querySelectorAll('.category-hidden');

      let currentlyVisible = 8; // Awalnya 2 baris
      const increment = 4; // Tambah 1 baris (4 cards) setiap klik

      if (loadMoreBtn && hiddenCategories.length > 0) {
        loadMoreBtn.addEventListener('click', function () {
          const nextHidden = document.querySelectorAll('.category-hidden');
          const toShow = Math.min(increment, nextHidden.length);

          for (let i = 0; i < toShow; i++) {
            nextHidden[i].classList.remove('category-hidden');
            nextHidden[i].classList.add('category-visible');
          }

          currentlyVisible += toShow;

          // Sembunyikan button jika semua kategori sudah visible
          if (currentlyVisible >= <?php echo count($categories); ?>) {
            loadMoreBtn.style.display = 'none';
            showLessBtn.style.display = 'inline-flex';
          }
        });

        showLessBtn.addEventListener('click', function () {
          // Sembunyikan semua kategori kecuali 8 pertama
          const allCategories = document.querySelectorAll('[data-category-index]');
          allCategories.forEach((category, index) => {
            if (index >= 8) {
              category.classList.add('category-hidden');
              category.classList.remove('category-visible');
            }
          });

          loadMoreBtn.style.display = 'inline-flex';
          showLessBtn.style.display = 'none';
          currentlyVisible = 8;

          // Scroll ke category section
          document.querySelector('.category').scrollIntoView({ behavior: 'smooth' });
        });
      }
    });
  </script>

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
    <a href="<?php echo generateFilterUrl(['filter_kategori' => 'all']); ?>"
        class="filter-tag <?php echo $selected_kategori === 'all' ? 'active' : ''; ?>">
        <i class="lni-grid-alt"></i> Semua Pekerjaan
    </a>
    <?php foreach ($filter_categories as $kategori): ?>
        <a href="<?php echo generateFilterUrl(['filter_kategori' => $kategori['id_kategori']]); ?>"
            class="filter-tag <?php echo $selected_kategori == $kategori['id_kategori'] ? 'active' : ''; ?>">
            <i class="<?php echo $icon_mapping[$kategori['nama_kategori']] ?? 'lni-folder'; ?>"></i>
            <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
            <?php if ($kategori['jumlah_lowongan'] > 0): ?>
                <span class="job-count-badge">(<?php echo $kategori['jumlah_lowongan']; ?>)</span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>
          </div>

          <div class="job-grid" id="jobGrid">
            <?php if (!empty($latest_jobs)): ?>
              <?php foreach (array_slice($latest_jobs, 0, 6) as $index => $job): ?>
                <a href="job-detail.php?id=<?php echo $job['id_lowongan']; ?>" class="job-card-link">
                  <div class="job-card"
                    data-category="<?php echo strtolower(str_replace([' ', '/', '&'], '', $job['nama_kategori'])); ?>">
                    <button class="save-job-btn" data-job-id="<?php echo $job['id_lowongan']; ?>">
                      <i class="lni-heart"></i>
                    </button>
                    <div class="job-tag-wrapper">
                      <span class="job-tag <?php echo strtolower(str_replace([' ', '/'], '', $job['nama_jenis'])); ?>">
                        <?php echo htmlspecialchars($job['nama_jenis']); ?>
                      </span>
                    </div>
                    <div class="job-header">
                      <img
                        src="adminpanel/src/images/jobs/<?php echo $job['gambar'] ?: 'img' . (($index % 3) + 1) . '.png'; ?>"
                        alt="Company Logo" class="company-logo" />
                      <div class="job-info">
                        <h3 class="job-title">
                          <?php echo htmlspecialchars($job['judul_lowongan']); ?>
                        </h3>
                        <p class="company-name">
                          <?php echo htmlspecialchars($job['nama_perusahaan']); ?>
                        </p>
                      </div>
                    </div>
                    <div class="job-meta">
                      <div class="job-meta-item">
                        <i class="lni-map-marker"></i>
                        <?php echo htmlspecialchars($job['nama_kota'] ?: 'Indonesia'); ?>
                      </div>
                      <div class="job-meta-item">
                        <i class="lni-briefcase"></i>
                        <?php echo htmlspecialchars($job['nama_jenis']); ?>
                      </div>
                      <div class="job-meta-item">
                        <i class="lni-graduation"></i> Min. S1
                      </div>
                    </div>
                    <div class="job-salary">
                      <?php echo formatSalary($job['gaji_lowongan']); ?>
                    </div>
                    <div class="job-footer">
                      <span class="job-type">
                        <?php echo htmlspecialchars($job['nama_kategori']); ?>
                      </span>
                      <span class="posted-time"><i class="lni-timer"></i>
                        <?php echo timeAgo($job['tanggal_posting']); ?>
                      </span>
                    </div>
                  </div>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12">
                <div class="no-jobs">
                  <i class="lni-briefcase" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                  <h4>Belum ada lowongan tersedia</h4>
                  <p>Lowongan pekerjaan terbaru akan segera tersedia. Silakan cek kembali nanti!</p>
                </div>
              </div>
            <?php endif; ?>
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
          <?php if (!empty($companies)): ?>
            <?php foreach ($companies as $company): ?>
              <div class="company-card">
                <img src="adminpanel/src/images/company/<?php echo htmlspecialchars($company['logo_display']); ?>"
                  alt="<?php echo htmlspecialchars($company['nama_perusahaan']); ?>" class="company-logo" />
                <h3 class="company-name"><?php echo htmlspecialchars($company['nama_perusahaan']); ?></h3>
                <div class="job-count">
                  <?php if ($company['jumlah_lowongan'] > 0): ?>
                    <?php echo $company['jumlah_lowongan']; ?> Lowongan Tersedia
                  <?php else: ?>
                    Belum ada lowongan
                  <?php endif; ?>
                </div>
                <a href="company-details.php?id=<?php echo $company['id_perusahaan']; ?>" class="btn-view-jobs">Lihat
                  Perusahaan</a>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center">
              <p>Belum ada perusahaan yang terdaftar.</p>
            </div>
          <?php endif; ?>
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
            <a href="jelajahi-perusahaan.php" class="btn btn-common">
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
                <img src="assets/img/logo2.png" alt="" />
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
  <script>
    // Save Job Functionality
    document.addEventListener('DOMContentLoaded', function () {
      // Migrate old localStorage format (only IDs) to new format (full objects)
      function migrateSavedJobs() {
        const oldSavedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');

        // Check if data is old format (array of strings/IDs only)
        if (oldSavedJobs.length > 0 && typeof oldSavedJobs[0] === 'string') {
          // Clear old format
          localStorage.setItem('savedJobs', '[]');
          console.log('Migrated old saved jobs format to new format');
        }
      }

      // Run migration
      migrateSavedJobs();

      // Load saved jobs from localStorage
      const savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');

      // Initialize save buttons
      document.querySelectorAll('.save-job-btn').forEach(button => {
        const jobId = button.getAttribute('data-job-id');

        // Check if job is already saved
        const isSaved = savedJobs.some(job => job.id === jobId);
        if (isSaved) {
          button.classList.add('saved');
          button.innerHTML = '<i class="lni-heart-filled"></i>';
        }

        // Add click event
        button.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();

          const jobId = this.getAttribute('data-job-id');
          const jobData = getJobDataFromCard(this);
          let savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
          const jobIndex = savedJobs.findIndex(job => job.id === jobId);

          if (jobIndex === -1) {
            // Save job with full data
            jobData.savedDate = new Date().toLocaleDateString('id-ID');
            savedJobs.push(jobData);
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

      // Function to get job data from card
      function getJobDataFromCard(button) {
        const card = button.closest('.job-card');

        const titleElement = card.querySelector('.job-title');
        const companyElement = card.querySelector('.company-name');
        const salaryElement = card.querySelector('.job-salary');
        const imageElement = card.querySelector('.company-logo');

        // Get location (first meta item with map-marker)
        const locationItem = card.querySelector('.job-meta-item .lni-map-marker')?.parentElement;
        let locationText = 'Indonesia';
        if (locationItem) {
          locationText = locationItem.textContent.replace('', '').trim();
        }

        // Get type (second meta item with briefcase)
        const typeItem = card.querySelector('.job-meta-item .lni-briefcase')?.parentElement;
        let typeText = 'Full-time';
        if (typeItem) {
          typeText = typeItem.textContent.replace('', '').trim();
        }

        // Get salary
        let salaryText = 'Negosiasi';
        if (salaryElement) {
          salaryText = salaryElement.textContent.trim();
        }

        const jobData = {
          id: button.getAttribute('data-job-id'),
          title: titleElement?.textContent?.trim() || 'Tidak ada judul',
          company: companyElement?.textContent?.trim() || 'Tidak ada perusahaan',
          location: locationText,
          type: typeText,
          salary: salaryText,
          image: imageElement?.src || 'assets/img/product/img1.png',
          link: `job-detail.php?id=${button.getAttribute('data-job-id')}`
        };

        return jobData;
      }
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