<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Verificar la estructura de la tabla para determinar el caso correcto de las columnas
$checkStructureQuery = "DESCRIBE Incidencias";
$structureResult = $conn->query($checkStructureQuery);
$columnas = [];

if ($structureResult) {
    while ($columna = $structureResult->fetch_assoc()) {
        $columnas[$columna['Field']] = true;
    }
}

// Determinar si es Estado o estado
$estadoColumn = isset($columnas['Estado']) ? 'Estado' : 'estado';
$idIncidenciaColumn = isset($columnas['ID_Incidencia']) ? 'ID_Incidencia' : 'id_incidencia';

// Parámetros para filtrado
$estado = isset($_GET['estado']) ? $_GET['estado'] : 'Pendiente';
$valid_estados = ['Pendiente', 'Resuelto', 'Todos'];
if (!in_array($estado, $valid_estados)) {
    $estado = 'Pendiente';
}

// Parámetros para paginación
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Construir la consulta SQL con JOIN para obtener información de rutas
$sqlCondition = $estado !== 'Todos' ? "WHERE i.$estadoColumn = '$estado'" : "";

$sqlIncidencias = "SELECT i.$idIncidenciaColumn as ID_Incidencia, i.id_ruta, i.Descripcion, i.categoria, 
                  i.$estadoColumn as Estado, i.fecha, r.Origen, r.Destino
                  FROM Incidencias i
                  LEFT JOIN Rutas r ON i.id_ruta = r.ID_Ruta
                  $sqlCondition
                  ORDER BY i.fecha DESC 
                  LIMIT $offset, $recordsPerPage";

$resultIncidencias = $conn->query($sqlIncidencias);

if (!$resultIncidencias) {
    $error_msg = "Error en la consulta: " . $conn->error;
    $resultIncidencias = false;
}

// Contar el total de registros para la paginación
$countQuery = "SELECT COUNT(*) as total FROM Incidencias i $sqlCondition";
$countResult = $conn->query($countQuery);

if (!$countResult) {
    $error_msg = isset($error_msg) ? $error_msg : "" . " Error en conteo: " . $conn->error;
    $totalRecords = 0;
    $totalPages = 1;
} else {
    $totalData = $countResult->fetch_assoc();
    $totalRecords = $totalData['total'];
    $totalPages = ceil($totalRecords / $recordsPerPage);
}

// Estadísticas de incidencias
$sqlStats = "SELECT $estadoColumn as Estado, COUNT(*) as total FROM Incidencias GROUP BY $estadoColumn";
$statsResult = $conn->query($sqlStats);
$stats = [];

if ($statsResult) {
    while ($row = $statsResult->fetch_assoc()) {
        $stats[$row['Estado']] = $row['total'];
    }
}

// Estadísticas por categoría
$sqlCategorias = "SELECT categoria, COUNT(*) as total FROM Incidencias GROUP BY categoria";
$resultCategorias = $conn->query($sqlCategorias);
$categorias = [];

if ($resultCategorias) {
    while ($row = $resultCategorias->fetch_assoc()) {
        $categorias[$row['categoria']] = $row['total'];
    }
}

?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="m-0"><i class="fas fa-exclamation-triangle me-2"></i>Gestión de Reportes de Incidencias</h2>
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
                    <h5 class="card-title">Filtrar por Estado</h5>
                    <form method="get" action="" class="mb-3">
                        <select name="estado" class="form-select" onchange="this.form.submit()">
                            <option value="Pendiente" <?php echo $estado === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="Resuelto" <?php echo $estado === 'Resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                            <option value="Todos" <?php echo $estado === 'Todos' ? 'selected' : ''; ?>>Todos</option>
                        </select>
                    </form>

                    <h5 class="mt-4">Estadísticas de Incidencias</h5>
                    <ul class="list-group">
                        <?php foreach ($stats as $estado => $total): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($estado); ?>
                                <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($total); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <h5 class="mt-4">Estadísticas por Categoría</h5>
                    <ul class="list-group">
                        <?php foreach ($categorias as $categoria => $total): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($categoria); ?>
                                <span class="badge bg-secondary rounded-pill"><?php echo htmlspecialchars($total); ?></span>
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
                                <th>Ruta</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($resultIncidencias && $resultIncidencias->num_rows > 0): ?>
                                <?php while ($row = $resultIncidencias->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['ID_Incidencia']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Origen'] . ' - ' . $row['Destino']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Descripcion']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Estado']); ?></td>
                                        <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                        <td>
                                            <a href="admin_resolve_report.php?id=<?php echo $row['ID_Incidencia']; ?>" class="btn btn-success btn-sm">Marcar como Resuelto</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron incidencias.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&estado=<?php echo urlencode($estado); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>