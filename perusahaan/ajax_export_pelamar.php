<?php
// perusahaan/ajax_export_pelamar.php
session_start();
require_once 'koneksi_perusahaan.php';

header('Content-Type: application/json');

// Tambahkan error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Cek koneksi database
if (!$db->koneksi) {
    sendErrorResponse('Koneksi database gagal: ' . mysqli_connect_error());
    exit();
}

// Build WHERE clause for filters
$where_conditions = ["low.id_perusahaan = ?"];
$params = [$company_id];
$types = "i";

// Filter provinsi
if (!empty($_POST['provinsi'])) {
    $where_conditions[] = "prov.id_provinsi = ?";
    $params[] = $_POST['provinsi'];
    $types .= "i";
}

// Filter kota
if (!empty($_POST['kota'])) {
    $where_conditions[] = "kota.id_kota = ?";
    $params[] = $_POST['kota'];
    $types .= "i";
}

// Filter jenis kelamin
if (!empty($_POST['jk'])) {
    $where_conditions[] = "pr.jk_user = ?";
    $params[] = $_POST['jk'];
    $types .= "s";
}

// Filter lowongan (tambahkan kondisi untuk filter berdasarkan lowongan)
if (!empty($_POST['id_lowongan'])) {
    $where_conditions[] = "low.id_lowongan = ?";
    $params[] = $_POST['id_lowongan'];
    $types .= "i";
}

// Filter tanggal lahir minimal
if (!empty($_POST['tanggal_lahir_min'])) {
    $where_conditions[] = "pr.tanggallahir_user >= ?";
    $params[] = $_POST['tanggal_lahir_min'];
    $types .= "s";
}

// Filter tanggal lahir maksimal
if (!empty($_POST['tanggal_lahir_max'])) {
    $where_conditions[] = "pr.tanggallahir_user <= ?";
    $params[] = $_POST['tanggal_lahir_max'];
    $types .= "s";
}

