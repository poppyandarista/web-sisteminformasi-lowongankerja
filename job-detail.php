<?php
session_start();
include 'config/database.php';

$db = new database();

// Get job ID from URL
$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($job_id <= 0) {
    header("Location: index.php");
    exit;
}

// Get job details with all related data
$query_job = "SELECT l.*, p.nama_perusahaan, p.email_perusahaan, p.nohp_perusahaan, 
                     p.deskripsi_perusahaan, p.logo_perusahaan, p.alamat_perusahaan,
                     pr.nama_provinsi, k.nama_kota, kat.nama_kategori, j.nama_jenis
              FROM lowongan l 
              LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan 
              LEFT JOIN provinsi pr ON l.id_provinsi = pr.id_provinsi
              LEFT JOIN kota k ON l.id_kota = k.id_kota
              LEFT JOIN kategori kat ON l.kategori_lowongan = kat.id_kategori
              LEFT JOIN jenis j ON l.id_jenis = j.id_jenis
              WHERE l.id_lowongan = ? AND l.status = 'Aktif'";
$stmt = $db->koneksi->prepare($query_job);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

// Cek apakah user sudah pernah melamar lowongan ini
$has_applied = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check_applied_query = "SELECT id_lamaran FROM lamaran WHERE id_lowongan = ? AND id_user = ?";
    $check_applied_stmt = $db->koneksi->prepare($check_applied_query);
    $check_applied_stmt->bind_param("ii", $job_id, $user_id);
    $check_applied_stmt->execute();
    $check_applied_result = $check_applied_stmt->get_result();
    $has_applied = $check_applied_result->num_rows > 0;
    $check_applied_stmt->close();
}

if (!$job) {
    header("Location: index.php");
    exit;
}

// Get company logo display
$logo_display = $job['logo_perusahaan'];
if (empty($logo_display)) {
    $logo_display = 'img' . ((($job['id_perusahaan'] - 1) % 3) + 1) . '.png';
}

// Function to format salary
function formatSalary($salary)
{
    if (!empty($salary) && is_numeric($salary)) {
        return "Rp" . number_format($salary, 0, ',', '.');
    }
    return "Negosiasi";
}

// Function to calculate time ago
function timeAgo($date)
{
    if (empty($date))
        return "Baru saja";

    $timestamp = strtotime($date);
    if ($timestamp === false)
        return "Baru saja";

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

// Function to format date
function formatDate($date)
{
    if (empty($date))
        return "Belum ditentukan";
    return date("d M Y", strtotime($date));
}

// Function to parse text with newlines to HTML list
function textToList($text)
{
    if (empty($text))
        return [];
    $lines = explode("\n", $text);
    $items = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $items[] = $line;
        }
    }
    return $items;
}

// Parse kualifikasi and pertanyaan
$kualifikasi_list = textToList($job['kualifikasi']);
$pertanyaan_list = textToList($job['pertanyaan']);

// Get similar jobs (optional)
$query_similar = "SELECT l.*, p.nama_perusahaan, k.nama_kota, kat.nama_kategori, j.nama_jenis
                  FROM lowongan l 
                  LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan 
                  LEFT JOIN kota k ON l.id_kota = k.id_kota
                  LEFT JOIN kategori kat ON l.kategori_lowongan = kat.id_kategori
                  LEFT JOIN jenis j ON l.id_jenis = j.id_jenis
                  WHERE l.status = 'Aktif' AND l.id_lowongan != ? 
                  AND l.kategori_lowongan = ?
                  LIMIT 3";
$stmt_similar = $db->koneksi->prepare($query_similar);
$stmt_similar->bind_param("ii", $job_id, $job['kategori_lowongan']);
$stmt_similar->execute();
$result_similar = $stmt_similar->get_result();
$similar_jobs = [];
while ($row = $result_similar->fetch_assoc()) {
    $similar_jobs[] = $row;
}
$stmt_similar->close();

