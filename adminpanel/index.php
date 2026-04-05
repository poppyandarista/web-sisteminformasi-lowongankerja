<?php
require_once 'session_check.php';
include 'koneksi.php';

$db = new database();

// Query untuk Total Lowongan Aktif
$query_lowongan = "SELECT COUNT(*) as total FROM lowongan WHERE status = 'Aktif'";
$result_lowongan = $db->koneksi->query($query_lowongan);
$total_lowongan_aktif = $result_lowongan ? $result_lowongan->fetch_assoc()['total'] : 0;

// Query untuk Total Pelamar Baru (user yang terdaftar)
$query_pelamar = "SELECT COUNT(*) as total FROM user";
$result_pelamar = $db->koneksi->query($query_pelamar);
$total_pelamar_baru = $result_pelamar ? $result_pelamar->fetch_assoc()['total'] : 0;

// Query untuk Total Perusahaan
$query_perusahaan = "SELECT COUNT(*) as total FROM perusahaan";
$result_perusahaan = $db->koneksi->query($query_perusahaan);
$total_perusahaan = $result_perusahaan ? $result_perusahaan->fetch_assoc()['total'] : 0;

// Query untuk Total Lamaran
$query_lamaran = "SELECT COUNT(*) as total FROM lamaran";
$result_lamaran = $db->koneksi->query($query_lamaran);
$total_lamaran = $result_lamaran ? $result_lamaran->fetch_assoc()['total'] : 0;

// Query untuk Lamaran vs Lowongan (Demand vs Supply) - PER MINGGU (SEMUA DATA)
// FIX: Ambil semua data tanpa batas waktu untuk mencocokkan total
$query_demand_supply_weekly = "
    SELECT 
        WEEK(tanggal_lamar, 1) as minggu_ke,
        CONCAT('Minggu ', WEEK(tanggal_lamar, 1)) as label_minggu,
        COUNT(*) as total_lamaran
    FROM lamaran 
    GROUP BY WEEK(tanggal_lamar, 1), label_minggu, minggu_ke
    ORDER BY minggu_ke";

$result_demand_supply_weekly = $db->koneksi->query($query_demand_supply_weekly);
$weekly_lamaran_data = [];
if ($result_demand_supply_weekly) {
  while ($row = $result_demand_supply_weekly->fetch_assoc()) {
    $weekly_lamaran_data[$row['minggu_ke']] = [
      'label' => $row['label_minggu'],
      'total_lamaran' => (int) $row['total_lamaran']
    ];
  }
}

// Query untuk Lowongan per minggu (SEMUA DATA)
$query_lowongan_weekly = "
    SELECT 
        WEEK(tanggal_posting, 1) as minggu_ke,
        CONCAT('Minggu ', WEEK(tanggal_posting, 1)) as label_minggu,
        COUNT(*) as total_lowongan
    FROM lowongan 
    WHERE status = 'Aktif'
    GROUP BY WEEK(tanggal_posting, 1), label_minggu, minggu_ke
    ORDER BY minggu_ke";

$result_lowongan_weekly = $db->koneksi->query($query_lowongan_weekly);
$weekly_lowongan_data = [];
if ($result_lowongan_weekly) {
  while ($row = $result_lowongan_weekly->fetch_assoc()) {
    $weekly_lowongan_data[$row['minggu_ke']] = [
      'label' => $row['label_minggu'],
      'total_lowongan' => (int) $row['total_lowongan']
    ];
  }
}

// Query untuk Pelamar aktif per minggu (SEMUA DATA)
$query_pelamar_weekly = "
    SELECT 
        WEEK(tanggal_lamar, 1) as minggu_ke,
        CONCAT('Minggu ', WEEK(tanggal_lamar, 1)) as label_minggu,
        COUNT(DISTINCT id_user) as total_pelamar
    FROM lamaran 
    GROUP BY WEEK(tanggal_lamar, 1), label_minggu, minggu_ke
    ORDER BY minggu_ke";

$result_pelamar_weekly = $db->koneksi->query($query_pelamar_weekly);
$weekly_pelamar_data = [];
if ($result_pelamar_weekly) {
  while ($row = $result_pelamar_weekly->fetch_assoc()) {
    $weekly_pelamar_data[$row['minggu_ke']] = [
      'label' => $row['label_minggu'],
      'total_pelamar' => (int) $row['total_pelamar']
    ];
  }
}

// Gabungkan data untuk chart Lamaran vs Lowongan per minggu
$lamaran_vs_lowongan_weekly = [];

// Ambil semua minggu yang ada dari kedua dataset
$all_weeks = array_unique(array_merge(
  array_keys($weekly_lamaran_data),
  array_keys($weekly_lowongan_data)
));
sort($all_weeks);

foreach ($all_weeks as $week) {
  $label = isset($weekly_lamaran_data[$week]) ? $weekly_lamaran_data[$week]['label'] :
    (isset($weekly_lowongan_data[$week]) ? $weekly_lowongan_data[$week]['label'] : "Minggu $week");

  $lamaran = isset($weekly_lamaran_data[$week]) ? $weekly_lamaran_data[$week]['total_lamaran'] : 0;
  $lowongan = isset($weekly_lowongan_data[$week]) ? $weekly_lowongan_data[$week]['total_lowongan'] : 0;

  $lamaran_vs_lowongan_weekly[] = [
    'minggu' => $label,
    'lamaran' => $lamaran,
    'lowongan' => $lowongan
  ];
}

// Gabungkan data untuk chart Pelamar vs Lowongan per minggu
$pelamar_vs_lowongan_weekly = [];

// Ambil semua minggu yang ada dari dataset PELAMAR dan LOWONGAN
$pelamar_weeks = array_unique(array_merge(
  array_keys($weekly_pelamar_data),
  array_keys($weekly_lowongan_data)
));
sort($pelamar_weeks);

