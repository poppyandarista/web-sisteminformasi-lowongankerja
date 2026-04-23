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

// Tambahkan di bagian atas file, setelah include config/database.php
// Tampilkan notifikasi jika ada
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Hapus session setelah ditampilkan
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
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

        /* Modal Konfirmasi Styles */
        #modalConfirmApply {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        #modalConfirmApply.show {
            display: flex;
        }

        #modalConfirmApply .modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        #modalConfirmApply .modal-container {
            position: relative;
            background: white;
            border-radius: 24px;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalPop 0.3s ease-out;
        }

        @keyframes modalPop {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .dark #modalConfirmApply .modal-container {
            background: #1f2937;
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            padding: 20px 24px;
            border-radius: 24px 24px 0 0;
            color: white;
        }

        .dark .modal-header-custom {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
        }

        .modal-body-custom {
            padding: 24px;
            text-align: center;
        }

        .warning-icon {
            width: 64px;
            height: 64px;
            background: #fef3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .dark .warning-icon {
            background: #78350f;
        }

        .warning-icon svg {
            width: 32px;
            height: 32px;
            color: #d97706;
        }

        .modal-footer-custom {
            display: flex;
            gap: 12px;
            padding: 16px 24px 24px;
        }

        .btn-cancel {
            flex: 1;
            padding: 10px 16px;
            background: #f3f4f6;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .btn-confirm {
            flex: 1;
            padding: 10px 16px;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-confirm:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .spinner-white {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 320px;
            max-width: 400px;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .custom-alert.show {
            transform: translateX(0);
        }

        .alert-success {
            background: #d1fae5;
            border-left: 4px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
        }

        .alert-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-text {
            flex: 1;
        }

        .alert-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .alert-success .alert-title { color: #065f46; }
        .alert-error .alert-title { color: #991b1b; }

        .alert-message {
            font-size: 13px;
            color: #6b7280;
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            .status-tabs { flex-direction: column; }
            .application-header { flex-direction: column; }
            .status-badge { margin-top: 15px; align-self: flex-start; }
            .application-footer { flex-direction: column; gap: 15px; align-items: flex-start; }
        }

        /* Status Badge Colors */
        .status-dikirim {
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #7dd3fc;
        }

        .status-diproses {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fbbf24;
        }

        .status-diterima {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
            font-weight: 600;
        }

        .status-ditolak {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .status-tersimpan {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #a5b4fc;
        }

        /* Status Info Modal Styles - DIPERBAIKI */
        .status-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(3px);
            z-index: 99999;
            justify-content: center;
            align-items: center;
        }

        .status-modal.show {
            display: flex !important;
        }

        .status-modal-content {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 20px;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.25s ease-out;
            overflow: hidden;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .status-modal-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-modal-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-modal-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-modal-icon svg {
            width: 20px;
            height: 20px;
            color: white;
        }

        .status-modal-title h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .status-modal-title p {
            font-size: 0.75rem;
            margin: 3px 0 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .status-modal-close {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            font-size: 22px;
            cursor: pointer;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .status-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .status-modal-body {
            padding: 24px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .status-info-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .status-info-item {
            display: flex;
            gap: 14px;
            padding: 14px;
            background: #f8fafc;
            border-radius: 14px;
            transition: all 0.2s;
        }

        .status-info-item:hover {
            background: #f1f5f9;
        }

        .status-info-badge {
            flex-shrink: 0;
        }

        .status-info-badge .status-badge {
            padding: 5px 14px;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 20px;
            white-space: nowrap;
        }

        .status-info-content h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin: 0 0 5px 0;
            color: #1e293b;
        }

        .status-info-content p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
            line-height: 1.45;
        }

        .status-modal-footer {
            padding: 16px 24px 24px;
            border-top: 1px solid #eef2f6;
        }

        .status-modal-btn {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }

        .status-modal-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }
        

        /* Saat modal terbuka, navbar disembunyikan sementara */
body.modal-open .navbar {
    z-index: 1;
    opacity: 0.5;
    pointer-events: none;
}

body.modal-open {
    overflow: hidden;
}
    </style>
</head>

<body>
    <?php include("header.php") ?>

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

    <!-- Notifikasi -->
    <?php if ($success_message): ?>
                <div class="container mt-4">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #d1fae5; border: 1px solid #10b981; border-radius: 12px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" fill="#10b981" />
                                    <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <strong class="d-block" style="color: #065f46;">Berhasil!</strong>
                                <span style="color: #047857;"><?php echo htmlspecialchars($success_message); ?></span>
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
                <div class="container mt-4">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #fee2e2; border: 1px solid #ef4444; border-radius: 12px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" fill="#ef4444" />
                                    <path d="M12 8V12M12 16H12.01" stroke="white" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <strong class="d-block" style="color: #991b1b;">Gagal!</strong>
                                <span style="color: #b91c1c;"><?php echo htmlspecialchars($error_message); ?></span>
                            </div>
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
    <?php endif; ?>

    <!-- Stats Section -->
    <section class="stats-container">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $app_stats['total']; ?></div>
                        <div class="stat-label">Total Lamaran</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $app_stats['diproses']; ?></div>
                        <div class="stat-label">Dalam Proses</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $app_stats['diterima']; ?></div>
                        <div class="stat-label">Diterima</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $app_stats['ditolak']; ?></div>
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

                        <div class="status-content">
                            <div class="tab-pane active" id="tersimpan-content">
                                <div id="saved-jobs-container">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="lni-bookmark"></i></div>
                                        <h4>Memuat lowongan tersimpan...</h4>
                                        <p>Lowongan yang Anda simpan akan tampil di sini.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="dilamar-content">
                                <div class="filter-section">
                                    <div class="filter-group">
                                        <span class="filter-label">Filter Status:</span>
                                        <button class="filter-btn active" data-filter="all">Semua</button>
                                        <button class="filter-btn" data-filter="diproses">Diproses</button>
                                        <button class="filter-btn" data-filter="diterima">Diterima</button>
                                        <button class="filter-btn" data-filter="ditolak">Ditolak</button>
                                    </div>
                                </div>

                                <div class="p-4" id="application-list">
                                    <?php if (!empty($applications)): ?>
                                                <?php foreach ($applications as $application): ?>
                                                            <div class="application-card" data-status="<?php echo strtolower($application['status_lamaran']); ?>">
                                                                <div class="application-header">
                                                                    <div class="d-flex align-items-start">
                                                                        <div class="company-logo">
                                                                            <?php if ($application['gambar']): ?>
                                                                                        <img src="adminpanel/src/images/jobs/<?php echo htmlspecialchars($application['gambar']); ?>" alt="Logo" />
                                                                            <?php elseif ($application['logo_perusahaan']): ?>
                                                                                        <img src="adminpanel/src/images/perusahaan/<?php echo htmlspecialchars($application['logo_perusahaan']); ?>" alt="Logo" />
                                                                            <?php else: ?>
                                                                                        <i class="lni-briefcase"></i>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="job-info">
                                                                            <h3 class="job-title"><?php echo htmlspecialchars($application['judul_lowongan']); ?></h3>
                                                                            <p class="company-name"><?php echo htmlspecialchars($application['nama_perusahaan']); ?></p>
                                                                            <div class="job-meta">
                                                                                <span class="meta-item"><i class="lni-map-marker"></i> <?php echo htmlspecialchars($application['nama_kota'] ?: 'Indonesia'); ?></span>
                                                                                <span class="meta-item"><i class="lni-briefcase"></i> Full-time</span>
                                                                                <span class="meta-item"><i class="lni-money-protection"></i> <?php echo $application['gaji_lowongan'] ? 'Rp ' . number_format($application['gaji_lowongan'], 0, ',', '.') : 'Negosiasi'; ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span class="status-badge status-<?php echo strtolower($application['status_lamaran']); ?>"><?php echo htmlspecialchars($application['status_lamaran']); ?></span>
                                                                </div>
                                                                <div class="application-footer">
                                                                    <div class="application-date"><i class="lni-timer"></i> Dilamar pada <?php echo date('d F Y', strtotime($application['tanggal_lamar'])); ?></div>
                                                                    <div class="application-actions">
                                                                        <a href="job-detail.php?id=<?php echo $application['id_lowongan']; ?>" class="action-btn">Lihat Detail</a>
                                                                        <?php if ($application['status_lamaran'] == 'Ditolak'): ?>
                                                                                    <button class="action-btn" onclick="showConfirmModal(<?php echo $application['id_lowongan']; ?>)" style="background: #10b981; color: white; border-color: #10b981;">
                                                                                        <i class="lni-reply"></i> Lamar Ulang
                                                                                    </button>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                <?php endforeach; ?>
                                    <?php else: ?>
                                                <div class="empty-state">
                                                    <div class="empty-state-icon"><i class="lni-briefcase"></i></div>
                                                    <h4>Belum ada lamaran</h4>
                                                    <p>Anda belum melamar pekerjaan apapun. Mulai jelajahi lowongan dan lamar sekarang!</p>
                                                    <a href="index.php" class="btn btn-common">Jelajahi Lowongan</a>
                                                </div>
                                    <?php endif; ?>
                                </div>

                                <div class="p-4 pt-0">
                                    <div class="info-section">
                                        <div class="info-header">
                                            <div class="info-icon"><i class="lni-information"></i></div>
                                            <h5 class="info-title">Informasi Status Lamaran</h5>
                                        </div>
                                        <p class="info-content">Tidak yakin dengan arti status lamaran Anda? <a href="#" class="info-link" onclick="openStatusModal(); return false;">Lihat arti dari setiap status lamaran di sini <i class="lni-arrow-right"></i></a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <section class="footer-Content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-xs-12">
                        <div class="widget">
                            <div class="footer-logo"><img src="assets/img/icon-linkup.png" alt="" /></div>
                            <div class="textwidget"><p>Platform yang menghubungkan pencari kerja berbakat dengan perusahaan terbaik. Temukan karier impian Anda sekarang.</p></div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4 col-xs-12">
                        <div class="widget">
                            <h3 class="block-title">Quick Links</h3>
                            <ul class="menu"><li><a href="index.php">Cari Lowongan</a></li><li><a href="jelajahi-perusahaan.php">Perusahaan</a></li><li><a href="statuslamaran.php">Status Lamaran</a></li></ul>
                            <ul class="menu"><li><a href="login.php">Masuk</a></li><li><a href="register.php">Daftar</a></li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div id="copyright"><div class="container"><div class="row"><div class="col-md-12"><div class="site-info text-center"><p>© 2025 LinkUp. All rights reserved</p></div></div></div></div></div>
    </footer>

    <a href="#" class="back-to-top"><i class="lni-arrow-up"></i></a>
    <div id="preloader"><div class="loader" id="loader-1"></div></div>

    <!-- Status Info Modal - VERSION SIMPLE YANG PASTI BERHASIL -->
    <div id="statusInfoModal" class="status-modal">
        <div class="status-modal-content">
            <div class="status-modal-header">
                <div class="status-modal-title">
                    <div class="status-modal-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Informasi Status Lamaran</h3>
                        <p>Pahami arti dari setiap status lamaran Anda</p>
                    </div>
                </div>
                <button class="status-modal-close" onclick="closeStatusModal()">&times;</button>
            </div>
            <div class="status-modal-body">
                <div class="status-info-list">
                    <div class="status-info-item">
                        <div class="status-info-badge">
                            <span class="status-badge status-diproses">Diproses</span>
                        </div>
                        <div class="status-info-content">
                            <h4>Lamaran Anda sedang diproses</h4>
                            <p>Perusahaan sedang meninjau lamaran Anda. Anda mungkin akan dihubungi untuk tahap seleksi berikutnya.</p>
                        </div>
                    </div>
                    <div class="status-info-item">
                        <div class="status-info-badge">
                            <span class="status-badge status-diterima">Diterima</span>
                        </div>
                        <div class="status-info-content">
                            <h4>Selamat! Lamaran Anda diterima</h4>
                            <p>Perusahaan telah memutuskan untuk menerima lamaran Anda. Anda akan dihubungi untuk informasi lebih lanjut.</p>
                        </div>
                    </div>
                    <div class="status-info-item">
                        <div class="status-info-badge">
                            <span class="status-badge status-ditolak">Ditolak</span>
                        </div>
                        <div class="status-info-content">
                            <h4>Lamaran Anda tidak diterima</h4>
                            <p>Saat ini lamaran Anda tidak sesuai dengan kebutuhan perusahaan. Jangan menyerah dan teruslah mencoba lowongan lainnya.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="status-modal-footer">
                <button class="status-modal-btn" onclick="closeStatusModal()">Mengerti, Terima kasih</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Lamar Ulang -->
    <div id="modalConfirmApply">
        <div class="modal-backdrop" onclick="closeConfirmModal()"></div>
        <div class="modal-container">
            <div class="modal-header-custom">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div>
                        <h4 style="font-size: 18px; font-weight: bold; margin: 0;">Konfirmasi Lamar Ulang</h4>
                        <p style="font-size: 12px; opacity: 0.8; margin: 4px 0 0;">Silakan konfirmasi tindakan Anda</p>
                    </div>
                </div>
                <button onclick="closeConfirmModal()" style="position: absolute; right: 20px; top: 20px; background: none; border: none; color: white; cursor: pointer; font-size: 20px;">&times;</button>
            </div>
            <div class="modal-body-custom">
                <div class="warning-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.406 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h5 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">Lamar Ulang Lowongan?</h5>
                <p style="font-size: 14px; color: #6b7280; margin-bottom: 8px;" id="confirmMessage">Apakah Anda yakin ingin melamar ulang lowongan ini?</p>
                <p style="font-size: 12px; color: #9ca3af;">Lamaran Anda yang sebelumnya akan diganti dengan yang baru.</p>
            </div>
            <div class="modal-footer-custom">
                <button class="btn-cancel" onclick="closeConfirmModal()">Batal</button>
                <button class="btn-confirm" id="confirmApplyBtn">Ya, Lamar Ulang</button>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.slicknav.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        $(document).ready(function() {
            function loadSavedJobs() {
                const savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
                const container = $('#saved-jobs-container');
                if (savedJobs.length === 0) {
                    container.html(`<div class="empty-state"><div class="empty-state-icon"><i class="lni-bookmark"></i></div><h4>Belum ada lowongan yang disimpan</h4><p>Simpan lowongan kerja yang Anda minati sehingga bisa dilihat lagi nanti.</p><a href="index.php" class="btn btn-common">Jelajahi Lowongan</a></div>`);
                } else {
                    let jobsHtml = '';
                    savedJobs.forEach(job => {
                        jobsHtml += `<div class="application-card"><div class="application-header"><div class="d-flex align-items-start"><div class="company-logo"><img src="${job.image || 'assets/img/product/img1.png'}" onerror="this.src='assets/img/product/img1.png'"></div><div class="job-info"><h3 class="job-title">${job.title}</h3><p class="company-name">${job.company}</p><div class="job-meta"><span class="meta-item"><i class="lni-map-marker"></i> ${job.location}</span><span class="meta-item"><i class="lni-briefcase"></i> ${job.type}</span><span class="meta-item"><i class="lni-money-protection"></i> ${job.salary}</span></div></div></div><span class="status-badge status-tersimpan">Tersimpan</span></div><div class="application-footer"><div class="application-date"><i class="lni-timer"></i> Disimpan pada ${job.savedDate}</div><div class="application-actions"><a href="job-detail.php?id=${job.id}" class="action-btn">Lihat Detail</a><button class="action-btn" onclick="removeSavedJob('${job.id}')">Hapus</button></div></div></div>`;
                    });
                    container.html(jobsHtml);
                }
            }
            window.removeSavedJob = function(jobId) {
                let savedJobs = JSON.parse(localStorage.getItem('savedJobs') || '[]');
                savedJobs = savedJobs.filter(job => job.id != jobId);
                localStorage.setItem('savedJobs', JSON.stringify(savedJobs));
                loadSavedJobs();
            };
            $('.status-tab').on('click', function() {
                const tabId = $(this).data('tab');
                $('.status-tab').removeClass('active');
                $(this).addClass('active');
                $('.tab-pane').removeClass('active');
                $(`#${tabId}-content`).addClass('active');
            });
            $('.filter-btn').on('click', function() {
                const filter = $(this).data('filter');
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                $('.application-card').each(function() {
                    $(this).data('status') === filter || filter === 'all' ? $(this).show() : $(this).hide();
                });
            });
            loadSavedJobs();
            $('.back-to-top').on('click', function(e) { e.preventDefault(); $('html, body').animate({ scrollTop: 0 }, 800); });
        });

        // ========== MODAL KONFIRMASI ==========
        let pendingJobId = null;

        function showConfirmModal(jobId) {
    pendingJobId = jobId;
    const modal = document.getElementById('modalConfirmApply');
    const messageEl = document.getElementById('confirmMessage');
    const btn = event?.target;
    const card = btn ? btn.closest('.application-card') : null;
    let jobTitle = card ? card.querySelector('.job-title')?.textContent : '';
    messageEl.innerHTML = jobTitle ? `Apakah Anda yakin ingin melamar ulang lowongan <strong style="color:#2563eb">"${jobTitle}"</strong>?` : 'Apakah Anda yakin ingin melamar ulang lowongan ini?';
    modal.classList.add('show');
    document.body.classList.add('modal-open');
}

function closeConfirmModal() {
    document.getElementById('modalConfirmApply').classList.remove('show');
    document.body.classList.remove('modal-open');
    pendingJobId = null;
}


        function confirmApplyAgain() {
            if (!pendingJobId) return;
            const confirmBtn = document.getElementById('confirmApplyBtn');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<span class="spinner-white"></span> Memproses...';
            confirmBtn.disabled = true;
            fetch('proses_lamar.php?id=' + pendingJobId)
                .then(res => res.json())
                .then(data => {
                    closeConfirmModal();
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showAlert('error', data.message);
                        confirmBtn.innerHTML = originalText;
                        confirmBtn.disabled = false;
                    }
                })
                .catch(err => {
                    closeConfirmModal();
                    showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                });
        }

        function showAlert(type, message) {
            const existing = document.querySelector('.custom-alert');
            if (existing) existing.remove();
            const alert = document.createElement('div');
            alert.className = `custom-alert alert-${type === 'success' ? 'success' : 'error'}`;
            alert.innerHTML = `<div class="alert-content"><div class="alert-icon">${type === 'success' ? '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#10b981"/><path d="M8 12L11 15L16 9" stroke="white" stroke-width="2"/></svg>' : '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="#ef4444"/><path d="M12 8V12M12 16H12.01" stroke="white" stroke-width="2"/></svg>'}</div><div class="alert-text"><div class="alert-title">${type === 'success' ? 'Berhasil!' : 'Gagal'}</div><div class="alert-message">${message}</div></div><button class="alert-close" onclick="this.parentElement.parentElement.remove()">×</button></div>`;
            document.body.appendChild(alert);
            setTimeout(() => alert.style.transform = 'translateX(0)', 10);
            setTimeout(() => { if (alert.parentElement) alert.remove(); }, 3000);
        }

        document.getElementById('confirmApplyBtn').onclick = confirmApplyAgain;
        document.addEventListener('keydown', function(e) { 
            if (e.key === 'Escape') {
                closeConfirmModal();
                closeStatusModal();
            }
        });

        // ========== STATUS INFO MODAL FUNCTIONS ==========
function openStatusModal() {
    var modal = document.getElementById('statusInfoModal');
    modal.style.display = 'flex';
    document.body.classList.add('modal-open');
}

function closeStatusModal() {
    var modal = document.getElementById('statusInfoModal');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

        // Tutup modal jika klik di luar content (tapi karena modal background sudah menutupi, ini otomatis)
        window.onclick = function(event) {
            var modal = document.getElementById('statusInfoModal');
            if (event.target === modal) {
                closeStatusModal();
            }
        }

        // ========== DELETE APPLICATION FUNCTION ==========
function deleteApplication(applicationId) {
    if (confirm('Apakah Anda yakin ingin menghapus lamaran ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Show loading state on the button
        const btn = event?.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-white"></span> Menghapus...';
        btn.disabled = true;
        
        fetch('hapus_lamaran.php?id=' + applicationId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Lamaran berhasil dihapus');
                // Remove the application card from DOM
                const card = btn.closest('.application-card');
                if (card) {
                    card.remove();
                }
                // Reload page after 1.5 seconds to update stats
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Gagal menghapus lamaran');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
}
    </script>
</body>

</html>