// Filter status lamaran
if (!empty($_POST['status_lamaran'])) {
    $where_conditions[] = "lm.status_lamaran = ?";
    $params[] = $_POST['status_lamaran'];
    $types .= "s";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// PERBAIKAN: Query untuk mengambil data PELAMAR UNIK (DISTINCT) seperti di tabel pelamar.php
$query = "SELECT DISTINCT 
            u.id_user,
            u.email_user,
            u.username_user,
            pr.nama_user,
            pr.nohp_user,
            pr.tanggallahir_user,
            pr.jk_user,
            pr.deskripsi_user,
            pr.kelebihan_user,
            pr.riwayatpekerjaan_user,
            pr.prestasi_user,
            pr.instagram_user,
            pr.facebook_user,
            pr.linkedin_user,
            prov.nama_provinsi,
            kota.nama_kota
          FROM lamaran lm
          JOIN lowongan low ON lm.id_lowongan = low.id_lowongan
          JOIN user u ON lm.id_user = u.id_user
          LEFT JOIN profil pr ON u.id_user = pr.id_user
          LEFT JOIN provinsi prov ON pr.id_provinsi = prov.id_provinsi
          LEFT JOIN kota kota ON pr.id_kota = kota.id_kota
          $where_clause
          ORDER BY pr.nama_user ASC";

$stmt = mysqli_prepare($db->koneksi, $query);

if (!$stmt) {
    sendErrorResponse('Error prepare statement: ' . mysqli_error($db->koneksi));
    exit();
}

if (!empty($params)) {
    // Buat array reference untuk bind_param
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

if (!$result) {
    sendErrorResponse('Error get result: ' . mysqli_stmt_error($stmt));
    exit();
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

mysqli_stmt_close($stmt);

$count = count($data);

if ($count === 0) {
    sendErrorResponse('Tidak ada data pelamar yang sesuai dengan filter yang dipilih');
    exit();
}

// Create exports directory if not exists
$export_dir = __DIR__ . '/exports/';
if (!is_dir($export_dir)) {
    if (!mkdir($export_dir, 0777, true)) {
        sendErrorResponse('Gagal membuat folder exports. Pastikan folder memiliki permission yang benar.');
        exit();
    }
}

// Cek apakah folder exports bisa ditulisi
if (!is_writable($export_dir)) {
    sendErrorResponse('Folder exports tidak dapat ditulisi. Ubah permission folder menjadi 755 atau 777.');
    exit();
}

// Generate file based on format
$filename = 'export_pelamar_' . date('Y-m-d_H-i-s');

try {
    // PANGGIL FUNGSI GENERATE YANG SESUAI
    switch ($format) {
        case 'csv':
            $file_url = generatePelamarCSV($data, $filename, $export_dir);
            break;
        case 'xls':
            $file_url = generatePelamarXLS($data, $filename, $export_dir);
            break;
        case 'pdf':
            $file_url = generatePelamarPDF($data, $filename, $export_dir);
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

// ============ FUNGSI GENERATE CSV ============
function generatePelamarCSV($data, $filename, $export_dir)
{
    $filepath = $export_dir . $filename . '.csv';
    $file = fopen($filepath, 'w');

    if (!$file) {
        throw new Exception('Tidak dapat membuat file CSV');
    }

    // Add UTF-8 BOM for proper encoding
    fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Header
    $headers = [
        'ID User',
        'Nama Lengkap',
        'Email',
        'Username',
        'No. Telepon',
        'Jenis Kelamin',
        'Tanggal Lahir',
        'Provinsi',
        'Kota'
    ];
    fputcsv($file, $headers);

    // Data
    foreach ($data as $row) {
        $jk_text = '';
        if ($row['jk_user'] == 'L') {
            $jk_text = 'Laki-laki';
        } elseif ($row['jk_user'] == 'P') {
            $jk_text = 'Perempuan';
        }

        $csv_row = [
            $row['id_user'],
            $row['nama_user'] ?? $row['username_user'] ?? '-',
            $row['email_user'] ?? '-',
            $row['username_user'] ?? '-',
            $row['nohp_user'] ?? '-',
            $jk_text,
            $row['tanggallahir_user'] ? date('d/m/Y', strtotime($row['tanggallahir_user'])) : '-',
            $row['nama_provinsi'] ?? '-',
            $row['nama_kota'] ?? '-'
        ];
        fputcsv($file, $csv_row);
    }

    fclose($file);
    return 'exports/' . $filename . '.csv';
}

// ============ FUNGSI GENERATE XLS ============
function generatePelamarXLS($data, $filename, $export_dir)
{
    $filepath = $export_dir . $filename . '.xls';

    $html = '<html>
    <head>
        <meta charset="UTF-8">
        <title>Export Data Pelamar</title>
        <style>
            th { background-color: #4f46e5; color: white; padding: 8px; }
            td { padding: 6px; border: 1px solid #ddd; }
            table { border-collapse: collapse; width: 100%; }
        </style>
    </head>
    <body>
        <h2>Laporan Data Pelamar</h2>
        <p>Tanggal Export: ' . date('d/m/Y H:i:s') . '</p>
        <p>Total Data: ' . count($data) . ' pelamar</p>
        <table border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Provinsi</th>
                    <th>Kota</th>
                </tr>
            </thead>
            <tbody>';

    $no = 1;
    foreach ($data as $row) {
        $jk_text = '';
        if ($row['jk_user'] == 'L') {
            $jk_text = 'Laki-laki';
        } elseif ($row['jk_user'] == 'P') {
            $jk_text = 'Perempuan';
        }

        $html .= '<tr>
            <td>' . $no++ . '</td>
            <td>' . htmlspecialchars($row['nama_user'] ?? $row['username_user'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['email_user'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['nohp_user'] ?? '-') . '</td>
            <td>' . $jk_text . '</td>
            <td>' . ($row['tanggallahir_user'] ? date('d/m/Y', strtotime($row['tanggallahir_user'])) : '-') . '</td>
            <td>' . htmlspecialchars($row['nama_provinsi'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['nama_kota'] ?? '-') . '</td>
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

// ============ FUNGSI GENERATE PDF ============
function generatePelamarPDF($data, $filename, $export_dir)
{
    $filepath = $export_dir . $filename . '.pdf';

    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Export Data Pelamar</title>
        <style>
            @page {
                margin: 1cm;
                size: A4 landscape;
            }
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
                font-size: 11px;
            }
            h1 { 
                color: #333; 
                text-align: center; 
                margin-bottom: 20px;
                font-size: 18px;
            }
            .header-info {
                margin-bottom: 20px;
                font-size: 10px;
                color: #666;
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 20px; 
            }
            th, td { 
                border: 1px solid #ddd; 
                padding: 6px; 
                text-align: left; 
                font-size: 9px;
            }
            th { 
                background-color: #4f46e5; 
                color: white; 
                font-weight: bold; 
            }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .summary { 
                margin-top: 20px; 
                font-weight: bold; 
                text-align: center;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <h1>LAPORAN DATA PELAMAR</h1>
        <div class="header-info">
            <p>Tanggal Export: ' . date('d/m/Y H:i:s') . '</p>
            <p>Total Data: ' . count($data) . ' pelamar</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nama Lengkap</th>
                    <th width="20%">Email</th>
                    <th width="12%">No. Telepon</th>
                    <th width="10%">JK</th>
                    <th width="12%">Tgl Lahir</th>
                    <th width="12%">Provinsi</th>
                    <th width="12%">Kota</th>
                </tr>
            </thead>
            <tbody>';

    $no = 1;
    foreach ($data as $row) {
        $jk_text = ($row['jk_user'] == 'L') ? 'Laki-laki' : (($row['jk_user'] == 'P') ? 'Perempuan' : '-');

        $html .= '<tr>
            <td>' . $no++ . '</td>
            <td>' . htmlspecialchars(substr($row['nama_user'] ?? $row['username_user'] ?? '-', 0, 30)) . '</td>
            <td>' . htmlspecialchars(substr($row['email_user'] ?? '-', 0, 30)) . '</td>
            <td>' . htmlspecialchars($row['nohp_user'] ?? '-') . '</td>
            <td>' . $jk_text . '</td>
            <td>' . ($row['tanggallahir_user'] ? date('d/m/Y', strtotime($row['tanggallahir_user'])) : '-') . '</td>
            <td>' . htmlspecialchars(substr($row['nama_provinsi'] ?? '-', 0, 20)) . '</td>
            <td>' . htmlspecialchars(substr($row['nama_kota'] ?? '-', 0, 20)) . '</td>
        </tr>';
    }

    $html .= '</tbody>
        </table>
        <div class="summary">
            <p>LAPORAN DATA PELAMAR - TOTAL ' . count($data) . ' DATA</p>
        </div>
    </body>
    </html>';

    file_put_contents($filepath, $html);
    return 'exports/' . $filename . '.pdf';
}
?>