foreach ($pelamar_weeks as $week) {
  $label = isset($weekly_pelamar_data[$week]) ? $weekly_pelamar_data[$week]['label'] :
    (isset($weekly_lowongan_data[$week]) ? $weekly_lowongan_data[$week]['label'] : "Minggu $week");

  $pelamar = isset($weekly_pelamar_data[$week]) ? $weekly_pelamar_data[$week]['total_pelamar'] : 0;
  $lowongan = isset($weekly_lowongan_data[$week]) ? $weekly_lowongan_data[$week]['total_lowongan'] : 0;

  $pelamar_vs_lowongan_weekly[] = [
    'minggu' => $label,
    'pelamar' => $pelamar,
    'lowongan' => $lowongan
  ];
}

// Query untuk Lowongan per Kategori Pekerjaan - CHANGED: Menghitung jumlah lowongan per kategori
$query_kategori = "SELECT 
    kategori_lowongan,
    COUNT(*) as total_lowongan
    FROM lowongan 
    WHERE status = 'Aktif'
    GROUP BY kategori_lowongan
    ORDER BY total_lowongan DESC
    LIMIT 5";
$result_kategori = $db->koneksi->query($query_kategori);
$kategori_data = [];
$kategori_labels = [];
$kategori_values = [];

// Debug: Tampilkan query dan hasilnya
// echo "Query Kategori: " . $query_kategori . "<br>";

if ($result_kategori) {
  $row_count = 0;
  while ($row = $result_kategori->fetch_assoc()) {
    $kategori_data[] = $row;
    $kategori_labels[] = $row['kategori_lowongan'];
    $kategori_values[] = (int) $row['total_lowongan'];
    $row_count++;
  }

  // Debug: Tampilkan jumlah data yang ditemukan
  // echo "Jumlah kategori dengan lowongan: " . $row_count . "<br>";
  // echo "Data kategori: <pre>" . print_r($kategori_data, true) . "</pre><br>";
} else {
  // Debug: Tampilkan error jika query gagal
  // echo "Error query kategori: " . $db->koneksi->error . "<br>";
  $kategori_labels = [];
  $kategori_values = [];
}

// Default period: 7 hari terakhir
$period = isset($_GET['period']) ? $_GET['period'] : '7days';
$period_text = 'Last 7 days';

// Set query berdasarkan period
$date_condition = "";
switch ($period) {
  case '6month':
    $date_condition = "DATE(tanggal_lamar) >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
    $period_text = 'Last 6 months';
    $group_by = "DATE_FORMAT(tanggal_lamar, '%Y-%m'), MONTH(tanggal_lamar), DATE_FORMAT(tanggal_lamar, '%b')";
    break;
  case '1year':
    $date_condition = "DATE(tanggal_lamar) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
    $period_text = 'Last 1 year';
    $group_by = "DATE_FORMAT(tanggal_lamar, '%Y-%m'), MONTH(tanggal_lamar), DATE_FORMAT(tanggal_lamar, '%b')";
    break;
  case '7days':
  default:
    $date_condition = "DATE(tanggal_lamar) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $period_text = 'Last 7 days';
    $group_by = "DATE(tanggal_lamar), DAYNAME(tanggal_lamar)";
    break;
}

// Query untuk data statistik lamaran berdasarkan period
$query_stats = "";
if ($period == '7days') {
  // Untuk 7 hari: grup per hari
  $query_stats = "SELECT 
                    DATE(tanggal_lamar) as tanggal,
                    DAYNAME(tanggal_lamar) as hari,
                    COUNT(*) as total,
                    SUM(CASE WHEN status_lamaran = 'Diproses' THEN 1 ELSE 0 END) as diproses,
                    SUM(CASE WHEN status_lamaran = 'Diterima' THEN 1 ELSE 0 END) as diterima,
                    SUM(CASE WHEN status_lamaran = 'Ditolak' THEN 1 ELSE 0 END) as ditolak
                  FROM lamaran 
                  WHERE $date_condition
                  GROUP BY DATE(tanggal_lamar), DAYNAME(tanggal_lamar)
                  ORDER BY tanggal ASC";
} else {
  // Untuk 6 bulan dan 1 tahun: grup per bulan
  $query_stats = "SELECT 
                    DATE_FORMAT(tanggal_lamar, '%Y-%m') as bulan,
                    MONTH(tanggal_lamar) as bulan_angka,
                    DATE_FORMAT(tanggal_lamar, '%b') as nama_bulan_singkat,
                    COUNT(*) as total,
                    SUM(CASE WHEN status_lamaran = 'Diproses' THEN 1 ELSE 0 END) as diproses,
                    SUM(CASE WHEN status_lamaran = 'Diterima' THEN 1 ELSE 0 END) as diterima,
                    SUM(CASE WHEN status_lamaran = 'Ditolak' THEN 1 ELSE 0 END) as ditolak
                  FROM lamaran 
                  WHERE $date_condition
                  GROUP BY $group_by
                  ORDER BY bulan ASC";
}

$result_stats = $db->koneksi->query($query_stats);
$lamaran_stats = [];
$total_period_ini = 0;
$total_diproses = 0;
$total_diterima = 0;
$total_ditolak = 0;

// Mapping nama hari ke bahasa Indonesia
$hariIndo = [
  'Sunday' => 'Min',
  'Monday' => 'Sen',
  'Tuesday' => 'Sel',
  'Wednesday' => 'Rab',
  'Thursday' => 'Kam',
  'Friday' => 'Jum',
  'Saturday' => 'Sab'
];

// Mapping nama bulan ke bahasa Indonesia
$bulanIndo = [
  'Jan' => 'Jan',
  'Feb' => 'Feb',
  'Mar' => 'Mar',
  'Apr' => 'Apr',
  'May' => 'Mei',
  'Jun' => 'Jun',
  'Jul' => 'Jul',
  'Aug' => 'Agt',
  'Sep' => 'Sep',
  'Oct' => 'Okt',
  'Nov' => 'Nov',
  'Dec' => 'Des'
];

if ($result_stats) {
  while ($row = $result_stats->fetch_assoc()) {
    if ($period == '7days') {
      // Format untuk 7 hari
      $hari = $hariIndo[$row['hari']] ?? substr($row['hari'], 0, 3);
      $label = $hari;
    } else {
      // Format untuk bulan: "Jan", "Feb", dst
      $nama_bulan = $bulanIndo[$row['nama_bulan_singkat']] ?? $row['nama_bulan_singkat'];
      $label = $nama_bulan;
    }

    $lamaran_stats[$label] = [
      'total' => (int) $row['total'],
      'diproses' => (int) $row['diproses'],
      'diterima' => (int) $row['diterima'],
      'ditolak' => (int) $row['ditolak']
    ];

    $total_period_ini += (int) $row['total'];
    $total_diproses += (int) $row['diproses'];
    $total_diterima += (int) $row['diterima'];
    $total_ditolak += (int) $row['ditolak'];
  }
}

