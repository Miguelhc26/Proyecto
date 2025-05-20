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

// Búsqueda
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

    // Consultar reportes por mes (últimos 6 meses)
    $monthly_stats_query = "SELECT 
                              DATE_FORMAT(fecha, '%Y-%m') as mes,
                              COUNT(*) as total
                           FROM Incidencias
                           WHERE Estado = 'Resuelto'
                              AND fecha >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                           GROUP BY DATE_FORMAT(fecha, '%Y-%m')
                           ORDER BY mes ASC";
    $monthly_stats_result = $conn->query($monthly_stats_query);
    $monthly_stats = [];
    while($row = $monthly_stats_result->fetch_assoc()) {
        $month_year = date('M Y', strtotime($row['mes'] . '-01'));
        $monthly_stats[$month_year] = $row['total'];
    }
    
    // Consultar tiempo promedio de resolución
    $avg_resolution_query = "SELECT 
                             AVG(TIMESTAMPDIFF(HOUR, fecha_creacion, fecha_resolucion)) as avg_time
                             FROM Incidencias
                             WHERE Estado = 'Resuelto'
                                AND fecha_creacion IS NOT NULL
                                AND fecha_resolucion IS NOT NULL";
    $avg_resolution_result = $conn->query($avg_resolution_query);
    $avg_resolution = 0;
    if ($avg_resolution_result && $row = $avg_resolution_result->fetch_assoc()) {
        $avg_resolution = round($row['avg_time'] ?? 0);
    }
    
    // Construimos la consulta base para los reportes
    $query = "SELECT i.*, r.Origen, r.Destino 
             FROM Incidencias i
             LEFT JOIN Rutas r ON i.id_ruta = r.ID_Ruta
             WHERE i.Estado = 'Resuelto'";
    
    // Añadir condición de búsqueda si existe
    if (!empty($search_term)) {
        $query .= " AND (i.Descripcion LIKE ? OR i.categoria LIKE ? OR r.Origen LIKE ? OR r.Destino LIKE ?)";
    }
    
    // Aplicar filtro por categoría si está establecido
    if (!empty($filter)) {
        $query .= " AND i.categoria = ?";
    }
    
    // Aplicar filtro por fecha si está establecido
    if (!empty($date_filter)) {
        switch($date_filter) {
            case 'today':
                $query .= " AND DATE(i.fecha) = CURDATE()";
                break;
            case 'week':
                $query .= " AND i.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $query .= " AND i.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;
            case 'quarter':
                $query .= " AND i.fecha >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                break;
            case 'year':
                $query .= " AND i.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                break;
        }
    }
    
    // Agregar paginación
    $query .= " ORDER BY i.fecha DESC LIMIT ?, ?";
    
    // Preparar la consulta
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }
    
    // Vincular parámetros
    if (!empty($search_term) && !empty($filter)) {
        $search_param = "%{$search_term}%";
        $stmt->bind_param("sssssii", $search_param, $search_param, $search_param, $search_param, $filter, $offset, $records_per_page);
    } elseif (!empty($search_term)) {
        $search_param = "%{$search_term}%";
        $stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $offset, $records_per_page);
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
    $count_query = "SELECT COUNT(*) as total FROM Incidencias i
                   LEFT JOIN Rutas r ON i.id_ruta = r.ID_Ruta
                   WHERE i.Estado = 'Resuelto'";
    
    if (!empty($search_term)) {
        $count_query .= " AND (i.Descripcion LIKE ? OR i.categoria LIKE ? OR r.Origen LIKE ? OR r.Destino LIKE ?)";
    }
    
    if (!empty($filter)) {
        $count_query .= " AND i.categoria = ?";
    }
    
    if (!empty($date_filter)) {
        switch($date_filter) {
            case 'today':
                $count_query .= " AND DATE(i.fecha) = CURDATE()";
                break;
            case 'week':
                $count_query .= " AND i.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $count_query .= " AND i.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                break;
            case 'quarter':
                $count_query .= " AND i.fecha >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                break;
            case 'year':
                $count_query .= " AND i.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                break;
        }
    }
    
    $count_stmt = $conn->prepare($count_query);
    
    if ($count_stmt === false) {
        throw new Exception("Error en la preparación de la consulta de conteo: " . $conn->error);
    }
    
    // Vincular parámetros para la consulta de conteo
    if (!empty($search_term) && !empty($filter)) {
        $count_stmt->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $filter);
    } elseif (!empty($search_term)) {
        $count_stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
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

// Fecha actual
$fecha_actual = date('d-m-Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Resueltos - MoveSync</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <style>
        /* Estilos generales */
        :root {
            --primary-color: #3a7bd5;
            --secondary-color: #00d2ff;
            --accent-color: #ffc107;
            --dark-color: #2c3e50;
            --light-color: #f8f9fa;
            --success-color: #28a745;
            --warning-color: #fd7e14;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
        }

        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            color: var(--gray-800);
        }
        
        .content-wrapper {
            flex: 1;
            padding: 25px;
        }
        
        /* Header y Navigation */
        .navbar {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            padding: 12px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            color: white !important;
        }

        .navbar-brand img {
            height: 36px;
            margin-right: 10px;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover, 
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white !important;
        }

        .navbar-nav .nav-link i {
            margin-right: 6px;
        }

        /* Tarjetas y Contenedores */
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 12px;
            border: none;
            margin-bottom: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 16px 20px;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        .section-title {
            position: relative;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.5rem;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: var(--gray-300);
            margin-left: 15px;
        }

        /* Tablas */
        .table th {
            font-weight: 600;
            background-color: var(--gray-100);
            border-top: none;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: var(--gray-700);
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
            border-color: var(--gray-200);
            padding: 12px 16px;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .table thead th {
            border-bottom: 2px solid var(--gray-300);
        }

        /* Badges y Estados */
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 6px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pendiente {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }

        .status-resuelto {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }

        /* Botones y Enlaces */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #336db5, #00b8d9);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Específico para gestión de reportes */
        .page-header {
            background: linear-gradient(135deg, #007bff, #00d2ff);
            color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: linear-gradient(transparent, rgba(255, 255, 255, 0.1));
            transform: rotate(30deg);
            pointer-events: none;
        }

        .search-container {
            position: relative;
        }

        .search-container i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: var(--gray-500);
        }

        .search-input {
            padding-left: 40px;
            border-radius: 25px;
            border: 1px solid var(--gray-300);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
            height: 45px;
            width: 100%;
        }

        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(58, 123, 213, 0.25);
            border-color: var(--primary-color);
        }

        .stat-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 18px;
        }

        .stat-pendiente {
            background-color: var(--warning-color);
        }

        .stat-resuelto {
            background-color: var(--success-color);
        }

        .stat-tiempo {
            background-color: var(--info-color);
        }

        .category-traffic {
            background-color: #FF5722;
        }

        .category-vehicle {
            background-color: #009688;
        }

        .category-service {
            background-color: #673AB7;
        }

        .category-other {
            background-color: #607D8B;
        }

        .report-stats {
            margin-bottom: 20px;
        }

        .pagination .page-link {
            border-color: var(--gray-200);
            color: var(--gray-700);
            margin: 0 3px;
            border-radius: 5px;
            font-weight: 500;
            padding: 8px 14px;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Descripción truncada */
        .description-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Footer */
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 40px 0 20px;
            margin-top: 40px;
        }

        footer h5 {
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        footer h5::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 40px;
            height: 2px;
            background-color: var(--accent-color);
        }

        footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--accent-color);
            text-decoration: none;
        }

        .footer-contact i {
            margin-right: 10px;
            color: var(--accent-color);
        }

        .footer-bottom {
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .social-icons a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background-color: var(--accent-color);
            transform: translateY(-3px);
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        /* KPI Cards */
        .kpi-card {
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary-color);
        }

        .kpi-card .value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1.2;
        }

        .kpi-card .label {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-top: 5px;
        }

        .kpi-card .icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            opacity: 0.7;
            margin-bottom: 10px;
        }

        .kpi-card.secondary {
            border-left-color: var(--success-color);
        }
        
        .kpi-card.secondary .value,
        .kpi-card.secondary .icon {
            color: var(--success-color);
        }
        
        .kpi-card.tertiary {
            border-left-color: var(--info-color);
        }
        
        .kpi-card.tertiary .value,
        .kpi-card.tertiary .icon {
            color: var(--info-color);
        }
    </style>
