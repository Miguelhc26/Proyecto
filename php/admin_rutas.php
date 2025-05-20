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

// Procesar eliminación de ruta si se solicita
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_ruta = $_GET['delete'];
    
    try {
        // Preparar la consulta de eliminación
        $delete_sql = "DELETE FROM Rutas WHERE ID_Ruta = ?";
        $stmt = $conn->prepare($delete_sql);
        
        if ($stmt === false) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id_ruta);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            $success_message = "Ruta eliminada correctamente";
        } else {
            $error_message = "Error al eliminar la ruta: " . $stmt->error;
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $error_message = "Error al eliminar la ruta: " . $e->getMessage();
    }
}

// Obtener todas las rutas
$rutas = [];
$sql = "SELECT * FROM Rutas ORDER BY ID_Ruta DESC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rutas[] = $row;
    }
    $result->free();
} else {
    $error_message = "Error al obtener las rutas: " . $conn->error;
}

// Fecha actual
$fecha_actual = date('d-m-Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Rutas - MoveSync</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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

        /* Específico para gestión de rutas */
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

        .route-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 500;
            color: white;
            margin-right: 5px;
        }

        .badge-bus {
            background-color: var(--primary-color);
        }

        /* DataTables personalización */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color);
            color: white !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--secondary-color);
            color: white !important;
            border: none;
        }

        table.dataTable {
            border-collapse: collapse !important;
        }

        .table th {
            background-color: var(--gray-100);
            border-color: var(--gray-300);
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
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

        /* Iconos de acción */
        .action-icons a {
            display: inline-block;
            width: 32px;
            height: 32px;
            line-height: 32px;
            text-align: center;
            border-radius: 4px;
            color: white;
            margin-right: 5px;
            transition: all 0.3s;
        }

        .action-icons a:hover {
            transform: translateY(-2px);
        }

        .icon-view {
            background-color: var(--info-color);
        }

        .icon-edit {
            background-color: var(--warning-color);
        }

        .icon-delete {
            background-color: var(--danger-color);
        }

        /* Stats Cards */
        .stats-card {
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stats-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-card p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
        }

        .stats-card .stats-icon {
            position: absolute;
            right: 20px;
            bottom: 20px;
            font-size: 3rem;
            opacity: 0.2;
        }

        .bg-gradient-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }

        .bg-gradient-success {
            background: linear-gradient(to right, #28a745, #75d481);
        }

        .bg-gradient-warning {
            background: linear-gradient(to right, #fd7e14, #ffb474);
        }

        .bg-gradient-danger {
            background: linear-gradient(to right, #dc3545, #ff8a98);
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
                <h2><i class="fas fa-route me-2"></i>Gestión de Rutas</h2>
                <p class="mb-0">Administra las rutas de transporte en el sistema. Hoy es <?php echo $fecha_actual; ?></p>
            </div>
            <div class="col-md-4 text-end">
                <a href="admin_add_route.php" class="btn btn-light"><i class="fas fa-plus me-2"></i>Añadir Ruta</a>
            </div>
        </div>
    </div>

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

    <!-- Estadísticas -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card bg-gradient-primary">
                <div>
                    <h3><?php echo count($rutas); ?></h3>
                    <p>Total de Rutas</p>
                </div>
                <i class="fas fa-route stats-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-success">
                <div>
                    <h3><?php echo count($rutas); ?></h3>
                    <p>Rutas de Autobús</p>
                </div>
                <i class="fas fa-bus stats-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-warning">
                <div>
                    <h3>0</h3>
                    <p>Rutas en Mantenimiento</p>
                </div>
                <i class="fas fa-tools stats-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-danger">
                <div>
                    <h3>0</h3>
                    <p>Incidencias Reportadas</p>
                </div>
                <i class="fas fa-exclamation-triangle stats-icon"></i>
            </div>
        </div>
    </div>

    <!-- Tabla de Rutas -->
    <div class="card animate-fade-in">
        <div class="card-body">
            <h5 class="section-title">Listado de Rutas</h5>
            
            <div class="table-responsive">
                <table class="table table-hover" id="rutasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Horario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rutas as $ruta): ?>
                        <tr>
                            <td><?php echo $ruta['ID_Ruta']; ?></td>
                            <td><strong><?php echo htmlspecialchars($ruta['Nombre']); ?></strong></td>
                            <td><span class="route-badge badge-bus">Autobús</span></td>
                            <td><?php echo htmlspecialchars($ruta['Origen']); ?></td>
                            <td><?php echo htmlspecialchars($ruta['Destino']); ?></td>
                            <td><?php echo htmlspecialchars($ruta['Horario']); ?></td>
                            <td class="action-icons">
                                <a href="#" class="icon-delete" title="Eliminar" 
                                   onclick="confirmDelete(<?php echo $ruta['ID_Ruta']; ?>, '<?php echo htmlspecialchars($ruta['Nombre']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($rutas)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay rutas registradas.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar la ruta <span id="routeName" class="fw-bold"></span>? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animación de elementos al cargar la página
        const elements = document.querySelectorAll('.card, .section-title, .page-header, .stats-card');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.classList.add('animate-fade-in');
            }, index * 100);
        });
        
        // Inicializar DataTables
        $('#rutasTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            responsive: true,
            order: [[0, 'desc']], // Ordenar por ID en orden descendente
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]]
        });
    });
    
    // Función para confirmar eliminación
    function confirmDelete(id, name) {
        document.getElementById('routeName').textContent = name;
        document.getElementById('deleteLink').href = 'admin_rutas.php?delete=' + id;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
</body>
</html>