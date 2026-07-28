<?php
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_username = getenv('DB_USERNAME') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';
$db_database = getenv('DB_DATABASE') ?: 'univercity';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($db_host, $db_username, $db_password, $db_database);
if($conn->connect_error){
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    die("خطا در اتصال به پایگاه داده");
}
$conn->set_charset('utf8mb4');
