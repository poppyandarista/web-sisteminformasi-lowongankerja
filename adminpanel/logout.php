<?php
session_start();

// Hapus semua variabel sesi
$_SESSION = array();

// Hancurkan sesi
session_destroy();

// Hapus cookie sesi
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect ke halaman login dengan header anti-cache
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
header("Location: sign-in.php");
exit();
?>