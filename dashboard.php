<?php
session_start();
include(__DIR__ . '/config/db_config.php'); 

if (!isset($_SESSION['usuario'])) {
    header("Location: php/altaLogin.php");
    exit();
}

$id_usuario = $_SESSION['usuario'];

// Obtener información del usuario
$sql = "SELECT * FROM Usuarios WHERE id_usuario = $id_usuario";
$result = $conn->query($sql);
$usuario = $result->fetch_assoc();

// Obtener las rutas más populares
$sqlRutas = "SELECT * FROM Rutas ORDER BY ID_Ruta LIMIT 3";
$resultRutas = $conn->query($sqlRutas);

// Obtener incidencias recientes
$sqlIncidencias = "SELECT i.*, r.Origen, r.Destino FROM Incidencias i 
                  JOIN Rutas r ON i.Ruta = r.ID_Ruta 
                  WHERE i.estado = 'Pendiente' 
                  ORDER BY i.ID_Incidencia DESC LIMIT 5";
$resultIncidencias = $conn->query($sqlIncidencias);

// Obtener pagos recientes si existen
$sqlPagos = "SELECT * FROM Pagos WHERE id_usuario = $id_usuario ORDER BY fecha_pago DESC LIMIT 3";
$resultPagos = $conn->query($sqlPagos);
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
                <div class="card-body text-center <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Reportar Incidencia</h5>
                    <p class="card-text">Informa sobre cualquier problema en las rutas</p>
                    <a href="php/report.php" class="btn btn-warning w-100">Reportar</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Realizar Pago</h5>
                    <p class="card-text">Gestiona tus pagos de manera sencilla</p>
                    <a href="php/pay.php" class="btn btn-success w-100">Pagar</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-sign-out-alt fa-3x text-danger mb-3"></i>
                    <h5 class="card-title">Cerrar Sesión</h5>
                    <p class="card-text">Sal de tu cuenta de forma segura</p>
                    <a href="php/logout.php" class="btn btn-danger w-100">Salir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Rutas Populares -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Rutas Populares</h4>
        </div>
        <?php while ($ruta = $resultRutas->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($ruta['Origen']) . " → " . htmlspecialchars($ruta['Destino']); ?></h5>
                        <p class="card-text">Horario: <?php echo htmlspecialchars($ruta['Horario']); ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Sección de Incidencias Recientes -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Incidencias Recientes</h4>
        </div>
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ruta</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($incidencia = $resultIncidencias->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($incidencia['ID_Incidencia']); ?></td>
                            <td><?php echo htmlspecialchars($incidencia['Origen']) . " → " . htmlspecialchars($incidencia['Destino']); ?></td>
                            <td><?php echo htmlspecialchars($incidencia['Descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($incidencia['Estado']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sección de Pagos Recientes -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="mb-3">Pagos Recientes</h4>
        </div>
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Pago</th>
                        <th>Monto</th>
                        <th>Método de Pago</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pago = $resultPagos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pago['id_pago']); ?></td>
                            <td><?php echo htmlspecialchars($pago['monto']); ?></td>
                            <td><?php echo htmlspecialchars($pago['metodo_pago']); ?></td>
                            <td><?php echo htmlspecialchars($pago['fecha_pago']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?