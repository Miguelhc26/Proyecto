<?php
session_start();
include(__DIR__ . '/config/db_config.php'); 

// Verificar sesión de usuario
if (!isset($_SESSION['usuario'])) {
    header("Location: php/altaLogin.php");
    exit();
}

$id_usuario = $_SESSION['usuario'];

// Función para manejar errores de consulta
function handleQueryError($conn, $error) {
    error_log("Error en la base de datos: " . $error);
    return "Ha ocurrido un error al obtener los datos. Por favor, inténtelo de nuevo más tarde.";
}

// Obtener información del usuario
try {
    $sql = "SELECT * FROM Usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception($conn->error);
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) throw new Exception($conn->error);
    $usuario = $result->fetch_assoc();
    
    if (!$usuario) {
        throw new Exception("No se encontró información para este usuario.");
    }
} catch (Exception $e) {
    $error_message = handleQueryError($conn, $e->getMessage());
    $usuario = ['nombre' => 'Usuario'];
}

// Obtener las rutas más populares
try {
    $sqlRutas = "SELECT * FROM Rutas ORDER BY ID_Ruta LIMIT 5";
    $resultRutas = $conn->query($sqlRutas);
    if (!$resultRutas) throw new Exception($conn->error);
} catch (Exception $e) {
    $error_message = handleQueryError($conn, $e->getMessage());
    $resultRutas = false;
}

// Obtener incidencias recientes
try {
    $sqlIncidencias = "SELECT i.*, r.Origen, r.Destino FROM Incidencias i 
                      JOIN Rutas r ON i.id_ruta = r.ID_Ruta 
                      ORDER BY i.ID_Incidencia DESC LIMIT 5";
    $resultIncidencias = $conn->query($sqlIncidencias);
    if (!$resultIncidencias) throw new Exception($conn->error);
} catch (Exception $e) {
    $error_message = handleQueryError($conn, $e->getMessage());
    $resultIncidencias = false;
}

// Obtener puntos de lealtad
try {
    $sqlPuntos = "SELECT * FROM LoyaltyPoints WHERE id_usuario = ?";
    $stmtPuntos = $conn->prepare($sqlPuntos);
    if (!$stmtPuntos) throw new Exception($conn->error);
    
    $stmtPuntos->bind_param("i", $id_usuario);
    $stmtPuntos->execute();
    $resultPuntos = $stmtPuntos->get_result();
    
    if (!$resultPuntos) throw new Exception($conn->error);
    $puntos = $resultPuntos->fetch_assoc();
} catch (Exception $e) {
    $error_message = handleQueryError($conn, $e->getMessage());
    $puntos = [];
}

// Calcular datos estadísticos para el dashboard
$puntosActuales = isset($puntos['points']) ? $puntos['points'] : 0;
$nivel = floor($puntosActuales / 100) + 1; // Nivel basado en puntos (cada 100 puntos = 1 nivel)
$porcentajeNivel = ($puntosActuales % 100); // Porcentaje para el siguiente nivel

// Obtener tiempo para siguiente nivel
$puntosParaSiguienteNivel = 100 - $porcentajeNivel;

// Obtener la fecha de última conexión
$ultima_conexion = date('d/m/Y H:i');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MoveSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos generales */
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }
        
        .content-wrapper {
            flex: 1;
            padding: 20px;
        }
        
        /* Tarjetas y elementos */
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }

        .text-primary {
            color: #007bff !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .progress {
            height: 20px;
        }

        .access-card {
            flex: 1;
            margin: 10px;
        }

        /* Estilos del footer */
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
            width: 100%;
            position: relative;
            bottom: 0;
        }

        footer a {
            color: #ffc107;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include(__DIR__ . '/includes/header.php'); ?>

<div class="container content-wrapper">
    <!-- Encabezado con información del usuario -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="assets/user_profile.jpg" alt="Perfil" class="img-fluid rounded-circle" style="max-width: 100px;">
                        </div>
                        <div class="col-md-7">
                            <h2>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>!</h2>
                            <p class="text-muted mb-0">Última conexión: <?php echo $ultima_conexion; ?></p>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="php/logout.php" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt"></i> Salir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Puntos -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Estadísticas de Puntos</h4>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Puntos Actuales: <?php echo $puntosActuales; ?></h5>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $porcentajeNivel; ?>%;" aria-valuenow="<?php echo $porcentajeNivel; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $porcentajeNivel; ?>%</div>
                    </div>
                    <p class="mt-2">Nivel: <?php echo $nivel; ?></p>
                    <p class="mt-2">Puntos para el siguiente nivel: <?php echo $puntosParaSiguienteNivel; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Accesos Rápidos</h4>
        </div>
        <div class="d-flex justify-content-between flex-wrap">
            <div class="access-card">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-route fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Consultar Rutas</h5>
                        <p class="card-text">Explora todas las rutas disponibles</p>
                        <a href="php/routes.php" class="btn btn-primary w-100">Ver Rutas</a>
                    </div>
                </div>
            </div>
            <div class="access-card">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Reportar Incidencia</h5>
                        <p class="card-text">Informa sobre cualquier problema</p>
                        <a href="php/report.php" class="btn btn-warning w-100">Reportar</a>
                    </div>
                </div>
            </div>
            <div class="access-card">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Mis Puntos de Fidelización</h5>
                        <p class="card-text">Consulta tus puntos acumulados</p>
                        <a href="php/LoyaltyPoints.php" class="btn btn-success w-100">Ver Puntos</a>
                    </div>
                </div>
            </div>
            <div class="access-card">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-cog fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Configuración</h5>
                        <p class="card-text">Ajusta tus preferencias</p>
                        <a href="php/settings.php" class="btn btn-info w-100">Configurar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incidencias Recientes -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Incidencias Recientes</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultIncidencias): ?>
                        <?php while ($incidencia = $resultIncidencias->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($incidencia['ID_Incidencia'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($incidencia['Descripcion'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($incidencia['Origen'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($incidencia['Destino'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($incidencia['estado'] ?? 'No definido'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay incidencias recientes.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rutas Populares -->
    <div class="row mb-4">
    <div class="col-md-12">
        <h4 class="mb-3">Rutas Populares</h4>
        <ul class="list-group">
            <?php if ($resultRutas): ?>
                <?php while ($ruta = $resultRutas->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($ruta['Nombre'] ?? 'Ruta sin nombre'); ?> - ID: <?php echo htmlspecialchars($ruta['ID_Ruta'] ?? 'No definido'); ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="list-group-item">No hay rutas populares disponibles.</li>
            <?php endif; ?>
        </ul>
    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>