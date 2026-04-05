<?php
// perusahaan/ajax_export_lamaran.php
session_start();
require_once 'koneksi_perusahaan.php';

header('Content-Type: application/json');

// Tambahkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log request untuk debugging
error_log("=== Export Lamaran Request ===");
error_log("POST data: " . print_r($_POST, true));


// Fungsi untuk mengirim response error
function sendErrorResponse($message)
{
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

if (!isset($_SESSION['company_id'])) {
    sendErrorResponse('Unauthorized - Silakan login kembali');
    exit();
}

$company_id = $_SESSION['company_id'];
$format = $_POST['format'] ?? '';

if (!in_array($format, ['csv', 'xls', 'pdf'])) {
    sendErrorResponse('Format tidak valid. Gunakan csv, xls, atau pdf');
    exit();
}

// Build WHERE clause for filters
$where_conditions = ["low.id_perusahaan = ?"];
$params = [$company_id];
$types = "i";

// Filter status lamaran
if (!empty($_POST['status_lamaran'])) {
    $where_conditions[] = "l.status_lamaran = ?";
    $params[] = $_POST['status_lamaran'];
    $types .= "s";
}

// Filter lowongan
if (!empty($_POST['id_lowongan'])) {
    $where_conditions[] = "low.id_lowongan = ?";
    $params[] = $_POST['id_lowongan'];
    $types .= "i";
}

// Filter tanggal mulai
if (!empty($_POST['tanggal_mulai'])) {
    $where_conditions[] = "DATE(l.tanggal_lamar) >= ?";
    $params[] = $_POST['tanggal_mulai'];
    $types .= "s";
}

// Filter tanggal akhir
if (!empty($_POST['tanggal_akhir'])) {
    $where_conditions[] = "DATE(l.tanggal_lamar) <= ?";
    $params[] = $_POST['tanggal_akhir'];
    $types .= "s";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Query untuk mengambil data lamaran
$query = "SELECT 
            l.id_lamaran,
            l.tanggal_lamar,
            l.status_lamaran,
            l.catatan_hrd,
            low.id_lowongan,
            low.judul_lowongan,
            u.id_user,
            u.email_user,
            u.username_user,
            pr.nama_user,
            pr.nohp_user,
            pr.jk_user,
            pr.tanggallahir_user,
            prov.nama_provinsi,
            kota.nama_kota
          FROM lamaran l
          JOIN lowongan low ON l.id_lowongan = low.id_lowongan
          JOIN user u ON l.id_user = u.id_user
          LEFT JOIN profil pr ON u.id_user = pr.id_user
          LEFT JOIN provinsi prov ON pr.id_provinsi = prov.id_provinsi
          LEFT JOIN kota kota ON pr.id_kota = kota.id_kota
          $where_clause
          ORDER BY l.tanggal_lamar DESC";

$stmt = mysqli_prepare($db->koneksi, $query);

if (!$stmt) {
    sendErrorResponse('Error prepare statement: ' . mysqli_error($db->koneksi));
    exit();
}

if (!empty($params)) {
    $bind_params = [$types];
    for ($i = 0; $i < count($params); $i++) {
        $bind_params[] = &$params[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_params);
}

if (!mysqli_stmt_execute($stmt)) {
    sendErrorResponse('Error execute statement: ' . mysqli_stmt_error($stmt));
    exit();
}

$result = mysqli_stmt_get_result($stmt);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

mysqli_stmt_close($stmt);
$count = count($data);

if ($count === 0) {
    sendErrorResponse('Tidak ada data lamaran yang sesuai dengan filter yang dipilih');
    exit();
}

// Create exports directory
$export_dir = __DIR__ . '/exports/';
if (!is_dir($export_dir)) {
    mkdir($export_dir, 0777, true);
}

$filename = 'export_lamaran_' . date('Y-m-d_H-i-s');

try {
    switch ($format) {
        case 'csv':
            $file_url = generateLamaranCSV($data, $filename, $export_dir);
            break;
        case 'xls':
            $file_url = generateLamaranXLS($data, $filename, $export_dir);
            break;
        case 'pdf':
            $file_url = generateLamaranPDF($data, $filename, $export_dir);
            break;
        default:
            sendErrorResponse('Format tidak dikenal');
            exit();
    }

    echo json_encode([
        'success' => true,
        'data_count' => $count,
        'download_url' => $file_url
    ]);
} catch (Exception $e) {
    sendErrorResponse('Error generating file: ' . $e->getMessage());
}

// ============ GENERATE CSV ============
function generateLamaranCSV($data, $filename, $export_dir)
{
    $filepath = $export_dir . $filename . '.csv';
    $file = fopen($filepath, 'w');

    fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

    $headers = [
        'ID Lamaran',
        'Nama Pelamar',
        'Email',
        'No. Telepon',
        'Jenis Kelamin',
        'Tanggal Lahir',
        'Provinsi',
        'Kota',
        'Lowongan',
        'Tanggal Lamar',
        'Status',
        'Catatan HRD'
    ];
    fputcsv($file, $headers);

    foreach ($data as $row) {
        $jk_text = '';
        if ($row['jk_user'] == 'L')
            $jk_text = 'Laki-laki';
        elseif ($row['jk_user'] == 'P')
            $jk_text = 'Perempuan';

        $csv_row = [
            $row['id_lamaran'],
            $row['nama_user'] ?? $row['username_user'] ?? '-',
            $row['email_user'] ?? '-',
            $row['nohp_user'] ?? '-',
            $jk_text,
            $row['tanggallahir_user'] ? date('d/m/Y', strtotime($row['tanggallahir_user'])) : '-',
            $row['nama_provinsi'] ?? '-',
            $row['nama_kota'] ?? '-',
            $row['judul_lowongan'] ?? '-',
            date('d/m/Y H:i', strtotime($row['tanggal_lamar'])),
            $row['status_lamaran'],
            strip_tags($row['catatan_hrd'] ?? '-')
        ];
        fputcsv($file, $csv_row);
    }

    fclose($file);
    return 'exports/' . $filename . '.csv';
}

// ============ GENERATE XLS ============
function generateLamaranXLS($data, $filename, $export_dir)
{
    $filepath = $export_dir . $filename . '.xls';

    $html = '<html>
    <head>
        <meta charset="UTF-8">
        <title>Export Data Lamaran</title>
        <style>
            th { background-color: #2563eb; color: white; padding: 8px; }
            td { padding: 6px; border: 1px solid #ddd; }
            table { border-collapse: collapse; width: 100%; }
        </style>
    </head>
    <body>
        <h2>Laporan Data Lamaran Masuk</h2>
        <p>Tanggal Export: ' . date('d/m/Y H:i:s') . '</p>
        <p>Total Data: ' . count($data) . ' lamaran</p>
        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelamar</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Jenis Kelamin</th>
                    <th>Lowongan</th>
                    <th>Tanggal Lamar</th>
                    <th>Status</th>
                    <th>Catatan HRD</th>
                </tr>
            </thead>
            <tbody>';

    $no = 1;
    foreach ($data as $row) {
        $jk_text = '';
        if ($row['jk_user'] == 'L')
            $jk_text = 'Laki-laki';
        elseif ($row['jk_user'] == 'P')
            $jk_text = 'Perempuan';

        $html .= '<tr>
            <td>' . $no++ . '</td>
            <td>' . htmlspecialchars($row['nama_user'] ?? $row['username_user'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['email_user'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['nohp_user'] ?? '-') . '</td>
            <td>' . $jk_text . '</td>
            <td>' . htmlspecialchars($row['judul_lowongan'] ?? '-') . '</td>
            <td>' . date('d/m/Y H:i', strtotime($row['tanggal_lamar'])) . '</td>
            <td>' . $row['status_lamaran'] . '</td>
            <td>' . htmlspecialchars(substr(strip_tags($row['catatan_hrd'] ?? '-'), 0, 100)) . '</td>
        </tr>';
    }

    $html .= '</tbody>
        </table>
        <p style="margin-top: 20px; text-align: center;">Dicetak dari Sistem LinkUp - ' . date('Y') . '</p>
    </body>
    </html>';

    file_put_contents($filepath, $html);
    return 'exports/' . $filename . '.xls';
}

// ============ GENERATE PDF ============
function generateLamaranPDF($data, $filename, $export_dir)
{
    $filepath = $export_dir . $filename . '.pdf';

    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Export Data Lamaran</title>
        <style>
            @page { margin: 1cm; size: A4 landscape; }
            body { font-family: Arial, sans-serif; font-size: 10px; }
            h1 { color: #2563eb; text-align: center; font-size: 16px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th { background-color: #2563eb; color: white; padding: 6px; font-size: 9px; }
            td { border: 1px solid #ddd; padding: 5px; }
            .header-info { margin-bottom: 15px; font-size: 9px; }
        </style>
    </head>
    <body>
        <h1>LAPORAN DATA LAMARAN MASUK</h1>
        <div class="header-info">
            <p>Tanggal Export: ' . date('d/m/Y H:i:s') . '</p>
            <p>Total Data: ' . count($data) . ' lamaran</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelamar</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Lowongan</th>
                    <th>Tgl Lamar</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>';

    $no = 1;
    foreach ($data as $row) {
        $html .= '<tr>
            <td>' . $no++ . '</td>
            <td>' . htmlspecialchars(substr($row['nama_user'] ?? $row['username_user'] ?? '-', 0, 25)) . '</td>
            <td>' . htmlspecialchars(substr($row['email_user'] ?? '-', 0, 25)) . '</td>
            <td>' . htmlspecialchars($row['nohp_user'] ?? '-') . '</td>
            <td>' . htmlspecialchars(substr($row['judul_lowongan'] ?? '-', 0, 30)) . '</td>
            <td>' . date('d/m/Y', strtotime($row['tanggal_lamar'])) . '</td>
            <td>' . $row['status_lamaran'] . '</td>
            <td>' . htmlspecialchars(substr(strip_tags($row['catatan_hrd'] ?? '-'), 0, 50)) . '</td>
        </tr>';
    }

    $html .= '</tbody>
        </table>
        <div style="text-align: center; margin-top: 20px; font-size: 9px;">
            Dicetak dari Sistem LinkUp - ' . date('Y') . '
        </div>
    </body>
    </html>';

    file_put_contents($filepath, $html);
    return 'exports/' . $filename . '.pdf';
}
?>