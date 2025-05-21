<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Inicializar variables
$message = '';
$redirect_url = 'admin_resueltos.php'; // URL por defecto para redirección

// Verificar si se ha proporcionado un ID válido
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID de reporte no válido.";
    header("Location: $redirect_url");
    exit();
}

$id_incidencia = (int)$_GET['id'];

try {
    // Comprobar si el reporte existe y está resuelto
    $check_query = "SELECT ID_Incidencia, Estado FROM Incidencias WHERE ID_Incidencia = ?";
    $check_stmt = $conn->prepare($check_query);
    
    if ($check_stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $check_stmt->bind_param("i", $id_incidencia);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $_SESSION['error_message'] = "El reporte #$id_incidencia no existe.";
        header("Location: $redirect_url");
        exit();
    }
    
    $report = $check_result->fetch_assoc();
    
    // Si queremos permitir solo eliminar reportes resueltos, descomentar estas líneas
    /*
    if ($report['Estado'] !== 'Resuelto') {
        $_SESSION['error_message'] = "Solo se pueden eliminar reportes con estado 'Resuelto'.";
        header("Location: $redirect_url");
        exit();
    }
    */
    
    // Eliminar el reporte
    $delete_query = "DELETE FROM Incidencias WHERE ID_Incidencia = ?";
    $delete_stmt = $conn->prepare($delete_query);
    
    if ($delete_stmt === false) {
        throw new Exception("Error en la preparación de la consulta de eliminación: " . $conn->error);
    }
    
    $delete_stmt->bind_param("i", $id_incidencia);
    
    if ($delete_stmt->execute()) {
        $_SESSION['success_message'] = "El reporte #$id_incidencia ha sido eliminado correctamente.";
    } else {
        $_SESSION['error_message'] = "Error al eliminar el reporte: " . $delete_stmt->error;
    }
    
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
} finally {
    // Cerrar conexiones
    if (isset($check_stmt)) {
        $check_stmt->close();
    }
    if (isset($delete_stmt)) {
        $delete_stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
    
    // Redirigir de vuelta a la página de reportes resueltos
    header("Location: $redirect_url");
    exit();
}
?>