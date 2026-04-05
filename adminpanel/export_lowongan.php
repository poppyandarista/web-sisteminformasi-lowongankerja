<?php
require_once 'koneksi.php';

$db = new database();

// Handle print/preview requests first
if (isset($_POST['format']) && in_array($_POST['format'], ['print', 'preview'])) {
    // Get selected columns
    $selected_columns = $_POST['columns'] ?? ['id_lowongan', 'nama_perusahaan', 'judul_lowongan', 'kategori_lowongan', 'waktukerja', 'lokasi_lowongan', 'gaji_lowongan', 'tanggal_tutup', 'status'];

    // Build WHERE clause for filters
    $where_conditions = [];
    $params = [];
    $types = '';

    // Filter by ID Range
    if (!empty($_POST['id_min'])) {
        $where_conditions[] = "l.id_lowongan >= ?";
        $params[] = max(0, intval($_POST['id_min']));
        $types .= 'i';
    }

    if (!empty($_POST['id_max'])) {
        $where_conditions[] = "l.id_lowongan <= ?";
        $params[] = max(0, intval($_POST['id_max']));
        $types .= 'i';
    }

    // Filter by Judul Lowongan
    if (!empty($_POST['filter_judul'])) {
        $where_conditions[] = "l.judul_lowongan LIKE ?";
        $params[] = '%' . $_POST['filter_judul'] . '%';
        $types .= 's';
    }

    // Filter by Perusahaan
    if (!empty($_POST['filter_perusahaan'])) {
        $where_conditions[] = "p.nama_perusahaan LIKE ?";
        $params[] = '%' . $_POST['filter_perusahaan'] . '%';
        $types .= 's';
    }

    // Filter by Kategori
    if (!empty($_POST['filter_kategori'])) {
        $where_conditions[] = "l.kategori_lowongan = ?";
        $params[] = $_POST['filter_kategori'];  // <- SEKARANG INI ID (integer)
        $types .= 'i';  // <- UBAH DARI 's' MENJADI 'i'
    }

    // Filter by Status
    if (!empty($_POST['filter_status'])) {
        $where_conditions[] = "l.status = ?";
        $params[] = $_POST['filter_status'];
        $types .= 's';
    }

    // Filter by Date Range
    if (!empty($_POST['tanggal_dari'])) {
        $where_conditions[] = "DATE(l.created_at) >= ?";
        $params[] = $_POST['tanggal_dari'];
        $types .= 's';
    }

    if (!empty($_POST['tanggal_sampai'])) {
        $where_conditions[] = "DATE(l.created_at) <= ?";
        $params[] = $_POST['tanggal_sampai'];
        $types .= 's';
    }

    // Build the complete query
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Map column names to display names
    $column_map = [
        'id_lowongan' => 'ID Lowongan',
        'nama_perusahaan' => 'Perusahaan',
        'judul_lowongan' => 'Judul Lowongan',
        'nama_kategori' => 'Kategori', // <- UBAH JADI nama_kategori
        'waktukerja' => 'Waktu Kerja',
        'lokasi_lowongan' => 'Lokasi',
        'gaji_lowongan' => 'Gaji',
        'tanggal_tutup' => 'Tanggal Tutup',
        'status' => 'Status'
    ];

    $select_columns = [];
    foreach ($selected_columns as $column) {
        if (isset($column_map[$column])) {
            if ($column === 'nama_perusahaan') {
                $select_columns[] = 'p.nama_perusahaan';
            } elseif ($column === 'nama_kategori') {
                $select_columns[] = 'k.nama_kategori'; // <- TAMBAHKAN INI
            } else {
                $select_columns[] = 'l.' . $column;
            }
        }
    }

    if (empty($select_columns)) {
        $select_columns = ['l.id_lowongan', 'p.nama_perusahaan', 'l.judul_lowongan', 'l.kategori_lowongan', 'l.waktukerja', 'l.lokasi_lowongan', 'l.gaji_lowongan', 'l.tanggal_tutup', 'l.status'];
    }

    // Ubah menjadi:
    $query = "SELECT " . implode(', ', $select_columns) . " 
          FROM lowongan l 
          LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan
          LEFT JOIN kategori k ON l.kategori_lowongan = k.id_kategori
          " . $where_clause . " 
          ORDER BY l.id_lowongan ASC";

    // Execute query
    if (!empty($params)) {
        $result = $db->koneksi->prepare($query);
        $result->bind_param($types, ...$params);
        $result->execute();
        $data = $result->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $data = $db->koneksi->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    // Generate HTML for print/preview
    $html = generatePrintHTML($data, $selected_columns, $column_map, $_POST['format']);

    echo $html;
    exit();
}

// Get selected columns
$selected_columns = $_POST['columns'] ?? ['id_lowongan', 'nama_perusahaan', 'judul_lowongan', 'kategori_lowongan', 'waktukerja', 'lokasi_lowongan', 'gaji_lowongan', 'tanggal_tutup', 'status'];

// Build WHERE clause for filters
$where_conditions = [];
$params = [];
$types = '';

// Filter by ID Range
if (!empty($_POST['id_min'])) {
    $where_conditions[] = "l.id_lowongan >= ?";
    $params[] = max(0, intval($_POST['id_min']));
    $types .= 'i';
}

if (!empty($_POST['id_max'])) {
    $where_conditions[] = "l.id_lowongan <= ?";
    $params[] = max(0, intval($_POST['id_max']));
    $types .= 'i';
}

// Filter by Judul Lowongan
if (!empty($_POST['filter_judul'])) {
    $where_conditions[] = "l.judul_lowongan LIKE ?";
    $params[] = '%' . $_POST['filter_judul'] . '%';
    $types .= 's';
}

// Filter by Perusahaan
if (!empty($_POST['filter_perusahaan'])) {
    $where_conditions[] = "p.nama_perusahaan LIKE ?";
    $params[] = '%' . $_POST['filter_perusahaan'] . '%';
    $types .= 's';
}

// Filter by Kategori
if (!empty($_POST['filter_kategori'])) {
    $where_conditions[] = "l.kategori_lowongan = ?";
    $params[] = $_POST['filter_kategori'];
    $types .= 's';
}

// Filter by Status
if (!empty($_POST['filter_status'])) {
    $where_conditions[] = "l.status = ?";
    $params[] = $_POST['filter_status'];
    $types .= 's';
}

// Filter by Date Range
if (!empty($_POST['tanggal_dari'])) {
    $where_conditions[] = "DATE(l.created_at) >= ?";
    $params[] = $_POST['tanggal_dari'];
    $types .= 's';
}

if (!empty($_POST['tanggal_sampai'])) {
    $where_conditions[] = "DATE(l.created_at) <= ?";
    $params[] = $_POST['tanggal_sampai'];
    $types .= 's';
}

// Build column names for SELECT
$column_select = [];
foreach ($selected_columns as $column) {
    if ($column === 'nama_perusahaan') {
        $column_select[] = 'p.nama_perusahaan';
    } elseif ($column === 'kategori_lowongan') {
        // Jangan ambil ID kategori, tapi ambil nama_kategori
        $column_select[] = 'k.nama_kategori AS kategori_lowongan';
    } else {
        $column_select[] = 'l.' . $column;
    }
}

// Build final query with proper JOINs
$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get data dengan JOIN ke tabel kategori
$query = "SELECT " . implode(', ', $column_select) . " 
          FROM lowongan l 
          LEFT JOIN perusahaan p ON l.id_perusahaan = p.id_perusahaan
          LEFT JOIN kategori k ON l.kategori_lowongan = k.id_kategori
          $where_clause 
          ORDER BY l.id_lowongan";

if (!empty($params)) {
    $result = $db->koneksi->prepare($query);
    $result->bind_param($types, ...$params);
    $result->execute();
    $data = $result->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $data = $db->koneksi->query($query)->fetch_all(MYSQLI_ASSOC);
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
    default:
        header('Location: datalowongan.php');
        exit();
}

function exportToCSV($data, $selected_columns)
{
    $filename = "data_lowongan_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    $column_headers = [
        'id_lowongan' => 'ID Lowongan',
        'nama_perusahaan' => 'Perusahaan',
        'judul_lowongan' => 'Judul Lowongan',
        'kategori_lowongan' => 'Kategori',  // <- TETAP PAKAI 'kategori_lowongan' untuk key
        'waktukerja' => 'Waktu Kerja',
        'lokasi_lowongan' => 'Lokasi',
        'gaji_lowongan' => 'Gaji',
        'tanggal_tutup' => 'Tanggal Tutup',
        'status' => 'Status'
    ];
    // Output CSV
    $output = fopen('php://output', 'w');

    // Add BOM for UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Header
    $headers = [];
    foreach ($selected_columns as $col) {
        $headers[] = $column_headers[$col] ?? $col;
    }
    fputcsv($output, $headers);

    // Data
    foreach ($data as $row) {
        $csv_row = [];
        foreach ($selected_columns as $col) {
            $value = $row[$col] ?? '';
            if ($col === 'gaji_lowongan') {
                $value = $value ? 'Rp ' . number_format($value, 0, ',', '.') : '-';
            } elseif ($col === 'tanggal_tutup') {
                $value = $value ? date('d/m/Y', strtotime($value)) : '-';
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
    $filename = "data_lowongan_" . date('Y-m-d_H-i-s') . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    $column_headers = [
        'id_lowongan' => 'ID Lowongan',
        'nama_perusahaan' => 'Perusahaan',
        'judul_lowongan' => 'Judul Lowongan',
        'kategori_lowongan' => 'Kategori',  // <- TETAP PAKAI 'kategori_lowongan' untuk key
        'waktukerja' => 'Waktu Kerja',
        'lokasi_lowongan' => 'Lokasi',
        'gaji_lowongan' => 'Gaji',
        'tanggal_tutup' => 'Tanggal Tutup',
        'status' => 'Status'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Lowongan</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #4CAF50; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .gaji-cell { text-align: right; }
        .tanggal-cell { text-align: center; }
        .status-cell { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>';

    foreach ($selected_columns as $col) {
        $class = '';
        if ($col === 'id_lowongan')
            $class = ' id-cell';
        elseif ($col === 'gaji_lowongan')
            $class = ' gaji-cell';
        elseif ($col === 'tanggal_tutup')
            $class = ' tanggal-cell';
        elseif ($col === 'status')
            $class = ' status-cell';

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
            $value = '';

            if ($col === 'id_lowongan') {
                $class = ' id-cell';
                $value = 'L' . sprintf("%04d", $row[$col] ?? 0);
            } elseif ($col === 'gaji_lowongan') {
                $class = ' gaji-cell';
                $value = $row[$col] ? 'Rp ' . number_format($row[$col], 0, ',', '.') : '-';
            } elseif ($col === 'tanggal_tutup') {
                $class = ' tanggal-cell';
                $value = $row[$col] ? date('d/m/Y', strtotime($row[$col])) : '-';
            } elseif ($col === 'status') {
                $class = ' status-cell';
                $value = htmlspecialchars($row[$col] ?? '');
            } else {
                $value = htmlspecialchars($row[$col] ?? '');
            }

            $html .= '<td class="' . $class . '">' . $value . '</td>';
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
    $filename = "data_lowongan_" . date('Y-m-d_H-i-s') . ".pdf";

    $column_headers = [
        'id_lowongan' => 'ID Lowongan',
        'nama_perusahaan' => 'Perusahaan',
        'judul_lowongan' => 'Judul Lowongan',
        'kategori_lowongan' => 'Kategori',  // <- TETAP PAKAI 'kategori_lowongan' untuk key
        'waktukerja' => 'Waktu Kerja',
        'lokasi_lowongan' => 'Lokasi',
        'gaji_lowongan' => 'Gaji',
        'tanggal_tutup' => 'Tanggal Tutup',
        'status' => 'Status'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Lowongan</title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body { font-family: Arial; margin: 20px; font-size: 12px; }
        h1 { color: #1e40af; text-align: center; margin-bottom: 20px; }
        .info { background: #f8fafc; padding: 10px; margin-bottom: 15px; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .gaji-cell { text-align: right; font-size: 9px; }
        .tanggal-cell { text-align: center; font-size: 9px; }
        .status-cell { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <h1>LAPORAN DATA LOWONGAN</h1>
    
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
        if ($col === 'id_lowongan')
            $class = ' id-cell';
        elseif ($col === 'gaji_lowongan')
            $class = ' gaji-cell';
        elseif ($col === 'tanggal_tutup')
            $class = ' tanggal-cell';
        elseif ($col === 'status')
            $class = ' status-cell';

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
            $value = '';

            if ($col === 'id_lowongan') {
                $class = ' id-cell';
                $value = 'L' . sprintf("%04d", $row[$col] ?? 0);
            } elseif ($col === 'gaji_lowongan') {
                $class = ' gaji-cell';
                $value = $row[$col] ? 'Rp ' . number_format($row[$col], 0, ',', '.') : '-';
            } elseif ($col === 'tanggal_tutup') {
                $class = ' tanggal-cell';
                $value = $row[$col] ? date('d/m/Y', strtotime($row[$col])) : '-';
            } elseif ($col === 'status') {
                $class = ' status-cell';
                $value = htmlspecialchars($row[$col] ?? '');
            } else {
                $value = htmlspecialchars($row[$col] ?? '');
            }

            $html .= '<td class="' . $class . '">' . $value . '</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</tbody>
    </table>
    
    <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #666;">
        Generated by Admin Panel - ' . date('Y') . '
    </div>
</body>
</html>';

    // Set headers for PDF download
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

    $column_headers = [
        'id_lowongan' => 'ID Lowongan',
        'nama_perusahaan' => 'Perusahaan',
        'judul_lowongan' => 'Judul Lowongan',
        'kategori_lowongan' => 'Kategori',  // <- TETAP PAKAI 'kategori_lowongan' untuk key
        'waktukerja' => 'Waktu Kerja',
        'lokasi_lowongan' => 'Lokasi',
        'gaji_lowongan' => 'Gaji',
        'tanggal_tutup' => 'Tanggal Tutup',
        'status' => 'Status'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . ($is_preview ? 'Preview' : 'Print') . ' Data Lowongan</title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body { font-family: Arial; margin: 20px; font-size: 12px; }
        h1 { color: #1e40af; text-align: center; margin-bottom: 20px; }
        .info { background: #f8fafc; padding: 10px; margin-bottom: 15px; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .gaji-cell { text-align: right; font-size: 9px; }
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
    <h1>LAPORAN DATA LOWONGAN</h1>
    
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
        if ($col === 'id_lowongan')
            $class = ' id-cell';
        elseif ($col === 'gaji_lowongan')
            $class = ' gaji-cell';
        elseif ($col === 'tanggal_tutup')
            $class = ' tanggal-cell';
        elseif ($col === 'status')
            $class = ' status-cell';

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
            $value = '';

            if ($col === 'id_lowongan') {
                $class = ' id-cell';
                $value = 'L' . sprintf("%04d", $row[$col] ?? 0);
            } elseif ($col === 'gaji_lowongan') {
                $class = ' gaji-cell';
                $value = $row[$col] ? 'Rp ' . number_format($row[$col], 0, ',', '.') : '-';
            } elseif ($col === 'tanggal_tutup') {
                $class = ' tanggal-cell';
                $value = $row[$col] ? date('d/m/Y', strtotime($row[$col])) : '-';
            } elseif ($col === 'status') {
                $class = ' status-cell';
                $value = htmlspecialchars($row[$col] ?? '');
            } else {
                $value = htmlspecialchars($row[$col] ?? '');
            }

            $html .= '<td class="' . $class . '">' . $value . '</td>';
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