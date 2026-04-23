<?php
session_start();
include 'config/database.php';

$db = new database();

// Set header JSON
header('Content-Type: application/json');

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu untuk melamar pekerjaan.',
        'redirect' => 'login.php'
    ]);
    exit();
}

// Cek apakah ada id lowongan
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Lowongan tidak ditemukan.'
    ]);
    exit();
}

$job_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Cek apakah user sudah pernah melamar lowongan ini (kecuali status Ditolak)
$check_query = "SELECT id_lamaran, status_lamaran FROM lamaran WHERE id_lowongan = ? AND id_user = ?";
$check_stmt = $db->koneksi->prepare($check_query);
$check_stmt->bind_param("ii", $job_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

$already_applied = false;
$lamaran_id = null;
$existing_status = '';

if ($check_result->num_rows > 0) {
    $existing = $check_result->fetch_assoc();
    $existing_status = $existing['status_lamaran'];
    $lamaran_id = $existing['id_lamaran'];

    // Jika status Ditolak, izinkan melamar ulang (hapus lamaran lama dulu)
    if ($existing_status == 'Ditolak') {
        // Hapus lamaran yang ditolak
        $delete_query = "DELETE FROM lamaran WHERE id_lamaran = ?";
        $delete_stmt = $db->koneksi->prepare($delete_query);
        $delete_stmt->bind_param("i", $lamaran_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        $already_applied = false;
    } else {
        $already_applied = true;
    }
}
$check_stmt->close();

if ($already_applied) {
    echo json_encode([
        'success' => false,
        'message' => 'Anda sudah pernah melamar lowongan ini. Status: ' . $existing_status
    ]);
    exit();
}

// Insert data lamaran
$query = "INSERT INTO lamaran (id_lowongan, id_user, status_lamaran, tanggal_lamar) VALUES (?, ?, 'Diproses', NOW())";
$stmt = $db->koneksi->prepare($query);
$stmt->bind_param("ii", $job_id, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Lamaran berhasil dikirim! Perusahaan akan segera memproses lamaran Anda.'
    ]);
    exit();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengirim lamaran. Silakan coba lagi.'
    ]);
    exit();
}

$stmt->close();
?>