<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Obtener todas las rutas
$sqlRutas = "SELECT * FROM Rutas";
$resultRutas = $conn->query($sqlRutas);
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center">Gestión de Rutas</h1>
    <a href="admin_add_route.php" class="btn btn-primary mb-3">Agregar Nueva Ruta</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Horario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultRutas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['ID_Ruta']); ?></td>
                    <td><?php echo htmlspecialchars($row['Origen']); ?></td>
                    <td><?php echo htmlspecialchars($row['Destino']); ?></td>
                    <td><?php echo htmlspecialchars($row['Horario']); ?></td>
                    <td>
                        <a href="admin_edit_route.php?id=<?php echo $row['ID_Ruta']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="admin_delete_route.php?id=<?php echo $row['ID_Ruta']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta ruta?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