// Query untuk total period sebelumnya (untuk persentase perubahan)
$previous_period_condition = "";
switch ($period) {
  case '6month':
    $previous_period_condition = "tanggal_lamar >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND tanggal_lamar < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
    break;
  case '1year':
    $previous_period_condition = "tanggal_lamar >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND tanggal_lamar < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
    break;
  case '7days':
  default:
    $previous_period_condition = "tanggal_lamar >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND tanggal_lamar < DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    break;
}

$query_previous_period = "SELECT COUNT(*) as total FROM lamaran WHERE $previous_period_condition";
$result_previous_period = $db->koneksi->query($query_previous_period);
$total_period_sebelumnya = $result_previous_period ? $result_previous_period->fetch_assoc()['total'] : 0;

// Hitung persentase perubahan (tidak digunakan lagi)
$persentase_perubahan = 0;
if ($total_period_sebelumnya > 0) {
  $persentase_perubahan = (($total_period_ini - $total_period_sebelumnya) / $total_period_sebelumnya) * 100;
}

// Data untuk chart - selalu gunakan bar chart
if ($period == '7days') {
  // Untuk 7 hari: urutkan hari
  $days_order = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
  $chart_labels = $days_order;

  // Inisialisasi semua hari dengan nilai 0
  foreach ($days_order as $day) {
    if (!isset($lamaran_stats[$day])) {
      $lamaran_stats[$day] = [
        'total' => 0,
        'diproses' => 0,
        'diterima' => 0,
        'ditolak' => 0
      ];
    }
  }

  // Urutkan sesuai days_order
  $sorted_stats = [];
  foreach ($days_order as $day) {
    if (isset($lamaran_stats[$day])) {
      $sorted_stats[$day] = $lamaran_stats[$day];
    }
  }
  $lamaran_stats = $sorted_stats;
  $chart_labels = array_keys($lamaran_stats);
} else {
  // Untuk 6 bulan dan 1 tahun: ambil labels dari data yang ada
  $chart_labels = array_keys($lamaran_stats);
}

// Data untuk JavaScript
$chart_data_json = json_encode($lamaran_stats);
$chart_labels_json = json_encode($chart_labels);
$kategori_labels_json = json_encode($kategori_labels);
$kategori_values_json = json_encode($kategori_values);

// Data untuk weekly charts
$lamaran_vs_lowongan_json = json_encode($lamaran_vs_lowongan_weekly);
$pelamar_vs_lowongan_json = json_encode($pelamar_vs_lowongan_weekly);

