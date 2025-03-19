<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Verificar si se ha pasado un ID de incidencia
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_incidencia = $_GET['id'];

    // Actualizar el estado de la incidencia a "Resuelto"
    $sqlUpdate = "UPDATE Incidencias SET Estado = 'Resuelto' WHERE ID_Incidencia = ?";
    $stmt = $conn->prepare($sqlUpdate);
    
    if ($stmt) {
        $stmt->bind_param("i", $id_incidencia);
        if ($stmt->execute()) {
            // Redirigir a la página de gestión de reportes con un mensaje de éxito
            header("Location: admin_reportes.php?message=Incidencia resuelta exitosamente.&type=success");
            exit();
        } else {
            // Redirigir a la página de gestión de reportes con un mensaje de error
            header("Location: admin_reportes.php?message=Error al resolver la incidencia: " . $stmt->error . "&type=danger");
            exit();
        }
    } else {
        // Redirigir a la página de gestión de reportes con un mensaje de error
        header("Location: admin_reportes.php?message=Error al preparar la consulta: " . $conn->error . "&type=danger");
        exit();
    }
} else {
    // Redirigir a la página de gestión de reportes si no se proporciona un ID válido
    header("Location: admin_reportes.php?message=ID de incidencia no válido.&type=danger");
    exit();
}
?>