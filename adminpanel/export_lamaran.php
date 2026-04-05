<?php
session_start();
include 'koneksi.php';

$db = new database();

// Cek apakah user sudah login
if (!isset($_SESSION['email_admin'])) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'export') {
    $format = $_POST['format'] ?? 'csv';

    // Handle print and preview formats
    if ($format === 'print' || $format === 'preview') {
        // Get selected columns
        $selected_columns = $_POST['columns'] ?? ['id_lamaran', 'nama_user', 'email_user', 'nama_perusahaan', 'judul_lowongan', 'tanggal_lamar', 'status_lamaran', 'catatan_hrd'];

        // Build WHERE clause for filters
        $where_conditions = [];
        $params = [];
        $types = '';

        // Filter by ID Range
        if (!empty($_POST['id_min'])) {
            $where_conditions[] = "l.id_lamaran >= ?";
            $params[] = max(0, intval($_POST['id_min']));
            $types .= 'i';
        }

        if (!empty($_POST['id_max'])) {
            $where_conditions[] = "l.id_lamaran <= ?";
            $params[] = max(0, intval($_POST['id_max']));
            $types .= 'i';
        }

        // Filter by Status
        if (!empty($_POST['filter_status'])) {
            $where_conditions[] = "l.status_lamaran = ?";
            $params[] = $_POST['filter_status'];
            $types .= 's';
        }

        // Filter by Date Range
        if (!empty($_POST['tanggal_dari'])) {
            $where_conditions[] = "DATE(l.tanggal_lamar) >= ?";
            $params[] = $_POST['tanggal_dari'];
            $types .= 's';
        }

        if (!empty($_POST['tanggal_sampai'])) {
            $where_conditions[] = "DATE(l.tanggal_lamar) <= ?";
            $params[] = $_POST['tanggal_sampai'];
            $types .= 's';
        }

        // Build the complete query
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

        // Map column names to display names
        $column_map = [
            'id_lamaran' => 'ID Lamaran',
            'nama_user' => 'Nama Pelamar',
            'email_user' => 'Email Pelamar',
            'nama_perusahaan' => 'Perusahaan',
            'judul_lowongan' => 'Lowongan',
            'tanggal_lamar' => 'Tanggal Lamar',
            'status_lamaran' => 'Status Lamaran',
            'catatan_hrd' => 'Catatan HRD'
        ];

        // Build SELECT clause
        $select_columns = [];
        foreach ($selected_columns as $column) {
            if (isset($column_map[$column])) {
                if ($column === 'nama_user') {
                    $select_columns[] = 'p.nama_user';
                } elseif ($column === 'email_user') {
                    $select_columns[] = 'u.email_user';
                } elseif ($column === 'nama_perusahaan') {
                    $select_columns[] = 'per.nama_perusahaan';
                } elseif ($column === 'judul_lowongan') {
                    $select_columns[] = 'low.judul_lowongan';
                } else {
                    $select_columns[] = 'l.' . $column;
                }
            }
        }

        if (empty($select_columns)) {
            $select_columns = ['l.id_lamaran', 'p.nama_user', 'u.email_user', 'per.nama_perusahaan', 'low.judul_lowongan', 'l.tanggal_lamar', 'l.status_lamaran', 'l.catatan_hrd'];
        }

        // Build query with proper JOINs (sesuai dengan tampil_data_lamaran)
        $query = "SELECT " . implode(', ', $select_columns) . " 
                 FROM lamaran l 
                 LEFT JOIN lowongan low ON l.id_lowongan = low.id_lowongan
                 LEFT JOIN perusahaan per ON low.id_perusahaan = per.id_perusahaan
                 LEFT JOIN user u ON l.id_user = u.id_user
                 LEFT JOIN profil p ON l.id_user = p.id_user
                 " . $where_clause . " 
                 ORDER BY l.id_lamaran ASC";

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
        $html = generatePrintHTML($data, $selected_columns, $column_map, $format);

        echo $html;
        exit();
    }

    // Get selected columns
    $selected_columns = $_POST['columns'] ?? ['id_lamaran', 'nama_user', 'email_user', 'nama_perusahaan', 'judul_lowongan', 'tanggal_lamar', 'status_lamaran', 'catatan_hrd'];

    // Build WHERE clause for filters
    $where_conditions = [];
    $params = [];
    $types = '';

    // Filter by ID Range
    if (!empty($_POST['id_min'])) {
        $where_conditions[] = "l.id_lamaran >= ?";
        $params[] = max(0, intval($_POST['id_min']));
        $types .= 'i';
    }

    if (!empty($_POST['id_max'])) {
        $where_conditions[] = "l.id_lamaran <= ?";
        $params[] = max(0, intval($_POST['id_max']));
        $types .= 'i';
    }

    // Filter by Nama Pelamar
    if (!empty($_POST['filter_nama_pelamar'])) {
        $where_conditions[] = "p.nama_user LIKE ?";
        $params[] = '%' . $_POST['filter_nama_pelamar'] . '%';
        $types .= 's';
    }

    // Filter by Perusahaan
    if (!empty($_POST['filter_perusahaan'])) {
        $where_conditions[] = "per.nama_perusahaan LIKE ?";
        $params[] = '%' . $_POST['filter_perusahaan'] . '%';
        $types .= 's';
    }

    // Filter by Status
    if (!empty($_POST['filter_status'])) {
        $where_conditions[] = "l.status_lamaran = ?";
        $params[] = $_POST['filter_status'];
        $types .= 's';
    }

    // Filter by Lowongan
    if (!empty($_POST['filter_lowongan'])) {
        $where_conditions[] = "low.judul_lowongan LIKE ?";
        $params[] = '%' . $_POST['filter_lowongan'] . '%';
        $types .= 's';
    }

    // Filter by Lowongan
    if (!empty($_POST['filter_lowongan'])) {
        $where_conditions[] = "low.judul_lowongan LIKE ?";
        $params[] = '%' . $_POST['filter_lowongan'] . '%';
        $types .= 's';
    }

    // Filter by Date Range
    if (!empty($_POST['tanggal_dari'])) {
        $where_conditions[] = "DATE(l.tanggal_lamar) >= ?";
        $params[] = $_POST['tanggal_dari'];
        $types .= 's';
    }

    if (!empty($_POST['tanggal_sampai'])) {
        $where_conditions[] = "DATE(l.tanggal_lamar) <= ?";
        $params[] = $_POST['tanggal_sampai'];
        $types .= 's';
    }

    // Build column names for SELECT
    $column_select = [];
    foreach ($selected_columns as $column) {
        if ($column === 'nama_user') {
            $column_select[] = 'p.nama_user';
        } elseif ($column === 'email_user') {
            $column_select[] = 'u.email_user';
        } elseif ($column === 'nama_perusahaan') {
            $column_select[] = 'per.nama_perusahaan';
        } elseif ($column === 'judul_lowongan') {
            $column_select[] = 'low.judul_lowongan';
        } else {
            $column_select[] = 'l.' . $column;
        }
    }

    // Build final query with proper JOINs
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Get data with proper JOINs (sesuai dengan tampil_data_lamaran)
    $query = "SELECT " . implode(', ', $column_select) . " 
              FROM lamaran l 
              LEFT JOIN lowongan low ON l.id_lowongan = low.id_lowongan
              LEFT JOIN perusahaan per ON low.id_perusahaan = per.id_perusahaan
              LEFT JOIN user u ON l.id_user = u.id_user
              LEFT JOIN profil p ON l.id_user = p.id_user
              $where_clause 
              ORDER BY l.id_lamaran";

    if (!empty($params)) {
        $result = $db->koneksi->prepare($query);
        $result->bind_param($types, ...$params);
        $result->execute();
        $data = $result->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $data = $db->koneksi->query($query)->fetch_all(MYSQLI_ASSOC);
    }

    // Export based on format
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
            header("Location: datalamaran.php");
            break;
    }
}

