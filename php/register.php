<?php
session_start();
include('../config/db_config.php'); // Conexión con la base de datos

// Si ya está autenticado, lo redirigimos al Dashboard
if (isset($_SESSION['usuario'])) {
    header("Location: ../dashboard.php");
    exit();
}

// Procesar el registro del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Comprobar si el correo ya está registrado
    $checkEmail = "SELECT * FROM Usuarios WHERE correo='$correo'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "<script>alert('El correo ya está registrado.'); window.location.href = 'register.php';</script>";
        exit();
    }

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO Usuarios (nombre, correo, contrasena, tipo) VALUES ('$nombre', '$correo', '$password', 'Usuario')";
    if ($conn->query($sql) === TRUE) {
        // Redirección al login con un mensaje de éxito
        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href = 'altaLogin.php';</script>";
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MoveSync</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    
    <style>
        /* Variables de colores (coinciden con las del dashboard) */
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

        /* Estilos generales */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: var(--gray-800);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Contenedor principal */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .login-wrapper {
            display: flex;
            max-width: 900px;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        /* Panel izquierdo con imagen/branding */
        .login-brand {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            flex: 0 0 45%;
            padding: 40px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .login-brand-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('assets/login-bg.jpg') center/cover no-repeat;
            opacity: 0.2;
        }

        .login-brand-content {
            position: relative;
            z-index: 2;
        }

        .login-brand-logo {
            margin-bottom: 2rem;
        }

        .login-brand-logo i {
            font-size: 3rem;
            background: white;
            color: var(--primary-color);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .login-brand h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .login-brand p {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .login-features {
            text-align: left;
            margin-top: 2rem;
        }

        .login-feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .login-feature-item i {
            background-color: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 0.8rem;
        }

        /* Panel derecho con formulario */
        .login-form {
            flex: 0 0 55%;
            background: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-700);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            font-size: 1rem;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            background-color: var(--gray-100);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(58, 123, 213, 0.1);
            background-color: white;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .form-check input {
            margin-right: 0.5rem;
        }

        .form-check label {
            color: var(--gray-600);
        }

        /* Botones */
        .btn {
            display: inline-block;
            padding: 14px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: 0 4px 15px rgba(58, 123, 213, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #3672c6, #00c4ee);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(58, 123, 213, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            margin-top: 1rem;
        }

        .btn-outline:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-400);
        }

        .btn i {
            margin-right: 8px;
        }

        /* Adaptación responsive */
        @media (max-width: 992px) {
            .login-wrapper {
                max-width: 700px;
            }
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 500px;
            }
            
            .login-brand,
            .login-form {
                flex: 0 0 100%;
            }
            
            .login-brand {
                padding: 30px;
            }
            
            .login-brand h2 {
                font-size: 1.8rem;
            }
            
            .login-features {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 1rem;
            }
            
            .login-form,
            .login-brand {
                padding: 25px;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-wrapper {
            animation: fadeIn 0.6s ease forwards;
        }

        /* Clase para el estado de error de campos */
        .is-invalid {
            border-color: var(--danger-color);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px 16px;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-wrapper">
            <!-- Panel izquierdo (branding) -->
            <div class="login-brand">
                <div class="login-brand-overlay"></div>
                <div class="login-brand-content">
                    <div class="login-brand-logo">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h2>MoveSync</h2>
                    <p>Tu plataforma integral de transporte público</p>
                    
                    <div class="login-features">
                        <div class="login-feature-item">
                            <i class="fas fa-check"></i>
                            <span>Planifica tus rutas diarias</span>
                        </div>
                        <div class="login-feature-item">
                            <i class="fas fa-check"></i>
                            <span>Recibe alertas en tiempo real</span>
                        </div>
                        <div class="login-feature-item">
                            <i class="fas fa-check"></i>
                            <span>Gana puntos reportando incidencias</span>
                        </div>
                        <div class="login-feature-item">
                            <i class="fas fa-check"></i>
                            <span>Conecta con la comunidad de usuarios</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Panel derecho (formulario) -->
            <div class="login-form">
                <div class="login-header">
                    <h1>Crear una nueva cuenta</h1>
                    <p>Únete a la comunidad de MoveSync</p>
                </div>
                
                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Introduce tu nombre completo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" placeholder="Introduce tu correo electrónico" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Crea una contraseña segura" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Registrarse
                    </button>
                </form>
                
                <a href="altaLogin.php" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i> Ya tengo una cuenta
                </a>
            </div>
        </div>
        
        <!-- Botón para volver a la página principal -->
        <a href="../index.php" class="position-absolute top-0 start-0 m-3 text-decoration-none" style="color: var(--primary-color); z-index: 1000;">
            <i class="fas fa-arrow-left me-2"></i> Volver al inicio
        </a>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para mostrar/ocultar contraseña
        document.addEventListener('DOMContentLoaded', function() {
            // Puedes añadir cualquier funcionalidad de JavaScript aquí
            // Por ejemplo, para mostrar/ocultar la contraseña:
            
            // Añadir el botón para mostrar/ocultar contraseña
            const passwordField = document.getElementById('password');
            const passwordContainer = passwordField.parentElement;
            
            // Crear el botón de toggle
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'password-toggle';
            toggleButton.innerHTML = '<i class="far fa-eye"></i>';
            toggleButton.style.position = 'absolute';
            toggleButton.style.right = '15px';
            toggleButton.style.top = '70%';
            toggleButton.style.transform = 'translateY(-50%)';
            toggleButton.style.border = 'none';
            toggleButton.style.background = 'transparent';
            toggleButton.style.color = 'var(--gray-600)';
            toggleButton.style.cursor = 'pointer';
            
            // Hacer que el contenedor sea posición relativa
            passwordContainer.style.position = 'relative';
            
            // Añadir el botón al contenedor
            passwordContainer.appendChild(toggleButton);
            
            // Añadir el evento
            toggleButton.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                toggleButton.innerHTML = type === 'password' ? '<i class="far fa-eye"></i>' : '<i class="far fa-eye-slash"></i>';
            });
        });
    </script>
</body>
</html>