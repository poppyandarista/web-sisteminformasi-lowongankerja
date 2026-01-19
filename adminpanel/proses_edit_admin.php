<?php
session_start();
include 'koneksi.php';

// Set header JSON dengan charset UTF-8
header('Content-Type: application/json; charset=utf-8');

// Enable error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Nonaktifkan display error di production
ini_set('log_errors', 1);

// Cek apakah request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Metode request tidak valid. Hanya POST yang diizinkan.'
    ]);
    exit();
}

// Validasi input
$id_admin = isset($_POST['id_admin']) ? trim($_POST['id_admin']) : '';
$nama_admin = isset($_POST['nama_admin']) ? trim($_POST['nama_admin']) : '';
$email_admin = isset($_POST['email_admin']) ? trim($_POST['email_admin']) : '';

// Log input untuk debugging
error_log("Edit Admin Input: id_admin=$id_admin, nama_admin=$nama_admin, email_admin=$email_admin");

// Cek data kosong
if (empty($id_admin) || empty($nama_admin) || empty($email_admin)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Semua field harus diisi'
    ]);
    exit();
}

// Validasi email
if (!filter_var($email_admin, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Format email tidak valid'
    ]);
    exit();
}

try {
    $db = new database();

    // Update data admin
    $result = $db->update_admin($id_admin, $nama_admin, $email_admin);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Data admin berhasil diperbarui'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal memperbarui data admin. Data mungkin tidak berubah.'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error in proses_edit_admin.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
    ]);
}
?>