$current_period = $period;
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>
    Dashboard | LinkUp
  </title>
  <link rel="icon" type="image/png" href="src/images/logo/favicon.png">
  <link href="style.css" rel="stylesheet">
  <!-- ApexCharts CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.39.0/dist/apexcharts.css">
  <style>
    /* Custom CSS untuk ukuran card yang lebih kecil */
    .compact-card {
      padding: 12px !important;
      min-height: 100px !important;
    }

    .compact-card .icon-container {
      height: 36px !important;
      width: 36px !important;
      margin-bottom: 10px !important;
    }

    .compact-card .icon-container svg {
      width: 16px !important;
      height: 16px !important;
    }

    .compact-card .stat-value {
      font-size: 1.125rem !important;
      /* 18px */
      line-height: 1.5rem !important;
      /* 24px */
      margin-top: 2px !important;
    }

    .compact-card .stat-label {
      font-size: 0.6875rem !important;
      /* 11px */
      line-height: 0.875rem !important;
      /* 14px */
      white-space: nowrap;
    }

    .compact-card .percentage-badge {
      font-size: 0.6875rem !important;
      /* 11px */
      padding: 1px 6px !important;
    }

    /* Untuk memastikan card tetap kompak */
    @media (min-width: 768px) {
      .compact-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
      }
    }

    @media (min-width: 1024px) {
      .compact-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
      }

      .compact-card {
        min-height: 110px !important;
      }
    }

    @media (min-width: 1280px) {
      .compact-card {
        min-height: 120px !important;
      }
    }

    /* Memperkecil padding container utama */
    .main-container {
      padding-left: 1rem !important;
      padding-right: 1rem !important;
    }

    @media (min-width: 768px) {
      .main-container {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
      }
    }

    /* CSS untuk chart baru */
    .stat-badge-positive {
      background-color: rgba(16, 185, 129, 0.1) !important;
      border-color: rgba(16, 185, 129, 0.2) !important;
      color: #10b981 !important;
    }

    .stat-badge-negative {
      background-color: rgba(239, 68, 68, 0.1) !important;
      border-color: rgba(239, 68, 68, 0.2) !important;
      color: #ef4444 !important;
    }

    .chart-container {
      margin-top: 1rem;
      min-height: 350px;
    }

    /* Style untuk dropdown */
    .dropdown-container {
      position: relative;
      display: inline-block;
    }

    .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 50;
      margin-top: 0.25rem;
      display: none;
      min-width: 160px;
    }

    .dropdown-menu.show {
      display: block;
    }

    /* Fix untuk grid statistik */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
      margin-bottom: 1.5rem;
      padding: 0.75rem 0;
    }

    .stats-grid dl {
      margin: 0;
      padding: 0.75rem;
      background-color: #f9fafb;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 80px;
      width: 100%;
    }

    .stats-grid dl dt {
      font-size: 0.75rem !important;
      font-weight: 500;
      color: #6b7280;
      margin-bottom: 0.25rem;
      text-align: center;
    }

    .stats-grid dl dd {
      font-size: 1.25rem !important;
      font-weight: 700;
      color: #111827;
      margin: 0;
    }

    /* Dark mode untuk stats grid */
    .dark .stats-grid dl {
      background-color: rgba(31, 41, 55, 0.5);
      border-color: #374151;
    }

    .dark .stats-grid dl dt {
      color: #d1d5db;
    }

    .dark .stats-grid dl dd {
      color: #f9fafb;
    }

    /* Rotate untuk dropdown arrow */
    .rotate-180 {
      transform: rotate(180deg);
      transition: transform 0.2s ease;
    }

    /* Style untuk bar chart yang lebih baik */
    .apexcharts-bar-series.apexcharts-plot-series .apexcharts-series path {
      stroke-width: 1;
    }

    .apexcharts-legend {
      padding-top: 10px;
    }

    /* Style untuk header chart dengan icon */
    .chart-header-icon {
      margin-right: 1rem !important;
      width: 48px !important;
      height: 48px !important;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .chart-header-content {
      display: flex;
      flex-direction: column;
    }

    .chart-header-content h5 {
      font-size: 1.75rem !important;
      line-height: 2rem !important;
      margin-bottom: 0.25rem;
      font-weight: 700;
    }

    .chart-header-content p {
      font-size: 0.875rem !important;
      color: #6b7280;
      margin: 0;
    }

    /* Dark mode untuk chart header */
    .dark .chart-header-content h5 {
      color: #f9fafb;
    }

    .dark .chart-header-content p {
      color: #d1d5db;
    }

    /* Font styling untuk chart labels */
    .apexcharts-xaxis-label,
    .apexcharts-yaxis-label {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    }

    .apexcharts-legend-text {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
      font-size: 12px !important;
      font-weight: 400 !important;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
      .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .stats-grid dl {
        min-height: 70px;
        padding: 0.5rem;
      }

      .chart-header-icon {
        width: 40px !important;
        height: 40px !important;
        margin-right: 0.75rem !important;
      }

      .chart-header-content h5 {
        font-size: 1.5rem !important;
      }
    }

    @media (min-width: 768px) and (max-width: 1023px) {
      .stats-grid dl dt {
        font-size: 0.6875rem !important;
      }

      .stats-grid dl dd {
        font-size: 1.125rem !important;
      }
    }

    /* Remove percentage badges from compact cards */
    .compact-card .percentage-badge {
      display: none !important;
    }

    /* PERBAIKAN UNTUK STATISTICS CHART - Mencegah ketumpukan */
    .statistics-chart-wrapper {
      position: relative;
      z-index: 1;
    }

    .statistics-tabs {
      display: flex;
      gap: 0.25rem;
      background-color: #f3f4f6;
      padding: 0.25rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      position: relative;
      z-index: 2;
    }

    .statistics-tab {
      flex: 1;
      padding: 0.5rem 1rem;
      text-align: center;
      font-size: 0.875rem;
      font-weight: 500;
      color: #6b7280;
      background: transparent;
      border: none;
      border-radius: 0.375rem;
      cursor: pointer;
      transition: all 0.2s;
      position: relative;
      z-index: 3;
    }

    .statistics-tab.active {
      background-color: white;
      color: #111827;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    .statistics-tab:hover:not(.active) {
      color: #374151;
    }

    #chartThree {
      position: relative;
      z-index: 1;
      min-width: 100% !important;
      width: 100% !important;
      overflow: visible !important;
    }

    .chart-three-container {
      min-height: 350px;
      padding-top: 1rem;
      position: relative;
      z-index: 1;
      width: 100%;
      overflow: visible !important;
    }

    /* Override untuk custom-scrollbar agar tidak mengganggu */
    .custom-scrollbar {
      position: relative;
      z-index: 1;
      width: 100%;
    }

    /* Pastikan chart Three memiliki width yang benar */
    #chartThree .apexcharts-canvas {
      width: 100% !important;
      position: relative !important;
    }

    /* Dark mode untuk statistics tabs */
    .dark .statistics-tabs {
      background-color: #374151;
    }

    .dark .statistics-tab.active {
      background-color: #1f2937;
      color: #f9fafb;
    }

    .dark .statistics-tab:hover:not(.active) {
      color: #d1d5db;
    }

    /* Reset untuk template asli yang mungkin mengganggu */
    .rounded-xl.border-gray-200.bg-white {
      position: relative;
      overflow: visible !important;
    }

    .rounded-xl.border-gray-200.bg-white .custom-scrollbar {
      overflow-x: auto !important;
      overflow-y: visible !important;
    }

    /* Fix untuk layout grid */
    .grid.grid-cols-12.gap-3 {
      position: relative;
    }
  </style>
</head>

