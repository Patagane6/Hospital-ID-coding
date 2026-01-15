<?php
// Use environment variables where possible so the code works in containers
$db_host = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: 'mysql';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'hospitalid_db';

// Prefer TCP over socket when host is non-empty; leave as-is if host is 'localhost' but many containers use 'mysql'
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Fail with a concise message but avoid leaking credentials
    error_log('Database connection error: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection failed.');
}
?>

