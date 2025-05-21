<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

$sqlUsuarios = "SELECT COUNT(*) AS total FROM Usuarios";
$resultUsuarios = $conn->query($sqlUsuarios);
$totalUsuarios = $resultUsuarios->fetch_assoc()['total'];

$sqlReportes = "SELECT COUNT(*) AS total FROM Incidencias WHERE estado='Pendiente'";
$resultReportes = $conn->query($sqlReportes);
$totalReportes = $resultReportes->fetch_assoc()['total'];

$sqlReportesResueltos = "SELECT COUNT(*) AS total FROM Incidencias WHERE estado='Resuelto'";
$resultReportesResueltos = $conn->query($sqlReportesResueltos);
$totalReportesResueltos = $resultReportesResueltos->fetch_assoc()['total'];

$sqlUltimasIncidencias = "SELECT i.ID_Incidencia, i.Descripcion, i.categoria, i.Estado, i.fecha, u.nombre 
                          FROM Incidencias i 
                          LEFT JOIN Usuarios u ON i.ID_Usuario = u.ID_Usuario
                          ORDER BY i.fecha DESC LIMIT 5";
$resultUltimasIncidencias = $conn->query($sqlUltimasIncidencias);

$sqlCategorias = "SELECT categoria, COUNT(*) as total FROM Incidencias GROUP BY categoria";
$resultCategorias = $conn->query($sqlCategorias);
$categorias = [];
$totalCategoria = [];
if ($resultCategorias) {
    while ($row = $resultCategorias->fetch_assoc()) {
        $categorias[] = $row['categoria'];
        $totalCategoria[] = $row['total'];
    }
}

$sqlRutas = "SELECT r.Nombre, COUNT(i.ID_Incidencia) as total_incidencias 
            FROM Rutas r 
            LEFT JOIN Incidencias i ON r.ID_Ruta = i.ID_Ruta 
            GROUP BY r.ID_Ruta 
            ORDER BY total_incidencias DESC 
            LIMIT 5";
$resultRutas = $conn->query($sqlRutas);

// Obtener usuarios más activos
$sqlUsuariosActivos = "SELECT u.nombre, COUNT(i.ID_Incidencia) as total_reportes 
                      FROM Usuarios u 
                      LEFT JOIN Incidencias i ON u.ID_Usuario = i.ID_Usuario 
                      GROUP BY u.ID_Usuario 
                      ORDER BY total_reportes DESC 
                      LIMIT 5";
$resultUsuariosActivos = $conn->query($sqlUsuariosActivos);

