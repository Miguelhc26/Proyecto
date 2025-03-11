<?php
session_start();

// Conexión a la base de datos
include(__DIR__ . '/../config/db_config.php'); 

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

// Obtener las rutas desde la base de datos para el formulario de reporte
$sql = "SELECT ID_Ruta, Origen, Destino FROM Rutas";
$result = $conn->query($sql);
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Reportar Incidencia</h1>
    <form action="procesarReporte.php" method="POST">
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
            <label for="descripcion" class="form-label">Descripción de la Incidencia:</label>
            <textarea class="form-control" name="descripcion" rows="4" placeholder="Describe el problema..." required></textarea>
        </div>
        <button type="submit" class="btn btn-warning w-100">Enviar Reporte</button>
    </form>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
