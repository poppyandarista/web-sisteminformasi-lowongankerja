<?php
session_start();
require_once 'koneksi.php';

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
        $selected_columns = $_POST['columns'] ?? ['id_admin', 'email_admin', 'nama_admin', 'foto_admin', 'created_at'];

        // Build WHERE clause for filters
        $where_conditions = [];
        $params = [];
        $types = '';

        // Filter by ID Range
        if (!empty($_POST['id_min'])) {
            $where_conditions[] = "id_admin >= ?";
            $params[] = max(0, intval($_POST['id_min']));
            $types .= 'i';
        }

        if (!empty($_POST['id_max'])) {
            $where_conditions[] = "id_admin <= ?";
            $params[] = max(0, intval($_POST['id_max']));
            $types .= 'i';
        }

        // Filter by Email
        if (!empty($_POST['filter_email'])) {
            $where_conditions[] = "email_admin LIKE ?";
            $params[] = '%' . $_POST['filter_email'] . '%';
            $types .= 's';
        }

        // Filter by Nama
        if (!empty($_POST['filter_nama'])) {
            $where_conditions[] = "nama_admin LIKE ?";
            $params[] = '%' . $_POST['filter_nama'] . '%';
            $types .= 's';
        }

        // Filter by Date Range
        if (!empty($_POST['tanggal_dari'])) {
            $where_conditions[] = "DATE(created_at) >= ?";
            $params[] = $_POST['tanggal_dari'];
            $types .= 's';
        }

        if (!empty($_POST['tanggal_sampai'])) {
            $where_conditions[] = "DATE(created_at) <= ?";
            $params[] = $_POST['tanggal_sampai'];
            $types .= 's';
        }

        // Build the complete query
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

        // Map column names to display names
        $column_map = [
            'id_admin' => 'ID Admin',
            'email_admin' => 'Email Admin',
            'nama_admin' => 'Nama Admin',
            'foto_admin' => 'Foto Admin',
            'created_at' => 'Tanggal Dibuat'
        ];

        // Build SELECT clause
        $select_columns = [];
        foreach ($selected_columns as $column) {
            if (isset($column_map[$column])) {
                $select_columns[] = $column;
            }
        }

        if (empty($select_columns)) {
            $select_columns = ['id_admin', 'email_admin', 'nama_admin', 'foto_admin', 'created_at'];
        }

        $query = "SELECT " . implode(', ', $select_columns) . " FROM admin " . $where_clause . " ORDER BY id_admin ASC";

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
    $selected_columns = $_POST['columns'] ?? ['id_admin', 'email_admin', 'nama_admin', 'foto_admin', 'created_at'];

    // Build WHERE clause for filters
    $where_conditions = [];
    $params = [];
    $types = '';

    // Filter by ID Range
    if (!empty($_POST['id_min'])) {
        $where_conditions[] = "id_admin >= ?";
        $params[] = max(0, intval($_POST['id_min']));
        $types .= 'i';
    }

    if (!empty($_POST['id_max'])) {
        $where_conditions[] = "id_admin <= ?";
        $params[] = max(0, intval($_POST['id_max']));
        $types .= 'i';
    }

    // Filter by Email
    if (!empty($_POST['filter_email'])) {
        $where_conditions[] = "email_admin LIKE ?";
        $params[] = '%' . $_POST['filter_email'] . '%';
        $types .= 's';
    }

    // Filter by Nama
    if (!empty($_POST['filter_nama'])) {
        $where_conditions[] = "nama_admin LIKE ?";
        $params[] = '%' . $_POST['filter_nama'] . '%';
        $types .= 's';
    }

    // Filter by Date Range
    if (!empty($_POST['tanggal_dari'])) {
        $where_conditions[] = "DATE(created_at) >= ?";
        $params[] = $_POST['tanggal_dari'];
        $types .= 's';
    }

    if (!empty($_POST['tanggal_sampai'])) {
        $where_conditions[] = "DATE(created_at) <= ?";
        $params[] = $_POST['tanggal_sampai'];
        $types .= 's';
    }

    // Build column names for SELECT
    $column_select = implode(', ', $selected_columns);

    // Build final query
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Get data
    $query = "SELECT $column_select FROM admin $where_clause ORDER BY id_admin";

    if (!empty($params)) {
        $stmt = $db->koneksi->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->koneksi->query($query);
    }

    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
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
            header("Location: dataadmin.php");
            break;
    }
} else {
    header("Location: dataadmin.php");
    exit();
}

function exportToCSV($data, $selected_columns)
{
    $filename = "data_admin_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Add BOM for UTF-8
    fwrite($output, "\xEF\xBB\xBF");

    // Column mapping
    $column_headers = [
        'id_admin' => 'ID Admin',
        'email_admin' => 'Email',
        'nama_admin' => 'Nama Admin',
        'foto_admin' => 'Foto',
        'created_at' => 'Tanggal Dibuat'
    ];

    // Build header based on selected columns
    $headers = [];
    foreach ($selected_columns as $col) {
        $headers[] = $column_headers[$col] ?? $col;
    }
    fputcsv($output, $headers);

    // Data
    foreach ($data as $row) {
        $row_data = [];
        foreach ($selected_columns as $col) {
            $value = $row[$col] ?? '-';
            if ($col === 'created_at' && $value !== '-') {
                $value = date('d/m/Y H:i:s', strtotime($value));
            }
            $row_data[] = $value;
        }
        fputcsv($output, $row_data);
    }

    fclose($output);
    exit();
}

