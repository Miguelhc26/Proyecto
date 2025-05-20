<?php
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Verificar la conexión a la base de datos
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Variables para mensajes de éxito o error
$success_message = "";
$error_message = "";
$form_data = [
    'nombre' => '',
    'descripcion' => '',
    'tipo' => 'Autobús',
    'origen' => '',
    'destino' => '',
    'horario_inicio' => '06:00',
    'horario_fin' => '22:00',
    'frecuencia' => '30',
    'estado' => 'Activa'
];

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y validar datos del formulario
    $form_data = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'tipo' => 'Autobús', // Tipo fijo: Autobús
        'origen' => trim($_POST['origen'] ?? ''),
        'destino' => trim($_POST['destino'] ?? ''),
        'horario' => sprintf('%s - %s (cada %s min)', 
            $_POST['horario_inicio'] ?? '06:00', 
            $_POST['horario_fin'] ?? '22:00',
            $_POST['frecuencia'] ?? '30'),
        'descripcion' => trim($_POST['descripcion'] ?? '')
    ];
    
    // Validaciones
    $errors = [];

    // Validar nombre
    if (empty($form_data['nombre'])) {
        $errors[] = "El nombre de la ruta es obligatorio";
    } elseif (strlen($form_data['nombre']) < 2) {
        $errors[] = "El nombre de la ruta debe tener al menos 2 caracteres";
    } else {
        // Verificar que el nombre de ruta no existe ya en la base de datos
        $check_name_sql = "SELECT nombre FROM Rutas WHERE nombre = ?";
        $check_name = $conn->prepare($check_name_sql);
        
        if ($check_name === false) {
            $errors[] = "Error en la preparación de la consulta: " . $conn->error;
        } else {
            $check_name->bind_param("s", $form_data['nombre']);
            $check_name->execute();
            $result = $check_name->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Este nombre de ruta ya está registrado";
            }
            
            $check_name->close();
        }
    }

    // Validar origen y destino
    if (empty($form_data['origen'])) {
        $errors[] = "El origen es obligatorio";
    }
    
    if (empty($form_data['destino'])) {
        $errors[] = "El destino es obligatorio";
    }
    
    // Validar que origen y destino sean diferentes
    if ($form_data['origen'] === $form_data['destino'] && !empty($form_data['origen'])) {
        $errors[] = "El origen y el destino no pueden ser iguales";
    }

    // Validar tipo de transporte (solo autobús es válido)
    if ($form_data['tipo'] !== 'Autobús') {
        $errors[] = "El tipo de transporte debe ser Autobús";
        $form_data['tipo'] = 'Autobús'; // Corregir automáticamente
    }

    // Validar horarios y formar cadena de horario
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $_POST['horario_inicio'])) {
        $errors[] = "El formato del horario de inicio no es válido (HH:MM)";
    }
    
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $_POST['horario_fin'])) {
        $errors[] = "El formato del horario de fin no es válido (HH:MM)";
    }
    
    // Validar frecuencia
    if (!is_numeric($_POST['frecuencia']) || $_POST['frecuencia'] < 1 || $_POST['frecuencia'] > 180) {
        $errors[] = "La frecuencia debe ser un número entre 1 y 180 minutos";
    }

    // Si no hay errores, proceder con la inserción
    if (empty($errors)) {
        try {
            // Fecha de creación
            $fecha_creacion = date('Y-m-d H:i:s');
            
            // Preparar la consulta
            $insert_sql = "INSERT INTO Rutas (Nombre, Origen, Destino, Horario, Descripcion) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            
            if ($stmt === false) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }
            
            $stmt->bind_param("sssss", 
                $form_data['nombre'], 
                $form_data['origen'], 
                $form_data['destino'], 
                $form_data['horario'],
                $form_data['descripcion']
            );
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                $success_message = "Ruta creada correctamente con ";
                // Limpiar formulario después de inserción exitosa
                $form_data = [
                    'nombre' => '',
                    'tipo' => 'Autobús',
                    'origen' => '',
                    'destino' => '',
                    'horario' => '06:00 - 22:00 (cada 30 min)',
                    'descripcion' => ''
                ];
            } else {
                $error_message = "Error al crear la ruta: " . $stmt->error;
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $error_message = "Error al crear la ruta: " . $e->getMessage();
        }
    } else {
        $error_message = implode("<br>", $errors);
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
    <title>Añadir Ruta - MoveSync</title> 
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

        /* Formularios */
        .form-label {
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid var(--gray-300);
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.25);
            border-color: var(--primary-color);
        }

        .form-text {
            color: var(--gray-600);
            font-size: 0.85rem;
        }

        .input-group-text {
            background-color: var(--gray-100);
            border-color: var(--gray-300);
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

        /* Específico para añadir ruta */
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

        .transport-type-card {
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
            opacity: 0.7;
            text-align: center;
            padding: 15px;
            border-radius: 10px;
        }

        .transport-type-card.active {
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(58, 123, 213, 0.3);
            opacity: 1;
            transform: translateY(-5px);
        }

        .transport-type-card:hover:not(.active) {
            opacity: 0.9;
        }

        .transport-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: white;
        }

        .type-bus {
            background-color: var(--primary-color);
        }

        /* Estado card */
        .status-indicator {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-active {
            background-color: var(--success-color);
        }

        .status-inactive {
            background-color: var(--danger-color);
        }

        .status-construction {
            background-color: var(--warning-color);
        }

        .status-maintenance {
            background-color: var(--info-color);
        }

        /* Alerts personalizados */
        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            border-left-width: 4px;
        }

        .alert-success {
            border-left-color: var(--success-color);
            background-color: rgba(40, 167, 69, 0.1);
        }

        .alert-danger {
            border-left-color: var(--danger-color);
            background-color: rgba(220, 53, 69, 0.1);
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
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
                    <a class="nav-link active" href="admin_rutas.php"><i class="fas fa-route"></i> Rutas</a>
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

<div class="container-fluid content-wrapper">
    <!-- Cabecera de Página -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2><i class="fas fa-bus me-2"></i>Añadir Nueva Ruta de Autobús</h2>
                <p class="mb-0">Crea una nueva ruta de autobús en el sistema. Hoy es <?php echo $fecha_actual; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="admin_rutas.php" class="btn btn-outline-light"><i class="fas fa-arrow-left me-2"></i>Volver</a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Mensajes de alerta -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show animate-fade-in" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <!-- Formulario de Añadir Ruta -->
            <div class="card animate-fade-in">
                <div class="card-body">
                    <h5 class="section-title">Información de la Ruta de Autobús</h5>
                    
                    <form id="addRouteForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <!-- Tipo de Transporte (solo autobús) -->
                        <div class="mb-4">
                            <label class="form-label">Tipo de Transporte</label>
                            <div class="row justify-content-center">
                                <div class="col-md-6 col-lg-4">
                                    <div class="transport-type-card active" data-type="Autobús">
                                        <div class="transport-icon type-bus">
                                            <i class="fas fa-bus"></i>
                                        </div>
                                        <h6>Autobús</h6>
                                    </div>
                                </div>
                            </div>
                            <!-- Campo oculto para almacenar el tipo de transporte -->
                            <input type="hidden" name="tipo" id="transportType" value="Autobús">
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre de la Ruta</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-signs"></i></span>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           placeholder="Ej: Línea 1, Ruta Norte-Sur..." 
                                           value="<?php echo htmlspecialchars($form_data['nombre']); ?>" required>
                                </div>
                                <div class="form-text">Ingrese un nombre único e identificativo para la ruta</div>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo de Ruta</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bus"></i></span>
                                    <input type="text" class="form-control" id="tipo" name="tipo" 
                                           value="Autobús" readonly>
                                </div>
                                <div class="form-text">Tipo de transporte fijo para esta ruta</div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="origen" class="form-label">Origen</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" class="form-control" id="origen" name="origen" 
                                           placeholder="Punto de inicio" 
                                           value="<?php echo htmlspecialchars($form_data['origen']); ?>" required>
                                </div>
                                <div class="form-text">Lugar donde comienza la ruta</div>
                            </div>
                            <div class="col-md-6">
                                <label for="destino" class="form-label">Destino</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                    <input type="text" class="form-control" id="destino" name="destino" 
                                           placeholder="Punto final" 
                                           value="<?php echo htmlspecialchars($form_data['destino']); ?>" required>
                                </div>
                                <div class="form-text">Lugar donde finaliza la ruta</div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="horario_inicio" class="form-label">Horario de Inicio</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="time" class="form-control" id="horario_inicio" name="horario_inicio" 
                                           value="06:00" required>
                                </div>
                                <div class="form-text">Hora de inicio del servicio (formato 24h)</div>
                            </div>
                            <div class="col-md-4">
                                <label for="horario_fin" class="form-label">Horario de Fin</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="time" class="form-control" id="horario_fin" name="horario_fin" 
                                           value="22:00" required>
                                </div>
                                <div class="form-text">Hora de finalización del servicio (formato 24h)</div>
                            </div>
                            <div class="col-md-4">
                                <label for="frecuencia" class="form-label">Frecuencia (minutos)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-stopwatch"></i></span>
                                    <input type="number" class="form-control" id="frecuencia" name="frecuencia" min="1" max="180"
                                           value="30" required>
                                    <span class="input-group-text">min</span>
                                </div>
                                <div class="form-text">Intervalo de tiempo entre cada servicio</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" 
                                     placeholder="Descripción detallada de la ruta, paradas importantes, etc."><?php echo htmlspecialchars($form_data['descripcion']); ?></textarea>
                            <div class="form-text">Incluya información relevante como paradas principales o conexiones con otras rutas</div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6><i class="fas fa-info-circle me-2 text-primary"></i>Información Importante</h6>
                                        <ul class="mb-0">
                                            <li>Las rutas creadas serán visibles inmediatamente para los usuarios si están en estado "Activa".</li>
                                            <li>Puede modificar la información de la ruta posteriormente desde el panel de gestión.</li>
                                            <li>Para añadir paradas específicas, utilice la sección de "Gestión de Paradas" después de crear la ruta.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="admin_rutas.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-bus me-2"></i>Crear Ruta de Autobús
                            </button>
                        </div>
                    </form>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Animación de elementos al cargar la página
        const elements = document.querySelectorAll('.card, .section-title, .page-header');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('animate-fade-in');
            }, index * 100);
        });
        
        // Validación del formulario antes de enviar
        const addRouteForm = document.getElementById('addRouteForm');
        addRouteForm.addEventListener('submit', function(event) {
            let hasErrors = false;
            
            // Validar nombre de la ruta
            if (document.getElementById('nombre').value.length < 2) {
                hasErrors = true;
                alert('El nombre de la ruta debe tener al menos 2 caracteres.');
            }
            
            // Validar origen y destino
            if (document.getElementById('origen').value === '') {
                hasErrors = true;
                alert('El origen es obligatorio.');
            }
            
            if (document.getElementById('destino').value === '') {
                hasErrors = true;
                alert('El destino es obligatorio.');
            }
            
            // Validar que origen y destino sean diferentes
            if (document.getElementById('origen').value === document.getElementById('destino').value 
                && document.getElementById('origen').value !== '') {
                hasErrors = true;
                alert('El origen y el destino no pueden ser iguales.');
            }
            
            // Validar horarios
            const horarioInicio = document.getElementById('horario_inicio').value;
            const horarioFin = document.getElementById('horario_fin').value;
            
            if (horarioInicio === '' || horarioFin === '') {
                hasErrors = true;
                alert('Los horarios de inicio y fin son obligatorios.');
            }
            
            // Validar frecuencia
            const frecuencia = document.getElementById('frecuencia').value;
            if (frecuencia === '' || frecuencia < 1 || frecuencia > 180) {
                hasErrors = true;
                alert('La frecuencia debe ser un número entre 1 y 180 minutos.');
            }
            
            // Si hay errores, detener el envío del formulario
            if (hasErrors) {
                event.preventDefault();
                alert('Por favor, corrija los errores en el formulario antes de continuar.');
            }
        });
    });
</script>
</body>
</html>