<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Parámetros para paginación
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Obtener todas las rutas con paginación
$sqlRutas = "SELECT * FROM Rutas LIMIT $offset, $recordsPerPage";
$resultRutas = $conn->query($sqlRutas);

// Contar el total de rutas para la paginación
$countQuery = "SELECT COUNT(*) as total FROM Rutas";
$countResult = $conn->query($countQuery);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

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
            <?php if ($resultRutas && $resultRutas->num_rows > 0): ?>
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
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No se encontraron rutas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>