<body
  x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'periodDropdownOpen': false, 'activeChart': 'lamaran-vs-lowongan' }"
  x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
  :class="{'dark bg-gray-900': darkMode === true}">
  <!-- ===== Preloader Start ===== -->
  <div x-show="loaded"
    x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
    class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
    <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
  </div>

  <!-- ===== Preloader End ===== -->

  <!-- ===== Page Wrapper Start ===== -->
  <div class="flex h-screen overflow-hidden">
    <!-- ===== Sidebar Start ===== -->

    <?php include 'sidebar.php'; ?>

    <!-- ===== Sidebar End ===== -->

    <!-- ===== Content Area Start ===== -->
    <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
      <!-- Small Device Overlay Start -->
      <div @click="sidebarToggle = false" :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
        class="fixed w-full h-screen z-9 bg-gray-900/50"></div>
      <!-- Small Device Overlay End -->

      <!-- ===== Header Start ===== -->
      <?php include 'header.php'; ?>
      <!-- ===== Header End ===== -->

      <!-- ===== Main Content Start ===== -->
      <main>
        <div class="main-container p-4 mx-auto max-w-screen-2xl md:p-4">
          <div class="grid grid-cols-12 gap-3 md:gap-4">
            <div class="col-span-12 space-y-6">
              <!-- Metric Group One -->
              <div class="compact-grid grid grid-cols-1 gap-3 md:gap-3">
                <!-- Card Total Perusahaan Terdaftar Start-->
                <div
                  class="compact-card rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                  <div class="icon-container flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-building-icon lucide-building">
                      <path d="M12 10h.01" />
                      <path d="M12 14h.01" />
                      <path d="M12 6h.01" />
                      <path d="M16 10h.01" />
                      <path d="M16 14h.01" />
                      <path d="M16 6h.01" />
                      <path d="M8 10h.01" />
                      <path d="M8 14h.01" />
                      <path d="M8 6h.01" />
                      <path d="M9 22v-3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                      <rect x="4" y="2" width="16" height="20" rx="2" />
                    </svg>
                  </div>

                  <div class="flex items-end justify-between">
                    <div>
                      <span class="stat-label text-gray-500 dark:text-gray-400">Total Perusahaan Terdaftar</span>
                      <h4 class="stat-value font-bold text-gray-800 dark:text-white/90">
                        <?php echo number_format($total_perusahaan, 0, ',', '.'); ?>
                      </h4>
                    </div>
                  </div>
                </div>
                <!-- Card Total Perusahaan Terdaftar End -->

                <!-- Card Total Semua Pelamar Start -->
                <div
                  class="compact-card rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                  <div class="icon-container flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-user-round-plus-icon lucide-user-round-plus">
                      <path d="M2 21a8 8 0 0 1 13.292-6" />
                      <circle cx="10" cy="8" r="5" />
                      <path d="M19 16v6" />
                      <path d="M22 19h-6" />
                    </svg>
                  </div>

                  <div class="flex items-end justify-between">
                    <div>
                      <span class="stat-label text-gray-500 dark:text-gray-400">Total Pelamar Saat Ini</span>
                      <h4 class="stat-value font-bold text-gray-800 dark:text-white/90">
                        <?php echo number_format($total_pelamar_baru, 0, ',', '.'); ?>
                      </h4>
                    </div>
                  </div>
                </div>
                <!-- Card Total Semua Pelamar End -->

                <!-- Card Total Lowongan Aktif Start -->
                <div
                  class="compact-card rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                  <div class="icon-container flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-mail-search-icon lucide-mail-search">
                      <path d="M22 12.5V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h7.5" />
                      <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                      <path d="M18 21a3 3 0 1 0 0-6 3 3 0 1 0 0 6Z" />
                      <circle cx="18" cy="18" r="3" />
                      <path d="m22 22-1.5-1.5" />
                    </svg>
                  </div>

                  <div class="flex items-end justify-between">
                    <div>
                      <span class="stat-label text-gray-500 dark:text-gray-400">Total Lowongan Aktif</span>
                      <h4 class="stat-value font-bold text-gray-800 dark:text-white/90">
                        <?php echo number_format($total_lowongan_aktif, 0, ',', '.'); ?>
                      </h4>
                    </div>
                  </div>
                </div>
                <!-- Card Total Lowongan Aktif End -->

                <!-- Card Total Lamaran Masuk Start -->
                <div
                  class="compact-card rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                  <div class="icon-container flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-file-user-icon lucide-file-user">
                      <path
                        d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
                      <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                      <path d="M16 22a4 4 0 0 0-8 0" />
                      <circle cx="12" cy="15" r="3" />
                    </svg>
                  </div>

                  <div class="flex items-end justify-between">
                    <div>
                      <span class="stat-label text-gray-500 dark:text-gray-400">Total Lamaran Masuk</span>
                      <h4 class="stat-value font-bold text-gray-800 dark:text-white/90">
                        <?php echo number_format($total_lamaran, 0, ',', '.'); ?>
                      </h4>
                    </div>
                  </div>
                </div>
                <!-- Card Total Lamaran Masuk End -->
              </div>
              <!-- Metric Group One -->

              <!-- ====== Chart Lamaran Masuk Start -->
              <div
                class="w-full bg-white border border-gray-200 rounded-lg shadow-xs p-5 md:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex justify-between pb-5 mb-5 border-b border-gray-200 dark:border-gray-800">
                  <div class="flex items-center">
                    <div
                      class="chart-header-icon bg-gray-100 border border-gray-200 flex items-center justify-center rounded-full dark:bg-gray-800 dark:border-gray-700">
                      <svg class="w-6 h-6 text-gray-700 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                          d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                      </svg>
                    </div>
                    <div class="chart-header-content">
                      <h5 class="text-2xl font-semibold text-gray-800 dark:text-white/90" id="total-lamaran-periode">
                        <?php echo number_format($total_period_ini, 0, ',', '.'); ?>
                      </h5>
                      <p class="text-sm text-gray-500 dark:text-gray-400" id="chart-title">
                        <?php
                        if ($period == '7days') {
                          echo 'Lamaran masuk per minggu';
                        } elseif ($period == '6month') {
                          echo 'Lamaran masuk 6 bulan terakhir';
                        } else {
                          echo 'Lamaran masuk 1 tahun terakhir';
                        }
                        ?>
                      </p>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <!-- Persentase dihapus -->
                  </div>
                </div>

                <div class="stats-grid mb-6">
                  <dl>
                    <dt>Diproses</dt>
                    <dd id="total-diproses">
                      <?php echo number_format($total_diproses, 0, ',', '.'); ?>
                    </dd>
                  </dl>
                  <dl>
                    <dt>Diterima</dt>
                    <dd id="total-diterima">
                      <?php echo number_format($total_diterima, 0, ',', '.'); ?>
                    </dd>
                  </dl>
                  <dl>
                    <dt>Ditolak</dt>
                    <dd id="total-ditolak">
                      <?php echo number_format($total_ditolak, 0, ',', '.'); ?>
                    </dd>
                  </dl>
                </div>

                <div id="lamaran-chart" class="chart-container"></div>

                <div
                  class="grid grid-cols-1 items-center border-gray-200 dark:border-gray-800 border-t justify-between mt-5">
                  <div class="flex justify-between items-center pt-5 md:pt-6">
                    <!-- Button -->
                    <div class="dropdown-container">
                      <button id="periodDropdownButton"
                        class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white text-center inline-flex items-center bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 px-3 py-1.5 rounded-lg transition-colors"
                        type="button">
                        <?php echo $period_text; ?>
                        <svg class="w-4 h-4 ms-1.5 transition-transform duration-200" id="dropdownArrow"
                          aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                          viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 9-7 7-7-7" />
                        </svg>
                      </button>

                      <!-- Dropdown menu -->
                      <div id="periodDropdownMenu"
                        class="dropdown-menu bg-white border border-gray-200 rounded-lg shadow-lg w-44 dark:bg-gray-800 dark:border-gray-700">
                        <ul class="p-2 text-sm text-gray-500 dark:text-gray-400 font-medium">
                          <li>
                            <a href="?period=7days"
                              class="period-option inline-flex items-center w-full p-2 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white rounded <?php echo $current_period == '7days' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white' : ''; ?>">Last
                              7 days</a>
                          </li>
                          <li>
                            <a href="?period=6month"
                              class="period-option inline-flex items-center w-full p-2 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white rounded <?php echo $current_period == '6month' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white' : ''; ?>">Last
                              6 month</a>
                          </li>
                          <li>
                            <a href="?period=1year"
                              class="period-option inline-flex items-center w-full p-2 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-white rounded <?php echo $current_period == '1year' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white' : ''; ?>">Last
                              1 year</a>
                          </li>
                        </ul>
                      </div>
                    </div>

                    <a href="datalamaran.php"
                      class="inline-flex items-center text-blue-600 bg-blue-50 hover:bg-blue-100 dark:text-blue-400 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 font-medium leading-5 rounded-lg text-sm px-4 py-2 transition-colors">
                      Lihat Detail
                      <svg class="w-4 h-4 ms-2 -me-0.5 rtl:rotate-180" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 12H5m14 0-4 4m4-4-4-4" />
                      </svg>
                    </a>
                  </div>
                </div>
              </div>
              <!-- ====== Chart Lamaran Masuk End -->
            </div>

            <div class="col-span-12">
              <!-- ====== Statistics Chart Start ====== -->
              <div
                class="statistics-chart-wrapper rounded-xl border border-gray-200 bg-white px-4 pt-4 pb-4 sm:px-5 sm:pt-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:justify-between">
                  <div class="w-full">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                      Statistics
                    </h3>
                    <p class="text-sm mt-1 text-gray-500 dark:text-gray-400">
                      Analisis pasar tenaga kerja berdasarkan berbagai metrik
                    </p>
                  </div>
                </div>

                <!-- Tabs untuk statistics -->
                <div class="statistics-tabs mb-4">
                  <button @click="activeChart = 'lamaran-vs-lowongan'; initializeStatisticsChart();"
                    :class="activeChart === 'lamaran-vs-lowongan' ? 'active' : ''" class="statistics-tab">
                    Lamaran vs Lowongan
                  </button>
                  <button @click="activeChart = 'pelamar-vs-lowongan'; initializeStatisticsChart();"
                    :class="activeChart === 'pelamar-vs-lowongan' ? 'active' : ''" class="statistics-tab">
                    Pelamar vs Lowongan
                  </button>
                  <button @click="activeChart = 'lowongan-per-kategori'; initializeStatisticsChart();"
                    :class="activeChart === 'lowongan-per-kategori' ? 'active' : ''" class="statistics-tab">
                    Lowongan per Kategori
                  </button>
                </div>

                <!-- Chart container -->
                <div class="chart-three-container">
                  <div id="chartThree" style="width: 100%; position: relative;"></div>
                </div>
              </div>
              <!-- ====== Statistics Chart End ====== -->
            </div>

          </div>
        </div>
      </main>
      <!-- ===== Main Content End ===== -->
    </div>
    <!-- ===== Content Area End ===== -->
  </div>
  <!-- ===== Page Wrapper End ===== -->

  <!-- ApexCharts Library -->
  <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.39.0/dist/apexcharts.min.js"></script>

  <script defer src="bundle.js"></script>
  <script>
    // Data dari PHP untuk chart utama
    const chartData = <?php echo $chart_data_json; ?>;
    const chartLabels = <?php echo $chart_labels_json; ?>;
    const currentPeriod = '<?php echo $current_period; ?>';

    // Data untuk statistics chart
    const kategoriLabels = <?php echo $kategori_labels_json; ?>;
    const kategoriValues = <?php echo $kategori_values_json; ?>;

    // Data weekly untuk statistics chart
    const lamaranVsLowonganData = <?php echo $lamaran_vs_lowongan_json; ?>;
    const pelamarVsLowonganData = <?php echo $pelamar_vs_lowongan_json; ?>;

    // Format angka dengan separator
    function formatNumber(num) {
      return new Intl.NumberFormat('id-ID').format(num);
    }

    // Fungsi untuk mendapatkan warna dari CSS variable
    function getColor(variableName, fallback) {
      const computedStyle = getComputedStyle(document.documentElement);
      return computedStyle.getPropertyValue(variableName).trim() || fallback;
    }

    // Warna untuk chart utama
    const colors = {
      diproses: getColor('--color-fg-brand', '#7592ff'),
      diterima: getColor('--color-fg-success', '#c2d6ff'),
      ditolak: getColor('--color-fg-error', '#2031d8'),
      // Warna untuk statistics chart
      lamaran: getColor('--color-fg-primary', '#465fff'),
      lowongan: getColor('--color-fg-secondary', '#9cb9ff'),
      pelamar: getColor('--color-fg-info', '#465fff')
    };

    // Siapkan data untuk chart utama
    const categories = chartLabels;
    const seriesData = [
      {
        name: 'Diproses',
        data: categories.map(label => chartData[label]?.diproses || 0),
        color: colors.diproses
      },
      {
        name: 'Diterima',
        data: categories.map(label => chartData[label]?.diterima || 0),
        color: colors.diterima
      },
      {
        name: 'Ditolak',
        data: categories.map(label => chartData[label]?.ditolak || 0),
        color: colors.ditolak
      }
    ];

    // Tentukan lebar bar berdasarkan jumlah data
    let columnWidth = '45%'; // Default

    if (currentPeriod === '7days') {
      // Untuk 7 hari: lebih lebar karena hanya 7 data
      columnWidth = '45%';
    } else if (currentPeriod === '6month') {
      // Untuk 6 bulan: lebih sempit karena ada 6 data
      columnWidth = '35%';
    } else if (currentPeriod === '1year') {
      // Untuk 1 tahun: paling sempit karena ada 12 data
      columnWidth = '25%';
    }

    // Selalu gunakan bar chart untuk semua period
    const chartOptions = {
      series: seriesData,
      chart: {
        type: 'bar',
        height: 320,
        stacked: true,
        toolbar: { show: false },
        fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        foreColor: '#6b7280'
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: columnWidth,
          borderRadius: 6,
          borderRadiusApplication: 'end',
          dataLabels: {
            position: 'top'
          }
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: true,
        width: 1,
        colors: ['transparent']
      },
      xaxis: {
        categories: categories,
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
          style: {
            fontSize: currentPeriod === '7days' ? '12px' : '11px',
            fontWeight: 400,
            colors: '#6b7280',
            fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'
          },
          trim: true,
          maxHeight: 120
        }
      },
      yaxis: {
        title: { text: '' },
        labels: {
          formatter: function (val) {
            return formatNumber(val);
          },
          style: {
            fontSize: '12px',
            colors: '#6b7280',
            fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'
          }
        }
      },
      fill: {
        opacity: 1
      },
      legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'left',
        fontSize: '12px',
        fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
        fontWeight: 400,
        markers: {
          radius: 12,
          width: 12,
          height: 12
        },
        itemMargin: {
          horizontal: 10
        },
        labels: {
          colors: '#6b7280',
          fontFamily: 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'
        }
      },
      grid: {
        borderColor: '#e5e7eb',
        strokeDashArray: 4,
        padding: {
          top: 10,
          right: 0,
          bottom: 0,
          left: 0
        }
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return formatNumber(val) + ' lamaran';
          }
        },
        shared: true,
        intersect: false
      }
    };

    // Fungsi untuk inisialisasi statistics chart
    function initializeStatisticsChart() {
      const activeChart = Alpine.$data(document.body).activeChart;
      let chartOptions;

      switch (activeChart) {
        case 'lamaran-vs-lowongan':
          // Siapkan data untuk chart Lamaran vs Lowongan per minggu
          const weeklyLabels = lamaranVsLowonganData.map(item => item.minggu);
          const lamaranData = lamaranVsLowonganData.map(item => item.lamaran);
          const lowonganData = lamaranVsLowonganData.map(item => item.lowongan);

          chartOptions = {
            series: [{
              name: 'Lamaran (Demand)',
              data: lamaranData
            }, {
              name: 'Lowongan (Supply)',
              data: lowonganData
            }],
            chart: {
              type: 'bar',
              height: 350,
              toolbar: { show: false },
              width: '100%'
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '45%',
                borderRadius: 6,
                borderRadiusApplication: 'end'
              }
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              show: true,
              width: 1,
              colors: ['transparent']
            },
            xaxis: {
              categories: weeklyLabels,
              labels: {
                style: {
                  fontSize: '12px',
                  fontFamily: 'Inter, sans-serif'
                }
              }
            },
            yaxis: {
              title: {
                text: 'Jumlah'
              },
              labels: {
                formatter: function (val) {
                  return formatNumber(val);
                }
              }
            },
            fill: {
              opacity: 1
            },
            colors: [colors.lamaran, colors.lowongan],
            tooltip: {
              y: {
                formatter: function (val) {
                  return formatNumber(val);
                }
              },
              shared: true,
              intersect: false
            },
            legend: {
              position: 'top',
              horizontalAlign: 'left',
              fontSize: '12px',
              fontFamily: 'Inter, sans-serif',
              fontWeight: 400
            }
          };
          break;

        case 'pelamar-vs-lowongan':
          // Siapkan data untuk chart Pelamar vs Lowongan per minggu
          const pelamarWeeklyLabels = pelamarVsLowonganData.map(item => item.minggu);
          const pelamarData = pelamarVsLowonganData.map(item => item.pelamar);
          const lowonganWeeklyData = pelamarVsLowonganData.map(item => item.lowongan);

          chartOptions = {
            series: [{
              name: 'Pelamar Aktif',
              data: pelamarData
            }, {
              name: 'Lowongan Baru',
              data: lowonganWeeklyData
            }],
            chart: {
              type: 'bar',
              height: 350,
              toolbar: { show: false },
              width: '100%'
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '45%',
                borderRadius: 6,
                borderRadiusApplication: 'end'
              }
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              show: true,
              width: 1,
              colors: ['transparent']
            },
            xaxis: {
              categories: pelamarWeeklyLabels,
              labels: {
                style: {
                  fontSize: '12px',
                  fontFamily: 'Inter, sans-serif'
                }
              }
            },
            yaxis: {
              title: {
                text: 'Jumlah'
              },
              labels: {
                formatter: function (val) {
                  return formatNumber(val);
                }
              }
            },
            fill: {
              opacity: 1
            },
            colors: [colors.pelamar, colors.lowongan],
            tooltip: {
              y: {
                formatter: function (val) {
                  return formatNumber(val);
                }
              },
              shared: true,
              intersect: false
            },
            legend: {
              position: 'top',
              horizontalAlign: 'left',
              fontSize: '12px',
              fontFamily: 'Inter, sans-serif',
              fontWeight: 400
            }
          };
          break;

        case 'lowongan-per-kategori':
          // Periksa apakah ada data kategori
          if (kategoriLabels.length === 0 || kategoriValues.length === 0) {
            // Jika tidak ada data, tampilkan chart kosong dengan pesan
            chartOptions = {
              series: [{
                name: 'Jumlah Lowongan',
                data: []
              }],
              chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                width: '100%'
              },
              plotOptions: {
                bar: {
                  horizontal: true,
                  borderRadius: 4,
                  borderRadiusApplication: 'end',
                  columnWidth: '25%', // MEMPERKECIL LEBAR BAR
                }
              },
              dataLabels: {
                enabled: false
              },
              xaxis: {
                categories: ['Belum ada data'],
                labels: {
                  style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                  },
                  trim: true,
                  maxHeight: 100
                },
                tickAmount: 5, // MENENTUKAN JUMLAH TICK PADA X-AXIS
                min: 0,
                max: 5, // MAKSIMAL 5 TICK
                labels: {
                  formatter: function (val) {
                    // MENGHAPUS ANGKA DESIMAL, HANYA TAMPILKAN ANGKA BULAT
                    return Math.round(val);
                  }
                }
              },
              yaxis: {
                labels: {
                  style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                  }
                }
              },
              colors: ['#3B82F6'], // WARNA BIRU DEFAULT UNTUK BAR PERTAMA
              tooltip: {
                y: {
                  formatter: function (val) {
                    return formatNumber(val) + ' lowongan';
                  }
                }
              },
              noData: {
                text: 'Belum ada lowongan per kategori',
                align: 'center',
                verticalAlign: 'middle',
                offsetX: 0,
                offsetY: 0,
                style: {
                  color: '#6b7280',
                  fontSize: '14px',
                  fontFamily: 'Inter, sans-serif'
                }
              }
            };
          } else {
            // WARNA UNTUK SETIAP KATEGORI (ARRAY WARNA)
            const categoryColors = [
              '#C7D2FE', // Soft Indigo Blue
              '#A5B4FC', // Light Indigo
              '#818CF8', // Indigo Medium
              '#6366F1', // Indigo Strong
              '#4F46E5', // Deep Indigo
              '#4338CA', // Dark Indigo
              '#3730A3', // Navy Indigo
              '#312E81', // Very Dark Indigo
            ];


            // Tentukan warna berdasarkan jumlah kategori
            let colorsArray;
            if (kategoriLabels.length === 1) {
              colorsArray = [categoryColors[0]];
            } else {
              colorsArray = categoryColors.slice(0, kategoriLabels.length);
            }

            // Tentukan maksimum nilai untuk menentukan rentang x-axis
            const maxValue = Math.max(...kategoriValues);
            const maxTick = Math.ceil(maxValue); // Pembulatan ke atas

            // Pastikan minimal ada tick 1, 2, 3, 4, 5
            const tickValues = [];
            const tickCount = Math.max(5, maxTick); // Minimal 5 tick, atau lebih jika maxValue > 5

            for (let i = 1; i <= tickCount; i++) {
              tickValues.push(i);
            }

            chartOptions = {
              series: [{
                name: 'Jumlah Lowongan',
                data: kategoriValues
              }],
              chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                width: '100%'
              },
              plotOptions: {
                bar: {
                  horizontal: true,
                  borderRadius: 4,
                  borderRadiusApplication: 'end',
                  columnWidth: '25%', // MEMPERKECIL LEBAR BAR DARI 60% MENJADI 25%
                  distributed: true, // MEMBUAT SETIAP BAR WARNA BERBEDA
                }
              },
              dataLabels: {
                enabled: true,
                formatter: function (val) {
                  return formatNumber(val);
                },
                style: {
                  fontSize: '11px',
                  colors: ['#fff'],
                  fontWeight: 'bold'
                },
                offsetX: 10,
                textAnchor: 'start'
              },
              xaxis: {
                categories: kategoriLabels,
                tickAmount: tickCount, // MENENTUKAN JUMLAH TICK
                min: 0,
                max: tickCount, // MAKSIMAL SESUAI DENGAN TICKCOUNT
                labels: {
                  style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                  },
                  formatter: function (val) {
                    // MENGHAPUS ANGKA DESIMAL, HANYA TAMPILKAN ANGKA BULAT
                    return Math.round(val);
                  }
                }
              },
              yaxis: {
                labels: {
                  style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                  },
                  maxWidth: 200,
                  trim: true
                }
              },
              colors: colorsArray, // MENGGUNAKAN ARRAY WARNA UNTUK SETIAP KATEGORI
              tooltip: {
                y: {
                  formatter: function (val) {
                    return formatNumber(val) + ' lowongan';
                  }
                }
              },
              grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4,
                xaxis: {
                  lines: {
                    show: true
                  }
                },
                yaxis: {
                  lines: {
                    show: false
                  }
                }
              }
            };
          }
          break;
      }

      const chartElement = document.getElementById('chartThree');
      if (chartElement) {
        // Destroy existing chart if exists
        if (window.statisticsChart) {
          window.statisticsChart.destroy();
        }

        // Create new chart
        window.statisticsChart = new ApexCharts(chartElement, chartOptions);
        window.statisticsChart.render();

        // Force redraw untuk memastikan chart tampil dengan benar
        setTimeout(() => {
          if (window.statisticsChart) {
            window.statisticsChart.updateSeries(chartOptions.series);
          }
        }, 100);
      }
    }

    // Inisialisasi chart
    document.addEventListener('DOMContentLoaded', function () {
      console.log('DOM Loaded, initializing chart...');

      // Inisialisasi chart utama
      const chartElement = document.getElementById('lamaran-chart');
      if (chartElement && typeof ApexCharts !== 'undefined') {
        console.log('ApexCharts is defined, creating main chart...');

        try {
          const chart = new ApexCharts(chartElement, chartOptions);
          chart.render();

          // Simpan chart instance untuk akses nanti
          window.lamaranChart = chart;
          console.log('Main chart rendered successfully!');
        } catch (error) {
          console.error('Error rendering main chart:', error);
        }
      } else {
        console.error('Chart element or ApexCharts not found!');
      }

      // Inisialisasi statistics chart
      setTimeout(() => {
        initializeStatisticsChart();
      }, 500);

      // Handle dropdown period dengan JavaScript biasa
      const dropdownButton = document.getElementById('periodDropdownButton');
      const dropdownMenu = document.getElementById('periodDropdownMenu');
      const dropdownArrow = document.getElementById('dropdownArrow');

      if (dropdownButton && dropdownMenu) {
        dropdownButton.addEventListener('click', function (e) {
          e.stopPropagation();
          dropdownMenu.classList.toggle('show');
          dropdownArrow.classList.toggle('rotate-180');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
          if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
            dropdownArrow.classList.remove('rotate-180');
          }
        });

        // Handle period selection
        const periodOptions = dropdownMenu.querySelectorAll('.period-option');
        periodOptions.forEach(option => {
          option.addEventListener('click', function (e) {
            // Remove active class from all options
            periodOptions.forEach(opt => {
              opt.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-white');
            });

            // Add active class to clicked option
            this.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-white');

            dropdownMenu.classList.remove('show');
            dropdownArrow.classList.remove('rotate-180');
          });
        });
      } else {
        console.log('Dropdown elements not found');
      }
    });
  </script>
</body>

</html>