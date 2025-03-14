<?php
session_start();

// Conexión a la base de datos
include(__DIR__ . '/../config/db_config.php'); 

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

// Obtener las 10 rutas desde la base de datos para el formulario de reporte
$sql = "SELECT ID_Ruta, Origen, Destino FROM Rutas LIMIT 10"; // Limitar a 10 rutas
$result = $conn->query($sql);

// Mensaje de éxito o error
$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Reportar Incidencia</h1>

    <?php if ($message): ?>
        <div class="alert alert-info text-center" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="procesarReporte.php" method="POST" class="shadow p-4 rounded bg-light" onsubmit="return validateForm()">
        <div class="mb-3">
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
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoría de la Incidencia:</label>
            <select class="form-select" name="categoria" required>
                <option value="">Selecciona una categoría</option>
                <option value="Retraso">Retraso</option>
                <option value="Falta de Servicio">Falta de Servicio</option>
                <option value="Problema Técnico">Problema Técnico</option>
                <option value="Otro">Otro</option>
            </select>
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
        alert("Por favor, selecciona una categoría.");
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