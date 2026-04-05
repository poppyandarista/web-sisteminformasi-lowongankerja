<?php
require_once 'koneksi.php';

$db = new database();

// Get selected columns
$selected_columns = $_POST['columns'] ?? ['id_user', 'nama_user', 'email_user', 'username_user', 'nohp_user', 'jk_user', 'lokasi', 'tanggal_daftar'];

// Build WHERE clause
$where_clause = "WHERE 1=1";
$params = [];
$types = '';

// Filter by ID range
if (!empty($_POST['id_min'])) {
    $where_clause .= " AND u.id_user >= ?";
    $params[] = $_POST['id_min'];
    $types .= 'i';
}

if (!empty($_POST['id_max'])) {
    $where_clause .= " AND u.id_user <= ?";
    $params[] = $_POST['id_max'];
    $types .= 'i';
}

// Filter by nama
if (!empty($_POST['filter_nama'])) {
    $where_clause .= " AND p.nama_user LIKE ?";
    $params[] = '%' . $_POST['filter_nama'] . '%';
    $types .= 's';
}

// Filter by email
if (!empty($_POST['filter_email'])) {
    $where_clause .= " AND u.email_user LIKE ?";
    $params[] = '%' . $_POST['filter_email'] . '%';
    $types .= 's';
}

// Filter by jenis kelamin
if (!empty($_POST['filter_jk'])) {
    $where_clause .= " AND p.jk_user = ?";
    $params[] = $_POST['filter_jk'];
    $types .= 's';
}

// Filter by lokasi
if (!empty($_POST['filter_lokasi'])) {
    $where_clause .= " AND (k.nama_kota LIKE ? OR pr.nama_provinsi LIKE ?)";
    $params[] = '%' . $_POST['filter_lokasi'] . '%';
    $params[] = '%' . $_POST['filter_lokasi'] . '%';
    $types .= 'ss';
}

// Get data with proper JOINs - menggunakan u.* untuk menghindari error kolom tidak ditemukan
$query = "SELECT u.id_user, u.email_user, u.username_user, 
                 p.nama_user, p.nohp_user, p.jk_user, p.tanggallahir_user,
                 CONCAT(COALESCE(k.nama_kota, ''), ', ', COALESCE(pr.nama_provinsi, '')) as lokasi";

// Tambahkan kolom created_at jika ada
$check_created_at = $db->koneksi->query("SHOW COLUMNS FROM user LIKE 'created_at'");
if ($check_created_at && $check_created_at->num_rows > 0) {
    $query .= ", u.created_at as tanggal_daftar";
} else {
    // Jika tidak ada created_at, gunakan timestamp dari tabel lain atau kosong
    $query .= ", '' as tanggal_daftar";
}

$query .= " FROM user u 
          LEFT JOIN profil p ON u.id_user = p.id_user 
          LEFT JOIN provinsi pr ON p.id_provinsi = pr.id_provinsi
          LEFT JOIN kota k ON p.id_kota = k.id_kota
          $where_clause 
          ORDER BY u.id_user";

// Debug: Tampilkan query untuk troubleshooting
// error_log("Query: " . $query);

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

switch ($format) {
    case 'csv':
        exportToCSV($data, $selected_columns);
        break;
    case 'excel':
        exportToExcel($data, $selected_columns);
        break;
    case 'pdf':
        exportToPDF($data, $selected_columns);
        break;
    case 'print':
    case 'preview':
        $column_map = [
            'id_user' => 'ID Pelamar',
            'nama_user' => 'Nama Lengkap',
            'email_user' => 'Email',
            'username_user' => 'Username',
            'nohp_user' => 'No. HP',
            'jk_user' => 'Jenis Kelamin',
            'lokasi' => 'Lokasi',
            'tanggal_daftar' => 'Tanggal Daftar'
        ];
        echo generatePrintHTML($data, $selected_columns, $column_map, $format);
        break;
    default:
        header('Location: datapelamar.php');
        exit();
}

