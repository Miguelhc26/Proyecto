<?php
// Variables de ejemplo - normalmente vendrían de una base de datos
$usuario = $usuario ?? ['nombre' => 'Usuario'];
$faqs = [
    [
        'id' => 'faq1',
        'pregunta' => '¿Cómo reporto una incidencia en mi ruta?',
        'respuesta' => 'Para reportar una incidencia, navega hasta la sección "Incidencias" desde el menú principal. Pulsa en el botón "Nueva incidencia" y completa el formulario con los detalles de la incidencia, incluyendo la ubicación, tipo de problema y una descripción. También puedes añadir fotos si es necesario.'
    ],
    [
        'id' => 'faq2',
        'pregunta' => '¿Cómo puedo acumular puntos de fidelización?',
        'respuesta' => 'Acumularás puntos de fidelización de varias maneras: reportando incidencias verificadas, utilizando la aplicación regularmente para planificar rutas, invitando a amigos a unirse a MoveSync, y completando desafíos mensuales. Puedes revisar tu progreso en la sección "Puntos".'
    ],
    [
        'id' => 'faq3',
        'pregunta' => '¿Puedo modificar una ruta guardada?',
        'respuesta' => 'Sí, puedes modificar cualquier ruta guardada. Ve a la sección "Rutas", selecciona la ruta que deseas editar y haz clic en el botón de edición (icono de lápiz). Realiza los cambios necesarios y guarda la ruta actualizada.'
    ],
    [
        'id' => 'faq4',
        'pregunta' => '¿Cómo puedo canjear mis puntos de fidelización?',
        'respuesta' => 'Para canjear tus puntos, dirígete a la sección "Puntos" y selecciona la pestaña "Canjear". Allí encontrarás todas las recompensas disponibles según tu nivel y puntos acumulados. Selecciona la recompensa deseada y confirma el canje.'
    ],
    [
        'id' => 'faq5',
        'pregunta' => '¿Es posible compartir una ruta con otros usuarios?',
        'respuesta' => 'Sí, puedes compartir cualquier ruta con otros usuarios. En la vista detallada de la ruta, haz clic en el botón "Compartir" y selecciona el método de compartir preferido: enlace directo, correo electrónico o código QR.'
    ],
    [
        'id' => 'faq6',
        'pregunta' => '¿Cuánto tiempo tarda en resolverse una incidencia reportada?',
        'respuesta' => 'El tiempo de resolución varía según la severidad y tipo de incidencia. Las incidencias urgentes se priorizan y generalmente se resuelven en 24-48 horas. Las incidencias estándar suelen resolverse en un plazo de 3-5 días hábiles. Puedes seguir el estado de tus incidencias en la sección "Incidencias".'
    ],
    [
        'id' => 'faq7',
        'pregunta' => '¿Cómo cambio mi contraseña o información personal?',
        'respuesta' => 'Para modificar tu información personal o cambiar tu contraseña, dirígete a la sección "Configuración" desde el menú principal. Allí encontrarás opciones para actualizar tus datos personales, preferencias de notificación y credenciales de acceso.'
    ],
    [
        'id' => 'faq8',
        'pregunta' => '¿Qué hago si encuentro un error en la aplicación?',
        'respuesta' => 'Si encuentras algún error o fallo técnico, ve a la sección "Ayuda y Soporte" y utiliza la opción "Reportar un problema". Describe el error encontrado con el mayor detalle posible, incluyendo los pasos para reproducirlo. Nuestro equipo técnico lo analizará lo antes posible.'
    ]
];

$categorias_ayuda = [
    [
        'icono' => 'fas fa-route',
        'titulo' => 'Rutas y Navegación',
        'descripcion' => 'Aprende a crear, guardar y optimizar tus rutas diarias.'
    ],
    [
        'icono' => 'fas fa-flag',
        'titulo' => 'Gestión de Incidencias',
        'descripcion' => 'Todo sobre cómo reportar y seguir incidencias en tu trayecto.'
    ],
    [
        'icono' => 'fas fa-star',
        'titulo' => 'Programa de Fidelización',
        'descripcion' => 'Descubre cómo acumular y canjear puntos por recompensas.'
    ],
    [
        'icono' => 'fas fa-user-cog',
        'titulo' => 'Cuenta y Configuración',
        'descripcion' => 'Gestiona tu perfil, privacidad y preferencias personales.'
    ],
    [
        'icono' => 'fas fa-mobile-alt',
        'titulo' => 'Uso de la Aplicación',
        'descripcion' => 'Guías paso a paso sobre todas las funcionalidades de MoveSync.'
    ],
    [
        'icono' => 'fas fa-shield-alt',
        'titulo' => 'Privacidad y Seguridad',
        'descripcion' => 'Información sobre cómo protegemos tus datos personales.'
    ]
];

