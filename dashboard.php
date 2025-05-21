<!DOCTYPE html>
<html lang="es">
<head>
<?php
$usuario = $usuario ?? ['nombre' => 'Usuario'];
$ultima_conexion = $ultima_conexion ?? 'Desconocida';
$nivel = $nivel ?? 1;
$puntosActuales = $puntosActuales ?? 0;
$porcentajeNivel = $porcentajeNivel ?? 0;
$puntosParaSiguienteNivel = $puntosParaSiguienteNivel ?? 100;
$resultRutas = $resultRutas ?? null;
$resultIncidencias = $resultIncidencias ?? null;
$mensaje = $mensaje ?? '';
?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MoveSync</title> 
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

        /* Tarjetas de Acceso Rápido */
        .quick-access-section {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
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

        /* Componentes específicos */
        .user-welcome {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .user-welcome::before {
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

        .user-welcome h2 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .user-welcome p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        .profile-image {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
        }

        .stats-card {
            padding: 25px;
            border-left: 5px solid var(--primary-color);
        }

        .stats-card h5 {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .progress {
            height: 12px;
            border-radius: 6px;
            background-color: rgba(0, 0, 0, 0.05);
            margin-bottom: 10px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 6px;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--gray-700);
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

        /* Media Queries */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 15px;
            }
            
            .user-welcome {
                text-align: center;
                padding: 20px;
            }
            
            .profile-image {
                margin-bottom: 15px;
            }
            
            .section-title {
                font-size: 1.3rem;
            }
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
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

        /* Tarjeta de nivel y puntos */
        .level-card {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: white;
            padding: 25px;
        }

        .level-card::after {
            content: "";
            position: absolute;
            bottom: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="rgba(255,255,255,0.1)"><path d="M12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28z"/></svg>');
            background-repeat: no-repeat;
            background-position: bottom right;
            background-size: 150px;
            opacity: 0.3;
            transform: rotate(15deg);
        }

        .level-badge {
            display: inline-block;
            padding: 5px 12px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }

        .points-display {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .level-progress {
            height: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
            margin: 15px 0;
        }

        .level-bar {
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
        }

        .level-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        /* Estilos para el resumen diario */
.summary-card {
    padding: 25px;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    border: none;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.summary-card::after {
    content: "";
    position: absolute;
    bottom: -30px;
    right: -30px;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, transparent, rgba(58, 123, 213, 0.1));
    border-radius: 50%;
    z-index: 0;
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 24px;
    box-shadow: 0 5px 15px rgba(58, 123, 213, 0.3);
}

.summary-card h5 {
    font-weight: 600;
    margin-bottom: 15px;
    color: var(--dark-color);
    position: relative;
    z-index: 1;
}

.summary-card p {
    color: var(--gray-600);
    margin-bottom: 0;
    position: relative;
    z-index: 1;
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
                    <a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i>Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="php/routes.php"><i class="fas fa-route"></i> Rutas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="php/report.php"><i class="fas fa-flag"></i> Incidencias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="php/LoyaltyPoints.php"><i class="fas fa-star"></i> Puntos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="php/settings.php"><i class="fas fa-cog"></i> Configuración</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container content-wrapper">
    <!-- Tarjeta de Bienvenida -->
    <div class="user-welcome">
        <div class="row align-items-center">
            <div class="col-md-2 col-sm-12 text-center">
                <img src="assets/user_profile.jpg" alt="Perfil" class="profile-image">
            </div>
            <div class="col-md-7 col-sm-12">
                <h2>¡Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>!</h2>
                <p><i class="far fa-clock me-2"></i>Última conexión: <?php echo $ultima_conexion; ?></p>

            </div>
        </div>
    </div>

    <!-- Sección de Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h4 class="section-title"><i class="fas fa-chart-line me-2"></i>Estadísticas</h4>
            <div class="level-card">
                <span class="level-badge">Nivel <?php echo $nivel; ?></span>
                <h5>Puntos de Fidelización</h5>
                <div class="points-display"><?php echo $puntosActuales; ?> pts</div>
                <div class="level-progress">
                    <div class="level-bar" style="width: <?php echo $porcentajeNivel; ?>%;"></div>
                </div>
                <div class="level-info">
                    <span>Progreso: <?php echo $porcentajeNivel; ?>%</span>
                    <span>Siguiente nivel: <?php echo $puntosParaSiguienteNivel; ?> pts</span>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <h4 class="section-title"><i class="fas fa-trophy me-2"></i>Resumen</h4>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card stats-card h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-flag fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Incidencias Reportadas</h6>
                                <h4 class="mb-0">12</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card stats-card h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-route fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Rutas Favoritas</h6>
                                <h4 class="mb-0">5</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card stats-card h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-medal fa-2x text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Posición Ranking</h6>
                                <h4 class="mb-0">#32</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card stats-card h-100">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-calendar-check fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Días Activo</h6>
                                <h4 class="mb-0">45</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Accesos Rápidos -->
    <h4 class="section-title"><i class="fas fa-bolt me-2"></i>Accesos Rápidos</h4>
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="access-card">
                <div class="icon-wrapper">
                    <i class="fas fa-route text-primary"></i>
                </div>
                <h5>Consultar Rutas</h5>
                <p>Explora todas las rutas disponibles para tu viaje</p>
                <a href="php/routes.php" class="btn btn-primary w-100">Ver Rutas</a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="access-card">
                <div class="icon-wrapper">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                </div>
                <h5>Reportar Incidencia</h5>
                <p>Informa sobre cualquier problema en tu trayecto</p>
                <a href="php/report.php" class="btn btn-outline-warning w-100">Reportar</a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="access-card">
                <div class="icon-wrapper">
                    <i class="fas fa-star text-warning"></i>
                </div>
                <h5>Mis Puntos</h5>
                <p>Consulta y canjea tus puntos acumulados</p>
                <a href="php/LoyaltyPoints.php" class="btn btn-outline-primary w-100">Ver Puntos</a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="access-card">
                <div class="icon-wrapper">
                    <i class="fas fa-cog text-secondary"></i>
                </div>
                <h5>Configuración</h5>
                <p>Ajusta tus preferencias y personaliza tu cuenta</p>
                <a href="php/settings.php" class="btn btn-outline-secondary w-100">Configurar</a>
            </div>
        </div>
    </div>

   <!-- Rutas Habituales -->
<div class="row mb-4">
    <div class="col-md-12">
        <h4 class="section-title"><i class="fas fa-map-marked-alt me-2"></i>Rutas Habituales</h4>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>NOMBRE</th>
                            <th>ORIGEN</th>
                            <th>DESTINO</th>
                            <th>FRECUENCIA</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-route text-primary me-2"></i>
                                    <strong>Ruta Express Centro</strong>
                                </div>
                            </td>
                            <td>Estación Central</td>
                            <td>Plaza Mayor</td>
                            <td>Cada 10 minutos</td>
                            <td>
                                <div class="btn-group">
                                    <a href="php/report.php?id=1" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-flag"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-route text-primary me-2"></i>
                                    <strong>Ruta Circular Norte</strong>
                                </div>
                            </td>
                            <td>Terminal Norte</td>
                            <td>Terminal Sur</td>
                            <td>Cada 15 minutos</td>
                            <td>
                                <div class="btn-group">
                                    <a href="php/report.php?id=2" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-flag"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-route text-primary me-2"></i>
                                    <strong>Línea Rápida Sur</strong>
                                </div>
                            </td>
                            <td>Estación Sur</td>
                            <td>Zona Residencial</td>
                            <td>Cada 12 minutos</td>
                            <td>
                                <div class="btn-group">
                                    <a href="php/report.php?id=3" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-flag"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-route text-primary me-2"></i>
                                    <strong>Ruta Universitaria</strong>
                                </div>
                            </td>
                            <td>Campus Universitario</td>
                            <td>Centro Estudiantil</td>
                            <td>Cada 5 minutos</td>
                            <td>
                                <div class="btn-group">
                                    <a href="php/report.php?id=6" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-flag"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-route text-primary me-2"></i>
                                    <strong>Ruta Nocturna</strong>
                                </div>
                            </td>
                            <td>Estación Nocturna</td>
                            <td>Zona de Entretenimiento</td>
                            <td>Cada 30 minutos</td>
                            <td>
                                <div class="btn-group">
                                    <a href="php/report.php?id=9" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-flag"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="php/routes.php" class="btn btn-sm btn-outline-primary">Ver todas las rutas</a>
            </div>
        </div>
    </div>
</div>

            <div class="row g-4">
            <div class="col-md-4">
                <div class="card summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5>Actividad Diaria</h5>
                    <p>Has completado 3 de 5 tareas asignadas para hoy.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5>Último Acceso</h5>
                    <p>19 de mayo, 10:43 AM desde IP 192.168.1.45</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h5>Consejo del Día</h5>
                    <p>Organiza tus rutas por prioridad para optimizar tus desplazamientos diarios.</p>
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
                    <li><a href="php/report.php"><i class="fas fa-angle-right me-2"></i>Reportar Incidencia</a></li>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Activar los elementos con animación
    const animateElements = document.querySelectorAll('.animate-fade-in');
    animateElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = 1;
            element.style.transform = 'translateY(0)';
        }, 100 * (index + 1));
    });
    
    // Auto ocultar alertas después de 5 segundos
    setTimeout(function() {
        var alertList = document.querySelectorAll('.alert')
        alertList.forEach(function (alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
</body>
</html>