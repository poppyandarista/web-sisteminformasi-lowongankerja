<?php
require_once 'koneksi.php';

$db = new database();

// Get selected columns
$selected_columns = $_POST['columns'] ?? ['id_perusahaan', 'nama_perusahaan', 'email_perusahaan', 'nohp_perusahaan', 'lokasi', 'alamat_perusahaan', 'tanggal_daftar'];

// Build WHERE clause
$where_clause = "WHERE 1=1";
$params = [];
$types = '';

// Filter by ID range
if (!empty($_POST['id_min'])) {
    $where_clause .= " AND p.id_perusahaan >= ?";
    $params[] = $_POST['id_min'];
    $types .= 'i';
}

if (!empty($_POST['id_max'])) {
    $where_clause .= " AND p.id_perusahaan <= ?";
    $params[] = $_POST['id_max'];
    $types .= 'i';
}

// Filter by nama perusahaan
if (!empty($_POST['filter_nama'])) {
    $where_clause .= " AND p.nama_perusahaan LIKE ?";
    $params[] = '%' . $_POST['filter_nama'] . '%';
    $types .= 's';
}

// Filter by email
if (!empty($_POST['filter_email'])) {
    $where_clause .= " AND p.email_perusahaan LIKE ?";
    $params[] = '%' . $_POST['filter_email'] . '%';
    $types .= 's';
}

// Filter by lokasi
if (!empty($_POST['filter_lokasi'])) {
    $where_clause .= " AND (k.nama_kota LIKE ? OR pr.nama_provinsi LIKE ?)";
    $params[] = '%' . $_POST['filter_lokasi'] . '%';
    $params[] = '%' . $_POST['filter_lokasi'] . '%';
    $types .= 'ss';
}

// Filter by nohp
if (!empty($_POST['filter_nohp'])) {
    $where_clause .= " AND p.nohp_perusahaan LIKE ?";
    $params[] = '%' . $_POST['filter_nohp'] . '%';
    $types .= 's';
}

// Get data with proper JOINs - menggunakan query yang aman
$query = "SELECT p.id_perusahaan, p.nama_perusahaan, p.email_perusahaan, p.nohp_perusahaan, 
                 p.alamat_perusahaan, p.deskripsi_perusahaan, p.logo_perusahaan,
                 p.id_provinsi, p.id_kota, pr.nama_provinsi, k.nama_kota";

// Tambahkan kolom created_at jika ada
$check_created_at = $db->koneksi->query("SHOW COLUMNS FROM perusahaan LIKE 'created_at'");
if ($check_created_at && $check_created_at->num_rows > 0) {
    $query .= ", p.created_at as tanggal_daftar";
} else {
    // Jika tidak ada created_at, gunakan string kosong
    $query .= ", '' as tanggal_daftar";
}

$query .= " FROM perusahaan p 
          LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
          LEFT JOIN kota k ON p.id_kota = k.id_kota
          $where_clause 
          ORDER BY p.id_perusahaan";

