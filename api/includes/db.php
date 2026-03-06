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

// Auto-Setup: Check if tables exist. If not, run setup_aiven.sql
$check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($check) == 0) {
    // Correct path considering we are in api/includes/
    $sql_file = __DIR__ . '/../../setup_aiven.sql';
    if (file_exists($sql_file)) {
        $sql = file_get_contents($sql_file);
        // Execute multi-query
        if (mysqli_multi_query($conn, $sql)) {
            // Must clear the results buffer
            do {
                if ($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_next_result($conn));
        }
    }
}

// Enable Database Sessions
require_once __DIR__ . '/session_handler.php';
setup_db_sessions($conn);
?>
