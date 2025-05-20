<?php
session_start();

// Conexión a la base de datos
include(__DIR__ . '/../config/db_config.php'); 

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

$usuario_id = $_SESSION['usuario'];

// Función para manejar errores
function handleError($message) {
    error_log("Error en report.php: " . $message);
    return "Ha ocurrido un error. Por favor, intenta de nuevo más tarde.";
}

// Obtener el nombre del usuario para personalizar la página
try {
    $userSql = "SELECT nombre FROM Usuarios WHERE id_usuario = ?";
    $userStmt = $conn->prepare($userSql);
    if (!$userStmt) throw new Exception($conn->error);
    
    $userStmt->bind_param("i", $usuario_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result(); 
    if ($userResult && $userResult->num_rows > 0) {
        $userData = $userResult->fetch_assoc();
        $nombre_usuario = $userData['nombre'];
    } else {
        $nombre_usuario = "Usuario";
    }
} catch (Exception $e) {
    $errorMsg = handleError($e->getMessage());
    $nombre_usuario = "Usuario";
}

// Obtener las rutas desde la base de datos para el formulario de reporte
try {
    $sql = "SELECT ID_Ruta, Origen, Destino FROM Rutas LIMIT 10";
    $result = $conn->query($sql);
    if (!$result) throw new Exception($conn->error);
} catch (Exception $e) {
    $errorMsg = handleError($e->getMessage());
    $result = false;
}

// Mensaje de éxito o error
$message = '';
$messageType = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    $messageType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
}

// Obtener resumen estadístico de incidencias del usuario
try {
    $statsSql = "SELECT 
                    COUNT(*) as total_incidencias,
                    SUM(CASE WHEN Estado = 'Resuelto' THEN 1 ELSE 0 END) as resueltas,
                    SUM(CASE WHEN Estado = 'En Proceso' THEN 1 ELSE 0 END) as en_proceso,
                    SUM(CASE WHEN Estado = 'Pendiente' OR Estado IS NULL THEN 1 ELSE 0 END) as pendientes
                 FROM Incidencias 
                 WHERE Id_Usuario = ?";
    $statsStmt = $conn->prepare($statsSql);
    if (!$statsStmt) throw new Exception($conn->error);
    
    $statsStmt->bind_param("i", $usuario_id);
    $statsStmt->execute();
    $statsResult = $statsStmt->get_result();
    
    if ($statsResult && $statsResult->num_rows > 0) {
        $stats = $statsResult->fetch_assoc();
    } else {
        $stats = [
            'total_incidencias' => 0,
            'resueltas' => 0,
            'en_proceso' => 0,
            'pendientes' => 0
        ];
    }
} catch (Exception $e) {
    $errorMsg = handleError($e->getMessage());
    $stats = [
        'total_incidencias' => 0,
        'resueltas' => 0,
        'en_proceso' => 0,
        'pendientes' => 0
    ];
}

// Obtener las 3 incidencias más recientes
try {
    $recentSql = "SELECT i.ID_Incidencia, i.Fecha_Reporte, i.Descripcion, i.Categoria,
                     COALESCE(i.Estado, 'Pendiente') AS Estado, 
                     r.Origen, r.Destino
                     FROM Incidencias i
                     JOIN Rutas r ON i.ID_Ruta = r.ID_Ruta
                     WHERE i.Id_Usuario = ?
                     ORDER BY i.Fecha_Reporte DESC LIMIT 3";
    $recentStmt = $conn->prepare($recentSql);
    if (!$recentStmt) throw new Exception($conn->error);
    
    $recentStmt->bind_param("i", $usuario_id);
    $recentStmt->execute();
    $recentResult = $recentStmt->get_result();
} catch (Exception $e) {
    $errorMsg = handleError($e->getMessage());
    $recentResult = false;
}