if (!empty($params)) {
    $result = $db->koneksi->prepare($query);
    if ($result === false) {
        die("Error preparing query: " . $db->koneksi->error);
    }
    $result->bind_param($types, ...$params);
    $result->execute();
    $data = $result->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $result = $db->koneksi->query($query);
    if ($result === false) {
        die("Error executing query: " . $db->koneksi->error);
    }
    $data = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle different export formats
$format = $_POST['format'] ?? 'csv';

// Process data to add lokasi field
$processed_data = [];
foreach ($data as $row) {
    $row['lokasi'] = trim($row['nama_kota'] . ', ' . $row['nama_provinsi'], ', ');
    $processed_data[] = $row;
}

switch ($format) {
    case 'csv':
        exportToCSV($processed_data, $selected_columns);
        break;
    case 'excel':
        exportToExcel($processed_data, $selected_columns);
        break;
    case 'pdf':
        exportToPDF($processed_data, $selected_columns);
        break;
    case 'print':
    case 'preview':
        $column_map = [
            'id_perusahaan' => 'ID Perusahaan',
            'nama_perusahaan' => 'Nama Perusahaan',
            'email_perusahaan' => 'Email',
            'nohp_perusahaan' => 'No. HP',
            'lokasi' => 'Lokasi',
            'alamat_perusahaan' => 'Alamat',
            'tanggal_daftar' => 'Tanggal Daftar'
        ];
        echo generatePrintHTML($processed_data, $selected_columns, $column_map, $format);
        break;
    default:
        header('Location: dataperusahaan.php');
        exit();
}

function exportToCSV($data, $selected_columns)
{
    $filename = "data_perusahaan_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    // Column mapping
    $column_headers = [
        'id_perusahaan' => 'ID Perusahaan',
        'nama_perusahaan' => 'Nama Perusahaan',
        'email_perusahaan' => 'Email',
        'nohp_perusahaan' => 'No. HP',
        'lokasi' => 'Lokasi',
        'alamat_perusahaan' => 'Alamat',
        'tanggal_daftar' => 'Tanggal Daftar'
    ];

    // Output CSV
    $output = fopen('php://output', 'w');

    // Add BOM for UTF-8
    fwrite($output, "\xEF\xBB\xBF");

    // Header row
    $headers = [];
    foreach ($selected_columns as $col) {
        $headers[] = $column_headers[$col] ?? $col;
    }
    fputcsv($output, $headers);

    // Data rows
    foreach ($data as $row) {
        $csv_row = [];
        foreach ($selected_columns as $col) {
            $value = $row[$col] ?? '';

            // Format specific columns
            if ($col === 'tanggal_daftar') {
                $value = date('d/m/Y H:i:s', strtotime($value));
            }

            $csv_row[] = $value;
        }
        fputcsv($output, $csv_row);
    }

    fclose($output);
    exit();
}

function exportToExcel($data, $selected_columns)
{
    $filename = "data_perusahaan_" . date('Y-m-d_H-i-s') . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    // Column mapping
    $column_headers = [
        'id_perusahaan' => 'ID Perusahaan',
        'nama_perusahaan' => 'Nama Perusahaan',
        'email_perusahaan' => 'Email',
        'nohp_perusahaan' => 'No. HP',
        'lokasi' => 'Lokasi',
        'alamat_perusahaan' => 'Alamat',
        'tanggal_daftar' => 'Tanggal Daftar'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Perusahaan</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .tanggal-cell { text-align: center; font-size: 9px; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>';

    foreach ($selected_columns as $col) {
        $class = '';
        if ($col === 'id_perusahaan')
            $class = ' id-cell';
        elseif ($col === 'tanggal_daftar')
            $class = ' tanggal-cell';

        $header_text = htmlspecialchars($column_headers[$col] ?? $col);
        $html .= '<th class="' . $class . '">' . $header_text . '</th>';
    }

    $html .= '
            </tr>
        </thead>
        <tbody>';

    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($selected_columns as $col) {
            $class = '';
            if ($col === 'id_perusahaan')
                $class = ' id-cell';
            elseif ($col === 'tanggal_daftar')
                $class = ' tanggal-cell';

            $value = $row[$col] ?? '';

            // Format specific columns
            if ($col === 'tanggal_daftar') {
                $value = date('d/m/Y H:i:s', strtotime($value));
            }

            $html .= '<td class="' . $class . '">' . htmlspecialchars($value) . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '
        </tbody>
    </table>
</body>
</html>';

    echo $html;
    exit();
}

function exportToPDF($data, $selected_columns)
{
    $filename = "data_perusahaan_" . date('Y-m-d_H-i-s') . ".pdf";

    // Column mapping
    $column_headers = [
        'id_perusahaan' => 'ID Perusahaan',
        'nama_perusahaan' => 'Nama Perusahaan',
        'email_perusahaan' => 'Email',
        'nohp_perusahaan' => 'No. HP',
        'lokasi' => 'Lokasi',
        'alamat_perusahaan' => 'Alamat',
        'tanggal_daftar' => 'Tanggal Daftar'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Perusahaan</title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body { font-family: Arial; margin: 20px; font-size: 12px; }
        h1 { color: #1e40af; text-align: center; margin-bottom: 20px; }
        .info { background: #f8fafc; padding: 10px; margin-bottom: 15px; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .tanggal-cell { text-align: center; font-size: 9px; }
    </style>
</head>
<body>
    <h1>LAPORAN DATA PERUSAHAAN</h1>
    
    <div class="info">
        <div>Tanggal Export: ' . date('d/m/Y H:i:s') . '</div>
        <div>Total Data: ' . count($data) . ' records</div>
        <div>Kolom: ' . implode(', ', array_map(function ($col) use ($column_headers) {
        return $column_headers[$col] ?? $col;
    }, $selected_columns)) . '</div>
    </div>
    
    <table>
        <thead>
            <tr>';

    foreach ($selected_columns as $col) {
        $class = '';
        if ($col === 'id_perusahaan')
            $class = ' id-cell';
        elseif ($col === 'tanggal_daftar')
            $class = ' tanggal-cell';

        $header_text = htmlspecialchars($column_headers[$col] ?? $col);
        $html .= '<th class="' . $class . '">' . $header_text . '</th>';
    }

    $html .= '
            </tr>
        </thead>
        <tbody>';

    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($selected_columns as $col) {
            $class = '';
            if ($col === 'id_perusahaan')
                $class = ' id-cell';
            elseif ($col === 'tanggal_daftar')
                $class = ' tanggal-cell';

            $value = $row[$col] ?? '';

            // Format specific columns
            if ($col === 'tanggal_daftar') {
                $value = date('d/m/Y H:i:s', strtotime($value));
            }

            $html .= '<td class="' . $class . '">' . htmlspecialchars($value) . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '
        </tbody>
    </table>
    
    <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #666;">
        Generated by Admin Panel - ' . date('Y') . '
    </div>
</body>
</html>';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    header('Pragma: public');
    header('Content-Length: ' . strlen($html));

    echo $html;
    exit();
}

// Fungsi untuk generate HTML print/preview
function generatePrintHTML($data, $selected_columns, $column_map, $format)
{
    $is_preview = $format === 'preview';

    // Column mapping
    $column_headers = [
        'id_perusahaan' => 'ID Perusahaan',
        'nama_perusahaan' => 'Nama Perusahaan',
        'email_perusahaan' => 'Email',
        'nohp_perusahaan' => 'No. HP',
        'lokasi' => 'Lokasi',
        'alamat_perusahaan' => 'Alamat',
        'tanggal_daftar' => 'Tanggal Daftar'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . ($is_preview ? 'Preview' : 'Print') . ' Data Perusahaan</title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body { font-family: Arial; margin: 20px; font-size: 12px; }
        h1 { color: #1e40af; text-align: center; margin-bottom: 20px; }
        .info { background: #f8fafc; padding: 10px; margin-bottom: 15px; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .tanggal-cell { text-align: center; font-size: 9px; }
        .action-buttons { text-align: center; margin: 20px 0; }
        .btn { padding: 10px 20px; margin: 0 10px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; border-radius: 4px; transition: all 0.3s ease; }
        .btn-print { background: #3b82f6; color: white; }
        .btn-print:hover { background: #2563eb; }
        .btn-close { background: #6b7280; color: white; }
        .btn-close:hover { background: #4b5563; }
        @media print { .action-buttons { display: none; } }
    </style>
</head>
<body>
    <h1>LAPORAN DATA PERUSAHAAN</h1>
    
    <div class="info">
        <div>Tanggal Export: ' . date('d/m/Y H:i:s') . '</div>
        <div>Total Data: ' . count($data) . ' records</div>
        <div>Kolom: ' . implode(', ', array_map(function ($col) use ($column_headers) {
        return $column_headers[$col] ?? $col;
    }, $selected_columns)) . '</div>
    </div>';

    $html .= '
    <table>
        <thead>
            <tr>';

    foreach ($selected_columns as $col) {
        $class = '';
        if ($col === 'id_perusahaan')
            $class = ' id-cell';
        elseif ($col === 'tanggal_daftar')
            $class = ' tanggal-cell';

        $header_text = htmlspecialchars($column_headers[$col] ?? $col);
        $html .= '<th class="' . $class . '">' . $header_text . '</th>';
    }

    $html .= '
            </tr>
        </thead>
        <tbody>';

    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($selected_columns as $col) {
            $class = '';
            if ($col === 'id_perusahaan')
                $class = ' id-cell';
            elseif ($col === 'tanggal_daftar')
                $class = ' tanggal-cell';

            $value = $row[$col] ?? '';

            // Format specific columns
            if ($col === 'tanggal_daftar') {
                $value = date('d/m/Y H:i:s', strtotime($value));
            }

            $html .= '<td class="' . $class . '">' . htmlspecialchars($value) . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</tbody>
    </table>';

    if ($is_preview) {
        $html .= '
    <div class="action-buttons">
        <button class="btn btn-print" onclick="window.print()">Print</button>
        <button class="btn btn-close" onclick="window.close()">Tutup</button>
    </div>';
    }

    $html .= '
    <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #666;">
        Generated by Admin Panel - ' . date('Y') . '
    </div>
</body>
</html>';

    return $html;
}
?>