function exportToCSV($data, $selected_columns)
{
    $filename = "data_pelamar_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    // Column mapping
    $column_headers = [
        'id_user' => 'ID Pelamar',
        'nama_user' => 'Nama Lengkap',
        'email_user' => 'Email',
        'username_user' => 'Username',
        'nohp_user' => 'No. HP',
        'jk_user' => 'Jenis Kelamin',
        'lokasi' => 'Lokasi',
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
            if ($col === 'jk_user') {
                $value = $value == 'L' ? 'Laki-laki' : ($value == 'P' ? 'Perempuan' : $value);
            } elseif ($col === 'tanggal_daftar') {
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
    $filename = "data_pelamar_" . date('Y-m-d_H-i-s') . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    // Column mapping
    $column_headers = [
        'id_user' => 'ID Pelamar',
        'nama_user' => 'Nama Lengkap',
        'email_user' => 'Email',
        'username_user' => 'Username',
        'nohp_user' => 'No. HP',
        'jk_user' => 'Jenis Kelamin',
        'lokasi' => 'Lokasi',
        'tanggal_daftar' => 'Tanggal Daftar'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pelamar</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .tanggal-cell { text-align: center; font-size: 9px; }
        .status-cell { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>';

    foreach ($selected_columns as $col) {
        $class = '';
        if ($col === 'id_user')
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
            if ($col === 'id_user')
                $class = ' id-cell';
            elseif ($col === 'tanggal_daftar')
                $class = ' tanggal-cell';

            $value = $row[$col] ?? '';

            // Format specific columns
            if ($col === 'jk_user') {
                $value = $value == 'L' ? 'Laki-laki' : ($value == 'P' ? 'Perempuan' : $value);
            } elseif ($col === 'tanggal_daftar') {
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
    $filename = "data_pelamar_" . date('Y-m-d_H-i-s') . ".pdf";

    // Column mapping
    $column_headers = [
        'id_user' => 'ID Pelamar',
        'nama_user' => 'Nama Lengkap',
        'email_user' => 'Email',
        'username_user' => 'Username',
        'nohp_user' => 'No. HP',
        'jk_user' => 'Jenis Kelamin',
        'lokasi' => 'Lokasi',
        'tanggal_daftar' => 'Tanggal Daftar'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pelamar</title>
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
        .status-cell { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <h1>LAPORAN DATA PELAMAR</h1>
    
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
        if ($col === 'id_user')
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
            if ($col === 'id_user')
                $class = ' id-cell';
            elseif ($col === 'tanggal_daftar')
                $class = ' tanggal-cell';

            $value = $row[$col] ?? '';

            // Format specific columns
            if ($col === 'jk_user') {
                $value = $value == 'L' ? 'Laki-laki' : ($value == 'P' ? 'Perempuan' : $value);
            } elseif ($col === 'tanggal_daftar') {
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
        'id_user' => 'ID Pelamar',
        'nama_user' => 'Nama Lengkap',
        'email_user' => 'Email',
        'username_user' => 'Username',
        'nohp_user' => 'No. HP',
        'jk_user' => 'Jenis Kelamin',
        'lokasi' => 'Lokasi',
        'tanggal_daftar' => 'Tanggal Daftar'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . ($is_preview ? 'Preview' : 'Print') . ' Data Pelamar</title>
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
        .status-cell { text-align: center; font-weight: bold; }
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
    <h1>LAPORAN DATA PELAMAR</h1>
    
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
        if ($col === 'id_user')
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
            if ($col === 'id_user')
                $class = ' id-cell';
            elseif ($col === 'tanggal_daftar')
                $class = ' tanggal-cell';

            $value = $row[$col] ?? '';

            // Format specific columns
            if ($col === 'jk_user') {
                $value = $value == 'L' ? 'Laki-laki' : ($value == 'P' ? 'Perempuan' : $value);
            } elseif ($col === 'tanggal_daftar') {
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