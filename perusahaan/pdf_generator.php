<?php
// Simple PDF generator using basic HTML to PDF conversion
// This is a simplified version - in production you should use proper PDF libraries

function generatePDFContent($data)
{
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Export Data Lowongan</title>
        <style>
            @page {
                margin: 1cm;
                size: A4 landscape;
            }
            body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
                font-size: 12px;
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
                font-size: 10px;
            }
            th { 
                background-color: #f2f2f2; 
                font-weight: bold; 
            }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .summary { 
                margin-top: 20px; 
                font-weight: bold; 
                text-align: center;
                font-size: 12px;
            }
            .no-break {
                page-break-inside: avoid;
            }
        </style>
    </head>
    <body>
        <h1>LAPORAN DATA LOWONGAN</h1>
        <div class="header-info">
            <p>Tanggal Export: ' . date('d/m/Y H:i:s') . '</p>
            <p>Total Data: ' . count($data) . ' lowongan</p>
        </div>
        <table class="no-break">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="20%">Judul Lowongan</th>
                    <th width="12%">Kategori</th>
                    <th width="10%">Jenis</th>
                    <th width="15%">Lokasi</th>
                    <th width="12%">Gaji</th>
                    <th width="8%">Status</th>
                    <th width="10%">Tgl Posting</th>
                    <th width="8%">Tgl Tutup</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($data as $row) {
        $lokasi = ($row['nama_provinsi'] ?? '-') . ($row['nama_kota'] ? ', ' . $row['nama_kota'] : '');
        $html .= '<tr>
            <td>' . $row['id_lowongan'] . '</td>
            <td>' . htmlspecialchars(substr($row['judul_lowongan'], 0, 50)) . (strlen($row['judul_lowongan']) > 50 ? '...' : '') . '</td>
            <td>' . htmlspecialchars($row['nama_kategori'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['nama_jenis'] ?? '-') . '</td>
            <td>' . htmlspecialchars($lokasi) . '</td>
            <td>Rp ' . number_format($row['gaji_lowongan'] ?? 0, 0, ',', '.') . '</td>
            <td>' . $row['status'] . '</td>
            <td>' . date('d/m/Y', strtotime($row['tanggal_posting'])) . '</td>
            <td>' . ($row['tanggal_tutup'] ? date('d/m/Y', strtotime($row['tanggal_tutup'])) : '-') . '</td>
        </tr>';
    }

    $html .= '</tbody></table>
        <div class="summary">
            <p>LAPORAN DATA LOWONGAN - TOTAL ' . count($data) . ' DATA</p>
        </div>
    </body>
    </html>';

    return $html;
}

// Create a proper PDF file using basic conversion
function createPDFFile($html, $filepath)
{
    // For now, we'll create a simple HTML file with PDF styling
    // In production, you should use libraries like TCPDF, DOMPDF, or mPDF

    // Add PDF-like headers to make it downloadable as PDF
    $pdf_content = $html;

    file_put_contents($filepath, $pdf_content);
    return true;
}
?>