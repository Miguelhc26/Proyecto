<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['admin'])) {
    header("Location: ../php/altaLogin.php");
    exit();
}

include(__DIR__ . '/../config/db_config.php');

// Parámetros para paginación
$recordsPerPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Parámetro de búsqueda
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = " WHERE nombre LIKE '%$search%' OR correo LIKE '%$search%' OR tipo LIKE '%$search%'";
}

// Contar el total de registros para la paginación
$countQuery = "SELECT COUNT(*) as total FROM Usuarios" . $searchCondition;
$countResult = $conn->query($countQuery);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Obtener usuarios con paginación
$sqlUsuarios = "SELECT id_usuario, nombre, correo, tipo FROM Usuarios" . 
               $searchCondition . 
               " ORDER BY id_usuario DESC LIMIT $offset, $recordsPerPage";
$resultUsuarios = $conn->query($sqlUsuarios);

// Para mostrar estadísticas
$sqlEstadisticas = "SELECT tipo, COUNT(*) as total FROM Usuarios GROUP BY tipo";
$resultEstadisticas = $conn->query($sqlEstadisticas);
$estadisticas = [];
while ($row = $resultEstadisticas->fetch_assoc()) {
    $estadisticas[$row['tipo']] = $row['total'];
}

// Fecha actual
$fecha_actual = date('d-m-Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - MoveSync</title> 
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

        /* Tablas */
        .table th {
            font-weight: 600;
            background-color: var(--gray-100);
            border-top: none;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: var(--gray-700);
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
            border-color: var(--gray-200);
            padding: 12px 16px;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.01);
        }

        .table thead th {
            border-bottom: 2px solid var(--gray-300);
        }

        /* Badges y Estados */
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 6px;
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

        /* Específico para gestión de usuarios */
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

        .search-container {
            position: relative;
        }

        .search-container i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: var(--gray-500);
        }

        .search-input {
            padding-left: 40px;
            border-radius: 25px;
            border: 1px solid var(--gray-300);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
            height: 45px;
            width: 100%;
        }

        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(58, 123, 213, 0.25);
            border-color: var(--primary-color);
        }

        .stat-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 18px;
        }

        .stat-admin {
            background-color: #3a7bd5;
        }

        .stat-user {
            background-color: #00d2ff;
        }

        .stat-support {
            background-color: #ffc107;
        }

        .user-stats {
            margin-bottom: 20px;
        }

        .role-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .role-admin {
            background-color: rgba(58, 123, 213, 0.1);
            color: var(--primary-color);
        }

        .role-user {
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--secondary-color);
        }

        .role-support {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }

        .user-action-btn {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 5px;
        }

        .pagination .page-link {
            border-color: var(--gray-200);
            color: var(--gray-700);
            margin: 0 3px;
            border-radius: 5px;
            font-weight: 500;
            padding: 8px 14px;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
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
                <h2><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
                <p class="mb-0">Administra los usuarios registrados en el sistema. Hoy es <?php echo $fecha_actual; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="admin_add_user.php" class="btn btn-light"><i class="fas fa-user-plus me-2"></i>Nuevo Usuario</a>
                <a href="admin_dashboard.php" class="btn btn-outline-light ms-2"><i class="fas fa-arrow-left me-2"></i>Volver</a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar con estadísticas y búsqueda -->
        <div class="col-lg-3">
            <!-- Tarjeta de búsqueda -->
            <div class="card mb-4 animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-search me-2"></i>Buscar Usuarios</h5>
                    <form method="get" action="" class="mb-3">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search" name="search" class="form-control search-input" 
                                   placeholder="Nombre, correo o rol" 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            <?php if (!empty($search)): ?>
                                <a href="admin_users.php" class="btn btn-outline-secondary">Limpiar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tarjeta de estadísticas -->
            <div class="card animate-fade-in">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-chart-pie me-2"></i>Estadísticas de Usuarios</h5>
                    <div class="user-stats">
                        <?php 
                        $bg_colors = [
                            'Administrador' => 'stat-admin',
                            'Usuario' => 'stat-user',
                            'Soporte' => 'stat-support'
                        ];
                        $icons = [
                            'Administrador' => 'fa-user-shield',
                            'Usuario' => 'fa-user',
                            'Soporte' => 'fa-headset'
                        ];
                        foreach ($estadisticas as $tipo => $total): 
                            $bg_class = isset($bg_colors[$tipo]) ? $bg_colors[$tipo] : 'bg-secondary';
                            $icon = isset($icons[$tipo]) ? $icons[$tipo] : 'fa-user';
                        ?>
                            <div class="stat-card bg-white shadow-sm">
                                <div class="stat-icon <?php echo $bg_class; ?>">
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($tipo); ?></h6>
                                    <div class="h4 mb-0 fw-bold"><?php echo htmlspecialchars($total); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla principal de usuarios -->
        <div class="col-lg-9">
            <div class="card animate-fade-in">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Lista de Usuarios
                            <?php if(!empty($search)): ?>
                                <span class="text-muted fs-6 ms-2">
                                    Resultados para: "<?php echo htmlspecialchars($search); ?>"
                                </span>
                            <?php endif; ?>
                        </h5>
                        
                        <span class="badge bg-primary"><?php echo $totalRecords; ?> usuarios</span>
                    </div>
                    
                    <?php if ($resultUsuarios->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>NOMBRE</th>
                                        <th>CORREO</th>
                                        <th>ROL</th>
                                        <th>ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $resultUsuarios->fetch_assoc()): 
                                        // Determinar la clase de badge según el rol
                                        $roleBadgeClass = 'role-user';
                                        if ($row['tipo'] == 'Administrador') {
                                            $roleBadgeClass = 'role-admin';
                                        } else if ($row['tipo'] == 'Soporte') {
                                            $roleBadgeClass = 'role-support';
                                        }
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo htmlspecialchars($row['id_usuario']); ?></strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <?php echo htmlspecialchars($row['nombre']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                            <td>
                                                <span class="role-badge <?php echo $roleBadgeClass; ?>">
                                                    <?php echo htmlspecialchars($row['tipo']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">    
                                                    <a href="admin_edit_user.php?id=<?php echo $row['id_usuario']; ?>" 
                                                       class="btn btn-sm btn-warning user-action-btn" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="admin_delete_user.php?id=<?php echo $row['id_usuario']; ?>" 
                                                       class="btn btn-sm btn-danger user-action-btn" 
                                                       onclick="return confirm('¿Estás seguro de eliminar este usuario?');" 
                                                       title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginación mejorada -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Paginación de usuarios" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>" aria-label="Anterior">
                                                <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php
                                    // Mostrar un número limitado de páginas
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($totalPages, $page + 2);
                                    
                                    if ($startPage > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '">1</a></li>';
                                        if ($startPage > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    
                                    for ($i = $startPage; $i <= $endPage; $i++) {
                                        echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
                                        echo '<a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a>';
                                        echo '</li>';
                                    }
                                    
                                    if ($endPage < $totalPages) {
                                        if ($endPage < $totalPages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&search=' . urlencode($search) . '">' . $totalPages . '</a></li>';
                                    }
                                    ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>" aria-label="Siguiente">
                                                <span aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            No se encontraron usuarios
                            <?php if (!empty($search)): ?>
                                con los criterios de búsqueda proporcionados.
                                <div class="mt-3">
                                    <a href="admin_users.php" class="btn btn-sm btn-outline-primary">Ver todos los usuarios</a>
                                </div>
                            <?php else: ?>
                                registrados en el sistema.
                                <div class="mt-3">
                                    <a href="admin_add_user.php" class="btn btn-sm btn-primary">Crear nuevo usuario</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
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
    // Animación de elementos al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.card, .section-title, .page-header');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('animate-fade-in');
            }, index * 100);
        });
        
        // Enfoque automático en el campo de búsqueda si hay un término de búsqueda
        const searchInput = document.getElementById('search');
        if (searchInput.value.trim() !== '') {
            searchInput.focus();
        }
    });
    
    // Confirmación para eliminar usuarios
    function confirmDelete(userId) {
        return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');
    }
</script>
</body>
</html>