$fecha_actual = date('d-m-Y');
$ultima_conexion = date('d-m-Y H:i:s');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - MoveSync</title> 
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

        /* Admin panel especifico */
        .admin-welcome {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .admin-welcome::before {
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

        .admin-welcome h2 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .admin-welcome p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        .stat-card {
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border-radius: 12px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 28px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .stat-icon.users {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        }

        .stat-icon.pending {
            background: linear-gradient(135deg, #fd7e14, #ffc107);
        }

        .stat-icon.resolved {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 15px 0;
        }

        .stat-card p {
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 20px;
        }

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

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .quick-access-section {
            margin-bottom: 30px;
        }

        .access-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            height: 100%;
            background-color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        
        .access-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .access-card .icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, rgba(58, 123, 213, 0.1), rgba(0, 210, 255, 0.1));
        }
        
        .access-card i {
            font-size: 28px;
            color: var(--primary-color);
        }
        
        .access-card h5 {
            margin-top: 15px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .access-card p {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-bottom: 20px;
        }

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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>
<body>

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
                    <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i> Panel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_users.php"><i class="fas fa-users"></i> Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_reportes.php"><i class="fas fa-flag"></i> Incidencias</a>
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

<div class="container content-wrapper">
    <!-- Tarjeta de Bienvenida Admin -->
    <div class="admin-welcome">
        <div class="row align-items-center">
            <div class="col-md-9">
                <h2><i class="fas fa-user-shield me-2"></i>Panel de Administración</h2>
                <p>Bienvenido al sistema de gestión de MoveSync. Hoy es <?php echo $fecha_actual; ?></p>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card stat-card h-100">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?php echo $totalUsuarios; ?></h3>
                <p>Usuarios Registrados</p>
                <a href="admin_users.php" class="btn btn-primary">Gestionar Usuarios</a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card stat-card h-100">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <h3><?php echo $totalReportes; ?></h3>
                <p>Reportes Pendientes</p>
                <a href="admin_reportes.php" class="btn btn-warning">Ver Reportes</a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card stat-card h-100">
                <div class="stat-icon resolved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3><?php echo $totalReportesResueltos; ?></h3>
                <p>Reportes Resueltos</p>
                <a href="admin_resueltos.php" class="btn btn-success">Ver Resueltos</a>
            </div>
        </div>
    </div>

    <h4 class="section-title"><i class="fas fa-bolt me-2"></i>Accesos Rápidos</h4>
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="access-card">
                <div class="icon-wrapper">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h5>Nuevo Usuario</h5>
                <p>Crear cuenta para un nuevo usuario en el sistema</p>
                <a href="admin_add_user.php" class="btn btn-primary w-100">Crear Usuario</a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="access-card">
                <div class="icon-wrapper">
                    <i class="fas fa-route"></i>
                </div>
                <h5>Nueva Ruta</h5>
                <p>Añadir una nueva ruta al sistema de transporte</p>
                <a href="admin_add_route.php" class="btn btn-outline-primary w-100">Crear Ruta</a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="access-card">
                <div class="icon-wrapper">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h5>Informes</h5>
                <p>Ver y generar informes detallados del sistema</p>
                <a href="admin_reports.php" class="btn btn-outline-primary w-100">Ver Informes</a>
            </div>
        </div>
      <div class="col-md-3 col-sm-6">
    <div class="access-card">
        <div class="icon-wrapper">
            <i class="fas fa-bell"></i>
        </div>
        <h5>Notificaciones</h5>
        <p>Gestionar alertas y mensajes del sistema</p>
        <a href="admin_notifications.php" class="btn btn-outline-primary w-100">Gestionar</a>
    </div>
    </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <h4 class="section-title"><i class="fas fa-chart-pie me-2"></i>Estadísticas</h4>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Incidencias por Categoría</h5>
                    <div class="chart-container">
                        <canvas id="categoriaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <h4 class="section-title"><i class="fas fa-route me-2"></i>Rutas Principales</h4>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Rutas con Más Incidencias</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>RUTA</th>
                                    <th>INCIDENCIAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resultRutas && $resultRutas->num_rows > 0): ?>
                                    <?php while ($ruta = $resultRutas->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-route text-primary me-2"></i>
                                                    <strong><?php echo htmlspecialchars($ruta['Nombre']); ?></strong>
                                                </div>
                                            </td>
                                            <td><?php echo $ruta['total_incidencias']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No hay datos disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <h4 class="section-title"><i class="fas fa-flag me-2"></i>Incidencias Recientes</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>DESCRIPCIÓN</th>
                                    <th>CATEGORÍA</th>
                                    <th>USUARIO</th>
                                    <th>ESTADO</th>
                                    <th>FECHA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resultUltimasIncidencias && $resultUltimasIncidencias->num_rows > 0): ?>
                                    <?php while ($incidencia = $resultUltimasIncidencias->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $incidencia['ID_Incidencia']; ?></td>
                                            <td><?php echo htmlspecialchars(substr($incidencia['Descripcion'], 0, 30)) . (strlen($incidencia['Descripcion']) > 30 ? '...' : ''); ?></td>
                                            <td><?php echo htmlspecialchars($incidencia['categoria']); ?></td>
                                            <td><?php echo htmlspecialchars($incidencia['nombre'] ?? 'Anónimo'); ?></td>
                                            <td>
                                                <?php 
                                                $statusClass = '';
                                                switch($incidencia['Estado']) {
                                                    case 'Pendiente':
                                                        $statusClass = 'status-pending';
                                                        break;
                                                    case 'Resuelto':
                                                        $statusClass = 'status-resolved';
                                                        break;
                                                    case 'En Proceso':
                                                        $statusClass = 'status-processing';
                                                        break;
                                                }
                                                ?>
                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($incidencia['Estado']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($incidencia['fecha']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay incidencias recientes</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <a href="admin_reportes.php" class="btn btn-primary">Ver todas las incidencias</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
                            <h4 class="section-title"><i class="fas fa-users me-2"></i>Usuarios Activos</h4>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Usuarios con Más Reportes</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>USUARIO</th>
                                    <th>REPORTES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resultUsuariosActivos && $resultUsuariosActivos->num_rows > 0): ?>
                                    <?php while ($usuario = $resultUsuariosActivos->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    <?php echo htmlspecialchars($usuario['nombre']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo $usuario['total_reportes']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No hay datos disponibles</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <a href="admin_users.php" class="btn btn-primary">Ver todos los usuarios</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const categoriaCtx = document.getElementById('categoriaChart').getContext('2d');
    const categoriaChart = new Chart(categoriaCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($categorias); ?>,
            datasets: [{
                data: <?php echo json_encode($totalCategoria); ?>,
                backgroundColor: [
                    'rgba(58, 123, 213, 0.8)',
                    'rgba(0, 210, 255, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
                ],
                borderColor: [
                    'rgba(58, 123, 213, 1)',
                    'rgba(0, 210, 255, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleFont: {
                        family: 'Poppins',
                        size: 14
                    },
                    bodyFont: {
                        family: 'Poppins',
                        size: 13
                    },
                    padding: 10,
                    cornerRadius: 6,
                    displayColors: false
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.card, .section-title, .admin-welcome');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('animate-fade-in');
            }, index * 100);
        });
    });
</script>
</body>
</html>