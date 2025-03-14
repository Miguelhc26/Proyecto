<?php
session_start();

// Conexión a la base de datos
include(__DIR__ . '/../config/db_config.php'); 

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

// Verificar si se han enviado datos del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener y sanitizar los datos del formulario
    $ruta = isset($_POST['ruta']) ? intval($_POST['ruta']) : null;
    $categoria = isset($_POST['categoria']) ? htmlspecialchars($_POST['categoria']) : null;
    $fecha = isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : null;
    $descripcion = isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : null;

    // Validar que todos los campos requeridos estén presentes
    if ($ruta && $categoria && $fecha && $descripcion) {
        // Preparar la consulta para insertar la incidencia
        $sql = "INSERT INTO Incidencias (id_ruta, categoria, fecha, descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // Verificar si la preparación fue exitosa
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        // Vincular parámetros
        $stmt->bind_param("isss", $ruta, $categoria, $fecha, $descripcion);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir con un mensaje de éxito
            header("Location: report.php?message=Reporte enviado correctamente.");
        } else {
            // Redirigir con un mensaje de error
            header("Location: report.php?message=Error al enviar el reporte: " . $stmt->error);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        // Redirigir con un mensaje de error si faltan campos
        header("Location: report.php?message=Por favor, completa todos los campos.");
    }
} else {
    // Redirigir si no se accede mediante POST
    header("Location: report.php");
}

// Cerrar la conexión
$conn->close();
?>