// Obtener categorías de incidencias más comunes
try {
    $categoriasSql = "SELECT Categoria, COUNT(*) as total 
                      FROM Incidencias 
                      GROUP BY Categoria 
                      ORDER BY total DESC 
                      LIMIT 5";
    $categoriasResult = $conn->query($categoriasSql);
    if (!$categoriasResult) throw new Exception($conn->error);
} catch (Exception $e) {
    $errorMsg = handleError($e->getMessage());
    $categoriasResult = false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportar Incidencia - MoveSync</title> 
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

        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-resolved {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-processing {
            background-color: #cce5ff;
            color: #004085;
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

        .btn-warning {
            background: linear-gradient(to right, #ff9a44, #fc6076);
            border: none;
            color: white;
        }

        .btn-warning:hover {
            background: linear-gradient(to right, #f78f3c, #e6546c);
            color: white;
        }

        /* Componentes del formulario */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid var(--gray-300);
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.15);
            border-color: var(--primary-color);
        }

        .form-label {
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        /* Estilos para las incidencias */
        .status-badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-resolved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-processing {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* Tarjeta de bienvenida */
        .page-header {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: white;
            border-radius: 12px;
            padding: 30px;
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

        .page-header h2 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-header p {
            opacity: 0.9;
            margin-bottom: 0;
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

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Dashboard de incidencias mejorado */
        .stat-card {
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            border-left: 4px solid;
            transition: all 0.3s;
            height: 100%;
        }

        .stat-card .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-card .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-total {
            border-color: var(--primary-color);
            background-color: rgba(58, 123, 213, 0.1);
        }

        .stat-resolved {
            border-color: var(--success-color);
            background-color: rgba(40, 167, 69, 0.1);
        }

        .stat-processing {
            border-color: var(--info-color);
            background-color: rgba(13, 202, 240, 0.1);
        }

        .stat-pending {
            border-color: var(--warning-color);
            background-color: rgba(253, 126, 20, 0.1);
        }

        /* Tarjetas de incidencias recientes */
        .incident-card {
            position: relative;
            margin-bottom: 15px;
            border-left: 4px solid;
            transition: all 0.3s;
        }

        .incident-pending {
            border-color: var(--warning-color);
        }

        .incident-resolved {
            border-color: var(--success-color);
        }

        .incident-processing {
            border-color: var(--info-color);
        }

        .incident-card .card-body {
            padding: 15px;
        }

        .incident-card .incident-date {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 0.8rem;
            color: var(--gray-600);
        }

        .incident-card .incident-route {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .incident-card .incident-description {
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: var(--gray-700);
        }

        .chart-container {
            height: 200px;
            width: 100%;
        }

        /* Resumen gráfico */
        .dashboard-summary {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .summary-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark-color);
        }

        .category-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            background-color: var(--gray-100);
            transition: all 0.3s;
        }

        .category-item:hover {
            background-color: var(--gray-200);
            transform: translateX(5px);
        }

        .category-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            margin-right: 12px;
        }

        .category-info {
            flex: 1;
        }

        .category-label {
            font-weight: 600;
            margin-bottom: 0;
        }

        .category-count {
            font-size: 0.8rem;
            color: var(--gray-600);
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: var(--gray-300);
            margin-top: 5px;
        }

        /* Timeline mejorada */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 15px;
            width: 2px;
            background-color: var(--gray-300);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -30px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: white;
            border: 2px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .timeline-dot i {
            font-size: 0.6rem;
        }

        .timeline-dot-pending {
            border-color: var(--warning-color);
            color: var(--warning-color);
        }

        .timeline-dot-processing {
            border-color: var(--info-color);
            color: var(--info-color);
        }

        .timeline-dot-resolved {
            border-color: var(--success-color);
            color: var(--success-color);
        }

        .timeline-content {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .timeline-date {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-bottom: 5px;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .timeline-description {
            font-size: 0.85rem;
            color: var(--gray-700);
        }

        /* Instrucciones y consejos */
        .tips-card {
            border-left: 4px solid var(--info-color);
            background-color: rgba(13, 202, 240, 0.05);
        }

        .tips-card i {
            color: var(--info-color);
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 15px;
            }
            
            .page-header {
                text-align: center;
                padding: 20px;
            }
            
            .section-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

<!-- Header con Navegación -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="fas fa-sync-alt"></i> MoveSync
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="routes.php"><i class="fas fa-route"></i> Rutas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="report.php"><i class="fas fa-flag"></i> Incidencias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="LoyaltyPoints.php"><i class="fas fa-star"></i> Puntos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Configuración</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container content-wrapper">
    <!-- Cabecera de página -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2><i class="fas fa-exclamation-triangle me-2"></i>Gestión de Incidencias</h2>
                <p>Reporta y haz seguimiento de tus incidencias en el transporte público</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="#reportForm" class="btn btn-light"><i class="fas fa-plus me-2"></i>Nueva Incidencia</a>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulario de reporte -->
        <div class="col-lg-8">
            <h4 class="section-title" id="reportForm"><i class="fas fa-pen me-2"></i>Reportar Nueva Incidencia</h4>
            <div class="card shadow-lg">
                <div class="card-body">
                    <form action="procesarReporte.php" method="POST" class="needs-validation" onsubmit="return validateForm()" novalidate>
                        <!-- Campo oculto para ID de usuario - IMPORTANTE -->
                        <input type="hidden" name="id_usuario" value="<?php echo $usuario_id; ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="ruta" class="form-label"><i class="fas fa-route me-1 text-primary"></i>Seleccionar Ruta:</label>
                                <select class="form-select" name="ruta" id="ruta" required>
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
                                <div class="invalid-feedback">Por favor, selecciona una ruta.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="categoria" class="form-label"><i class="fas fa-tag me-1 text-warning"></i>Categoría:</label>
                                <select class="form-select" name="categoria" id="categoria" required>
                                    <option value="">Selecciona una categoría</option>
                                    <option value="Retraso">Retraso</option>
                                    <option value="Falta de Servicio">Falta de Servicio</option>
                                    <option value="Problema Técnico">Problema Técnico</option>
                                    <option value="Seguridad">Seguridad</option>
                                    <option value="Limpieza">Limpieza</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <div class="invalid-feedback">Por favor, selecciona una categoría.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="fecha" class="form-label"><i class="fas fa-calendar-alt me-1 text-info"></i>Fecha y Hora:</label>
                            <input type="datetime-local" class="form-control" name="fecha" id="fecha" required>
                            <div class="invalid-feedback">Por favor, selecciona la fecha y hora.</div>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label"><i class="fas fa-comment-alt me-1 text-success"></i>Descripción:</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="4" placeholder="Describe con detalle la incidencia que has experimentado..." required></textarea>
                            <div class="invalid-feedback">Por favor, proporciona una descripción de la incidencia.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Reporte
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i>Restablecer Formulario
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dashboard de incidencias (REEMPLAZO DEL HISTORIAL) -->
            <h4 class="section-title mt-4"><i class="fas fa-chart-bar me-2"></i>Panel de Seguimiento</h4>
            
            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="card stat-card stat-total h-100">
                        <div class="stat-icon text-primary">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-value"><?php echo $stats['total_incidencias']; ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="card stat-card stat-resolved h-100">
                        <div class="stat-icon text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $stats['resueltas']; ?></div>
                        <div class="stat-label">Resueltas</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <div class="card stat-card stat-processing h-100">
                        <div class="stat-icon text-info">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="stat-value"><?php echo $stats['en_proceso']; ?></div>
                        <div class="stat-label">En Proceso</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card stat-card stat-pending h-100">
                        <div class="stat-icon text-warning">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-value"><?php echo $stats['pendientes']; ?></div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico y Actividad Reciente -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Distribución por Estado</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($stats['total_incidencias'] > 0): ?>
                                <div class="chart-container">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <img src="../assets/no-data.svg" alt="Sin datos" style="width: 100px; margin-bottom: 15px; opacity: 0.6;">
                                    <h6>No hay datos disponibles</h6>
                                    <p class="text-muted small">Reporta incidencias para ver estadísticas</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-bell me-2 text-primary"></i>Actividad Reciente</h5>
                            <a href="historialIncidencias.php" class="btn btn-sm btn-outline-primary">Ver todo</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($recentResult && $recentResult->num_rows > 0): ?>
                                <div class="timeline p-3">
                                    <?php while($row = $recentResult->fetch_assoc()): 
                                        $statusClass = '';
                                        $statusIcon = '';
                                        switch($row['Estado']) {
                                            case 'Pendiente':
                                                $statusClass = 'timeline-dot-pending';
                                                $statusIcon = 'hourglass';
                                                break;
                                            case 'En Proceso':
                                                $statusClass = 'timeline-dot-processing';
                                                $statusIcon = 'sync-alt';
                                                break;
                                            case 'Resuelto':
                                                $statusClass = 'timeline-dot-resolved';
                                                $statusIcon = 'check';
                                                break;
                                            default:
                                                $statusClass = 'timeline-dot-pending';
                                                $statusIcon = 'question';
                                        }
                                    ?>
                                        <div class="timeline-item">
                                            <div class="timeline-dot <?php echo $statusClass; ?>">
                                                <i class="fas fa-<?php echo $statusIcon; ?>"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-date">
                                                    <i class="far fa-calendar-alt me-1"></i> 
                                                    <?php echo date('d/m/Y H:i', strtotime($row['Fecha_Reporte'])); ?>
                                                </div>
                                                <div class="timeline-title">
                                                    <span class="badge bg-light text-dark me-2"><?php echo htmlspecialchars($row['Categoria']); ?></span>
                                                    <?php echo htmlspecialchars($row['Origen']) . " → " . htmlspecialchars($row['Destino']); ?>
                                                </div>
                                                <div class="timeline-description">
                                                    <?php echo substr(htmlspecialchars($row['Descripcion']), 0, 80) . (strlen($row['Descripcion']) > 80 ? '...' : ''); ?>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="verIncidencia.php?id=<?php echo $row['ID_Incidencia']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i> Detalles
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <img src="../assets/no-activity.svg" alt="Sin actividad" style="width: 100px; margin-bottom: 15px; opacity: 0.6;">
                                    <h6>No hay actividad reciente</h6>
                                    <p class="text-muted small">Tu actividad aparecerá aquí</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Categorías más comunes -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-tag me-2 text-primary"></i>Categorías de Incidencias Más Comunes</h5>
                </div>
                <div class="card-body">
                    <?php if ($categoriasResult && $categoriasResult->num_rows > 0): ?>
                        <div class="row">
                            <?php 
                            $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                            $icons = ['clock', 'ban', 'wrench', 'shield-alt', 'broom'];
                            $i = 0;
                            $total = 0;
                            $categorias = [];
                            
                            // Calcular el total primero
                            while($row = $categoriasResult->fetch_assoc()) {
                                $total += $row['total'];
                                $categorias[] = $row;
                            }
                            
                            foreach($categorias as $categoria): 
                                $percentage = ($total > 0) ? round(($categoria['total'] / $total) * 100) : 0;
                                $colorIndex = $i % count($colors);
                                $iconIndex = $i % count($icons);
                            ?>
                                <div class="col-md-6 mb-3">
                                    <div class="category-item">
                                        <div class="category-icon bg-<?php echo $colors[$colorIndex]; ?>">
                                            <i class="fas fa-<?php echo $icons[$iconIndex]; ?>"></i>
                                        </div>
                                        <div class="category-info">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="category-label"><?php echo htmlspecialchars($categoria['Categoria']); ?></h6>
                                                <span class="category-count"><?php echo $categoria['total']; ?> incidencias</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-<?php echo $colors[$colorIndex]; ?>" role="progressbar" style="width: <?php echo $percentage; ?>%" 
                                                    aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                $i++;
                                endforeach; 
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <img src="../assets/no-categories.svg" alt="Sin categorías" style="width: 100px; margin-bottom: 15px; opacity: 0.6;">
                            <h6>No hay datos de categorías</h6>
                            <p class="text-muted small">Las categorías más comunes aparecerán aquí cuando haya más incidencias</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Barra lateral con información útil -->
        <div class="col-lg-4">
            <h4 class="section-title"><i class="fas fa-info-circle me-2"></i>Información Útil</h4>

            <!-- Tarjeta de estado del sistema -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="d-inline-block p-3 rounded-circle mb-3" style="background-color: #d4edda;">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <h5>Todos los sistemas operativos</h5>
                    <p class="text-muted">Última actualización: <?php echo date('d/m/Y H:i'); ?></p>
                    <a href="#" class="btn btn-sm btn-outline-success w-100">Ver estado del servicio</a>
                </div>
            </div>

            <!-- Tarjeta de consejos -->
            <div class="card mb-4 tips-card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-lightbulb me-2"></i>Consejos para reportar</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Sé específico en la descripción</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Incluye la hora exacta del incidente</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Menciona el número del vehículo si es posible</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Describe cómo afectó a tu viaje</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Adjunta fotos si resulta relevante</li>
                    </ul>
                </div>
            </div>

            <!-- Tiempo de respuesta -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-clock me-2 text-primary"></i>Tiempo de respuesta</h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="progress flex-grow-1 me-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 85%"></div>
                        </div>
                        <span class="fw-bold">Rápido</span>
                    </div>
                    <p class="card-text small text-muted">
                        Actualmente estamos respondiendo a las incidencias reportadas en aproximadamente 24-48 horas.
                    </p>
                </div>
            </div>

            <!-- Contacto de soporte -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-headset me-2 text-primary"></i>¿Necesitas ayuda?</h5>
                    <p class="card-text">Si tienes dudas sobre cómo reportar una incidencia o quieres consultar el estado de alguna de tus solicitudes, contáctanos:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-phone-alt me-2 text-success"></i>644 01 59 22</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-success"></i>soporte@movesync.com</li>
                        <li><i class="fas fa-comment-dots me-2 text-success"></i>Chat en vivo (L-V: 9h-18h)</li>
                    </ul>
                    <a href="contacto.php" class="btn btn-outline-primary w-100 mt-3">
                        <i class="fas fa-paper-plane me-2"></i>Contactar soporte
                    </a>
                </div>
            </div>

            <!-- Nueva tarjeta: FAQ -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2 text-primary"></i>Preguntas Frecuentes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    ¿Cuánto tiempo tarda en procesarse mi incidencia?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Las incidencias suelen procesarse en un plazo de 24-48 horas hábiles. Las categorías de seguridad tienen prioridad y pueden procesarse más rápido.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    ¿Puedo editar una incidencia ya enviada?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Solo puedes editar incidencias que estén en estado "Pendiente". Una vez que entran en proceso o son resueltas, no se pueden modificar.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faqThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    ¿Cómo obtengo puntos por reportar incidencias?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Recibes 10 puntos por cada incidencia reportada, 5 puntos adicionales si incluyes todos los detalles solicitados y 15 puntos extra si tu incidencia ayuda a mejorar el servicio.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="faq.php" class="btn btn-sm btn-outline-secondary">Ver todas las preguntas</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Sobre MoveSync</h5>
                <p>Plataforma líder en gestión de rutas e incidencias de transporte, ayudando a miles de usuarios diariamente a mejorar su experiencia de viaje.</p>
                <div class="social-icons mt-3">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Enlaces Rápidos</h5>
                <ul class="list-unstyled">
                    <li><a href="php/routes.php"><i class="fas fa-angle-right me-2"></i>Rutas</a></li>
                    <li><a href="php/LoyaltyPoints.php"><i class="fas fa-angle-right me-2"></i>Programa de Fidelización</a></li>
                    <li><a href="php/help.php"><i class="fas fa-angle-right me-2"></i>Ayuda y Soporte</a></li>
                    <li><a href="php/terms.php"><i class="fas fa-angle-right me-2"></i>Términos y Condiciones</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contáctanos</h5>
                <div class="footer-contact">
                    <p><i class="fas fa-map-marker-alt"></i>Calle Rio Cares , Pola de Laviana</p>
                    <p><i class="fas fa-phone"></i> +34 644 01 59 22</p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:info@movesync.com">info@movesync.com</a></p>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <p class="mb-0">© 2025 MoveSync. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Activar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Establecer la fecha actual como valor por defecto
    const fechaInput = document.getElementById('fecha');
    if (fechaInput) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        fechaInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
    
    // Auto ocultar alertas después de 5 segundos
    setTimeout(function() {
        const alertList = document.querySelectorAll('.alert');
        alertList.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Activar los elementos con animación
    const animateElements = document.querySelectorAll('.animate-fade-in');
    animateElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = 1;
            element.style.transform = 'translateY(0)';
        }, 100 * (index + 1));
    });
    
    // Gráfico de estado de incidencias
    const statusChartEl = document.getElementById('statusChart');
    if (statusChartEl) {
        <?php if ($stats['total_incidencias'] > 0): ?>
        new Chart(statusChartEl, {
            type: 'doughnut',
            data: {
                labels: ['Resueltas', 'En Proceso', 'Pendientes'],
                datasets: [{
                    data: [
                        <?php echo $stats['resueltas']; ?>, 
                        <?php echo $stats['en_proceso']; ?>, 
                        <?php echo $stats['pendientes']; ?>
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(13, 202, 240, 0.8)',
                        'rgba(253, 126, 20, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(13, 202, 240, 1)',
                        'rgba(253, 126, 20, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
        <?php endif; ?>
    }
});

function validateForm() {
    const ruta = document.getElementById('ruta');
    const categoria = document.getElementById('categoria');
    const fecha = document.getElementById('fecha');
    const descripcion = document.getElementById('descripcion');
    let isValid = true;
    
    if (ruta.value === "") {
        ruta.classList.add('is-invalid');
        isValid = false;
    } else {
        ruta.classList.remove('is-invalid');
        ruta.classList.add('is-valid');
    }
    
    if (categoria.value === "") {
        categoria.classList.add('is-invalid');
        isValid = false;
    } else {
        categoria.classList.remove('is-invalid');
        categoria.classList.add('is-valid');
    }
    
    if (fecha.value === "") {
        fecha.classList.add('is-invalid');
        isValid = false;
    } else {
        fecha.classList.remove('is-invalid');
        fecha.classList.add('is-valid');
    }
    
    if (descripcion.value.trim() === "") {
        descripcion.classList.add('is-invalid');
        isValid = false;
    } else {
        descripcion.classList.remove('is-invalid');
        descripcion.classList.add('is-valid');
    }
    
    return isValid;
}
</script>
</body>
</html>