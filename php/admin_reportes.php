<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Obtener los reportes de incidencias pendientes
$sqlIncidencias = "SELECT * FROM Incidencias WHERE estado='Pendiente'";
$resultIncidencias = $conn->query($sqlIncidencias);
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center">Gestión de Reportes de Incidencias</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ruta</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultIncidencias->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ID_Incidencia']); ?></td>
                    <td><?php echo htmlspecialchars($row['Ruta']); ?></td>
                    <td><?php echo htmlspecialchars($row['Descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($row['Estado']); ?></td>
                    <td>
                        <a href="admin_resolve_report.php?id=<?php echo $row['ID_Incidencia']; ?>" class="btn btn-success btn-sm">Marcar como Resuelto</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