</head>
<body>

<!-- Header con Navegación -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="fas fa-sync-alt"></i> MoveSync Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Panel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_users.php"><i class="fas fa-users"></i> Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_reportes.php"><i class="fas fa-flag"></i> Incidencias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="admin_resueltos.php"><i class="fas fa-check-circle"></i> Resueltos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_settings.php"><i class="fas fa-cog"></i> Configuración</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../php/logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid content-wrapper">
    <!-- Cabecera de Página -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2><i class="fas fa-check-circle me-2"></i>Reportes Resueltos</h2>
                <p class="mb-0">Análisis y seguimiento de incidencias resueltas en el sistema. Fecha actual: <?php echo $fecha_actual; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="admin_dashboard.php" class="btn btn-outline-light"><i class="fas fa-arrow-left me-2"></i>Volver al Panel</a>
            </div>
        </div>
    </div>

    <!-- Indicadores KPI principales -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="kpi-card">
                <div class="icon"><i class="fas fa-check-double"></i></div>
                <div class="value"><?php echo number_format($stats['total_resueltos'] ?? 0); ?></div>
                <div class="label">Total Incidencias Resueltas</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card secondary">
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                <div class="value"><?php echo date('d/m/Y', strtotime($stats['fecha_mas_reciente'] ?? 'now')); ?></div>
                <div class="label">Última Resolución</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card tertiary">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="value"><?php echo $avg_resolution ?? 0; ?> h</div>
                <div class="label">Tiempo Medio de Resolución</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar con filtros y estadísticas -->
        <div class="col-lg-3">
            <!-- Tarjeta de búsqueda -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-search me-2"></i>Buscar Resueltos</h5>
                    <form method="get" action="" class="mb-3">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search" name="search" class="form-control search-input" 
                                   placeholder="Descripción, origen o destino" 
                                   value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
                        </div>
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                        <input type="hidden" name="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>">
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            <?php if (!empty($search_term)): ?>
                                <a href="?filter=<?php echo urlencode($filter); ?>&date_filter=<?php echo urlencode($date_filter); ?>" class="btn btn-outline-secondary">Limpiar búsqueda</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
                        <!-- Filtro por categoría -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-filter me-2"></i>Filtrar por Categoría</h5>
                    <form method="get" action="" class="mb-3">
                        <div class="form-group">
                            <select name="filter" class="form-select" onchange="this.form.submit()">
                                <option value="">Seleccionar Categoría</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $filter === $category ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
                        <input type="hidden" name="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>">
                    </form>
                </div>
            </div>

            <!-- Tarjeta de estadísticas -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-pie me-2"></i>Estadísticas de Incidencias</h5>
                    <div class="report-stats">
                        <?php 
                        foreach ($category_stats as $category => $total): 
                        ?>
                            <div class="stat-card bg-white shadow-sm">
                                <div class="stat-icon category-traffic">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($category); ?></h6>
                                    <div class="h4 mb-0 fw-bold"><?php echo htmlspecialchars($total); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla principal de reportes -->
        <div class="col-lg-9">
            <div class="card animate-fade-in">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Lista de Reportes Resueltos
                            <?php if(!empty($search_term)): ?>
                                <span class="text-muted fs-6 ms-2">
                                    Resultados para: "<?php echo htmlspecialchars($search_term); ?>"
                                </span>
                            <?php endif; ?>
                        </h5>
                        
                        <span class="badge bg-primary"><?php echo $total_records; ?> reportes</span>
                    </div>
                    
                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>RUTA</th>
                                        <th>DESCRIPCIÓN</th>
                                        <th>CATEGORÍA</th>
                                        <th>ESTADO</th>
                                        <th>FECHA</th>
                                        <th class="text-center">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo htmlspecialchars($row['ID_Incidencia']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['Origen'] . ' - ' . $row['Destino']); ?></td>
                                            <td class="description-cell" title="<?php echo htmlspecialchars($row['Descripcion']); ?>">
                                                <?php echo htmlspecialchars($row['Descripcion']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                                            <td>
                                                <span class="status-badge status-resuelto">
                                                    <?php echo htmlspecialchars($row['Estado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <a href="admin_delete_report.php?id=<?php echo $row['ID_Incidencia']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('¿Estás seguro de eliminar este reporte?');" 
                                                       title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginación mejorada -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Paginación de reportes" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($current_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $current_page-1; ?>&search=<?php echo urlencode($search_term); ?>&filter=<?php echo urlencode($filter); ?>&date_filter=<?php echo urlencode($date_filter); ?>" aria-label="Anterior">
                                                <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php
                                    // Mostrar un número limitado de páginas
                                    $start_page = max(1, $current_page - 2);
                                    $end_page = min($total_pages, $current_page + 2);
                                    
                                    if ($start_page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search_term) . '&filter=' . urlencode($filter) . '&date_filter=' . urlencode($date_filter) . '">1</a></li>';
                                        if ($start_page > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    
                                    for ($i = $start_page; $i <= $end_page; $i++) {
                                        echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">';
                                        echo '<a class="page-link" href="?page=' . $i . '&search=' . urlencode($search_term) . '&filter=' . urlencode($filter) . '&date_filter=' . urlencode($date_filter) . '">' . $i . '</a>';
                                        echo '</li>';
                                    }
                                    
                                    if ($end_page < $total_pages) {
                                        if ($end_page < $total_pages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '&search=' . urlencode($search_term) . '&filter=' . urlencode($filter) . '&date_filter=' . urlencode($date_filter) . '">' . $total_pages . '</a></li>';
                                    }
                                    ?>
                                    
                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $current_page+1; ?>&search=<?php echo urlencode($search_term); ?>&filter=<?php echo urlencode($filter); ?>&date_filter=<?php echo urlencode($date_filter); ?>" aria-label="Siguiente">
                                                <span aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron reportes resueltos
                            <?php if (!empty($search_term)): ?>
                                con los criterios de búsqueda proporcionados.
                                <div class="mt-3">
                                    <a href="admin_resueltos.php" class="btn btn-sm btn-outline-primary">Ver todos los reportes</a>
                                </div>
                            <?php else: ?>
                                registrados en el sistema.
                                <div class="mt-3">
                                    <a href="admin_add_report.php" class="btn btn-sm btn-primary">Crear nuevo reporte</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>MoveSync</h5>
                <p>Sistema de gestión de incidencias para el transporte público. Mejorando la movilidad urbana a través de la colaboración ciudadana.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Enlaces Rápidos</h5>
                <ul class="list-unstyled">
                    <li><a href="admin_dashboard.php"><i class="fas fa-angle-right me-2"></i>Panel de Control</a></li>
                    <li><a href="admin_users.php"><i class="fas fa-angle-right me-2"></i>Gestión de Usuarios</a></li>
                    <li><a href="admin_reportes.php"><i class="fas fa-angle-right me-2"></i>Gestión de Incidencias</a></li>
                    <li><a href="admin_rutas.php"><i class="fas fa-angle-right me-2"></i>Gestión de Rutas</a></li>
                    <li><a href="admin_settings.php"><i class="fas fa-angle-right me-2"></i>Configuración</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Contacto</h5>
                <div class="footer-contact">
                    <p><i class="fas fa-map-marker-alt"></i> Calle Rio Cares, Pola de Laviana</p>
                    <p><i class="fas fa-phone"></i> +34 644 01 59 22</p>
                    <p><i class="fas fa-envelope"></i> info@movesync.com</p>
                    <p><i class="fas fa-clock"></i> Lun-Vie: 9:00 - 18:00</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <p class="mb-0">© <?php echo date('Y'); ?> MoveSync. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Animación de elementos al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.card, .section-title, .page-header');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('animate-fade-in');
            }, index * 100);
        });
    });
</script>
</body>
</html>
