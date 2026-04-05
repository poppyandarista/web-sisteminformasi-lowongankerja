<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect ke login jika belum login
if (!isset($_SESSION['id_admin'])) {
    header("Location: signin.php");
    exit();
}
?>