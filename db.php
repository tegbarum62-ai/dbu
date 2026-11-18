<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'student_registration';

try {
    $server = new mysqli($host, $username, $password);
    $server->set_charset('utf8mb4');
    $server->query("CREATE DATABASE IF NOT EXISTS `$database`
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $server->close();

    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset('utf8mb4');

    $conn->query("
        CREATE TABLE IF NOT EXISTS students (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            student_id VARCHAR(50) NOT NULL UNIQUE,
            department VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            gender VARCHAR(20) NOT NULL,
            date_of_birth DATE NOT NULL,
            phone VARCHAR(30) NOT NULL,
            address TEXT NOT NULL,
            academic_year VARCHAR(20) NOT NULL,
            year_level VARCHAR(30) NOT NULL,
            semester VARCHAR(30) NOT NULL,
            registration_date DATE NOT NULL,
            profile_picture VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    exit('Database connection failed. Start MySQL and update db.php if needed.');
}
