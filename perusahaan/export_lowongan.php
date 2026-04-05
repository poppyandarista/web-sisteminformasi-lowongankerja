<?php
// perusahaan/export_lowongan.php
session_start();
require_once 'koneksi_perusahaan.php';

if (!isset($_SESSION['company_id'])) {
    header('Location: login.php');
    exit();
}

$company_id = $_SESSION['company_id'];
$company_name = $_SESSION['company_name'] ?? 'Perusahaan Saya';

// Ambil parameter format dan filter
$format = $_GET['format'] ?? 'excel';
$filter_status = $_GET['status'] ?? 'all';

// Ambil data lowongan
$lowongan_list = $db->getLowonganByPerusahaan($company_id);

// Filter berdasarkan status jika ada
$filtered_data = [];
foreach ($lowongan_list as $low) {
    if ($filter_status == 'all' || $low['status'] == $filter_status) {
        $filtered_data[] = $low;
    }
}

// Nama file
$filename = 'lowongan_perusahaan_' . date('Y-m-d_His');

// Helper function untuk ambil nama kategori
function getKategoriNama($kategori_id, $db)
{
    $kategori_list = $db->getAllKategori();
    foreach ($kategori_list as $kat) {
        if ($kat['id_kategori'] == $kategori_id) {
            return $kat['nama_kategori'];
        }
    }
    return '-';
}

// Helper function untuk ambil nama jenis
function getJenisNama($jenis_id, $db)
{
    $jenis_list = $db->getAllJenis();
    foreach ($jenis_list as $jenis) {
        if ($jenis['id_jenis'] == $jenis_id) {
            return $jenis['nama_jenis'];
        }
    }
    return '-';
}

