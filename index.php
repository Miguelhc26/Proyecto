<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoveSync - Tu plataforma de transporte público</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos generales */
        html, body {
            height: 100%;
            margin: 0;
        }
        
        body {
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .content-wrapper {
            flex: 1 0 auto;
        }
        
        /* Mejoras del carrusel */
        .carousel-item {
            position: relative;
        }
        
        .carousel-caption {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 20px;
            max-width: 80%;
            margin: 0 auto;
        }
        
        /* Tarjetas de características */
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            border-radius: 10px;
            border: none;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            height: 200px;
            object-fit: cover;
        }
        
        /* Tarjetas de testimonios */
        .testimonial-card {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 30px;
            height: 100%;
        }
        
        .testimonial-card i {
            color: #ffc107;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        /* Sección de CTA */
        .cta-section {
            background-color: #007bff;
            color: white;
            padding: 60px 0;
            margin-top: 50px;
            text-align: center;
        }
        
        /* Footer */
        .footer {
            flex-shrink: 0;
            background-color: #343a40;
            color: white;
            padding: 40px 0;
            width: 100%;
        }
        
        .footer h5 {
            color: #fff;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .footer a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer a:hover {
            color: #fff;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255 , 255, 0.1);
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/includes/header.php'); ?>

    <div class="content-wrapper">
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/slide1.jpg" class="d-block w-100" alt="Rutas de buses" style="height: 500px; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h2 class="display-4 fw-bold">MoveSync</h2>
                        <p class="lead">Tu compañero de viaje en transporte público</p>
                        <a href="php/altaLogin.php" class="btn btn-primary btn-lg">Comenzar Ahora</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="assets/slide2.jpg" class="d-block w-100" alt="Reportar incidencias" style="height: 500px; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h2 class="display-4 fw-bold">Reporta Incidencias</h2>
                        <p class="lead">Ayúdanos a mejorar el servicio para todos</p>
                        <a href="php/report.php" class="btn btn-warning btn-lg">Reportar</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="assets/slide3.jpg" class="d-block w-100" alt="Programa de fidelización" style="height: 500px; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h2 class="display-4 fw-bold">Gana Puntos</h2>
                        <p class="lead">Acumula puntos y disfruta de beneficios exclusivos</p>
                        <a href="php/loyalty.php" class="btn btn-success btn-lg">Ver Programa</a>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold">Simplifica tus viajes diarios</h2>
        <p class="lead text-muted">MoveSync ofrece todo lo que necesitas para moverte por la ciudad de manera eficiente y cómoda.</p>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <img src="assets/feature1.jpg" class="card-img-top" alt="Planifica tu Viaje">
                <div class="card-body text-center">
                    <h5 class="card-title">Planifica tu Viaje</h5>
                    <p class="card-text">Organiza tus rutas diarias y encuentra la mejor forma de llegar a tu destino.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <img src="assets/feature2.jpg" class="card-img-top" alt="Alertas en Vivo">
                <div class="card-body text-center">
                    <h5 class="card-title">Alertas en Vivo</h5>
                    <p class="card-text">Recibe notificaciones sobre cambios en el servicio y retrasos en tiempo real.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <img src="assets/feature3.jpg" class="card-img-top" alt="Reportes de la Comunidad">
                <div class="card-body text-center">
                    <h5 class="card-title">Reportes de la Comunidad</h5>
                    <p class="card-text">Contribuye a la mejora del servicio reportando incidencias y compartiendo experiencias.</p>
                </div>
            </div>
        </div>
    </div>
</div>
        <div class="container mt-5">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Lo que dicen nuestros usuarios</h2>
                <p class="lead text-muted">Escucha las experiencias de quienes ya utilizan MoveSync.</p>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left"></i>
                        <p class="card-text">"MoveSync ha cambiado la forma en que viajo. ¡Es tan fácil y conveniente!"</p>
                        <h5 class="card-title">- Carlos Gonzalez</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left"></i>
                        <p class="card-text">"Gracias a MoveSync, siempre llego a tiempo a mis citas. ¡Lo recomiendo!"</p>
                        <h5 class="card-title">- Francisco Penin</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left"></i>
                        <p class="card-text">"Me encanta poder reportar incidencias y ver que se toman en cuenta."</p>
                        <h5 class="card-title">- Sergio Alvarez</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="cta-section">
            <h2 class="display-4">¡Únete a MoveSync hoy!</h2>
            <p class="lead">Descubre cómo podemos hacer que tus viajes sean más fáciles y eficientes.</p>
            <a href="php/altaLogin.php" class="btn btn-light btn-lg">Regístrate Ahora</a>
        </div>
    </div>

    <footer class="footer">
        <div class="container text-center">
            <h5>Contáctanos</h5>
            <p><a href="mailto:info@movesync.com">info@movesync.com</a></p>
            <div class="footer-bottom">
                <span class="text-muted">© 2025 MoveSync. Todos los derechos reservados.</span>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>