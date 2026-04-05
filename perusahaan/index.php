<?php
// perusahaan/index.php - Dashboard Perusahaan
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['company_id']) || !isset($_SESSION['company_logged_in']) || $_SESSION['company_logged_in'] !== true) {
    // Redirect ke halaman login dengan pesan
    header('Location: login.php?message=Silakan login terlebih dahulu');
    exit();
}

// Cache control headers untuk mencegah back button setelah logout
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

require_once 'koneksi_perusahaan.php';

$company_id = $_SESSION['company_id'];
$stats = $db->getStats($company_id);
$perusahaan = $db->getPerusahaanById($company_id);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | JobPortal Perusahaan</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="app-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-wrapper">
            <?php include 'includes/navbar.php'; ?>

            <main class="main-content">
                <div class="content-header">
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Selamat datang kembali,
                        <?php echo htmlspecialchars($perusahaan['nama_perusahaan']); ?>! Berikut ringkasan aktivitas
                        perusahaan Anda.
                    </p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Lowongan</h3>
                            <div class="stat-value" id="totalLowongan"><?php echo $stats['total_lowongan']; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Pelamar</h3>
                            <div class="stat-value" id="totalPelamar"><?php echo $stats['total_pelamar']; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Lamaran</h3>
                            <div class="stat-value" id="totalLamaran"><?php echo $stats['total_lamaran']; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Lowongan Aktif</h3>
                            <div class="stat-value" id="lowonganAktif"><?php echo $stats['lowongan_aktif']; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Chart & Recent Jobs -->
                <div class="two-columns">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Statistik Lamaran</h3>
                            <span class="badge badge-info">6 bulan terakhir</span>
                        </div>
                        <div class="card-body">
                            <canvas id="lamaranChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-clock"></i> Lowongan Terbaru</h3>
                            <a href="lowongan.php" class="link-view">Lihat semua <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="card-body p-0">
                            <div class="recent-jobs-list" id="recentJobsList">
                                <?php if (count($stats['lowongan_terbaru']) > 0): ?>
                                    <?php foreach ($stats['lowongan_terbaru'] as $job): ?>
                                        <div class="recent-job-item">
                                            <div>
                                                <div class="recent-job-title">
                                                    <?php echo htmlspecialchars($job['judul_lowongan']); ?>
                                                </div>
                                                <div class="recent-job-meta">
                                                    <?php echo htmlspecialchars($job['lokasi_lowongan'] ?? 'Lokasi tidak tersedia'); ?>
                                                    •
                                                    Rp <?php echo number_format($job['gaji_lowongan'] ?? 0, 0, ',', '.'); ?>
                                                </div>
                                            </div>
                                            <span
                                                class="badge <?php echo $job['status'] == 'Aktif' ? 'badge-active' : 'badge-nonactive'; ?>">
                                                <?php echo $job['status']; ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="recent-job-item">Belum ada lowongan</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-envelope"></i> Lamaran Terbaru</h3>
                        <a href="lamaran.php" class="link-view">Kelola lamaran <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <table class="data-table" id="recentApplicationsTable">
                            <thead>
                                <tr>
                                    <th>Pelamar</th>
                                    <th>Lowongan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                            </thead>
                            <tbody id="recentAppsBody">
                                <?php if (count($stats['lamaran_terbaru']) > 0): ?>
                                    <?php foreach ($stats['lamaran_terbaru'] as $lamaran): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($lamaran['nama_user'] ?? $lamaran['username_user'] ?? '-'); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($lamaran['judul_lowongan']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($lamaran['tanggal_lamar'])); ?></td>
                                            <td>
                                                <span class="badge <?php
                                                echo $lamaran['status_lamaran'] == 'Diterima' ? 'badge-diterima' :
                                                    ($lamaran['status_lamaran'] == 'Ditolak' ? 'badge-ditolak' : 'badge-diproses');
                                                ?>">
                                                    <?php echo $lamaran['status_lamaran']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">Belum ada lamaran</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Mobile Menu Toggle
        $('.menu-toggle').click(function () {
            $('.sidebar').toggleClass('active');
        });

        // Close sidebar when clicking outside on mobile
        $(document).click(function (e) {
            if ($(window).width() <= 768) {
                if (!$(e.target).closest('.sidebar') && !$(e.target).closest('.menu-toggle')) {
                    $('.sidebar').removeClass('active');
                }
            }
        });
    </script>
    <script>
        // Chart data dari PHP
        const chartData = <?php
        $bulanMap = [];
        foreach ($stats['lamaran_per_bulan'] as $item) {
            $bulanMap[$item['tahun'] . '-' . str_pad($item['bulan'], 2, '0', STR_PAD_LEFT)] = $item['jumlah'];
        }

        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify("-$i month");
            $key = $date->format('Y-m');
            $labels[] = $date->format('M Y');
            $data[] = $bulanMap[$key] ?? 0;
        }
        echo json_encode(['labels' => $labels, 'data' => $data]);
        ?>;

        const ctx = document.getElementById('lamaranChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Lamaran Masuk',
                    data: chartData.data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.05)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>

</html>