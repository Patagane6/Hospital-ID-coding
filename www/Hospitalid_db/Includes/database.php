<?php
// Database connection — use environment variables when available.
// Default host is 'mysql' for container setups; fallback to 127.0.0.1 to force TCP if 'localhost' would use socket.
$db_host = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: 'mysql';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'hospitalid_db';

// If host is explicitly 'localhost', use 127.0.0.1 to avoid socket lookup failures when a TCP connection is desired.
if ($db_host === 'localhost') {
    $db_host = '127.0.0.1';
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Log the detailed error server-side, but show a concise message to the user
    error_log('DB connection error: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection failed.');
}
?>

