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
                            <button class="btn-apply" onclick="applyJob(<?php echo $job['id_lowongan']; ?>)">Lamar
                                Sekarang</button>
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
            // Check if user is logged in
            <?php if (isset($_SESSION['user_id'])): ?>
                // User is logged in, redirect to application page
                window.location.href = 'lamar-pekerjaan.php?id=' + jobId;
            <?php else: ?>
                // User is not logged in, show notification and redirect to login
                showNotification('Silakan login terlebih dahulu untuk melamar', 'info');
                setTimeout(function () {
                    window.location.href = 'login.php?redirect=job-detail.php?id=' + jobId;
                }, 2000);
            <?php endif; ?>
        }

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