<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new database();

    // Sanitize input
    $nama_admin = mysqli_real_escape_string($db->koneksi, trim($_POST['nama_admin'] ?? ''));
    $email_admin = mysqli_real_escape_string($db->koneksi, trim($_POST['email_admin'] ?? ''));
    $password_admin = $_POST['password_admin'] ?? '';

    // Validasi input
    if (empty($nama_admin) || empty($email_admin) || empty($password_admin)) {
        echo json_encode([
            'success' => false,
            'message' => 'Semua field harus diisi!'
        ]);
        exit;
    }

    // Validasi email format
    if (!filter_var($email_admin, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Format email tidak valid!'
        ]);
        exit;
    }

    // Validasi password length
    if (strlen($password_admin) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'Password minimal 6 karakter!'
        ]);
        exit;
    }

    // Validasi email sudah ada
    $checkEmail = mysqli_query($db->koneksi, "SELECT * FROM admin WHERE email_admin = '$email_admin'");

    if (mysqli_num_rows($checkEmail) > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email sudah terdaftar!'
        ]);
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password_admin, PASSWORD_DEFAULT);

    // Insert data ke database
    $query = "INSERT INTO admin (email_admin, password_admin, nama_admin) 
              VALUES ('$email_admin', '$hashed_password', '$nama_admin')";

    if (mysqli_query($db->koneksi, $query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Data admin berhasil ditambahkan!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan data. Error: ' . mysqli_error($db->koneksi)
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Metode request tidak valid'
    ]);
}
?>