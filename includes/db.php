<?php
// Database connection using environment variables (standard for Vercel/Aiven)
// Fallback to local XAMPP defaults if not set.

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db = getenv('DB_NAME') ?: 'pss_jier';
$port = getenv('DB_PORT') ?: '3306';

// Initialize MySQLi
$conn = mysqli_init();

// Use SSL if DB_SSL is set (useful for Aiven)
if (getenv('DB_SSL') === 'true') {
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);
}
else {
    mysqli_real_connect($conn, $host, $user, $pass, $db, $port);
}

if (!$conn || mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