$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="keywords" content="Lowongan Kerja, Cari Pekerjaan, Portal Karir, Loker Terbaru, Pencarian Kerja" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="author" content="UIdeck" />
    <title><?php echo htmlspecialchars($job['judul_lowongan']); ?> | LinkUp</title>

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

        .job-detail-header h1 {
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 1.8rem;
        }

        .job-detail-header p {
            opacity: 0.9;
            font-size: 1rem;
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
        }

        .job-meta-item i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        .job-content {
            padding: 30px 0;
        }

        .job-main-content {
            background: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 25px;
        }

        .job-main-content h3 {
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eee;
            color: #2d3748;
            font-size: 1.3rem;
        }

        .job-description {
            line-height: 1.6;
            color: #4a5568;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .job-requirements,
        .job-responsibilities,
        .job-benefits {
            margin-bottom: 20px;
        }

        .job-requirements h4,
        .job-responsibilities h4,
        .job-benefits h4 {
            font-size: 1.1rem;
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
            height: 70px;
            border-radius: 8px;
            object-fit: cover;
            margin: 0 auto 12px;
            border: 1px solid #f0f2f5;
            padding: 4px;
            background: white;
        }

        .company-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
        }

        .company-info p {
            font-size: 0.85rem;
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
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .job-card:hover {
            text-decoration: none;
            color: inherit;
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
            height: 45px;
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
            color: #2d3748;
        }

        .job-info p {
            margin: 0 0 8px;
            color: #6c757d;
            font-size: 0.85rem;
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
        }

        .job-meta i {
            margin-right: 4px;
            color: #a0aec0;
            font-size: 0.85rem;
        }

        .job-salary {
            font-weight: 600;
            color: #2563eb;
            margin: 8px 0;
            font-size: 0.9rem;
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
            color: #94a3b8;
        }

        .job-tag {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
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
            }

            .job-meta-info {
                gap: 8px;
            }

            .job-meta-item {
                font-size: 0.8rem;
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
    <!-- ===== Header Start ===== -->
    <?php include("header.php") ?>
    <!-- ===== Header End ===== -->

    <div class="job-detail-header">
        <div class="container">
            <div class="inner-header">
                <h1><?php echo htmlspecialchars($job['judul_lowongan']); ?></h1>
                <p><?php echo htmlspecialchars($job['nama_perusahaan']); ?> -
                    <?php echo htmlspecialchars($job['nama_kota'] ?: 'Indonesia'); ?>
                </p>

                <div class="job-meta-info">
                    <div class="job-meta-item">
                        <i class="lni-briefcase"></i> <?php echo htmlspecialchars($job['nama_jenis']); ?>
                    </div>
                    <div class="job-meta-item">
                        <i class="lni-coin"></i> <?php echo formatSalary($job['gaji_lowongan']); ?>
                    </div>
                    <div class="job-meta-item">
                        <i class="lni-timer"></i> Diposting <?php echo timeAgo($job['tanggal_posting']); ?>
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
                            <?php echo nl2br(htmlspecialchars($job['deskripsi_lowongan'])); ?>
                        </div>

                        <?php if (count($kualifikasi_list) > 0): ?>
                            <div class="job-requirements">
                                <h4>Kualifikasi yang Dibutuhkan:</h4>
                                <ul>
                                    <?php foreach ($kualifikasi_list as $item): ?>
                                        <li><?php echo htmlspecialchars($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (count($pertanyaan_list) > 0): ?>
                            <div class="job-benefits">
                                <h4>Pertanyaan dari Perusahaan:</h4>
                                <ul>
                                    <?php foreach ($pertanyaan_list as $item): ?>
                                        <li><?php echo htmlspecialchars($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Job Sidebar -->
                <div class="col-lg-4 col-md-5">
                    <div class="job-sidebar">
                        <a href="company-details.php?id=<?php echo $job['id_perusahaan']; ?>"
                            style="text-decoration: none; color: inherit;">
                            <div class="company-info">
                                <img src="adminpanel/src/images/company/<?php echo htmlspecialchars($logo_display); ?>"
                                    alt="Company Logo" class="company-logo" />
                                <h3 class="company-name"><?php echo htmlspecialchars($job['nama_perusahaan']); ?></h3>
                                <p><?php echo htmlspecialchars(substr($job['deskripsi_perusahaan'] ?? '', 0, 100)) . (strlen($job['deskripsi_perusahaan'] ?? '') > 100 ? '...' : ''); ?>
                                </p>
                            </div>
                        </a>

                        <div class="job-actions">
                            <?php if ($has_applied): ?>
                                <button class="btn-apply" disabled
                                    style="background: #9ca3af; cursor: not-allowed; opacity: 0.7;">
                                    <i class="lni-check-mark-circle"></i> Sudah Dilamar
                                </button>
                            <?php else: ?>
                                <button class="btn-apply" onclick="applyJob(<?php echo $job['id_lowongan']; ?>)">Lamar
                                    Sekarang</button>
                            <?php endif; ?>
                            <button class="btn-save" id="saveJobBtn" data-job-id="<?php echo $job['id_lowongan']; ?>">
                                <i class="lni-heart"></i> Simpan Lowongan
                            </button>
                        </div>

                        <ul class="job-details-list">
                            <li>
                                <span class="detail-label">Lokasi</span>
                                <span
                                    class="detail-value"><?php echo htmlspecialchars($job['nama_kota'] ?: 'Indonesia'); ?></span>
                            </li>
                            <li>
                                <span class="detail-label">Tipe Pekerjaan</span>
                                <span class="detail-value"><?php echo htmlspecialchars($job['nama_jenis']); ?></span>
                            </li>
                            <li>
                                <span class="detail-label">Gaji</span>
                                <span class="detail-value"><?php echo formatSalary($job['gaji_lowongan']); ?></span>
                            </li>
                            <li>
                                <span class="detail-label">Kategori</span>
                                <span class="detail-value"><?php echo htmlspecialchars($job['nama_kategori']); ?></span>
                            </li>
                            <li>
                                <span class="detail-label">Diposting</span>
                                <span class="detail-value"><?php echo formatDate($job['tanggal_posting']); ?></span>
                            </li>
                            <li>
                                <span class="detail-label">Berakhir</span>
                                <span class="detail-value"><?php echo formatDate($job['tanggal_tutup']); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Similar Jobs Section -->
            <?php if (count($similar_jobs) > 0): ?>
                <div class="similar-jobs">
                    <h3>Lowongan Serupa</h3>
                    <div class="row">
                        <?php foreach ($similar_jobs as $similar): ?>
                            <div class="col-lg-4 col-md-6">
                                <a href="job-detail.php?id=<?php echo $similar['id_lowongan']; ?>" class="job-card">
                                    <div class="job-header">
                                        <img src="adminpanel/src/images/jobs/<?php echo $similar['gambar'] ?: 'img1.png'; ?>"
                                            alt="Company Logo" class="job-logo" />
                                        <div class="job-info">
                                            <h4><?php echo htmlspecialchars($similar['judul_lowongan']); ?></h4>
                                            <p><?php echo htmlspecialchars($similar['nama_perusahaan']); ?></p>
                                        </div>
                                    </div>
                                    <div class="job-meta">
                                        <span><i class="lni-map-marker"></i>
                                            <?php echo htmlspecialchars($similar['nama_kota'] ?: 'Indonesia'); ?></span>
                                        <span><i class="lni-briefcase"></i>
                                            <?php echo htmlspecialchars($similar['nama_jenis']); ?></span>
                                    </div>
                                    <div class="job-salary">
                                        <?php echo formatSalary($similar['gaji_lowongan']); ?>
                                    </div>
                                    <div class="job-footer">
                                        <span
                                            class="job-tag <?php echo strtolower(str_replace([' ', '/'], '', $similar['nama_jenis'])); ?>">
                                            <?php echo htmlspecialchars($similar['nama_jenis']); ?>
                                        </span>
                                        <span class="posted-time"><i class="lni-timer"></i>
                                            <?php echo timeAgo($similar['tanggal_posting']); ?></span>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
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
                                    Temukan karier impian Anda sekarang.
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
            const currentJobId = saveJobBtn ? saveJobBtn.getAttribute('data-job-id') : null;

            if (saveJobBtn && currentJobId) {
                // Load saved jobs from localStorage
                let savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');

                // Check if job is already saved
                const isSaved = savedJobs.some(job => job.id === currentJobId);
                if (isSaved) {
                    saveJobBtn.classList.add('saved');
                    saveJobBtn.innerHTML = '<i class="lni-heart-filled"></i> Disimpan';
                }

                // Save job functionality
                saveJobBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Get job data
                    const jobData = {
                        id: currentJobId,
                        title: '<?php echo addslashes($job['judul_lowongan']); ?>',
                        company: '<?php echo addslashes($job['nama_perusahaan']); ?>',
                        location: '<?php echo addslashes($job['nama_kota']); ?>',
                        type: '<?php echo addslashes($job['nama_jenis']); ?>',
                        salary: '<?php echo addslashes(formatSalary($job['gaji_lowongan'])); ?>',
                        image: 'adminpanel/src/images/jobs/<?php echo $job['gambar'] ?: 'img1.png'; ?>',
                        link: 'job-detail.php?id=' + currentJobId,
                        savedDate: new Date().toLocaleDateString('id-ID')
                    };

                    let savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
                    const jobIndex = savedJobs.findIndex(job => job.id === currentJobId);

                    if (jobIndex === -1) {
                        // Save job
                        savedJobs.push(jobData);
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
            }
        });

        // Apply button functionality
        function applyJob(jobId) {
            // Tampilkan loading
            const applyBtn = document.querySelector('.btn-apply');
            const originalText = applyBtn.innerHTML;
            applyBtn.innerHTML = '<span class="spinner"></span> Memproses...';
            applyBtn.disabled = true;

            // Fetch ke proses_lamar.php
            fetch('proses_lamar.php?id=' + jobId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Tampilkan notifikasi sukses seperti di login
                        showAlert('success', data.message);
                        // Ubah tombol menjadi "Sudah Dilamar"
                        applyBtn.innerHTML = '<i class="lni-check-mark-circle"></i> Sudah Dilamar';
                        applyBtn.style.background = '#9ca3af';
                        applyBtn.style.cursor = 'not-allowed';
                        applyBtn.disabled = true;
                        // Hapus onclick
                        applyBtn.removeAttribute('onclick');
                    } else {
                        // Tampilkan notifikasi error
                        showAlert('error', data.message);
                        applyBtn.innerHTML = originalText;
                        applyBtn.disabled = false;

                        // Jika perlu redirect ke login
                        if (data.redirect) {
                            setTimeout(function () {
                                window.location.href = data.redirect;
                            }, 2000);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
                    applyBtn.innerHTML = originalText;
                    applyBtn.disabled = false;
                });
        }

        // Fungsi showAlert seperti di halaman login
        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlert = document.querySelector('.custom-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            // Create alert element
            const alert = document.createElement('div');
            alert.className = `custom-alert alert-${type}`;
            alert.innerHTML = `
        <div class="alert-content">
            <div class="alert-icon">
                ${type === 'success' ?
                    '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM16.2803 9.21967C16.5732 8.92678 16.5732 8.4519 16.2803 8.15899C15.9874 7.86609 15.5126 7.86609 15.2197 8.15899L10.5 12.8787L8.78033 11.159C8.48744 10.8661 8.01256 10.8661 7.71967 11.159C7.42678 11.4519 7.42678 11.9268 7.71967 12.2197L9.96967 14.4697C10.2626 14.7626 10.7374 14.7626 11.0303 14.4697L16.2803 9.21967Z" fill="#10b981"/></svg>' :
                    '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM11.25 7.5C11.25 6.94772 11.6977 6.5 12.25 6.5H12.75C13.3023 6.5 13.75 6.94772 13.75 7.5C13.75 8.05228 13.3023 8.5 12.75 8.5H12.25C11.6977 8.5 11.25 8.05228 11.25 7.5ZM12 10.25C12.4142 10.25 12.75 10.5858 12.75 11V16C12.75 16.4142 12.4142 16.75 12 16.75C11.5858 16.75 11.25 16.4142 11.25 16V11C11.25 10.5858 11.5858 10.25 12 10.25Z" fill="#ef4444"/></svg>'
                }
            </div>
            <div class="alert-text">
                <div class="alert-title">${type === 'success' ? 'Berhasil!' : 'Gagal'}</div>
                <div class="alert-message">${message}</div>
            </div>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

            // Add styles
            alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 350px;
        max-width: 450px;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: all 0.3s ease;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    `;

            const alertContent = alert.querySelector('.alert-content');
            alertContent.style.cssText = `
        display: flex;
        align-items: flex-start;
        gap: 12px;
    `;

            const alertIcon = alert.querySelector('.alert-icon');
            alertIcon.style.cssText = `
        width: 24px;
        height: 24px;
        flex-shrink: 0;
        margin-top: 2px;
    `;

            const alertText = alert.querySelector('.alert-text');
            alertText.style.cssText = `
        flex: 1;
    `;

            const alertTitle = alert.querySelector('.alert-title');
            alertTitle.style.cssText = `
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
        line-height: 1.4;
    `;

            const alertMessage = alert.querySelector('.alert-message');
            alertMessage.style.cssText = `
        font-size: 13px;
        font-weight: 400;
        color: #6b7280;
        line-height: 1.4;
    `;

            const alertClose = alert.querySelector('.alert-close');
            alertClose.style.cssText = `
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #9ca3af;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
        flex-shrink: 0;
    `;

            // Set colors based on type
            if (type === 'success') {
                alert.style.background = '#ecfdf3';
                alert.style.border = '1px solid #10b981';
                alertTitle.style.color = '#065f46';
                alertMessage.style.color = '#047857';
                alertClose.style.color = '#10b981';
            } else {
                alert.style.background = '#fef3f2';
                alert.style.border = '1px solid #ef4444';
                alertTitle.style.color = '#991b1b';
                alertMessage.style.color = '#b91c1c';
                alertClose.style.color = '#ef4444';
            }

            // Add hover effects
            alertClose.addEventListener('mouseenter', () => {
                alertClose.style.background = 'rgba(0, 0, 0, 0.05)';
            });
            alertClose.addEventListener('mouseleave', () => {
                alertClose.style.background = 'none';
            });

            // Append to body
            document.body.appendChild(alert);

            // Animate in
            setTimeout(() => {
                alert.style.transform = 'translateX(0)';
            }, 100);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.style.transform = 'translateX(100%)';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 3000);
        }

        // Tambahkan CSS spinner
        const styleSpinner = document.createElement('style');
        styleSpinner.textContent = `
    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.6s linear infinite;
        margin-right: 8px;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
        document.head.appendChild(styleSpinner);

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
    </script>
</body>

</html>