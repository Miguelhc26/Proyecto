<?php
// Iniciar buffer de salida para evitar problemas con headers
ob_start();

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
    $usuario_id = $_SESSION['usuario']; // Obtener el ID del usuario de la sesión
    
    // También podemos usar el campo oculto en caso de que esté presente
    if (isset($_POST['id_usuario']) && !empty($_POST['id_usuario'])) {
        $usuario_id = intval($_POST['id_usuario']);
    }
    
    // Para depuración
    error_log("Procesando reporte - Usuario ID: $usuario_id, Ruta: $ruta, Categoría: $categoria");

    // Validar que todos los campos requeridos estén presentes
    if ($ruta && $categoria && $fecha && $descripcion && $usuario_id) {
        // Preparar la consulta para insertar la incidencia
        // Usando los nombres CORRECTOS de las columnas según la estructura de la tabla
        $sql = "INSERT INTO Incidencias (id_usuario, id_ruta, Descripcion, categoria, fecha, Estado) 
                VALUES (?, ?, ?, ?, ?, 'Pendiente')";
        $stmt = $conn->prepare($sql);
        
        // Verificar si la preparación fue exitosa
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        // Vincular parámetros - observa el orden correcto según la consulta SQL
        $stmt->bind_param("iisss", $usuario_id, $ruta, $descripcion, $categoria, $fecha);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Registrar el éxito para depuración
            $id_incidencia = $conn->insert_id;
            error_log("Incidencia creada correctamente. ID: $id_incidencia para usuario ID: $usuario_id");
            
            // Añadir puntos directamente sin incluir LoyaltyPoints.php
            try {
                // Añadir puntos en la tabla LoyaltyPoints
                $pointsSql = "INSERT INTO LoyaltyPoints (id_usuario, total_points) 
                              VALUES (?, 20) 
                              ON DUPLICATE KEY UPDATE total_points = total_points + 20";
                $pointsStmt = $conn->prepare($pointsSql);
                
                if (!$pointsStmt) {
                    throw new Exception("Error preparando consulta de puntos: " . $conn->error);
                }
                
                $pointsStmt->bind_param("i", $usuario_id);
                $pointsSuccess = $pointsStmt->execute();
                
                if ($pointsSuccess) {
                    // Registrar la transacción
                    $transactionSql = "INSERT INTO LoyaltyTransactions 
                                      (id_usuario, points, description, transaction_date) 
                                      VALUES (?, 20, ?, NOW())";
                    $transactionStmt = $conn->prepare($transactionSql);
                    
                    if (!$transactionStmt) {
                        throw new Exception("Error preparando consulta de transacción: " . $conn->error);
                    }
                    
                    $description = "Puntos por reportar incidencia: " . $categoria;
                    $transactionStmt->bind_param("is", $usuario_id, $description);
                    $transactionStmt->execute();
                    
                    error_log("Se añadieron 20 puntos al usuario ID: $usuario_id por reportar incidencia");
                }
                
                // Redirigir con un mensaje de éxito que incluya los puntos obtenidos
                header("Location: report.php?message=" . urlencode("Reporte enviado correctamente. ¡Has ganado 20 puntos de fidelización!") . "&type=success");
                exit();
            } catch (Exception $e) {
                // Si hay un error al añadir puntos, aún reportamos el éxito del reporte
                error_log("Error al añadir puntos: " . $e->getMessage());
                header("Location: report.php?message=" . urlencode("Reporte enviado correctamente, pero hubo un problema al añadir los puntos.") . "&type=success");
                exit();
            }
        } else {
            // Registrar el error para depuración
            error_log("Error al insertar incidencia: " . $stmt->error);
            
            // Redirigir con un mensaje de error
            header("Location: report.php?message=" . urlencode("Error al enviar el reporte: " . $stmt->error) . "&type=danger");
            exit();
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        // Registrar el error para depuración
        error_log("Faltan campos requeridos: Ruta=$ruta, Categoria=$categoria, Fecha=$fecha, Usuario=$usuario_id");
        
        // Redirigir con un mensaje de error si faltan campos
        header("Location: report.php?message=" . urlencode("Por favor, completa todos los campos.") . "&type=warning");
        exit();
    }
} else {
    // Redirigir si no se accede mediante POST
    header("Location: report.php");
    exit();
}

// Cerrar la conexión
$conn->close();

// Finalizar el buffer de salida
ob_end_flush();