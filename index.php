<?php include(__DIR__ . '/includes/header.php'); ?>

<!-- Carrusel Principal -->
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

<!-- Sección de características principales -->
<div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold">Simplifica tus viajes diarios</h2>
        <p class="lead text-muted">MoveSync ofrece todo lo que necesitas para moverte por la ciudad de manera eficiente y cómoda.</p>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <img src="assets/img/features/trip-planner.jpg" class="card-img-top" alt="Planificador de Viajes">
                <div class="card-body text-center">
                    <h5 class="card-title">Planifica tu Viaje</h5>
                    <p class="card-text">Organiza tus rutas diarias y encuentra la mejor forma de llegar a tu destino.</p>
                    <a href="php/trip_planner.php" class="btn btn-info">Planificar Ruta</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <img src="assets/img/features/live-alerts.jpg" class="card-img-top" alt=" Alertas en Vivo">
                <div class="card-body text-center">
                    <h5 class="card-title">Alertas en Vivo</h5>
                    <p class="card-text">Recibe notificaciones sobre cambios en el servicio y retrasos en tiempo real.</p>
                    <a href="php/live_alerts.php" class="btn btn-info">Ver Alertas</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <img src="assets/img/features/community-reports.jpg" class="card-img-top" alt="Reportes de la Comunidad">
                <div class="card-body text-center">
                    <h5 class="card-title">Reportes de la Comunidad</h5>
                    <p class="card-text">Contribuye a la mejora del servicio reportando incidencias y compartiendo experiencias.</p>
                    <a href="php/community_reports.php" class="btn btn-info">Reportar Incidencia</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Testimonios -->
<div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold">Lo que dicen nuestros usuarios</h2>
        <p class="lead text-muted">Escucha las experiencias de quienes ya utilizan MoveSync.</p>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <p class="card-text">"MoveSync ha cambiado la forma en que viajo. ¡Es tan fácil y conveniente!"</p>
                    <h5 class="card-title">- Carlos Gonzalez</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <p class="card-text">"Gracias a MoveSync, siempre llego a tiempo a mis citas. ¡Lo recomiendo!"</p>
                    <h5 class="card-title">- Francisco Penin</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <p class="card-text">"Me encanta poder reportar incidencias y ver que se toman en cuenta."</p>
                    <h5 class="card-title">- Sergio Alvarez</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/includes/footer.php'); ?>