<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - MoveSync</title>
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

        /* Estilos específicos para términos */
        .terms-container {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .terms-section {
            margin-bottom: 30px;
        }

        .terms-section h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-weight: 600;
            border-bottom: 2px solid var(--gray-200);
            padding-bottom: 8px;
        }

        .terms-section p {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .terms-section ul {
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .terms-section li {
            margin-bottom: 8px;
        }

        .last-updated {
            font-size: 0.9rem;
            color: var(--gray-600);
            text-align: right;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 15px;
            }
            
            .terms-container {
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
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-sync-alt"></i> MoveSync
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="routes.php"><i class="fas fa-route"></i> Rutas</a>
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
    <div class="row">
        <div class="col-12">
            <h1 class="section-title"><i class="fas fa-file-contract me-2"></i>Términos y Condiciones de Uso</h1>
            
            <div class="terms-container">
                <div class="terms-section">
                    <h3>1. Aceptación de los Términos</h3>
                    <p>Al acceder y utilizar el servicio de MoveSync ("nosotros", "nuestro", "nos"), usted ("usuario", "usted") acepta estar legalmente obligado por estos términos y condiciones ("Términos"). Si no está de acuerdo con alguno de estos Términos, no podrá utilizar nuestros servicios.</p>
                </div>
                
                <div class="terms-section">
                    <h3>2. Descripción del Servicio</h3>
                    <p>MoveSync es una plataforma que proporciona información sobre rutas de transporte público y permite a los usuarios reportar incidencias en el mismo. Nuestros servicios incluyen, pero no se limitan a:</p>
                    <ul>
                        <li>Consulta de rutas y horarios de transporte público</li>
                        <li>Reporte de incidencias en el servicio de transporte</li>
                        <li>Programa de fidelización con puntos por participación</li>
                        <li>Notificaciones sobre cambios en las rutas habituales</li>
                    </ul>
                </div>
                
                <div class="terms-section">
                    <h3>3. Registro y Cuenta de Usuario</h3>
                    <p>Para acceder a ciertas funcionalidades, deberá registrarse y crear una cuenta ("Cuenta"). Usted acepta:</p>
                    <ul>
                        <li>Proporcionar información precisa, actual y completa durante el registro</li>
                        <li>Mantener y actualizar su información para mantenerla precisa, actual y completa</li>
                        <li>Ser responsable de toda la actividad que ocurra bajo su Cuenta</li>
                        <li>Notificarnos inmediatamente de cualquier uso no autorizado de su Cuenta</li>
                        <li>No compartir su contraseña con terceros</li>
                    </ul>
                </div>
                
                <div class="terms-section">
                    <h3>4. Uso Aceptable</h3>
                    <p>Usted acepta utilizar nuestros servicios solo para fines legales y de acuerdo con estos Términos. No podrá:</p>
                    <ul>
                        <li>Utilizar nuestros servicios de manera fraudulenta o engañosa</li>
                        <li>Reportar información falsa o inexacta sobre incidencias</li>
                        <li>Interferir con el funcionamiento normal de nuestros servicios</li>
                        <li>Acceder a nuestros servicios mediante cualquier medio no autorizado</li>
                        <li>Violar cualquier ley, regulación o derechos de terceros</li>
                    </ul>
                </div>
                
                <div class="terms-section">
                    <h3>5. Contenido Generado por el Usuario</h3>
                    <p>Al enviar cualquier contenido (como reportes de incidencias o comentarios) a través de nuestros servicios:</p>
                    <ul>
                        <li>Usted nos otorga una licencia mundial, no exclusiva, libre de regalías para usar, reproducir, modificar y distribuir dicho contenido</li>
                        <li>Garantiza que tiene todos los derechos necesarios para otorgar esta licencia</li>
                        <li>Reconoce que somos responsables de moderar, pero no necesariamente respaldamos, el contenido generado por usuarios</li>
                    </ul>
                </div>
                
                <div class="terms-section">
                    <h3>6. Programa de Fidelización</h3>
                    <p>Nuestro programa de puntos está sujeto a las siguientes condiciones:</p>
                    <ul>
                        <li>Los puntos se otorgan por actividades específicas definidas por nosotros</li>
                        <li>Nos reservamos el derecho de modificar el sistema de puntos en cualquier momento</li>
                        <li>Los puntos no tienen valor monetario y no son transferibles</li>
                        <li>Nos reservamos el derecho de cancelar puntos en caso de uso fraudulento</li>
                    </ul>
                </div>
                
                <div class="terms-section">
                    <h3>7. Limitación de Responsabilidad</h3>
                    <p>MoveSync no garantiza la exactitud, integridad o utilidad de la información proporcionada a través de nuestros servicios. Usted reconoce y acepta que:</p>
                    <ul>
                        <li>La información sobre rutas y horarios puede cambiar sin previo aviso</li>
                        <li>No somos responsables de decisiones tomadas basadas en la información proporcionada</li>
                        <li>No garantizamos la disponibilidad ininterrumpida o libre de errores de nuestros servicios</li>
                        <li>En la máxima medida permitida por la ley, no seremos responsables por daños indirectos, incidentales o consecuentes</li>
                    </ul>
                </div>
                
                <div class="terms-section">
                    <h3>8. Privacidad</h3>
                    <p>Su privacidad es importante para nosotros. Nuestra Política de Privacidad explica cómo recopilamos, usamos y protegemos su información personal. Al utilizar nuestros servicios, usted acepta nuestras prácticas de privacidad.</p>
                </div>
                
                <div class="terms-section">
                    <h3>9. Modificaciones</h3>
                    <p>Nos reservamos el derecho de modificar estos Términos en cualquier momento. Las modificaciones entrarán en vigor inmediatamente después de su publicación. Su uso continuado de nuestros servicios constituirá su aceptación de los Términos modificados.</p>
                </div>
                
                <div class="terms-section">
                    <h3>10. Terminación</h3>
                    <p>Podemos suspender o terminar su acceso a nuestros servicios en cualquier momento, sin previo aviso, por cualquier motivo, incluyendo pero no limitado a violaciones de estos Términos.</p>
                </div>
                
                <div class="terms-section">
                    <h3>11. Ley Aplicable</h3>
                    <p>Estos Términos se regirán e interpretarán de acuerdo con las leyes de España, sin tener en cuenta sus disposiciones sobre conflictos de leyes.</p>
                </div>
                
                <div class="last-updated">
                    <p><strong>Última actualización:</strong> 19 de mayo de 2025</p>
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
                    <li><a href="routes.php"><i class="fas fa-angle-right me-2"></i>Rutas</a></li>
                    <li><a href="report.php"><i class="fas fa-angle-right me-2"></i>Reportar Incidencia</a></li>
                    <li><a href="LoyaltyPoints.php"><i class="fas fa-angle-right me-2"></i>Programa de Fidelización</a></li>
                    <li><a href="help.php"><i class="fas fa-angle-right me-2"></i>Ayuda y Soporte</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contáctanos</h5>
                <div class="footer-contact">
                    <p><i class="fas fa-map-marker-alt"></i>Calle Rio Cares, Pola de Laviana</p>
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
});
</script>
</body>
</html>