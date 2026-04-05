<?php
session_start();
include 'config/database.php';
$db = new database();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get user application statistics
$app_stats = $db->get_user_application_stats($user_id);

// Get user applications
$applications = $db->get_user_applications($user_id);

// Get saved jobs (from localStorage simulation)
$saved_jobs = $db->get_saved_jobs($user_id);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="keywords" content="Bootstrap, Landing page, Template, Registration, Landing" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="author" content="UIdeck" />
    <title>LinkUp - Status Lamaran</title>

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

        .page-header h3 {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 2.2rem;
        }

        .page-header p {
            opacity: 0.9;
            font-size: 1.1rem;
            color: white;
        }

        .stats-container {
            margin-top: -30px;
            position: relative;
            z-index: 10;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(61, 142, 255, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--gray);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .status-container {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 40px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .status-tabs {
            display: flex;
            background: var(--gray-light);
            padding: 0 20px;
        }

        .status-tab {
            flex: 1;
            text-align: center;
            padding: 20px 15px;
            background: none;
            border: none;
            font-weight: 600;
            color: var(--gray);
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            border-bottom: 3px solid transparent;
        }

        .status-tab.active {
            color: var(--primary);
            background: white;
            border-bottom: 3px solid var(--primary);
        }

        .status-tab:hover:not(.active) {
            color: var(--primary);
            background: rgba(61, 142, 255, 0.05);
        }

        .status-content {
            padding: 0;
        }

        .tab-pane {
            display: none;
            padding: 30px;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: var(--primary);
            font-size: 50px;
        }

        .empty-state h4 {
            color: var(--gray-dark);
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .empty-state p {
            color: var(--gray);
            margin-bottom: 20px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            font-size: 0.9rem;
        }

        .filter-section {
            background: var(--gray-light);
            padding: 20px 30px;
            border-bottom: 1px solid #e9ecef;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-btn {
            padding: 8px 20px;
            border: 2px solid transparent;
            background: white;
            color: var(--gray);
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s;
            cursor: pointer;
        }

        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .filter-btn:hover:not(.active) {
            border-color: var(--primary);
            color: var(--primary);
        }

        .application-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .application-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .application-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--hover-shadow);
        }

        .application-card:hover::before {
            opacity: 1;
        }

        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        /* Specific styling for saved jobs - smaller images */
        #saved-jobs-container .company-logo {
            width: 45px;
            height: 45px;
            margin-right: 15px;
            border-radius: 8px;
        }

        #saved-jobs-container .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .job-info {
            flex: 1;
        }

        .job-title {
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: var(--gray-dark);
        }

        .company-name {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .job-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            color: var(--gray);
            font-size: 0.9rem;
        }

        .meta-item i {
            margin-right: 5px;
            color: var(--primary);
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-dikirim {
            background: rgba(61, 142, 255, 0.1);
            color: var(--primary);
            border: 1px solid rgba(61, 142, 255, 0.3);
        }

        .status-diproses {
            background: rgba(255, 193, 7, 0.1);
            color: #b78a00;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-direkrut {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-ditolak {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .application-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .application-date {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .application-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 6px 15px;
            border: 1px solid #e9ecef;
            background: white;
            border-radius: 6px;
            color: var(--gray);
            font-size: 0.85rem;
            transition: all 0.3s;
            cursor: pointer;
        }

        .action-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .info-section {
            background: linear-gradient(135deg, #f8fbff 0%, #f0f7ff 100%);
            border-radius: 12px;
            padding: 25px;
            margin-top: 40px;
            border: 1px solid rgba(61, 142, 255, 0.2);
        }

        .info-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }

        .info-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--gray-dark);
            margin: 0;
        }

        .info-content {
            color: var(--gray);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .info-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }

        .info-link:hover {
            text-decoration: underline;
        }

        .info-link i {
            margin-left: 5px;
            transition: transform 0.3s;
        }

        .info-link:hover i {
            transform: translateX(3px);
        }

        .login-reminder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 25px;
            color: white;
            margin-top: 30px;
            position: relative;
            overflow: hidden;
        }

        .login-reminder::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }

        .login-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
        }

        .login-icon {
            font-size: 50px;
            margin-right: 25px;
            opacity: 0.9;
        }

        .login-text {
            flex: 1;
        }

        .login-text h5 {
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 1rem;
        }

        .login-text p {
            color: white;
            margin-bottom: 0;
            opacity: 0.9;
            font-size: 0.85rem;
        }

        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.7);
            color: white;
            font-weight: 600;
            padding: 8px 25px;
            border-radius: 25px;
            transition: all 0.3s;
        }

        .btn-outline-light:hover {
            background: white;
            color: var(--primary);
            border-color: white;
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
        }

        .modal-title {
            font-weight: 600;
            color: var(--gray-dark);
            font-size: 1.1rem;
        }

        .modal-body {
            padding: 15px 20px 20px;
        }

        .status-info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .status-info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .status-info-title {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .status-badge-small {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 12px;
            min-width: 80px;
            text-align: center;
        }

        .status-info-desc {
            color: var(--gray);
            margin: 0;
            padding-left: 92px;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        @media (max-width: 991.98px) {
            .stats-container .col-md-3 {
                margin-bottom: 15px;
            }

            .stat-card {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 768px) {
            .status-tabs {
                flex-direction: column;
            }

            .application-header {
                flex-direction: column;
            }

            .status-badge {
                margin-top: 15px;
                align-self: flex-start;
            }

            .application-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .login-content {
                flex-direction: column;
                text-align: center;
            }

            .login-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .status-info-desc {
                padding-left: 0;
            }
    </style>
</head>

<body>
    <!-- ===== Header Start ===== -->
    <?php include("header.php") ?>
    <!-- ===== Header End ===== -->

    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="inner-header text-center">
                        <h3>Status Lamaran Kerja</h3>
                        <p>Kelola dan pantau semua lamaran kerja Anda di satu tempat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Stats Section -->
    <section class="stats-container">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php echo $app_stats['total']; ?>
                        </div>
                        <div class="stat-label">Total Lamaran</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php echo $app_stats['diproses']; ?>
                        </div>
                        <div class="stat-label">Dalam Proses</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php echo $app_stats['diterima']; ?>
                        </div>
                        <div class="stat-label">Direkrut</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php echo $app_stats['ditolak']; ?>
                        </div>
                        <div class="stat-label">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section Start -->
    <section class="section pt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="status-container">
                        <div class="status-tabs">
                            <button class="status-tab active" data-tab="tersimpan">Tersimpan</button>
                            <button class="status-tab" data-tab="dilamar">Dilamar</button>
                        </div>

                        <!-- Tersimpan Tab Content -->
                        <div class="status-content">
                            <div class="tab-pane active" id="tersimpan-content">
                                <div id="saved-jobs-container">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="lni-bookmark"></i>
                                        </div>
                                        <h4>Memuat lowongan tersimpan...</h4>
                                        <p>Lowongan yang Anda simpan akan tampil di sini.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dilamar Tab Content -->
                            <div class="tab-pane" id="dilamar-content">
                                <!-- Filter Section -->
                                <div class="filter-section">
                                    <div class="filter-group">
                                        <span class="filter-label">Filter Status:</span>
                                        <button class="filter-btn active" data-filter="all">Semua</button>
                                        <button class="filter-btn" data-filter="dikirim">Dikirim</button>
                                        <button class="filter-btn" data-filter="diproses">Diproses</button>
                                        <button class="filter-btn" data-filter="direkrut">Direkrut</button>
                                        <button class="filter-btn" data-filter="ditolak">Ditolak</button>
                                    </div>
                                </div>

                                <!-- Application Cards -->
                                <div class="p-4" id="application-list">
                                    <?php if (!empty($applications)): ?>
                                        <?php foreach ($applications as $application): ?>
                                            <div class="application-card"
                                                data-status="<?php echo strtolower($application['status_lamaran']); ?>">
                                                <div class="application-header">
                                                    <div class="d-flex align-items-start">
                                                        <div class="company-logo">
                                                            <?php if ($application['gambar']): ?>
                                                                <img src="adminpanel/src/images/jobs/<?php echo htmlspecialchars($application['gambar']); ?>"
                                                                    alt="<?php echo htmlspecialchars($application['nama_perusahaan']); ?>" />
                                                            <?php elseif ($application['logo_perusahaan']): ?>
                                                                <img src="adminpanel/src/images/perusahaan/<?php echo htmlspecialchars($application['logo_perusahaan']); ?>"
                                                                    alt="<?php echo htmlspecialchars($application['nama_perusahaan']); ?>" />
                                                            <?php else: ?>
                                                                <i class="lni-briefcase"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="job-info">
                                                            <h3 class="job-title">
                                                                <?php echo htmlspecialchars($application['judul_lowongan']); ?>
                                                            </h3>
                                                            <p class="company-name">
                                                                <?php echo htmlspecialchars($application['nama_perusahaan']); ?>
                                                            </p>
                                                            <div class="job-meta">
                                                                <span class="meta-item">
                                                                    <i class="lni-map-marker"></i>
                                                                    <?php echo htmlspecialchars($application['nama_kota'] ?: 'Indonesia'); ?>
                                                                </span>
                                                                <span class="meta-item">
                                                                    <i class="lni-briefcase"></i> Full-time
                                                                </span>
                                                                <span class="meta-item">
                                                                    <i class="lni-money-protection"></i>
                                                                    <?php echo $application['gaji_lowongan'] ? 'Rp ' . number_format($application['gaji_lowongan'], 0, ',', '.') : 'Negosiasi'; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="status-badge status-<?php echo strtolower($application['status_lamaran']); ?>">
                                                        <?php echo htmlspecialchars($application['status_lamaran']); ?>
                                                    </span>
                                                </div>
                                                <div class="application-footer">
                                                    <div class="application-date">
                                                        <i class="lni-timer"></i> Dilamar pada
                                                        <?php echo date('d F Y', strtotime($application['tanggal_lamar'])); ?>
                                                    </div>
                                                    <div class="application-actions">
                                                        <a href="job-detail.php?id=<?php echo $application['id_lowongan']; ?>"
                                                            class="action-btn">Lihat Detail</a>
                                                        <button class="action-btn"
                                                            onclick="deleteApplication(<?php echo $application['id_lamaran']; ?>)">Hapus</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="lni-briefcase"></i>
                                            </div>
                                            <h4>Belum ada lamaran</h4>
                                            <p>Anda belum melamar pekerjaan apapun. Mulai jelajahi lowongan dan lamar
                                                sekarang!</p>
                                            <a href="index.php" class="btn btn-common">Jelajahi Lowongan</a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Info Section -->
                                <div class="p-4 pt-0">
                                    <div class="info-section">
                                        <div class="info-header">
                                            <div class="info-icon">
                                                <i class="lni-information"></i>
                                            </div>
                                            <h5 class="info-title">Informasi Status Lamaran</h5>
                                        </div>
                                        <p class="info-content">
                                            Tidak yakin dengan arti status lamaran Anda?
                                            <a href="#" class="info-link" data-toggle="modal"
                                                data-target="#statusInfoModal">
                                                Lihat arti dari setiap status lamaran di sini <i
                                                    class="lni-arrow-right"></i>
                                            </a>
                                        </p>
                                    </div>

                                    <!-- Login Reminder - Hanya tampil jika belum login -->
                                    <?php if (!isset($_SESSION['user_id'])): ?>
                                        <div class="login-reminder">
                                            <div class="login-content">
                                                <div class="login-icon">
                                                    <i class="lni-user"></i>
                                                </div>
                                                <div class="login-text">
                                                    <h5>Masuk untuk melamar dan menyimpan pekerjaan</h5>
                                                    <p>Anda perlu masuk ke akun Anda untuk dapat melamar pekerjaan dan
                                                        menyimpan lowongan yang menarik.</p>
                                                </div>
                                                <a href="login.php" class="btn btn-outline-light">Masuk Sekarang</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Content Section End -->

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

    <!-- Status Info Modal -->
    <div class="modal fade" id="statusInfoModal" tabindex="-1" role="dialog" aria-labelledby="statusInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusInfoModalLabel">Arti Status Lamaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="status-info-item">
                        <div class="status-info-title">
                            <span class="status-badge status-dikirim status-badge-small">Dikirim</span>
                            <h6 class="mb-0">Lamaran Anda telah berhasil dikirim</h6>
                        </div>
                        <p class="status-info-desc">
                            Lamaran Anda telah diterima oleh sistem dan sedang menunggu untuk ditinjau oleh perusahaan.
                        </p>
                    </div>

                    <div class="status-info-item">
                        <div class="status-info-title">
                            <span class="status-badge status-diproses status-badge-small">Diproses</span>
                            <h6 class="mb-0">Lamaran Anda sedang diproses</h6>
                        </div>
                        <p class="status-info-desc">
                            Perusahaan sedang meninjau lamaran Anda. Anda mungkin akan dihubungi untuk tahap seleksi
                            berikutnya.
                        </p>
                    </div>

                    <div class="status-info-item">
                        <div class="status-info-title">
                            <span class="status-badge status-direkrut status-badge-small">Direkrut</span>
                            <h6 class="mb-0">Selamat! Anda direkrut</h6>
                        </div>
                        <p class="status-info-desc">
                            Perusahaan telah memutuskan untuk merekrut Anda. Anda akan dihubungi untuk informasi lebih
                            lanjut mengenai proses onboarding.
                        </p>
                    </div>

                    <div class="status-info-item">
                        <div class="status-info-title">
                            <span class="status-badge status-ditolak status-badge-small">Ditolak</span>
                            <h6 class="mb-0">Lamaran Anda tidak diterima</h6>
                        </div>
                        <p class="status-info-desc">
                            Saat ini lamaran Anda tidak sesuai dengan kebutuhan perusahaan. Jangan menyerah dan teruslah
                            mencoba lowongan lainnya.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

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
        $(document).ready(function () {
            // Load saved jobs from localStorage
            function loadSavedJobs() {
                const savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
                const container = $('#saved-jobs-container');

                if (savedJobs.length === 0) {
                    container.html(`
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="lni-bookmark"></i>
                            </div>
                            <h4>Belum ada lowongan yang disimpan</h4>
                            <p>Simpan lowongan kerja yang Anda minati sehingga bisa dilihat lagi nanti.</p>
                            <a href="index.php" class="btn btn-common">Jelajahi Lowongan</a>
                        </div>
                    `);
                } else {
                    let jobsHtml = '';
                    savedJobs.forEach(job => {
                        // Handle undefined values
                        const jobTitle = job.title || job.judul_lowongan || 'Tidak ada judul';
                        const companyName = job.company || job.nama_perusahaan || 'Tidak ada perusahaan';
                        const jobLocation = job.location || job.nama_kota || 'Indonesia';
                        const jobType = job.type || job.nama_jenis || 'Full-time';
                        const jobSalary = job.salary || job.gaji_lowongan || 'Negosiasi';
                        const jobImage = job.image || job.gambar || 'assets/img/product/img1.png';
                        const jobId = job.id || job.id_lowongan || Math.random().toString(36).substr(2, 9);
                        const savedDate = job.savedDate || job.tanggal_simpan || 'Baru saja';

                        jobsHtml += `
                            <div class="application-card">
                                <div class="application-header">
                                    <div class="d-flex align-items-start">
                                        <div class="company-logo">
                                            <img src="${jobImage}" alt="${companyName}" 
                                                 onerror="this.src='assets/img/product/img1.png'">
                                        </div>
                                        <div class="job-info">
                                            <h3 class="job-title">${jobTitle}</h3>
                                            <p class="company-name">${companyName}</p>
                                            <div class="job-meta">
                                                <span class="meta-item">
                                                    <i class="lni-map-marker"></i> ${jobLocation}
                                                </span>
                                                <span class="meta-item">
                                                    <i class="lni-briefcase"></i> ${jobType}
                                                </span>
                                                <span class="meta-item">
                                                    <i class="lni-money-protection"></i> ${jobSalary}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="status-badge status-tersimpan">Tersimpan</span>
                                </div>
                                <div class="application-footer">
                                    <div class="application-date">
                                        <i class="lni-timer"></i> Disimpan pada ${savedDate}
                                    </div>
                                    <div class="application-actions">
                                        <a href="job-detail.php?id=${jobId}" class="action-btn">Lihat Detail</a>
                                        <button class="action-btn" onclick="removeSavedJob('${jobId}')">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    container.html(jobsHtml);
                }
            }

            // Remove saved job
            window.removeSavedJob = function (jobId) {
                let savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
                savedJobs = savedJobs.filter(job => job.id !== jobId);
                localStorage.setItem('savedJobs', JSON.stringify(savedJobs));
                loadSavedJobs(); // Reload the saved jobs
            };

            // Tab switching functionality
            $('.status-tab').on('click', function () {
                const tabId = $(this).data('tab');

                // Update active tab
                $('.status-tab').removeClass('active');
                $(this).addClass('active');

                // Show corresponding content
                $('.tab-pane').removeClass('active');
                $(`#${tabId}-content`).addClass('active');
            });

            // Filter functionality
            $('.filter-btn').on('click', function () {
                const filter = $(this).data('filter');

                // Update active filter button
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                // Filter application cards
                $('.application-card').each(function () {
                    const cardStatus = $(this).data('status');
                    if (filter === 'all' || cardStatus === filter) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Load saved jobs on page load
            loadSavedJobs();
        });
    </script>

    <script>
        $(document).ready(function () {
            // Back to top button
            $('.back-to-top').on('click', function (e) {
                e.preventDefault();
                $('html, body').animate({ scrollTop: 0 }, 800);
            });
        });
    </script>
</body>

</html>