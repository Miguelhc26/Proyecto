<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MoveSync</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../css/estiloAlta.css">
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
                    <h1>¡Bienvenido de nuevo!</h1>
                    <p>Inicia sesión para acceder a tu cuenta</p>
                </div>
                
                <form action="procesarLogin.php" method="post">
                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" placeholder="Introduce tu correo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Introduce tu contraseña" required>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" id="remember" name="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Recordarme</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </button>
                </form>
                
                <a href="register.php" class="btn btn-outline">
                    <i class="fas fa-user-plus"></i> Crear una cuenta nueva
                </a>
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