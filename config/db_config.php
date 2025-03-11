<?php
$host = "bewurhsoyviu0k1qmvjo-mysql.services.clever-cloud.com";
$user = "uwfnuqattb08vowd";
$password = "6iEjjVDGWKDkjGhEBFSy";
$dbname = "bewurhsoyviu0k1qmvjo";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
