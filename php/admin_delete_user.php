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

// Eliminar el usuario de la base de datos
$sqlDelete = "DELETE FROM Usuarios WHERE id_usuario=$id_usuario";
if ($conn->query($sqlDelete) === TRUE) {
    echo "<script>alert('Usuario eliminado exitosamente'); window.location.href='admin_users.php';</script>";
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
