<?php
session_start();
include(__DIR__ . '/config/db_config.php'); 

if (!isset($_SESSION['usuario'])) {
    header("Location: php/altaLogin.php");
    exit();
}

$id_usuario = $_SESSION['usuario'];

// Obtener información del usuario
$sql = "SELECT * FROM Usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Error en la consulta del usuario: " . $conn->error);
}
$usuario = $result->fetch_assoc();

// Obtener las rutas más populares
$sqlRutas = "SELECT * FROM Rutas ORDER BY ID_Ruta LIMIT 3";
$resultRutas = $conn->query($sqlRutas);
if (!$resultRutas) {
    die("Error en la consulta de rutas: " . $conn->error);
}

// Obtener incidencias recientes
$sqlIncidencias = "SELECT i.*, r.Origen, r.Destino FROM Incidencias i 
                  JOIN Rutas r ON i.id_ruta = r.ID_Ruta 
                  WHERE i.estado = 'Pendiente' 
                  ORDER BY i.ID_Incidencia DESC LIMIT 5";
$resultIncidencias = $conn->query($sqlIncidencias);
if (!$resultIncidencias) {
    die("Error en la consulta de incidencias: " . $conn->error);
}

// Obtener puntos de lealtad
$sqlPuntos = "SELECT * FROM LoyaltyPoints WHERE id_usuario = ?";
$stmtPuntos = $conn->prepare($sqlPuntos);
$stmtPuntos->bind_param("i", $id_usuario);
$stmtPuntos->execute();
$resultPuntos = $stmtPuntos->get_result();
if (!$resultPuntos) {
    die("Error en la consulta de puntos: " . $conn->error);
}
$puntos = $resultPuntos->fetch_assoc();
?>

<?php include(__DIR__ . '/includes/header.php'); ?>

<div class="container mt-4">
    <!-- Encabezado con información del usuario -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="img/user_profile.png" alt="Perfil" class="img-fluid rounded-circle" style="max-width: 100px;">
                        </div>
                        <div class="col-md-7">
                            <h2>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>!</h2>
                            <p class="text-muted mb-0">Última conexión: <?php echo date('d/m/Y H:i'); ?></p>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="php/perfil.php" class="btn btn-outline-primary me-2">
                                <i class="fas fa-user-edit"></i> Editar Perfil
                            </a>
                            <a href="php/logout.php" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt"></i> Salir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos -->
<div class="row mb-4">
    <div class="col-md-12">
        <h4 class="mb-3">Accesos Rápidos</h4>
    </div>
    <div class="d-flex justify-content-between">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-route fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Consultar Rutas</h5>
                    <p class="card-text">Explora todas las rutas disponibles</p>
                    <a href="php/routes.php" class="btn btn-primary w-100">Ver Rutas</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Reportar Incidencia</h5>
                    <p class="card-text">Informa sobre cualquier problema</p>
                    <a href="php/report.php" class="btn btn-warning w-100">Reportar</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Mis Puntos de Fidelización</h5>
                    <p class="card-text">Consulta tus puntos acumulados</p>
                    <a href="php/LoyaltyPoints.php" class="btn btn-success w-100">Ver Puntos</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
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
                    <?php while ($incidencia = $resultIncidencias->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($incidencia['ID_Incidencia']); ?></td>
                            <td><?php echo htmlspecialchars($incidencia['Descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($incidencia['Origen']); ?></td>
                            <td><?php echo htmlspecialchars($incidencia['Destino']); ?></td>
                            <td><?php echo htmlspecialchars($incidencia['estado']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rutas Populares -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Rutas Populares</h4>
            <ul class="list-group">
                <?php while ($ruta = $resultRutas->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($ruta['Nombre']); ?> - ID: <?php echo htmlspecialchars($ruta['ID_Ruta']); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/includes/footer.php'); ?>