<?php
session_start();

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Variables para mensajes de éxito o error
$success_message = "";
$error_message = "";
$form_data = [
    'nombre' => '',
    'correo' => '',
    'password' => '',
    'password_confirm' => '',
    'tipo' => 'Usuario'
];

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y validar datos del formulario
    $form_data = [
        'nombre' => trim($_POST['nombre']),
        'correo' => trim($_POST['correo']),
        'password' => $_POST['password'],
        'password_confirm' => $_POST['password_confirm'],
        'tipo' => $_POST['tipo']
    ];
    
    // Validaciones
    $errors = [];

    // Validar nombre
    if (empty($form_data['nombre'])) {
        $errors[] = "El nombre es obligatorio";
    } elseif (strlen($form_data['nombre']) < 3) {
        $errors[] = "El nombre debe tener al menos 3 caracteres";
    }

    // Validar correo
    if (empty($form_data['correo'])) {
        $errors[] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($form_data['correo'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico no es válido";
    } else {
        // Verificar que el correo no existe ya en la base de datos
        $check_email_sql = "SELECT correo FROM Usuarios WHERE correo = ?";
        $check_email = $conn->prepare($check_email_sql);
        
        if ($check_email === false) {
            $errors[] = "Error en la preparación de la consulta: " . $conn->error;
        } else {
            $check_email->bind_param("s", $form_data['correo']);
            $check_email->execute();
            $result = $check_email->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Este correo electrónico ya está registrado";
            }
            
            $check_email->close();
        }
    }

    // Validar contraseña
    if (empty($form_data['password'])) {
        $errors[] = "La contraseña es obligatoria";
    } elseif (strlen($form_data['password']) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres";
    } elseif ($form_data['password'] !== $form_data['password_confirm']) {
        $errors[] = "Las contraseñas no coinciden";
    }

    // Validar tipo de usuario
    $tipos_validos = ['Administrador', 'Usuario', 'Soporte'];
    if (!in_array($form_data['tipo'], $tipos_validos)) {
        $errors[] = "El tipo de usuario seleccionado no es válido";
    }

    // Si no hay errores, proceder con la inserción
    if (empty($errors)) {
        try {
            // Hash de la contraseña
            $hashed_password = password_hash($form_data['password'], PASSWORD_DEFAULT);
            
            // Preparar la consulta
            $insert_sql = "INSERT INTO Usuarios (nombre, correo, password, tipo) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            
            if ($stmt === false) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }
            
            $stmt->bind_param("ssss", $form_data['nombre'], $form_data['correo'], $hashed_password, $form_data['tipo']);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                $success_message = "Usuario creado correctamente con ID: " . $conn->insert_id;
                // Limpiar formulario después de inserción exitosa
                $form_data = [
                    'nombre' => '',
                    'correo' => '',
                    'password' => '',
                    'password_confirm' => '',
                    'tipo' => 'Usuario'
                ];
            } else {
                $error_message = "Error al crear el usuario: " . $stmt->error;
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $error_message = "Error al crear el usuario: " . $e->getMessage();
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
    <title>Añadir Usuario - MoveSync</title> 
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

        /* Específico para añadir usuario */
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

        .user-role-card {
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
            opacity: 0.7;
        }

        .user-role-card.active {
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(58, 123, 213, 0.3);
            opacity: 1;
            transform: translateY(-5px);
        }

        .user-role-card:hover:not(.active) {
            opacity: 0.9;
        }

        .user-role-icon {
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

        .role-admin {
            background-color: var(--primary-color);
        }

        .role-user {
            background-color: var(--secondary-color);
        }

        .role-support {
            background-color: var(--warning-color);
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

        /* Password strength meter */
        .password-strength-meter {
            height: 6px;
            background-color: var(--gray-200);
            border-radius: 3px;
            margin-top: 5px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        
        .password-strength-meter-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .strength-weak {
            background-color: var(--danger-color);
            width: 33.33%;
        }
        
        .strength-medium {
            background-color: var(--warning-color);
            width: 66.66%;
        }
        
        .strength-strong {
            background-color: var(--success-color);
            width: 100%;
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
                    <a class="nav-link active" href="admin_users.php"><i class="fas fa-users"></i> Usuarios</a>
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
                <h2><i class="fas fa-user-plus me-2"></i>Añadir Nuevo Usuario</h2>
                <p class="mb-0">Crea una nueva cuenta de usuario en el sistema. Hoy es <?php echo $fecha_actual; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="admin_users.php" class="btn btn-outline-light"><i class="fas fa-arrow-left me-2"></i>Volver a Usuarios</a>
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

            <!-- Formulario de Añadir Usuario -->
            <div class="card animate-fade-in">
                <div class="card-body">
                    <h5 class="section-title">Datos del Usuario</h5>
                    
                    <form method="post" action="" id="addUserForm">
                        <div class="row mb-4">
                            <div class="col-md-4 mb-4 mb-md-0">
                                <div class="card user-role-card" data-role="Administrador" id="roleAdmin">
                                    <div class="card-body text-center py-4">
                                        <div class="user-role-icon role-admin">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                        <h5>Administrador</h5>
                                        <p class="mb-0 text-muted">Control total del sistema</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4 mb-md-0">
                                <div class="card user-role-card active" data-role="Usuario" id="roleUser">
                                    <div class="card-body text-center py-4">
                                        <div class="user-role-icon role-user">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <h5>Usuario</h5>
                                        <p class="mb-0 text-muted">Acceso estándar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card user-role-card" data-role="Soporte" id="roleSupport">
                                    <div class="card-body text-center py-4">
                                        <div class="user-role-icon role-support">
                                            <i class="fas fa-headset"></i>
                                        </div>
                                        <h5>Soporte</h5>
                                        <p class="mb-0 text-muted">Atención de incidencias</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Campo oculto para almacenar el tipo de usuario -->
                            <input type="hidden" name="tipo" id="userRole" value="<?php echo htmlspecialchars($form_data['tipo']); ?>">
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           placeholder="Ingrese nombre completo" 
                                           value="<?php echo htmlspecialchars($form_data['nombre']); ?>" required>
                                </div>
                                <div class="form-text">Ingrese nombre y apellidos del usuario</div>
                            </div>
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="correo" name="correo" 
                                           placeholder="Ingrese correo electrónico" 
                                           value="<?php echo htmlspecialchars($form_data['correo']); ?>" required>
                                </div>
                                <div class="form-text">Será usado como identificador para iniciar sesión</div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Ingrese contraseña" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength-meter mt-2">
                                    <div class="password-strength-meter-fill" id="passwordStrength"></div>
                                </div>
                                <div id="passwordFeedback" class="form-text">La contraseña debe tener al menos 6 caracteres</div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                           placeholder="Confirme contraseña" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordMatch" class="form-text">Las contraseñas deben coincidir</div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="admin_users.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Crear Usuario
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
        
        // Selección de rol de usuario
        const roleCards = document.querySelectorAll('.user-role-card');
        const hiddenRoleInput = document.getElementById('userRole');
        
        roleCards.forEach(card => {
            card.addEventListener('click', function() {
                // Quitar clase active de todas las tarjetas
                roleCards.forEach(c => c.classList.remove('active'));
                // Añadir clase active a la tarjeta seleccionada
                this.classList.add('active');
                // Actualizar el valor del campo
                                hiddenRoleInput.value = this.getAttribute('data-role');
            });
        });

        // Función para mostrar la fortaleza de la contraseña
        const passwordInput = document.getElementById('password');
        const passwordStrengthMeter = document.getElementById('passwordStrength');
        const passwordFeedback = document.getElementById('passwordFeedback');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 6) {
                strength++;
            }
            if (/[A-Z]/.test(password)) {
                strength++;
            }
            if (/[0-9]/.test(password)) {
                strength++;
            }
            if (/[^A-Za-z0-9]/.test(password)) {
                strength++;
            }

            if (strength === 1) {
                passwordStrengthMeter.className = 'password-strength-meter-fill strength-weak';
            } else if (strength === 2) {
                passwordStrengthMeter.className = 'password-strength-meter-fill strength-medium';
            } else if (strength === 3 || strength === 4) {
                passwordStrengthMeter.className = 'password-strength-meter-fill strength-strong';
            } else {
                passwordStrengthMeter.className = 'password-strength-meter-fill';
            }
        });

        // Validar coincidencia de contraseñas
        const passwordConfirmInput = document.getElementById('password_confirm');
        const passwordMatchFeedback = document.getElementById('passwordMatch');

        passwordConfirmInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                passwordMatchFeedback.style.color = 'red';
                passwordMatchFeedback.textContent = 'Las contraseñas no coinciden';
            } else {
                passwordMatchFeedback.style.color = 'green';
                passwordMatchFeedback.textContent = 'Las contraseñas coinciden';
            }
        });
    });
</script>
</body>
</html>
