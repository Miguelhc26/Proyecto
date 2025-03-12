<?php
// config/db_config.php

// Definiciones de constantes para la conexión a la base de datos
define('DB_HOST', 'bewurhsoyviu0k1qmvjo-mysql.services.clever-cloud.com');
define('DB_USER', 'uwfnuqattb08vowd');
define('DB_PASS', '6iEjjVDGWKDkjGhEBFSy');
define('DB_NAME', 'bewurhsoyviu0k1qmvjo');

// Crear conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>