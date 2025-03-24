<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

if (!isset($_GET['id'])) {
    echo "<script>alert('Usuario no encontrado'); window.location.href='admin_users.php';</script>";
    exit();
}

$id_usuario = $_GET['id'];

// Eliminar los puntos de lealtad del usuario
$sqlDeletePoints = "DELETE FROM LoyaltyPoints WHERE id_usuario = ?";
$stmtDeletePoints = $conn->prepare($sqlDeletePoints);
if (!$stmtDeletePoints) {
    echo "Error al preparar la consulta de eliminación de puntos: " . $conn->error;
    exit();
}
$stmtDeletePoints->bind_param("i", $id_usuario);
$stmtDeletePoints->execute();

// Ahora eliminar el usuario de la base de datos
$sqlDelete = "DELETE FROM Usuarios WHERE id_usuario = ?";
$stmtDelete = $conn->prepare($sqlDelete);
if (!$stmtDelete) {
    echo "Error al preparar la consulta de eliminación de usuario: " . $conn->error;
    exit();
}
$stmtDelete->bind_param("i", $id_usuario);

if ($stmtDelete->execute()) {
    echo "<script>alert('Usuario eliminado exitosamente'); window.location.href='admin_users.php';</script>";
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>