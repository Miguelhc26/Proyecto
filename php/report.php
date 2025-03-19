<?php
session_start();

// Conexión a la base de datos
include(__DIR__ . '/../config/db_config.php'); 

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

$usuario_id = $_SESSION['usuario'];

// Función para manejar errores
function handleError($message) {
    error_log("Error en report.php: " . $message);
    return "Ha ocurrido un error. Por favor, intenta de nuevo más tarde.";
}

// Obtener el nombre del usuario para personalizar la página
try {
    $userSql = "SELECT nombre FROM Usuarios WHERE id_usuario = ?";
    $userStmt = $conn->prepare($userSql);
    if (!$userStmt) throw new Exception($conn->error);
    
    $userStmt->bind_param("i", $usuario_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result(); if ($userResult && $userResult->num_rows > 0) {
        $userData = $userResult->fetch_assoc();
        $nombre_usuario = $userData['nombre'];
    } else {
        $nombre_usuario = "Usuario";
    }
} catch (Exception $e) {
    $errorMsg = handleError($e->getMessage());
    $nombre_usuario = "Usuario";
}

// Obtener las 10 rutas desde la base de datos para el formulario de reporte
try {
    $sql = "SELECT ID_Ruta, Origen, Destino FROM Rutas LIMIT 10"; // Limitar a 10 rutas
    $result = $conn->query($sql);
    if (!$result) throw new Exception($conn->error);
} catch (Exception $e) {
    $errorMsg = handleError($e->getMessage());
    $result = false;
}

// Mensaje de éxito o error
$message = '';
$messageType = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    $messageType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
}
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-warning text-dark">
                    <h3 class="text-center font-weight-light my-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>Reportar Incidencia
                    </h3>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> text-center" role="alert">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="procesarReporte.php" method="POST" class="shadow p-4 rounded bg-light" onsubmit="return validateForm()">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ruta" class="form-label">Seleccionar Ruta:</label>
                                <select class="form-select" name="ruta" required>
                                    <option value="">Selecciona una ruta</option>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($row['ID_Ruta']); ?>">
                                                <?php echo htmlspecialchars($row['Origen']) . " → " . htmlspecialchars($row['Destino']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <option value="">No hay rutas disponibles</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="categoria" class="form-label">Categoría de la Incidencia:</label>
                                <select class="form-select" name="categoria" required>
                                    <option value="">Selecciona una categoría</option>
                                    <option value="Retraso">Retraso</option>
                                    <option value="Falta de Servicio">Falta de Servicio</option>
                                    <option value="Problema Técnico">Problema Técnico</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha y Hora de la Incidencia:</label>
                            <input type="datetime-local" class="form-control" name="fecha" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción de la Incidencia:</label>
                            <textarea class="form-control" name="descripcion" rows="4" placeholder="Ejemplo: El autobús llegó tarde..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Enviar Reporte</button>
                        <button type="reset" class="btn btn-secondary w-100 mt-2">Restablecer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    const ruta = document.querySelector('select[name="ruta"]');
    const categoria = document.querySelector('select[name="categoria"]');
    const descripcion = document.querySelector('textarea[name="descripcion"]');

    if (ruta.value === "") {
        alert("Por favor, selecciona una ruta.");
        return false;
    }

    if (categoria.value === "") {
        alert(" Por favor, selecciona una categoría.");
        return false;
    }

    if (descripcion.value.trim() === "") {
        alert("Por favor, proporciona una descripción de la incidencia.");
        return false;
    }

    return true;
}
</script>

<?php include(__DIR__ . '/../includes/footer.php'); ?>