$contactos = [
    [
        'icono' => 'fas fa-headset',
        'titulo' => 'Soporte Técnico',
        'descripcion' => 'Lunes a Viernes: 9:00 - 20:00',
        'contacto' => 'soporte@movesync.com',
        'telefono' => '+34 644 01 58 95'
    ],
    [
        'icono' => 'fas fa-comment-dots',
        'titulo' => 'Chat en Vivo',
        'descripcion' => 'Disponible 24/7',
        'contacto' => 'chat.movesync.com',
        'telefono' => ''
    ],
    [
        'icono' => 'fas fa-question-circle',
        'titulo' => 'Consultas Generales',
        'descripcion' => 'Lunes a Domingo: 8:00 - 22:00',
        'contacto' => 'info@movesync.com',
        'telefono' => '+34 644 01 59 22'
    ]
];

$mensaje = $mensaje ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda y Soporte - MoveSync</title> 
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

        /* Estilos para la página de ayuda */
        .help-hero {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .help-hero::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(transparent, rgba(255, 255, 255, 0.1));
            transform: rotate(30deg);
            pointer-events: none;
        }

        .help-hero h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .help-hero p {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto 25px;
            opacity: 0.9;
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            height: 55px;
            width: 100%;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 30px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding-right: 50px;
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 5px;
            height: 45px;
            width: 45px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .category-card {
            background-color: white;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .category-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(58, 123, 213, 0.1), rgba(0, 210, 255, 0.1));
            border-radius: 50%;
            margin: 0 auto 20px;
            font-size: 1.8rem;
            color: var(--primary-color);
        }

        .category-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark-color);
        }

        .category-card p {
            color: var(--gray-600);
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 10px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 50px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }

        .faq-container {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(58, 123, 213, 0.05);
            color: var(--primary-color);
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: var(--gray-300);
        }

        .accordion-item {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
            border: 1px solid var(--gray-200);
        }

        .accordion-button {
            font-weight: 500;
            padding: 15px 20px;
        }

        .accordion-body {
            padding: 20px;
            line-height: 1.6;
            color: var(--gray-700);
            background-color: var(--gray-50);
        }

        .contact-card {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-left: 5px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .contact-icon {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .contact-card h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .contact-card p {
            color: var(--gray-600);
            margin-bottom: 15px;
        }

        .contact-card .contact-info {
            color: var(--primary-color);
            font-weight: 500;
        }

        .contact-form {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--gray-300);
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }

        .form-label {
            font-weight: 500;
            color: var(--gray-700);
        }

        .btn-submit {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .video-tutorials {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .video-card {
            background-color: var(--gray-100);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .video-thumbnail {
            position: relative;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            background-color: var(--gray-200);
            overflow: hidden;
        }

        .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .video-card:hover .play-icon {
            background-color: var(--primary-color);
            color: white;
        }

        .video-info {
            padding: 15px;
        }

        .video-info h5 {
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 1rem;
        }

        .video-info p {
            color: var(--gray-600);
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* Estilos adicionales para la sección de recursos */
        .resource-card {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .resource-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .resource-icon {
            min-width: 50px;
            height: 50px;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(58, 123, 213, 0.1), rgba(0, 210, 255, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-right: 20px;
        }

        .resource-info h5 {
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 1rem;
        }

        .resource-info p {
            color: var(--gray-600);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .resource-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
        }

        .resource-link i {
            margin-left: 5px;
            transition: transform 0.3s ease;
        }

        .resource-link:hover i {
            transform: translateX(3px);
        }

        /* Estilos para el soporte de chat en vivo */
        .chat-bubble {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .chat-bubble:hover {
            transform: scale(1.1);
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
            .help-hero {
                padding: 40px 0;
            }
            
            .help-hero h1 {
                font-size: 2rem;
            }
            
            .category-card {
                margin-bottom: 20px;
            }
            
            .search-input {
                height: 50px;
                font-size: 0.9rem;
            }
            
            .faq-container, .contact-form {
                padding: 20px;
            }
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
                    <a class="nav-link active" href="help.php"><i class="fas fa-question-circle"></i> Ayuda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sección Hero con Búsqueda -->
<div class="help-hero">
    <div class="container">
        <h1>Centro de Ayuda y Soporte</h1>
        <p>Encuentra respuestas a tus preguntas, tutoriales y recursos para aprovechar al máximo MoveSync.</p>
        <div class="search-container">
            <form action="" method="GET">
                <input type="text" class="search-input" placeholder="¿Qué estás buscando?" name="search">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</div>

<div class="container content-wrapper">
    <?php if (!empty($mensaje)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?php echo $mensaje; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Preguntas Frecuentes -->
    <section class="mb-5">
        <h2 class="section-title">Preguntas Frecuentes</h2>
        <div class="faq-container">
            <div class="accordion" id="accordionExample">
                <?php foreach ($faqs as $index => $faq): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?php echo $faq['id']; ?>">
                        <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $faq['id']; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $faq['id']; ?>">
                            <?php echo $faq['pregunta']; ?>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $faq['id']; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $faq['id']; ?>" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <?php echo $faq['respuesta']; ?>
                        </div>
                    </div>
                </div>
                                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- Contacto y Soporte -->
    <section class="mb-5">
        <h2 class="section-title">Contacto y Soporte</h2>
        <div class="row mb-4">
            <?php foreach ($contactos as $contacto): ?>
            <div class="col-md-4 mb-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="<?php echo $contacto['icono']; ?>"></i>
                    </div>
                    <h4><?php echo $contacto['titulo']; ?></h4>
                    <p><?php echo $contacto['descripcion']; ?></p>
                    <p class="contact-info"><?php echo $contacto['contacto']; ?></p>
                    <?php if (!empty($contacto['telefono'])): ?>
                    <p class="contact-info"><?php echo $contacto['telefono']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="contact-form">
            <h4 class="mb-4">Envíanos un mensaje</h4>
            <form action="process_help_form.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Asunto</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Mensaje</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-submit">Enviar Mensaje</button>
            </form>
        </div>
    </section>
</div>

<!-- Chat en vivo -->
<div class="chat-bubble" id="chatBubble">
    <i class="fas fa-comments"></i>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>Sobre MoveSync</h5>
                <p>Plataforma de movilidad urbana que optimiza rutas y reporta incidencias en tiempo real para mejorar la experiencia de desplazamiento en la ciudad.</p>
                <div class="social-icons mt-3">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Enlaces Rápidos</h5>
                <ul class="list-unstyled">
                    <li><a href="../dashboard.php"><i class="fas fa-angle-right me-2"></i>Inicio</a></li>
                    <li><a href="routes.php"><i class="fas fa-angle-right me-2"></i>Rutas</a></li>
                    <li><a href="report.php"><i class="fas fa-angle-right me-2"></i>Incidencias</a></li>
                    <li><a href="LoyaltyPoints.php"><i class="fas fa-angle-right me-2"></i>Programa de Fidelización</a></li>
                    <li><a href="settings.php"><i class="fas fa-angle-right me-2"></i>Configuración</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Contacto</h5>
                <ul class="list-unstyled footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> Calle Innovación, 123, Madrid</li>
                    <li><i class="fas fa-phone"></i> +34 644 01 59 22</li>
                    <li><i class="fas fa-envelope"></i> info@movesync.com</li>
                </ul>
            </div>
        </div>
        <div class="row footer-bottom">
            <div class="col-md-6">
                <p>&copy; 2023 MoveSync. Todos los derechos reservados.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="privacy.php" class="me-3">Política de Privacidad</a>
                <a href="terms.php">Términos de Servicio</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script para el chat en vivo
    document.getElementById('chatBubble').addEventListener('click', function() {
        alert('El chat en vivo estará disponible próximamente. Por favor, utiliza nuestros otros canales de contacto.');
    });
</script>
</body>
</html>
