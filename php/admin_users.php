<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Parámetros para paginación
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Parámetro de búsqueda
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " WHERE nombre LIKE '%$search%' OR correo LIKE '%$search%' OR tipo LIKE '%$search%'";
}

// Contar el total de registros para la paginación
$countQuery = "SELECT COUNT(*) as total FROM Usuarios" . $searchCondition;
$countResult = $conn->query($countQuery);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Obtener usuarios con paginación
$sqlUsuarios = "SELECT id_usuario, nombre, correo, tipo FROM Usuarios" . 
               $searchCondition . 
               " ORDER BY id_usuario DESC LIMIT $offset, $recordsPerPage";
$resultUsuarios = $conn->query($sqlUsuarios);

// Para mostrar estadísticas
$sqlEstadisticas = "SELECT tipo, COUNT(*) as total FROM Usuarios GROUP BY tipo";
$resultEstadisticas = $conn->query($sqlEstadisticas);
$estadisticas = [];
while ($row = $resultEstadisticas->fetch_assoc()) {
    $estadisticas[$row['tipo']] = $row['total'];
}
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="m-0"><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
                    <div>
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h5 class="card-title">Buscar Usuarios</h5>
                    <form method="get" action="" class="mb-3">
                        <div class="input-group">
                            <input type="text" id="search" name="search" class="form-control" 
                                   placeholder="Buscar por nombre, correo o rol" 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <h5 class="mt- 4">Estadísticas de Usuarios</h5>
                    <ul class="list-group">
                        <?php foreach ($estadisticas as $tipo => $total): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($tipo); ?>
                                <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($total); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $resultUsuarios->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                                    <td>
                                        <a href="admin_edit_user.php?id=<?php echo $row['id_usuario']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="admin_delete_user.php?id=<?php echo $row['id_usuario']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>