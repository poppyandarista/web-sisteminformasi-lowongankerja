<?php
// perusahaan/ajax_export_lowongan.php
session_start();
require_once 'koneksi_perusahaan.php';

header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$company_id = $_SESSION['company_id'];
$format = $_POST['format'] ?? '';

if (!in_array($format, ['csv', 'xls', 'pdf'])) {
    echo json_encode(['success' => false, 'message' => 'Format tidak valid']);
    exit();
}

// Build WHERE clause for filters
$where_conditions = ["l.id_perusahaan = ?"];
$params = [$company_id];
$types = "i";

// Filter provinsi
if (!empty($_POST['provinsi'])) {
    $where_conditions[] = "l.id_provinsi = ?";
    $params[] = $_POST['provinsi'];
    $types .= "i";
}

// Filter kota
if (!empty($_POST['kota'])) {
    $where_conditions[] = "l.id_kota = ?";
    $params[] = $_POST['kota'];
    $types .= "i";
}

// Filter kategori
if (!empty($_POST['kategori'])) {
    $where_conditions[] = "l.kategori_lowongan = ?";
    $params[] = $_POST['kategori'];
    $types .= "i";
}

// Filter jenis
if (!empty($_POST['jenis'])) {
    $where_conditions[] = "l.id_jenis = ?";
    $params[] = $_POST['jenis'];
    $types .= "i";
}

// Filter gaji minimal
if (!empty($_POST['gaji_min'])) {
    $where_conditions[] = "l.gaji_lowongan >= ?";
    $params[] = $_POST['gaji_min'];
    $types .= "d";
}

// Filter gaji maksimal
if (!empty($_POST['gaji_max'])) {
    $where_conditions[] = "l.gaji_lowongan <= ?";
    $params[] = $_POST['gaji_max'];
    $types .= "d";
}

// Filter status
if (!empty($_POST['status'])) {
    $where_conditions[] = "l.status = ?";
    $params[] = $_POST['status'];
    $types .= "s";
}

// Filter tanggal posting
if (!empty($_POST['tanggal_posting'])) {
    $where_conditions[] = "l.tanggal_posting >= ?";
    $params[] = $_POST['tanggal_posting'];
    $types .= "s";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Query to get filtered data
$query = "SELECT l.*, 
                 p.nama_provinsi, 
                 k.nama_kota,
                 kat.nama_kategori,
                 j.nama_jenis
          FROM lowongan l
          LEFT JOIN provinsi p ON l.id_provinsi = p.id_provinsi
          LEFT JOIN kota k ON l.id_kota = k.id_kota
          LEFT JOIN kategori kat ON l.kategori_lowongan = kat.id_kategori
          LEFT JOIN jenis j ON l.id_jenis = j.id_jenis
          $where_clause
          ORDER BY l.tanggal_posting DESC";

$stmt = mysqli_prepare($db->koneksi, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

$count = count($data);

if ($count === 0) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada data yang sesuai dengan filter']);
    exit();
}

// Generate file based on format
$filename = 'export_lowongan_' . date('Y-m-d_H-i-s');

switch ($format) {
    case 'csv':
        $file_url = generateCSV($data, $filename);
        break;
    case 'xls':
        $file_url = generateXLS($data, $filename);
        break;
    case 'pdf':
        $file_url = generatePDF($data, $filename);
        break;
}

echo json_encode([
    'success' => true,
    'data_count' => $count,
    'download_url' => $file_url
]);

function generateCSV($data, $filename)
{
    $filepath = 'exports/' . $filename . '.csv';

    // Create exports directory if not exists
    if (!is_dir('exports')) {
        mkdir('exports', 0777, true);
    }

    $file = fopen($filepath, 'w');

    // Add UTF-8 BOM for proper encoding
    fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Header
    $headers = [
        'ID Lowongan',
        'Judul Lowongan',
        'Kategori',
        'Jenis Pekerjaan',
        'Provinsi',
        'Kota',
        'Lokasi',
        'Gaji',
        'Kualifikasi',
        'Deskripsi',
        'Tanggal Posting',
        'Tanggal Tutup',
        'Status'
    ];
    fputcsv($file, $headers);

    // Data
    foreach ($data as $row) {
        $csv_row = [
            $row['id_lowongan'],
            $row['judul_lowongan'],
            $row['nama_kategori'] ?? '-',
            $row['nama_jenis'] ?? '-',
            $row['nama_provinsi'] ?? '-',
            $row['nama_kota'] ?? '-',
            $row['lokasi_lowongan'] ?? '-',
            'Rp ' . number_format($row['gaji_lowongan'] ?? 0, 0, ',', '.'),
            strip_tags($row['kualifikasi'] ?? '-'),
            strip_tags($row['deskripsi_lowongan'] ?? '-'),
            date('d/m/Y', strtotime($row['tanggal_posting'])),
            $row['tanggal_tutup'] ? date('d/m/Y', strtotime($row['tanggal_tutup'])) : '-',
            $row['status']
        ];
        fputcsv($file, $csv_row);
    }

    fclose($file);
    return $filepath;
}

function generateXLS($data, $filename)
{
    // Simple HTML table that can be opened as XLS
    $filepath = 'exports/' . $filename . '.xls';

    // Create exports directory if not exists
    if (!is_dir('exports')) {
        mkdir('exports', 0777, true);
    }

    $html = '<table border="1">
        <thead>
            <tr>
                <th>ID Lowongan</th>
                <th>Judul Lowongan</th>
                <th>Kategori</th>
                <th>Jenis Pekerjaan</th>
                <th>Provinsi</th>
                <th>Kota</th>
                <th>Lokasi</th>
                <th>Gaji</th>
                <th>Kualifikasi</th>
                <th>Deskripsi</th>
                <th>Tanggal Posting</th>
                <th>Tanggal Tutup</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($data as $row) {
        $html .= '<tr>
            <td>' . $row['id_lowongan'] . '</td>
            <td>' . htmlspecialchars($row['judul_lowongan']) . '</td>
            <td>' . htmlspecialchars($row['nama_kategori'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['nama_jenis'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['nama_provinsi'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['nama_kota'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['lokasi_lowongan'] ?? '-') . '</td>
            <td>Rp ' . number_format($row['gaji_lowongan'] ?? 0, 0, ',', '.') . '</td>
            <td>' . htmlspecialchars(strip_tags($row['kualifikasi'] ?? '-')) . '</td>
            <td>' . htmlspecialchars(strip_tags($row['deskripsi_lowongan'] ?? '-')) . '</td>
            <td>' . date('d/m/Y', strtotime($row['tanggal_posting'])) . '</td>
            <td>' . ($row['tanggal_tutup'] ? date('d/m/Y', strtotime($row['tanggal_tutup'])) : '-') . '</td>
            <td>' . $row['status'] . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    file_put_contents($filepath, $html);
    return $filepath;
}

function generatePDF($data, $filename)
{
    require_once 'pdf_generator.php';

    // Create exports directory if not exists
    if (!is_dir('exports')) {
        mkdir('exports', 0777, true);
    }

    $filepath = 'exports/' . $filename . '.pdf';

    // Generate PDF content
    $html = generatePDFContent($data);

    // Create PDF file
    createPDFFile($html, $filepath);

    return $filepath;
}
?>