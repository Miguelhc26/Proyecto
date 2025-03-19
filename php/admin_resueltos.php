<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Inicializar variables
$message = '';
$error_message = '';
$search_term = '';
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($current_page - 1) * $records_per_page;
$result = null;
$total_pages = 1;
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

// Busqueda
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// Consultar reportes resueltos
try {
    // Consultar estadísticas
    $stats_query = "SELECT 
                        COUNT(*) as total_resueltos,
                        MIN(fecha) as fecha_mas_antigua,
                        MAX(fecha) as fecha_mas_reciente
                    FROM Incidencias 
                    WHERE Estado = 'Resuelto'";
    $stats_result = $conn->query($stats_query);
    $stats = $stats_result->fetch_assoc();
    
    // Consultar reportes por categoría
    $category_stats_query = "SELECT 
                                categoria, 
                                COUNT(*) as total 
                            FROM Incidencias 
                            WHERE Estado = 'Resuelto' 
                            GROUP BY categoria 
                            ORDER BY total DESC";
    $category_stats_result = $conn->query($category_stats_query);
    $category_stats = [];
    while($row = $category_stats_result->fetch_assoc()) {
        $category_stats[$row['categoria']] = $row['total'];
    }
    
    // Construimos la consulta base
    $query = "SELECT * FROM Incidencias WHERE Estado = 'Resuelto'";
    
    // Añadir condición de búsqueda si existe
    if (!empty($search_term)) {
        $query .= " AND (Descripcion LIKE ? OR categoria LIKE ?)";
    }
    
    // Aplicar filtro por categoría si está establecido
    if (!empty($filter)) {
        $query .= " AND categoria = ?";
    }
    
    // Aplicar filtro por fecha si está establecido
    if (!empty($date_filter)) {
        switch($date_filter) {
            case 'today':
                $query .= " AND DATE(fecha) = CURDATE()";
                break;
            case 'week':
                $query .= " AND fecha >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $query .= " AND fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;
        }
    }
    
    // Agregar paginación
    $query .= " ORDER BY fecha DESC LIMIT ?, ?";
    
    // Preparar la consulta
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }
    
    // Vincular parámetros
    if (!empty($search_term) && !empty($filter)) {
        $search_param = "%{$search_term}%";
        $stmt->bind_param("sssii", $search_param, $search_param, $filter, $offset, $records_per_page);
    } elseif (!empty($search_term)) {
        $search_param = "%{$search_term}%";
        $stmt->bind_param("ssii", $search_param, $search_param, $offset, $records_per_page);
    } elseif (!empty($filter)) {
        $stmt->bind_param("sii", $filter, $offset, $records_per_page);
    } else {
        $stmt->bind_param("ii", $offset, $records_per_page);
    }
    
    // Ejecutar la consulta
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Obtener categorías para el filtro
    $categories_query = "SELECT DISTINCT categoria FROM Incidencias WHERE Estado = 'Resuelto' ORDER BY categoria";
    $categories_result = $conn->query($categories_query);
    $categories = [];
    
    if ($categories_result) {
        while ($category = $categories_result->fetch_assoc()) {
            $categories[] = $category['categoria'];
        }
    }
    
    // Contar el total de registros para la paginación
    $count_query = "SELECT COUNT(*) as total FROM Incidencias WHERE Estado = 'Resuelto'";
    
    if (!empty($search_term)) {
        $count_query .= " AND (Descripcion LIKE ? OR categoria LIKE ?)";
    }
    
    if (!empty($filter)) {
        $count_query .= " AND categoria = ?";
    }
    
    if (!empty($date_filter)) {
        switch($date_filter) {
            case 'today':
                $count_query .= " AND DATE(fecha) = CURDATE()";
                break;
            case 'week':
                $count_query .= " AND fecha >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $count_query .= " AND fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;
        }
    }
    
    $count_stmt = $conn->prepare($count_query);
    
    if ($count_stmt === false) {
        throw new Exception("Error en la preparación de la consulta de conteo: " . $conn->error);
    }
    
    // Vincular parámetros para la consulta de conteo
    if (!empty($search_term) && !empty($filter)) {
        $count_stmt->bind_param("sss", $search_param, $search_param, $filter);
    } elseif (!empty($search_term)) {
        $count_stmt->bind_param("ss", $search_param, $search_param);
    } elseif (!empty($filter)) {
        $count_stmt->bind_param("s", $filter);
    }
    
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $records_per_page);
    
} catch (Exception $e) {
    $error_message = "Error al obtener los reportes: " . $e->getMessage();
}
?>

<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Reportes Resueltos</h1>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-4">
        <form method="get" action="" class="w-100">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Buscar reportes..." value="<?php echo htmlspecialchars($search_term); ?>">
                <select name="filter" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $filter === $category ? 'selected' : ''; ?>><?php echo htmlspecialchars($category); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="date_filter" class="form-select">
                    <option value="">Todas las fechas</option>
                    <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Hoy</option>
                    <option value="week" <?php echo $date_filter === 'week' ? 'selected' : ''; ?>>Última semana</option>
                    <option value="month" <?php echo $date_filter === 'month' ? 'selected' : ''; ?>>Último mes</option>
                </select>
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary ms-2 ">Volver</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['ID_Incidencia']); ?></td>
                        <td><?php echo htmlspecialchars($row['Descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                        <td><?php echo htmlspecialchars($row['Estado']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                        <td><?php echo isset($row['id_usuario']) ? htmlspecialchars($row['id_usuario']) : 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>&filter=<?php echo urlencode($filter); ?>&date_filter=<?php echo urlencode($date_filter); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }
    h1 {
        color: #343a40;
    }
    .alert {
        margin-top: 20px;
    }
    .statistics {
        background-color: #e9ecef;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .pagination {
        margin-top: 20px;
    }
</style>

<?php include(__DIR__ . '/../includes/footer.php'); ?>