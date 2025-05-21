<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Parámetros para filtrado
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'Todas';
$valid_tipos = ['Sistema', 'Incidencia', 'Usuario', 'Todas'];
if (!in_array($tipo, $valid_tipos)) {
    $tipo = 'Todas';
}

// Parámetros para búsqueda
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " AND (mensaje LIKE '%$search%' OR tipo LIKE '%$search%')";
}

// Marcar notificaciones como leídas
if (isset($_GET['marcar_leidas']) && $_GET['marcar_leidas'] == 'true') {
    $updateQuery = "UPDATE Notificaciones SET leida = 1 WHERE leida = 0";
    $conn->query($updateQuery);
    header("Location: admin_notifications.php");
    exit();
}

// Eliminar notificaciones
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = $conn->real_escape_string($_GET['eliminar']);
    $deleteQuery = "DELETE FROM Notificaciones WHERE id_notificacion = $id";
    $conn->query($deleteQuery);
    header("Location: admin_notifications.php");
    exit();
}

// Parámetros para paginación
$recordsPerPage = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Construir la consulta SQL
$sqlCondition = $tipo !== 'Todas' ? "WHERE tipo = '$tipo'" : "WHERE 1=1";
$sqlCondition .= $searchCondition;

$sqlNotificaciones = "SELECT id_notificacion, tipo, mensaje, leida, fecha_creacion, prioridad
                    FROM Notificaciones
                    $sqlCondition
                    ORDER BY fecha_creacion DESC
                    LIMIT $offset, $recordsPerPage";

$resultNotificaciones = $conn->query($sqlNotificaciones);

if (!$resultNotificaciones) {
    $error_msg = "Error en la consulta: " . $conn->error;
    $resultNotificaciones = false;
}

// Contar el total de registros para la paginación
$countQuery = "SELECT COUNT(*) as total FROM Notificaciones $sqlCondition";
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

// Estadísticas de notificaciones
$sqlStats = "SELECT tipo, COUNT(*) as total FROM Notificaciones GROUP BY tipo";
$statsResult = $conn->query($sqlStats);
$stats = [];

if ($statsResult) {
    while ($row = $statsResult->fetch_assoc()) {
        $stats[$row['tipo']] = $row['total'];
    }
}

// Estadísticas por estado (leídas/no leídas)
$sqlEstados = "SELECT leida, COUNT(*) as total FROM Notificaciones GROUP BY leida";
$resultEstados = $conn->query($sqlEstados);
$estados = [0 => 0, 1 => 0]; // Inicializar con valores por defecto

if ($resultEstados) {
    while ($row = $resultEstados->fetch_assoc()) {
        $estados[$row['leida']] = $row['total'];
    }
}