// Header untuk export
if ($format == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, ['ID Lowongan', 'Judul Lowongan', 'Kategori', 'Jenis Pekerjaan', 'Lokasi', 'Gaji', 'Tanggal Posting', 'Tanggal Tutup', 'Status', 'Kualifikasi', 'Deskripsi']);

    foreach ($filtered_data as $low) {
        fputcsv($output, [
            $low['id_lowongan'],
            $low['judul_lowongan'],
            getKategoriNama($low['kategori_lowongan'], $db),
            getJenisNama($low['id_jenis'], $db),
            $low['lokasi_lowongan'] ?? $low['nama_kota'] ?? '-',
            'Rp ' . number_format($low['gaji_lowongan'] ?? 0, 0, ',', '.'),
            date('d/m/Y H:i', strtotime($low['tanggal_posting'])),
            $low['tanggal_tutup'] ? date('d/m/Y', strtotime($low['tanggal_tutup'])) : '-',
            $low['status'],
            strip_tags($low['kualifikasi'] ?? ''),
            strip_tags($low['deskripsi_lowongan'] ?? '')
        ]);
    }
    fclose($output);

} elseif ($format == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');

    echo '<html>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<body>';
    echo '<h2>Laporan Data Lowongan</h2>';
    echo '<p><strong>Perusahaan:</strong> ' . htmlspecialchars($company_name) . '</p>';
    echo '<p><strong>Tanggal Export:</strong> ' . date('d/m/Y H:i:s') . '</p>';
    echo '<p><strong>Total Lowongan:</strong> ' . count($filtered_data) . '</p>';
    echo '<p><strong>Filter Status:</strong> ' . ($filter_status == 'all' ? 'Semua' : $filter_status) . '</p>';
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>ID Lowongan</th>';
    echo '<th>Judul Lowongan</th>';
    echo '<th>Kategori</th>';
    echo '<th>Jenis Pekerjaan</th>';
    echo '<th>Lokasi</th>';
    echo '<th>Gaji</th>';
    echo '<th>Tanggal Posting</th>';
    echo '<th>Tanggal Tutup</th>';
    echo '<th>Status</th>';
    echo '<th>Kualifikasi</th>';
    echo '<th>Deskripsi</th>';
    echo '</tr>';

    foreach ($filtered_data as $low) {
        echo '<tr>';
        echo '<td>' . $low['id_lowongan'] . '</td>';
        echo '<td>' . htmlspecialchars($low['judul_lowongan']) . '</td>';
        echo '<td>' . htmlspecialchars(getKategoriNama($low['kategori_lowongan'], $db)) . '</td>';
        echo '<td>' . htmlspecialchars(getJenisNama($low['id_jenis'], $db)) . '</td>';
        echo '<td>' . htmlspecialchars($low['lokasi_lowongan'] ?? $low['nama_kota'] ?? '-') . '</td>';
        echo '<td>Rp ' . number_format($low['gaji_lowongan'] ?? 0, 0, ',', '.') . '</td>';
        echo '<td>' . date('d/m/Y H:i', strtotime($low['tanggal_posting'])) . '</td>';
        echo '<td>' . ($low['tanggal_tutup'] ? date('d/m/Y', strtotime($low['tanggal_tutup'])) : '-') . '</td>';
        echo '<td>' . $low['status'] . '</td>';
        echo '<td>' . nl2br(htmlspecialchars(substr($low['kualifikasi'] ?? '', 0, 200))) . '</td>';
        echo '<td>' . nl2br(htmlspecialchars(substr($low['deskripsi_lowongan'] ?? '', 0, 300))) . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '</body>';
    echo '</html>';

} elseif ($format == 'pdf') {
    // Untuk PDF, kita tetap output HTML tapi dengan header download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');

    // Gunakan library wkhtmltopdf jika ada, atau fallback ke HTML
    // Karena tidak ada library PDF, kita akan output HTML yang bisa di-print ke PDF

    $html = '<!DOCTYPE html>';
    $html .= '<html>';
    $html .= '<head>';
    $html .= '<meta charset="UTF-8">';
    $html .= '<title>Export Lowongan - ' . htmlspecialchars($company_name) . '</title>';
    $html .= '<style>';
    $html .= 'body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }';
    $html .= 'h2 { color: #2563eb; margin-bottom: 10px; }';
    $html .= '.info { margin-bottom: 20px; padding: 10px; background: #f1f5f9; border-radius: 8px; }';
    $html .= 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
    $html .= 'th { background: #2563eb; color: white; padding: 10px; text-align: left; font-size: 11px; }';
    $html .= 'td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }';
    $html .= 'tr:nth-child(even) { background: #f9fafb; }';
    $html .= '.status-aktif { color: #10b981; font-weight: bold; }';
    $html .= '.status-nonaktif { color: #ef4444; font-weight: bold; }';
    $html .= '.footer { margin-top: 30px; text-align: center; font-size: 10px; color: #64748b; }';
    $html .= '@media print { body { margin: 0; } .no-print { display: none; } }';
    $html .= '</style>';
    $html .= '</head>';
    $html .= '<body>';
    $html .= '<h2>Laporan Data Lowongan</h2>';
    $html .= '<div class="info">';
    $html .= '<strong>Perusahaan:</strong> ' . htmlspecialchars($company_name) . '<br>';
    $html .= '<strong>Tanggal Export:</strong> ' . date('d/m/Y H:i:s') . '<br>';
    $html .= '<strong>Total Lowongan:</strong> ' . count($filtered_data) . '<br>';
    $html .= '<strong>Filter Status:</strong> ' . ($filter_status == 'all' ? 'Semua' : $filter_status) . '<br>';
    $html .= '</div>';
    $html .= '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>No</th>';
    $html .= '<th>Judul Lowongan</th>';
    $html .= '<th>Kategori</th>';
    $html .= '<th>Lokasi</th>';
    $html .= '<th>Gaji</th>';
    $html .= '<th>Tgl Posting</th>';
    $html .= '<th>Status</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';

    $no = 1;
    foreach ($filtered_data as $low) {
        $status_class = $low['status'] == 'Aktif' ? 'status-aktif' : 'status-nonaktif';
        $html .= '<tr>';
        $html .= '<td>' . $no++ . '</td>';
        $html .= '<td>' . htmlspecialchars($low['judul_lowongan']) . '</td>';
        $html .= '<td>' . htmlspecialchars(getKategoriNama($low['kategori_lowongan'], $db)) . '</td>';
        $html .= '<td>' . htmlspecialchars($low['lokasi_lowongan'] ?? $low['nama_kota'] ?? '-') . '</td>';
        $html .= '<td>Rp ' . number_format($low['gaji_lowongan'] ?? 0, 0, ',', '.') . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($low['tanggal_posting'])) . '</td>';
        $html .= '<td class="' . $status_class . '">' . $low['status'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '<div class="footer">';
    $html .= 'Dicetak dari Sistem LinkUp - ' . date('Y');
    $html .= '</div>';
    $html .= '<div class="no-print" style="text-align: center; margin-top: 20px;">';
    $html .= '<button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer;">💾 Simpan sebagai PDF</button>';
    $html .= '</div>';
    $html .= '<script>window.onload = function() { setTimeout(function() { window.print(); }, 500); }</script>';
    $html .= '</body>';
    $html .= '</html>';

    echo $html;
}
?>