function exportToCSV($data, $selected_columns)
{
    $filename = "data_lamaran_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Add BOM for UTF-8
    fwrite($output, "\xEF\xBB\xBF");

    // Column mapping
    $column_headers = [
        'id_lamaran' => 'ID Lamaran',
        'nama_user' => 'Nama Pelamar',
        'email_user' => 'Email Pelamar',
        'nama_perusahaan' => 'Perusahaan',
        'judul_lowongan' => 'Lowongan',
        'tanggal_lamar' => 'Tanggal Lamar',
        'status_lamaran' => 'Status Lamaran',
        'catatan_hrd' => 'Catatan HRD'
    ];

    // Write headers
    $headers = [];
    foreach ($selected_columns as $col) {
        $headers[] = $column_headers[$col] ?? $col;
    }
    fputcsv($output, $headers);

    // Write data
    foreach ($data as $row) {
        $csv_row = [];
        foreach ($selected_columns as $col) {
            $value = $row[$col] ?? '';
            if ($col === 'tanggal_lamar' && $value) {
                $value = date('d/m/Y H:i', strtotime($value));
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
    $filename = "data_lamaran_" . date('Y-m-d_H-i-s') . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    // Column mapping
    $column_headers = [
        'id_lamaran' => 'ID Lamaran',
        'nama_user' => 'Nama Pelamar',
        'email_user' => 'Email Pelamar',
        'nama_perusahaan' => 'Perusahaan',
        'judul_lowongan' => 'Lowongan',
        'tanggal_lamar' => 'Tanggal Lamar',
        'status_lamaran' => 'Status Lamaran',
        'catatan_hrd' => 'Catatan HRD'
    ];

    $html = '<table border="1">';

    // Headers
    $html .= '<tr>';
    foreach ($selected_columns as $col) {
        $html .= '<th style="background-color: #4CAF50; color: white; font-weight: bold;">' . htmlspecialchars($column_headers[$col] ?? $col) . '</th>';
    }
    $html .= '</tr>';

    // Data
    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($selected_columns as $col) {
            $value = $row[$col] ?? '';
            if ($col === 'tanggal_lamar' && $value) {
                $value = date('d/m/Y H:i', strtotime($value));
            }
            $html .= '<td>' . htmlspecialchars($value) . '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</table>';

    echo $html;
    exit();
}

function exportToPDF($data, $selected_columns)
{
    $filename = "data_lamaran_" . date('Y-m-d_H-i-s') . ".pdf";

    // Column mapping
    $column_headers = [
        'id_lamaran' => 'ID Lamaran',
        'nama_user' => 'Nama Pelamar',
        'email_user' => 'Email Pelamar',
        'nama_perusahaan' => 'Perusahaan',
        'judul_lowongan' => 'Lowongan',
        'tanggal_lamar' => 'Tanggal Lamar',
        'status_lamaran' => 'Status Lamaran',
        'catatan_hrd' => 'Catatan HRD'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Lamaran</title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body { font-family: Arial; margin: 20px; font-size: 12px; }
        h1 { color: #1e40af; text-align: center; margin-bottom: 20px; }
        .info { background: #f8fafc; padding: 10px; margin-bottom: 15px; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .email-cell { font-size: 9px; }
        .nama-cell { font-weight: 500; }
        .tanggal-cell { text-align: center; font-size: 9px; }
        .status-cell { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <h1>LAPORAN DATA LAMARAN</h1>
    
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
        if ($col === 'id_lamaran')
            $class = ' id-cell';
        elseif ($col === 'email_user')
            $class = ' email-cell';
        elseif ($col === 'nama_user')
            $class = ' nama-cell';
        elseif ($col === 'tanggal_lamar')
            $class = ' tanggal-cell';
        elseif ($col === 'status_lamaran')
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

            if ($col === 'id_lamaran') {
                $class = ' id-cell';
                $value = 'A' . sprintf("%04d", $row[$col] ?? 0);
            } elseif ($col === 'nama_user') {
                $class = ' nama-cell';
                $value = htmlspecialchars($row[$col] ?? '');
            } elseif ($col === 'email_user') {
                $class = ' email-cell';
                $value = htmlspecialchars($row[$col] ?? '');
            } elseif ($col === 'nama_perusahaan') {
                $value = htmlspecialchars($row[$col] ?? '');
            } elseif ($col === 'judul_lowongan') {
                $value = htmlspecialchars($row[$col] ?? '');
            } elseif ($col === 'tanggal_lamar') {
                $class = ' tanggal-cell';
                $value = $row[$col] ? date('d/m/Y H:i', strtotime($row[$col])) : '-';
            } elseif ($col === 'status_lamaran') {
                $class = ' status-cell';
                $value = htmlspecialchars($row[$col] ?? '');
            } elseif ($col === 'catatan_hrd') {
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
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . ($format === 'print' ? 'Print Data Lamaran' : 'Preview Data Lamaran') . '</title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
        h1 { color: #1e40af; text-align: center; margin-bottom: 20px; }
        .info { background: #f8fafc; padding: 10px; margin-bottom: 15px; font-size: 10px; border: 1px solid #e2e8f0; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: bold; }
        .id-cell { text-align: center; font-weight: bold; }
        .email-cell { font-size: 11px; }
        .nama-cell { font-weight: 500; }
        .tanggal-cell { text-align: center; font-size: 10px; }
        .status-cell { text-align: center; font-weight: bold; }
        .no-data { text-align: center; padding: 20px; color: #666; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h1>LAPORAN DATA LAMARAN</h1>
    
    <div class="info">
        <div>Tanggal: ' . date('d/m/Y H:i:s') . '</div>
        <div>Total Data: ' . count($data) . ' records</div>
        <div>Kolom: ' . implode(', ', array_map(function ($col) use ($column_map) {
        return $column_map[$col] ?? $col;
    }, $selected_columns)) . '</div>
    </div>';

    if (empty($data)) {
        $html .= '<div class="no-data">Tidak ada data yang ditemukan dengan filter yang dipilih.</div>';
    } else {
        $html .= '<table>
            <thead>
                <tr>';

        // Generate headers
        foreach ($selected_columns as $col) {
            $class = '';
            if ($col === 'id_lamaran')
                $class = ' id-cell';
            elseif ($col === 'email_user')
                $class = ' email-cell';
            elseif ($col === 'nama_user')
                $class = ' nama-cell';
            elseif ($col === 'tanggal_lamar')
                $class = ' tanggal-cell';
            elseif ($col === 'status_lamaran')
                $class = ' status-cell';

            $header_text = htmlspecialchars($column_map[$col] ?? $col);
            $html .= '<th class="' . $class . '">' . $header_text . '</th>';
        }

        $html .= '</tr>
            </thead>
            <tbody>';

        // Generate data rows
        foreach ($data as $row) {
            $html .= '<tr>';

            foreach ($selected_columns as $col) {
                $class = '';
                $value = '';

                if ($col === 'id_lamaran') {
                    $class = ' id-cell';
                    $value = 'A' . sprintf("%04d", $row[$col] ?? 0);
                } elseif ($col === 'nama_user') {
                    $class = ' nama-cell';
                    $value = htmlspecialchars($row[$col] ?? '');
                } elseif ($col === 'email_user') {
                    $class = ' email-cell';
                    $value = htmlspecialchars($row[$col] ?? '');
                } elseif ($col === 'nama_perusahaan') {
                    $value = htmlspecialchars($row[$col] ?? '');
                } elseif ($col === 'judul_lowongan') {
                    $value = htmlspecialchars($row[$col] ?? '');
                } elseif ($col === 'tanggal_lamar') {
                    $class = ' tanggal-cell';
                    $value = $row[$col] ? date('d/m/Y H:i', strtotime($row[$col])) : '-';
                } elseif ($col === 'status_lamaran') {
                    $class = ' status-cell';
                    $value = htmlspecialchars($row[$col] ?? '');
                } elseif ($col === 'catatan_hrd') {
                    $value = htmlspecialchars($row[$col] ?? '');
                }

                $html .= '<td class="' . $class . '">' . $value . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody>
        </table>';
    }

    if ($format === 'preview') {
        $html .= '<div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Print
            </button>
            <button onclick="window.close()" style="margin-left: 10px; padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Tutup
            </button>
        </div>';
    }

    $html .= '</body>
</html>';

    return $html;
}

?>