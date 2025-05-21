<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

// Definición de rutas predeterminadas
$rutas = [
    [
        'id' => 1,
        'nombre' => 'Ruta Express Centro',
        'origen' => 'Estación Central',
        'destino' => 'Plaza Mayor',
        'paradas' => ['Estación Central', 'Avenida Principal', 'Calle Comercio', 'Plaza Mayor'],
        'tiempo_total' => '25 minutos',
        'tiempos_parada' => ['0 min', '8 min', '15 min', '25 min'],
        'frecuencia' => 'Cada 10 minutos',
        'color' => '#4CAF50',
        'icono' => 'bus'
    ],
    [
        'id' => 2,
        'nombre' => 'Ruta Circular Norte',
        'origen' => 'Terminal Norte',
        'destino' => 'Terminal Sur',
        'paradas' => ['Terminal Norte', 'Hospital General', 'Parque Industrial', 'Centro Comercial', 'Universidad', 'Terminal Norte'],
        'tiempo_total' => '45 minutos',
        'tiempos_parada' => ['0 min', '10 min', '20 min', '30 min', '35 min', '45 min'],
        'frecuencia' => 'Cada 15 minutos',
        'color' => '#2196F3',
        'icono' => 'bus-alt'
    ],
    [
        'id' => 3,
        'nombre' => 'Línea Rápida Sur',
        'origen' => 'Estación Sur',
        'destino' => 'Zona Residencial',
        'paradas' => ['Estación Sur', 'Avenida del Sur', 'Centro Deportivo', 'Parque Tecnológico', 'Zona Residencial'],
        'tiempo_total' => '35 minutos',
        'tiempos_parada' => ['0 min', '8 min', '18 min', '25 min', '35 min'],
        'frecuencia' => 'Cada 12 minutos',
        'color' => '#FF5722',
        'icono' => 'shuttle-van'
    ],
    [
        'id' => 4,
        'nombre' => 'Conexión Este-Oeste',
        'origen' => 'Terminal Este',
        'destino' => 'Terminal Oeste',
        'paradas' => ['Terminal Este', 'Barrio Antiguo', 'Centro Cultural', 'Zona Financiera', 'Terminal Oeste'],
        'tiempo_total' => '40 minutos',
        'tiempos_parada' => ['0 min', '10 min', '20 min', '30 min', '40 min'],
        'frecuencia' => 'Cada 20 minutos',
        'color' => '#9C27B0',
        'icono' => 'bus'
    ],
    [
        'id' => 5,
        'nombre' => 'Ruta Turística',
        'origen' => 'Estación Turística',
        'destino' => 'Mirador',
        'paradas' => ['Estación Turística', 'Museo de Arte', 'Plaza de la Ciudad', 'Mirador'],
        'tiempo_total' => '50 minutos',
        'tiempos_parada' => ['0 min', '15 min', '30 min', '50 min'],
        'frecuencia' => 'Cada 30 minutos',
        'color' => '#FF9800',
        'icono' => 'camera'
    ],
    [
        'id' => 6,
        'nombre' => 'Ruta Universitaria',
        'origen' => 'Campus Universitario',
        'destino' => 'Centro Estudiantil',
        'paradas' => ['Campus Universitario', 'Biblioteca', 'Cafetería', 'Centro Estudiantil'],
        'tiempo_total' => '20 minutos',
        'tiempos_parada' => ['0 min', '5 min', '10 min', '20 min'],
        'frecuencia' => 'Cada 5 minutos',
        'color' => '#3F51B5',
        'icono' => 'graduation-cap'
    ],
    [
        'id' => 7,
        'nombre' => 'Ruta de la Salud',
        'origen' => 'Hospital General',
        'destino' => 'Clínica Especializada',
        'paradas' => ['Hospital General', 'Centro de Salud', 'Clínica Especializada'],
        'tiempo_total' => '15 minutos',
        'tiempos_parada' => ['0 min', '7 min', '15 min'],
        'frecuencia' => 'Cada 8 minutos',
        'color' => '#8BC34A',
        'icono' => 'heartbeat'
    ],
    [
        'id' => 8,
        'nombre' => 'Ruta de Compras',
        'origen' => 'Plaza de Compras',
        'destino' => 'Centro Comercial',
        'paradas' => ['Plaza de Compras', 'Supermercado', 'Centro Comercial'],
        'tiempo_total' => '30 minutos',
        'tiempos_parada' => ['0 min', '10 min', '30 min'],
        'frecuencia' => 'Cada 15 minutos',
        'color' => '#FFEB3B',
        'icono' => 'shopping-cart'
    ],
    [
        'id' => 9,
        'nombre' => 'Ruta Nocturna',
        'origen' => 'Estación Nocturna',
        'destino' => 'Zona de Entretenimiento',
        'paradas' => ['Estación Nocturna', 'Calle de los Bares', 'Zona de Entretenimiento'],
        'tiempo_total' => '25 minutos',
        'tiempos_parada' => ['0 min', '10 min', '25 min'],
        'frecuencia' => 'Cada 30 minutos',
        'color' => '#F44336',
        'icono' => 'moon'
    ],
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Rutas - MoveSync</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        /* Sección de cabecera */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 12px;
            padding: 25px 30px;
            margin-bottom: 30px;
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
            font-size: 1.1rem;
        }

        /* Tarjetas de rutas */
        .route-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            border: none;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            height: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .route-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .route-card-header {
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8));
            padding: 20px;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }

        .route-card-header::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: var(--color);
        }

        .route-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .route-card-title {
            color: var(--color);
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .route-card-subtitle {
            color: var(--gray-600);
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .route-card-body {
            padding: 20px;
            flex: 1;
        }

        .route-info-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 20px;
        }

        .route-info-list li {
            padding: 8px 0;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
        }

        .route-info-list li:last-child {
            border-bottom: none;
        }

        .route-info-list i {
            color: var(--color);
            width: 20px;
            margin-right: 10px;
        }

        .stops-timeline {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }

        .stops-timeline::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 9px;
            width: 2px;
            background-color: var(--color);
            opacity: 0.3;
        }

        .timeline-stop {
            position: relative;
            margin-bottom: 15px;
        }

        .timeline-stop:last-child {
            margin-bottom: 0;
        }

        .timeline-stop::before {
            content: "";
            position: absolute;
            left: -30px;
            top: 6px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid var(--color);
            background-color: white;
        }

        .timeline-stop:first-child::before {
            background-color: var(--color);
        }

        .timeline-stop:last-child::before {
            background-color: var(--color);
        }

        .timeline-stop-name {
            font-weight: 600;
            margin-bottom: 0;
        }

        .timeline-stop-time {
            font-size: 0.85rem;
            color: var(--gray-600);
        }

        .route-card-footer {
            background-color: var(--gray-100);
            border-top: 1px solid var(--gray-200);
            padding: 15px 20px;
        }

        /* Botones */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-select {
            color: white;
            border: none;
            width: 100%;
        }

        .btn-select:hover {
            filter: brightness(1.1);
        }

        .btn-back {
            background: linear-gradient(to right, var(--gray-600), var(--gray-700));
            color: white;
            border: none;
            margin-top: 20px;
        }

        .btn-back:hover {
            background: linear-gradient(to right, var(--gray-700), var(--gray-800));
            color: white;
        }

        /* Filtro */
        .filter-section {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .filter-input {
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 1rem;
            border: 1px solid var(--gray-300);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .filter-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.25);
        }

        .filter-input::placeholder {
            color: var(--gray-500);
        }

        .filter-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 1.2rem;
            pointer-events: none;
        }

        /* Estilos responsivos */
        @media (max-width: 992px) {
            .route-card {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 15px;
            }
            
            .page-header {
                text-align: center;
                padding: 20px;
            }
            
            .filter-section {
                padding: 15px;
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

        /* Estilos para la insignia de frecuencia */
        .frequency-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            background-color: rgba(58, 123, 213, 0.1);
            color: var(--primary-color);
            margin-top: 10px;
        }

        /* Estilos para la duración total */
        .duration-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--accent-color);
            margin-left: 10px;
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
            <a class="navbar-brand" href="../dashboard.php">
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
                        <a class="nav-link active" href="routes.php"><i class="fas fa-route"></i> Rutas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="report.php"><i class="fas fa-flag"></i> Incidencias</a>
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
        <!-- Cabecera de la página -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-route me-2"></i>Rutas Disponibles</h2>
                    <p>Encuentra la ruta perfecta para tu destino con nuestras opciones de transporte.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-light text-dark p-2"><i class="fas fa-map-marked-alt me-1"></i> <?php echo count($rutas); ?> rutas activas</span>
                </div>
            </div>
        </div>

        <!-- Sección de filtro -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="position-relative">
                        <input type="text" id="filter" class="form-control filter-input" placeholder="Buscar rutas por nombre, origen o destino...">
                        <i class="fas fa-search filter-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenedor de tarjetas de rutas -->
        <div class="row" id="route-list">
            <?php foreach ($rutas as $ruta): ?>
                <div class="col-lg-4 col-md-6 mb-4 route-item animate-fade-in" 
                     data-name="<?php echo htmlspecialchars(strtolower($ruta['nombre'])); ?>"
                     data-origin="<?php echo htmlspecialchars(strtolower($ruta['origen'])); ?>"
                     data-destination="<?php echo htmlspecialchars(strtolower($ruta['destino'])); ?>">
                    <div class="route-card" style="--color: <?php echo htmlspecialchars($ruta['color']); ?>">
                        <div class="route-card-header">
                            <div class="d-flex align-items-center mb-3">
                                <div class="route-icon" style="background-color: <?php echo htmlspecialchars($ruta['color']); ?>">
                                    <i class="fas fa-<?php echo htmlspecialchars($ruta['icono']); ?>"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="route-card-title" style="color: <?php echo htmlspecialchars($ruta['color']); ?>">
                                        <?php echo htmlspecialchars($ruta['nombre']); ?>
                                    </h5>
                                    <p class="route-card-subtitle">
                                        <i class="fas fa-map-marker-alt me-1" style="color: <?php echo htmlspecialchars($ruta['color']); ?>"></i> Ruta #<?php echo $ruta['id']; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="route-card-body">
                            <ul class="route-info-list">
                                <li>
                                    <i class="fas fa-play-circle" style="color: <?php echo htmlspecialchars($ruta['color']); ?>"></i>
                                    <div>
                                        <strong>Origen:</strong>
                                        <div><?php echo htmlspecialchars($ruta['origen']); ?></div>
                                    </div>
                                </li>
                                <li>
                                    <i class="fas fa-flag-checkered" style="color: <?php echo htmlspecialchars($ruta['color']); ?>"></i>
                                    <div>
                                        <strong>Destino:</strong>
                                        <div><?php echo htmlspecialchars($ruta['destino']); ?></div>
                                    </div>
                                </li>
                                <li>
                                    <i class="fas fa-clock" style="color: <?php echo htmlspecialchars($ruta['color']); ?>"></i>
                                    <div>
                                        <strong>Tiempo total:</strong>
                                        <div><?php echo htmlspecialchars($ruta['tiempo_total']); ?></div>
                                    </div>
                                </li>
                                <li>
                                    <i class="fas fa-sync-alt" style="color: <?php echo htmlspecialchars($ruta['color']); ?>"></i>
                                    <div>
                                        <strong>Frecuencia:</strong>
                                        <div><?php echo htmlspecialchars($ruta['frecuencia']); ?></div>
                                    </div>
                                </li>
                            </ul>

                            <h6 class="mb-3"><i class="fas fa-map-signs me-2" style="color: <?php echo htmlspecialchars($ruta['color']); ?>"></i>Paradas</h6>
                            <div class="stops-timeline" style="--color: <?php echo htmlspecialchars($ruta['color']); ?>">
                                <?php foreach ($ruta['paradas'] as $index => $parada): ?>
                                    <div class="timeline-stop">
                                        <p class="timeline-stop-name"><?php echo htmlspecialchars($parada); ?></p>
                                        <span class="timeline-stop-time"><?php echo htmlspecialchars($ruta['tiempos_parada'][$index]); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex align-items-center">
                                <span class="frequency-badge">
                                    <i class="fas fa-history me-1"></i> <?php echo htmlspecialchars($ruta['frecuencia']); ?>
                                </span>
                                <span class="duration-badge">
                                    <i class="fas fa-hourglass-half me-1"></i> <?php echo htmlspecialchars($ruta['tiempo_total']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="route-card-footer mt-auto">
                            <button class="btn btn-select" style="background-color: <?php echo htmlspecialchars($ruta['color']); ?>" 
                                    onclick="selectRoute(<?php echo $ruta['id']; ?>, '<?php echo htmlspecialchars(addslashes($ruta['nombre'])); ?>')">
                                <i class="fas fa-check-circle me-2"></i>Seleccionar Ruta
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Botón de volver -->
        <div class="text-center">
            <a href="../dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
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
                        <li><a href="report.php"><i class="fas fa-angle-right me-2"></i>Reportar Incidencia</a></li>
                        <li><a href="LoyaltyPoints.php"><i class="fas fa-angle-right me-2"></i>Programa de Fidelización</a></li>
                        <li><a href="help.php"><i class="fas fa-angle-right me-2"></i>Ayuda y Soporte</a></li>
                        <li><a href="terms.php"><i class="fas fa-angle-right me-2"></i>Términos y Condiciones</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contáctanos</h5>
                    <div class="footer-contact">
                        <p><i class="fas fa-map-marker-alt"></i>Calle Rio Cares, Pola de Laviana</p>
                        <p><i class="fas fa-phone"></i> +34 644 01 59 22</p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:info@movesync.com">info@movesync.com</a></p>
                    </div>