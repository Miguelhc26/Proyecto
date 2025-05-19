<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoveSync - Tu plataforma de transporte público</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <style>
        /* Variables de colores */
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
        html, body {
            height: 100%;
            margin: 0;
            scroll-behavior: smooth;
        }
        
        body {
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            color: var(--gray-800);
        }
        
        .content-wrapper {
            flex: 1 0 auto;
        }

        /* Header y navegación */
        .navbar {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            padding: 12px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
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

        /* Mejoras del hero carrusel */
        .hero-carousel {
            position: relative;
        }
        
        .carousel-item {
            height: 650px;
            position: relative;
        }
        
        .carousel-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6));
        }
        
        .carousel-item img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            filter: brightness(0.9);
        }
        
        .carousel-caption {
            bottom: 25%;
            left: 15%;
            right: 15%;
            text-align: left;
            z-index: 10;
        }
        
        .carousel-caption h2 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 1s ease-out;
        }
        
        .carousel-caption p {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 1s ease-out 0.2s;
            animation-fill-mode: both;
            max-width: 600px;
        }
        
        .carousel-caption .btn {
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease-out 0.4s;
            animation-fill-mode: both;
            transition: all 0.3s ease;
        }
        
        .carousel-caption .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        .carousel-indicators {
            z-index: 15;
        }
        
        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 5px;
        }

        .carousel-control-prev, .carousel-control-next {
            width: 5%;
            z-index: 15;
        }

        /* Sección principal */
        .main-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-heading {
            margin-bottom: 60px;
            position: relative;
        }
        
        .section-heading h2 {
            font-weight: 700;
            position: relative;
            z-index: 1;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .section-heading h2::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }
        
        .section-heading p {
            color: var(--gray-600);
            max-width: 800px;
            margin: 0 auto;
        }

        /* Tarjetas de características */
        .feature-card {
            transition: all 0.4s ease;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            border: none;
            position: relative;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card img {
            height: 220px;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .feature-card:hover img {
            transform: scale(1.1);
        }
        
        .feature-card .card-img-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0));
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 25px;
        }
        
        .feature-card .card-title {
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .feature-card .card-text {
            color: rgba(255,255,255,0.9);
            font-size: 1rem;
        }
        
        .feature-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Stats section */
        .stats-section {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            padding: 70px 0;
            color: white;
            text-align: center;
        }
        
        .stat-item {
            padding: 20px;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Tarjetas de testimonios */
        .testimonials-section {
            padding: 80px 0;
            background-color: var(--gray-100);
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 15px;
            margin-bottom: 30px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-card .quote-icon {
            color: var(--primary-color);
            font-size: 2rem;
            position: absolute;
            top: 15px;
            left: 20px;
            opacity: 0.1;
        }
        
        .testimonial-card .content {
            padding-top: 25px;
            font-style: italic;
            font-size: 1.1rem;
            color: var(--gray-700);
            line-height: 1.6;
        }
        
        .testimonial-card .author {
            margin-top: 20px;
            display: flex;
            align-items: center;
        }
        
        .testimonial-card .author img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 3px solid var(--gray-200);
        }
        
        .testimonial-card .author-info h5 {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .testimonial-card .author-info p {
            margin: 0;
            color: var(--gray-600);
            font-size: 0.9rem;
        }
        
        .testimonial-card .rating {
            color: var(--accent-color);
            margin-top: 15px;
        }

        /* Steps section */
        .steps-section {
            padding: 80px 0;
            background-color: white;
        }
        
        .step-item {
            text-align: center;
            padding: 20px;
            position: relative;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 20px;
            position: relative;
            z-index: 2;
        }
        
        .step-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark-color);
        }
        
        .step-description {
            color: var(--gray-600);
        }
        
        .step-connector {
            position: absolute;
            top: 45px;
            left: 50%;
            width: 100%;
            height: 2px;
            background-color: var(--gray-300);
            z-index: 1;
        }
        
        .step-item:last-child .step-connector {
            display: none;
        }

        /* Sección de CTA */
        .cta-section {
            background: linear-gradient(135deg, rgba(58,123,213,0.9), rgba(0,210,255,0.9)), url('assets/cta-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2);
        }
        
        .cta-content {
            position: relative;
            z-index: 10;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .cta-section h2 {
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .cta-section .btn {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .cta-section .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        /* Footer */
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 60px 0 20px;
        }
        
        .footer h5 {
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
        
        .footer h5::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 40px;
            height: 2px;
            background-color: var(--accent-color);
        }
        
        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--accent-color);
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
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 1s ease forwards;
        }
        
        .animate-fade-up {
            animation: fadeInUp 1s ease forwards;
        }
        
        /* Delay animations */
        .delay-1 {
            animation-delay: 0.2s;
        }
        
        .delay-2 {
            animation-delay: 0.4s;
        }
        
        .delay-3 {
            animation-delay: 0.6s;
        }

        /* Botones */
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }
        
        .btn-primary:hover {
            background: linear-gradient(to right, #3672c6, #00c4ee);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-lg {
            padding: 12px 30px;
            font-size: 1.1rem;
        }

        /* Media Queries */
        @media (max-width: 992px) {
            .carousel-item {
                height: 550px;
            }
            
            .carousel-caption {
                bottom: 20%;
            }
            
            .carousel-caption h2 {
                font-size: 2.8rem;
            }
            
            .step-connector {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .carousel-item {
                height: 450px;
            }
            
            .carousel-caption {
                bottom: 15%;
                text-align: center;
                left: 10%;
                right: 10%;
            }
            
            .carousel-caption h2 {
                font-size: 2.5rem;
            }
            
            .carousel-caption p {
                font-size: 1.2rem;
                margin: 0 auto 1.5rem;
            }
            
            .section-heading {
                margin-bottom: 40px;
            }
            
            .cta-section {
                padding: 60px 0;
            }
            
            .cta-section h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .carousel-item {
                height: 400px;
            }
            
            .carousel-caption h2 {
                font-size: 2rem;
            }
            
            .carousel-caption p {
                font-size: 1rem;
            }
            
            .feature-card {
                margin-bottom: 20px;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header con Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-sync-alt"></i> MoveSync
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="fas fa-home"></i> Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features"><i class="fas fa-star"></i> Características</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials"><i class="fas fa-comment"></i> Testimonios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact"><i class="fas fa-envelope"></i> Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="php/altaLogin.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light rounded-pill ms-2" href="php/altaLogin.php?register=true">Registrarse</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <!-- Hero Carousel -->
        <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/slide1.jpg" class="d-block" alt="MoveSync Platform">
                    <div class="carousel-caption">
                        <h2>Simplifica tu movilidad urbana</h2>
                        <p>MoveSync facilita tu día a día integrando todas las opciones de transporte público en una sola plataforma.</p>
                        <a href="php/altaLogin.php" class="btn btn-primary btn-lg">Comenzar Ahora <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="assets/slide2.jpg" class="d-block" alt="Reportar incidencias">
                    <div class="carousel-caption">
                        <h2>Reporta incidencias en tiempo real</h2>
                        <p>Contribuye con la comunidad reportando incidencias y obtén puntos por tus aportes.</p>
                        <a href="php/report.php" class="btn btn-warning btn-lg">Reportar Ahora <i class="fas fa-flag ms-2"></i></a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="assets/slide3.jpg" class="d-block" alt="Programa de fidelización">
                    <div class="carousel-caption">
                        <h2>Acumula puntos y obtén beneficios</h2>
                        <p>Nuestro programa de fidelización premia tu participación con descuentos y ventajas exclusivas.</p>
                        <a href="php/loyalty.php" class="btn btn-success btn-lg">Ver Beneficios <i class="fas fa-gift ms-2"></i></a>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <!-- Sección de Características -->
        <section class="main-section" id="features">
            <div class="container">
                <div class="section-heading text-center">
                    <h2>Simplifica tus viajes diarios</h2>
                    <p>MoveSync ofrece todo lo que necesitas para moverte por la ciudad de manera eficiente y cómoda.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6 animate-fade-up">
                        <div class="card feature-card">
                            <img src="assets/feature1.jpg" class="card-img" alt="Planifica tu Viaje">
                            <div class="feature-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div class="card-img-overlay">
                                <h5 class="card-title">Planifica tu Viaje</h5>
                                <p class="card-text">Organiza tus rutas diarias y encuentra la mejor forma de llegar a tu destino.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 animate-fade-up delay-1">
                        <div class="card feature-card">
                            <img src="assets/feature2.jpg" class="card-img" alt="Alertas en Vivo">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="card-img-overlay">
                                <h5 class="card-title">Alertas en Vivo</h5>
                                <p class="card-text">Recibe notificaciones sobre cambios en el servicio y retrasos en tiempo real.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 animate-fade-up delay-2">
                        <div class="card feature-card">
                            <img src="assets/feature3.jpg" class="card-img" alt="Reportes de la Comunidad">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-img-overlay">
                                <h5 class="card-title">Reportes de la Comunidad</h5>
                                <p class="card-text">Contribuye a la mejora del servicio reportando incidencias y compartiendo experiencias.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Estadísticas -->
        <section class="stats-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="stat-item animate-fade-in">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number" data-count="25000">25,000+</div>
                            <div class="stat-label">Usuarios activos</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item animate-fade-in delay-1">
                            <div class="stat-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <div class="stat-number" data-count="500">500+</div>
                            <div class="stat-label">Rutas cubiertas</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item animate-fade-in delay-2">
                            <div class="stat-icon">
                                <i class="fas fa-flag"></i>
                            </div>
                            <div class="stat-number" data-count="12500">12,500+</div>
                            <div class="stat-label">Incidencias reportadas</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-item animate-fade-in delay-3">
                            <div class="stat-icon">
                                <i class="fas fa-city"></i>
                            </div>
                            <div class="stat-number" data-count="15">15+</div>
                            <div class="stat-label">Ciudades conectadas</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cómo funciona -->
        <section class="steps-section">
            <div class="container">
                <div class="section-heading text-center">
                    <h2>Cómo funciona MoveSync</h2>
                    <p>Comienza a usar nuestra plataforma en simples pasos</p>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="step-item animate-fade-up">
                            <div class="step-number">1</div>
                            <div class="step-connector"></div>
                            <h4 class="step-title">Regístrate</h4>
                            <p class="step-description">Crea tu cuenta gratuita en nuestra plataforma en menos de un minuto</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="step-item animate-fade-up delay-1">
                            <div class="step-number">2</div>
                            <div class="step-connector"></div>
                            <h4 class="step-title">Configura tu perfil</h4>
                            <p class="step-description">Personaliza tus preferencias de viaje y rutas frecuentes</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="step-item animate-fade-up delay-2">
                            <div class="step-number">3</div>
                            <div class="step-connector"></div>
                            <h4 class="step-title">Consulta rutas</h4>
                            <p class="step-description">Accede a la información actualizada sobre rutas y horarios</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="step-item animate-fade-up delay-3">
                            <div class="step-number">4</div>
                            <h4 class="step-title">Reporta incidencias</h4>
                            <p class="step-description">Contribuye con la comunidad y gana puntos de fidelización</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Testimonios -->
        <section class="testimonials-section" id="testimonials">
            <div class="container">
                <div class="section-heading text-center">
                    <h2>Lo que dicen nuestros usuarios</h2>
                    <p>Experiencias reales de personas que ya utilizan MoveSync en su día a día</p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card animate-fade-in">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <div class="content">
                                "MoveSync ha cambiado por completo la forma en que me desplazo por la ciudad. La información en tiempo real me ayuda a evitar retrasos y llegar siempre puntual. ¡Es una herramienta imprescindible!"
                            </div>
                            <div class="author">
                                <img src="assets/testimonial1.jpg" alt="Carlos González">
                                <div class="author-info">
                                    <h5>Carlos González</h5>
                                    <p>Profesional de Marketing</p>
                                </div>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card animate-fade-in delay-1">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <div class="content">
                                "Gracias a MoveSync, siempre estoy informado sobre cualquier interrupción en mis rutas habituales. La aplicación me sugiere alternativas y me ha ahorrado mucho tiempo. La recomiendo sin dudarlo."
                            </div>
                            <div class="author">
                                <img src="assets/testimonial2.jpg" alt="Francisco Penin">
                                <div class="author-info">
                                    <h5>Francisco Penin</h5>
                                    <p>Estudiante universitario</p>
                                </div>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card animate-fade-in delay-2">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <div class="content">
                                "Me encanta poder reportar incidencias y ver que realmente se toman en cuenta. El sistema de puntos es genial y lo mejor es ver cómo mis reportes ayudan a otros usuarios. MoveSync crea una verdadera comunidad."
                            </div>
                            <div class="author">
                                <img src="assets/testimonial3.jpg" alt="Sergio Alvarez">
                                <div class="author-info">
                                    <h5>Sergio Alvarez</h5>
                                    <p>Ingeniero de Software</p>
                                </div>
                            </div>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- App Features -->
        <section class="main-section bg-light">
            <div class="container">
                <div class="section-heading text-center">
                    <h2>Más razones para elegir MoveSync</h2>
                    <p>Descubre todas las ventajas que nuestra plataforma tiene para ofrecerte</p>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <img src="assets/app-features.jpg" alt="MoveSync Features" class="img-fluid rounded-3 shadow">
                    </div>
                    <div class="col-lg-6">
                        <div class="row g-4">
                            <div class="col-md-6 animate-fade-up">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3 text-primary">
                                        <i class="fas fa-map-marked-alt fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5>Mapas interactivos</h5>
                                        <p class="text-muted">Visualiza las rutas y paradas en mapas detallados e interactivos.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 animate-fade-up delay-1">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3 text-warning">
                                        <i class="fas fa-bell fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5>Notificaciones</h5>
                                        <p class="text-muted">Recibe alertas personalizadas sobre tus rutas favoritas.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 animate-fade-up delay-2">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3 text-success">
                                        <i class="fas fa-star fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5>Programa de fidelización</h5>
                                        <p class="text-muted">Acumula puntos y canjéalos por beneficios exclusivos.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 animate-fade-up delay-3">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3 text-info">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5>Estadísticas personales</h5>
                                        <p class="text-muted">Visualiza datos sobre tus viajes y contribuciones.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 animate-fade-up delay-2">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3 text-danger">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5>Horarios en tiempo real</h5>
                                        <p class="text-muted">Consulta los horarios actualizados y las demoras.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 animate-fade-up delay-3">
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3 text-secondary">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5>Comunidad activa</h5>
                                        <p class="text-muted">Forma parte de una red colaborativa de usuarios.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section" id="contact">
            <div class="cta-content">
                <h2>¡Únete a MoveSync hoy!</h2>
                <p>Descubre cómo podemos hacer que tus viajes diarios sean más fáciles, previsibles y eficientes. Regístrate ahora y comienza a disfrutar de todos nuestros beneficios.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="php/altaLogin.php?register=true" class="btn btn-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i> Registrarse
                    </a>
                    <a href="php/altaLogin.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                    </a>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer" id="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                    <h5>Sobre MoveSync</h5>
                    <p>MoveSync es una plataforma innovadora diseñada para mejorar la experiencia de transporte público a través de la colaboración y la información en tiempo real.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php"><i class="fas fa-angle-right me-2"></i>Inicio</a></li>
                        <li><a href="#features"><i class="fas fa-angle-right me-2"></i>Características</a></li>
                        <li><a href="#testimonials"><i class="fas fa-angle-right me-2"></i>Testimonios</a></li>
                        <li><a href="php/faq.php"><i class="fas fa-angle-right me-2"></i>Preguntas frecuentes</a></li>
                        <li><a href="php/about.php"><i class="fas fa-angle-right me-2"></i>Sobre nosotros</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="php/terms.php"><i class="fas fa-angle-right me-2"></i>Términos de uso</a></li>
                        <li><a href="php/privacy.php"><i class="fas fa-angle-right me-2"></i>Política de privacidad</a></li>
                        <li><a href="php/cookies.php"><i class="fas fa-angle-right me-2"></i>Política de cookies</a></li>
                        <li><a href="php/legal.php"><i class="fas fa-angle-right me-2"></i>Aviso legal</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5>Contacto</h5>
                    <div class="footer-contact">
                        <p><i class="fas fa-map-marker-alt"></i> Av. Innovación, 123, Madrid</p>
                        <p><i class="fas fa-phone"></i> +34 91 123 45 67</p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:info@movesync.com">info@movesync.com</a></p>
                        <p><i class="fas fa-clock"></i> Lun - Vie: 9:00 - 18:00</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p class="mb-0">© 2025 MoveSync. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Activar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Contador para números en estadísticas
            function animateValue(obj, start, end, duration) {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    obj.innerHTML = Math.floor(progress * (end - start) + start).toLocaleString() + (obj.innerHTML.includes('+') ? '+' : '');
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }
            
            // Detectar cuando elementos entran en el viewport
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };
            
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Si es un contador de estadísticas
                        if (entry.target.classList.contains('stat-number')) {
                            const count = parseInt(entry.target.getAttribute('data-count'));
                            animateValue(entry.target, 0, count, 2000);
                        }
                        
                        // Si es un elemento animado
                        if (entry.target.classList.contains('animate-fade-in') || 
                            entry.target.classList.contains('animate-fade-up')) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            // Observar todos los elementos que deben animarse
            document.querySelectorAll('.stat-number, .animate-fade-in, .animate-fade-up').forEach(el => {
                observer.observe(el);
                // Ocultar inicialmente
                if (el.classList.contains('animate-fade-in') || el.classList.contains('animate-fade-up')) {
                    el.style.opacity = '0';
                    el.style.transform = el.classList.contains('animate-fade-up') ? 'translateY(30px)' : 'none';
                    el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                }
            });
            
            // Scroll suave para enlaces internos
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>