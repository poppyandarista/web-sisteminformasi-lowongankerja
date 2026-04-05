<?php
// perusahaan/check_session.php - Check session status
session_start();

header('Content-Type: application/json');

// Check if user is logged in
$logged_in = isset($_SESSION['company_id']) && isset($_SESSION['company_logged_in']) && $_SESSION['company_logged_in'] === true;

echo json_encode([
    'logged_in' => $logged_in,
    'company_id' => $logged_in ? $_SESSION['company_id'] : null,
    'company_name' => $logged_in ? $_SESSION['company_name'] : null
]);
?>
