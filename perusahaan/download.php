<?php
// download.php - Force download handler for export files

session_start();

// Check if user is logged in
if (!isset($_SESSION['company_id'])) {
    die('Unauthorized access');
}

$file = $_GET['file'] ?? '';

// Security: Only allow files from exports directory
$allowed_dir = 'exports/';
$real_path = realpath($allowed_dir . $file);

if (!$real_path || strpos($real_path, realpath($allowed_dir)) !== 0) {
    die('Invalid file access');
}

if (!file_exists($real_path)) {
    die('File not found');
}

// Get file info
$filename = basename($real_path);
$file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

// Set appropriate content type based on file extension
switch ($file_extension) {
    case 'csv':
        $content_type = 'text/csv';
        break;
    case 'xls':
        $content_type = 'application/vnd.ms-excel';
        break;
    case 'pdf':
        $content_type = 'application/pdf';
        break;
    case 'html':
        $content_type = 'text/html';
        break;
    default:
        $content_type = 'application/octet-stream';
}

// Set headers for force download
header('Content-Description: File Transfer');
header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($real_path));

// Clear output buffer
ob_clean();
flush();

// Read file and output
readfile($real_path);
exit;
?>