function exportToExcel($data, $selected_columns)
{
    $filename = "data_admin_" . date('Y-m-d_H-i-s') . ".xls";

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');

    // Column mapping
    $column_headers = [
        'id_admin' => 'ID Admin',
        'email_admin' => 'Email',
        'nama_admin' => 'Nama Admin',
        'foto_admin' => 'Foto',
        'created_at' => 'Tanggal Dibuat'
    ];

    // Create Excel file in HTML format
    echo '<table border="1">';

    // Build header based on selected columns
    echo '<tr>';
    foreach ($selected_columns as $col) {
        echo '<th>' . htmlspecialchars($column_headers[$col] ?? $col) . '</th>';
    }
    echo '</tr>';

    // Data
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($selected_columns as $col) {
            $value = $row[$col] ?? '-';
            if ($col === 'created_at' && $value !== '-') {
                $value = date('d/m/Y H:i:s', strtotime($value));
            }
            echo '<td>' . htmlspecialchars($value) . '</td>';
        }
        echo '</tr>';
    }

    echo '</table>';
    exit();
}

function exportToPDF($data, $selected_columns)
{
    $filename = "data_admin_" . date('Y-m-d_H-i-s') . ".pdf";

    $column_headers = [
        'id_admin' => 'ID Admin',
        'email_admin' => 'Email',
        'nama_admin' => 'Nama Admin',
        'foto_admin' => 'Foto',
        'created_at' => 'Tanggal Dibuat'
    ];

    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Admin</title>
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
    </style>
</head>
<body>
    <h1>LAPORAN DATA ADMIN</h1>
    
    <div class="info">
        <div>Tanggal Export: ' . date('d/m/Y H:i:s') . '</div>
        <div>Total Data: ' . count($data) . ' tercatat</div>
        <div>Kolom: ' . implode(', ', array_map(function ($col) use ($column_headers) {
        return $column_headers[$col] ?? $col;
    }, $selected_columns)) . '</div>
    </div>

    <table>
        <thead>
            <tr>';

    foreach ($selected_columns as $col) {
        $class = '';
        if ($col === 'id_admin')
            $class = ' id-cell';
        elseif ($col === 'email_admin')
            $class = ' email-cell';
        elseif ($col === 'nama_admin')
            $class = ' nama-cell';
        elseif ($col === 'created_at')
            $class = ' tanggal-cell';

        $header_text = htmlspecialchars($column_headers[$col] ?? $col);
        $html .= '<th class="' . $class . '">' . $header_text . '</th>';
    }

    $html .= '
            </tr>
        </thead>
        <tbody>';

    foreach ($data as $index => $row) {
        $html .= '<tr>';

        foreach ($selected_columns as $col) {
            $value = $row[$col] ?? '-';

            // Format value
            if ($col === 'created_at' && $value !== '-') {
                $value = date('d/m/Y H:i', strtotime($value));
            } elseif ($col === 'id_admin' && $value !== '-') {
                $value = '#' . $value;
            }

            $class = '';
            if ($col === 'id_admin')
                $class = ' id-cell';
            elseif ($col === 'email_admin')
                $class = ' email-cell';
            elseif ($col === 'nama_admin')
                $class = ' nama-cell';
            elseif ($col === 'created_at')
                $class = ' tanggal-cell';

            $html .= '<td class="' . $class . '">' . htmlspecialchars($value) . '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '
        </tbody>
    </table>
    
    <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #666;">
        Generated by Admin Panel LinkUp - ' . date('Y') . '
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
    <title>' . ($format === 'print' ? 'Print Data Admin' : 'Preview Data Admin') . '</title>
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
        .no-data { text-align: center; padding: 20px; color: #666; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h1>LAPORAN DATA ADMIN</h1>
    
    <div class="info">
        <div>Tanggal: ' . date('d/m/Y H:i:s') . '</div>
        <div>Total Data: ' . count($data) . ' tercatat</div>
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
            if ($col === 'id_admin')
                $class = ' id-cell';
            elseif ($col === 'email_admin')
                $class = ' email-cell';
            elseif ($col === 'nama_admin')
                $class = ' nama-cell';
            elseif ($col === 'created_at')
                $class = ' tanggal-cell';

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

                if ($col === 'id_admin') {
                    $class = ' id-cell';
                    $value = htmlspecialchars($row[$col] ?? '');
                } elseif ($col === 'email_admin') {
                    $class = ' email-cell';
                    $value = htmlspecialchars($row[$col] ?? '');
                } elseif ($col === 'nama_admin') {
                    $class = ' nama-cell';
                    $value = htmlspecialchars($row[$col] ?? '');
                } elseif ($col === 'foto_admin') {
                    $value = $row[$col] ? 'Ada Foto' : 'Tidak Ada Foto';
                } elseif ($col === 'created_at') {
                    $class = ' tanggal-cell';
                    $value = $row[$col] ? date('d/m/Y H:i', strtotime($row[$col])) : '-';
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