// Fecha actual
$fecha_actual = date('d-m-Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Notificaciones - MoveSync</title> 
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

        /* Específico para gestión de notificaciones */
        .page-header {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
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

        .stat-sistema {
            background-color: var(--primary-color);
        }

        .stat-incidencia {
            background-color: var(--warning-color);
        }

        .stat-usuario {
            background-color: var(--info-color);
        }

        .stat-todas {
            background-color: var(--dark-color);
        }

        .stat-no-leidas {
            background-color: var(--danger-color);
        }

        .stat-leidas {
            background-color: var(--success-color);
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

        /* Notificaciones */
        .notification-item {
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 15px;
            background-color: white;
            border-left: 4px solid;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .notification-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .notification-item.no-leida {
            background-color: rgba(58, 123, 213, 0.05);
        }

        .notification-item.prioridad-alta {
            border-left-color: var(--danger-color);
        }

        .notification-item.prioridad-media {
            border-left-color: var(--warning-color);
        }

        .notification-item.prioridad-baja {
            border-left-color: var(--success-color);
        }

        .notification-tipo {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .notification-tipo.sistema {
            background-color: rgba(58, 123, 213, 0.1);
            color: var(--primary-color);
        }

        .notification-tipo.incidencia {
            background-color: rgba(253, 126, 20, 0.1);
            color: var(--warning-color);
        }

        .notification-tipo.usuario {
            background-color: rgba(13, 202, 240, 0.1);
            color: var(--info-color);
        }

        .notification-fecha {
            color: var(--gray-600);
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .notification-mensaje {
            margin-bottom: 0;
        }

        .notification-actions {
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .notification-actions .btn {
            width: 30px;
            height: 30px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 0.8rem;
            margin-left: 5px;
        }

        .filter-badge {
            background-color: var(--primary-color);
            color: white;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.75rem;
            margin-left: 10px;
        }

        .badge-counter {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* Botón flotante para marcar todas como leídas */
        .floating-action-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .floating-action-btn:hover {
            transform: translateY(-5px) rotate(10deg);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
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
                    <a class="nav-link" href="admin_reports.php"><i class="fas fa-flag"></i> Incidencias</a>
                </li>
                <li class="nav-item position-relative">
                    <a class="nav-link active" href="admin_notifications.php">
                        <i class="fas fa-bell"></i> Notificaciones
                        <?php if($estados[0] > 0): ?>
                        <span class="badge-counter"><?php echo $estados[0]; ?></span>
                        <?php endif; ?>
                    </a>
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
                <h2><i class="fas fa-bell me-2"></i>Centro de Notificaciones</h2>
                <p class="mb-0">Gestiona todas las notificaciones del sistema. Hoy es <?php echo $fecha_actual; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="admin_dashboard.php" class="btn btn-outline-light"><i class="fas fa-arrow-left me-2"></i>Volver</a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar con filtros y estadísticas -->
        <div class="col-lg-3">
            <!-- Tarjeta de búsqueda -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-search me-2"></i>Buscar Notificaciones</h5>
                    <form method="get" action="" class="mb-3">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search" name="search" class="form-control search-input" 
                                   placeholder="Mensaje o tipo" 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        </div>
                        <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>">
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            <?php if (!empty($search)): ?>
                                <a href="?tipo=<?php echo urlencode($tipo); ?>" class="btn btn-outline-secondary">Limpiar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Filtro por tipo -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-filter me-2"></i>Filtrar por Tipo</h5>
                    <form method="get" action="" class="mb-3">
                        <?php if (!empty($search)): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                        <select name="tipo" class="form-select" onchange="this.form.submit()">
                            <option value="Todas" <?php echo $tipo === 'Todas' ? 'selected' : ''; ?>>Todas</option>
                            <option value="Sistema" <?php echo $tipo === 'Sistema' ? 'selected' : ''; ?>>Sistema</option>
                            <option value="Incidencia" <?php echo $tipo === 'Incidencia' ? 'selected' : ''; ?>>Incidencia</option>
                            <option value="Usuario" <?php echo $tipo === 'Usuario' ? 'selected' : ''; ?>>Usuario</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Tarjeta de estadísticas -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-pie me-2"></i>Estadísticas de Notificaciones</h5>
                    <div class="report-stats">
                        <!-- Estadísticas por tipo -->
                        <?php 
                        $bg_colors = [
                            'Sistema' => 'stat-sistema',
                            'Incidencia' => 'stat-incidencia',
                            'Usuario' => 'stat-usuario'
                        ];
                        $icons = [
                            'Sistema' => 'fa-server',
                            'Incidencia' => 'fa-exclamation-triangle',
                            'Usuario' => 'fa-user'
                        ];
                        foreach ($stats as $tipoName => $total): 
                            $bg_class = isset($bg_colors[$tipoName]) ? $bg_colors[$tipoName] : 'bg-secondary';
                            $icon = isset($icons[$tipoName]) ? $icons[$tipoName] : 'fa-bell';
                        ?>
                            <div class="stat-card bg-white shadow-sm">
                                <div class="stat-icon <?php echo $bg_class; ?>">
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($tipoName); ?></h6>
                                    <div class="h4 mb-0 fw-bold"><?php echo htmlspecialchars($total); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="stat-card bg-white shadow-sm">
                            <div class="stat-icon stat-todas">
                                <i class="fas fa-list"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Total</h6>
                                <div class="h4 mb-0 fw-bold"><?php echo array_sum($stats); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de estado de notificaciones -->
            <div class="card animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-eye me-2"></i>Estado de Notificaciones</h5>
                    <div class="report-stats">
                        <div class="stat-card bg-white shadow-sm">
                            <div class="stat-icon stat-no-leidas">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">No Leídas</h6>
                                <div class="h4 mb-0 fw-bold"><?php echo htmlspecialchars($estados[0]); ?></div>
                            </div>
                        </div>
                        <div class="stat-card bg-white shadow-sm">
                            <div class="stat-icon stat-leidas">
                                <i class="fas fa-envelope-open"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Leídas</h6>
                                <div class="h4 mb-0 fw-bold"><?php echo htmlspecialchars($estados[1]); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($estados[0] > 0): ?>
                    <div class="d-grid gap-2 mt-4">
                        <a href="?marcar_leidas=true" class="btn btn-primary">
                            <i class="fas fa-check-double me-2"></i>Marcar todas como leídas
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Lista principal de notificaciones -->
        <div class="col-lg-9">
            <div class="card animate-fade-in">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bell me-2"></i>Notificaciones
                            <?php if($tipo !== 'Todas'): ?>
                                <span class="filter-badge"><?php echo htmlspecialchars($tipo); ?></span>
                            <?php endif; ?>
                            <?php if(!empty($search)): ?>
                                <span class="text-muted fs-6 ms-2">
                                    Resultados para: "<?php echo htmlspecialchars($search); ?>"
                                </span>
                            <?php endif; ?>
                        </h5>
                        
                        <span class="badge bg-primary"><?php echo $totalRecords; ?> notificaciones</span>
                    </div>
                    
                    <?php if ($resultNotificaciones && $resultNotificaciones->num_rows > 0): ?>
                        <div class="notification-list">
                            <?php while ($row = $resultNotificaciones->fetch_assoc()): 
                                // Determinar clases según el tipo y estado
                                $itemClass = $row['leida'] == 0 ? 'notification-item no-leida' : 'notification-item';
                                $itemClass .= ' prioridad-' . strtolower($row['prioridad']);
                                
                                // Determinar iconos y clases para los tipos
                                $tipoClass = 'notification-tipo ' . strtolower($row['tipo']);
                                $fechaFormateada = date('d/m/Y H:i', strtotime($row['fecha_creacion']));
                            ?>
                                <div class="<?php echo $itemClass; ?>">
                                    <div class="notification-actions">
                                        <?php if ($row['leida'] == 0): ?>
                                            <a href="admin_notifications.php?marcar_leida=<?php echo $row['id_notificacion']; ?>" class="btn btn-sm btn-outline-success" title="Marcar como leída">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="admin_notifications.php?eliminar=<?php echo $row['id_notificacion']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Eliminar" 
                                           onclick="return confirm('¿Está seguro de que desea eliminar esta notificación?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    <span class="<?php echo $tipoClass; ?>">
                                        <?php echo htmlspecialchars($row['tipo']); ?>
                                    </span>
                                    <div class="notification-fecha">
                                        <i class="far fa-clock me-1"></i> <?php echo $fechaFormateada; ?>
                                        <?php if ($row['prioridad'] == 'Alta'): ?>
                                            <span class="badge bg-danger ms-2">Prioridad Alta</span>
                                        <?php elseif ($row['prioridad'] == 'Media'): ?>
                                            <span class="badge bg-warning text-dark ms-2">Prioridad Media</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="notification-mensaje">
                                        <?php echo htmlspecialchars($row['mensaje']); ?>
                                    </p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <!-- Paginación mejorada -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Paginación de notificaciones" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&tipo=<?php echo urlencode($tipo); ?>" aria-label="Anterior">
                                                <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php
                                    // Mostrar un número limitado de páginas
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($totalPages, $page + 2);
                                    
                                    if ($startPage > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '&tipo=' . urlencode($tipo) . '">1</a></li>';
                                        if ($startPage > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    
                                    for ($i = $startPage; $i <= $endPage; $i++) {
                                        echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
                                        echo '<a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '&tipo=' . urlencode($tipo) . '">' . $i . '</a>';
                                        echo '</li>';
                                    }
                                    
                                    if ($endPage < $totalPages) {
                                        if ($endPage < $totalPages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&search=' . urlencode($search) . '&tipo=' . urlencode($tipo) . '">' . $totalPages . '</a></li>';
                                    }
                                    ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&tipo=<?php echo urlencode($tipo); ?>" aria-label="Siguiente">
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
                            No se encontraron notificaciones
                            <?php if (!empty($search) || $tipo !== 'Todas'): ?>
                                con los criterios de filtrado proporcionados.
                                <div class="mt-3">
                                    <a href="admin_notifications.php" class="btn btn-sm btn-outline-primary">Ver todas las notificaciones</a>
                                </div>
                            <?php else: ?>
                                en el sistema.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Tarjeta de configuración de notificaciones -->
            <div class="card animate-fade-in mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-cog me-2"></i>Configuración de Notificaciones</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-envelope-open-text mb-3" style="font-size: 2rem; color: var(--primary-color);"></i>
                                    <h5>Email</h5>
                                    <p class="mb-3">Recibir notificaciones por correo electrónico</p>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input me-2" type="checkbox" id="emailSwitch" checked>
                                        <label class="form-check-label" for="emailSwitch">Activado</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-desktop mb-3" style="font-size: 2rem; color: var(--info-color);"></i>
                                    <h5>Plataforma</h5>
                                    <p class="mb-3">Mostrar notificaciones en la plataforma</p>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input me-2" type="checkbox" id="platformSwitch" checked>
                                        <label class="form-check-label" for="platformSwitch">Activado</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-mobile-alt mb-3" style="font-size: 2rem; color: var(--success-color);"></i>
                                    <h5>SMS</h5>
                                    <p class="mb-3">Recibir notificaciones por mensaje de texto</p>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input me-2" type="checkbox" id="smsSwitch">
                                        <label class="form-check-label" for="smsSwitch">Desactivado</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid gap-2 col-md-6 mx-auto mt-3">
                        <button type="button" class="btn btn-primary" id="saveSettings">
                            <i class="fas fa-save me-2"></i>Guardar Configuración
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botón flotante para marcar todas como leídas (visible solo si hay no leídas) -->
<?php if($estados[0] > 0): ?>
<a href="?marcar_leidas=true" class="floating-action-btn" title="Marcar todas como leídas">
    <i class="fas fa-check-double"></i>
</a>
<?php endif; ?>

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
                    <li><a href="admin_reports.php"><i class="fas fa-angle-right me-2"></i>Gestión de Incidencias</a></li>
                    <li><a href="admin_notifications.php"><i class="fas fa-angle-right me-2"></i>Notificaciones</a></li>
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
        const elements = document.querySelectorAll('.card, .section-title, .page-header, .notification-item');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('animate-fade-in');
            }, index * 100);
        });
        
        // Funcionalidad para los interruptores de configuración
        const emailSwitch = document.getElementById('emailSwitch');
        const platformSwitch = document.getElementById('platformSwitch');
        const smsSwitch = document.getElementById('smsSwitch');
        const saveButton = document.getElementById('saveSettings');
        
        if (emailSwitch && platformSwitch && smsSwitch && saveButton) {
            emailSwitch.addEventListener('change', function() {
                this.nextElementSibling.textContent = this.checked ? 'Activado' : 'Desactivado';
            });
            
            platformSwitch.addEventListener('change', function() {
                this.nextElementSibling.textContent = this.checked ? 'Activado' : 'Desactivado';
            });
            
            smsSwitch.addEventListener('change', function() {
                this.nextElementSibling.textContent = this.checked ? 'Activado' : 'Desactivado';
            });
            
            saveButton.addEventListener('click', function() {
                // Aquí se podría implementar la lógica para guardar la configuración
                alert('Configuración guardada correctamente');
            });
        }
    });
</script>
</body>
</html>