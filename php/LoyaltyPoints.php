<?php
// Comprobar si la sesión ya está activa antes de iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once(__DIR__ . '/../config/db_config.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: altaLogin.php");
    exit();
}

$id_usuario = $_SESSION['usuario'];

class LoyaltyPoints {
    private $conn;
    private $id_usuario;

    public function __construct($conn, $id_usuario) {
        $this->conn = $conn;
        $this->id_usuario = $id_usuario;
    }

    // Método para obtener los puntos de lealtad del usuario
    public function getPoints() {
        $sql = "SELECT total_points FROM LoyaltyPoints WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total_points'];
        } else {
            // Si no hay registro, crear uno con 0 puntos
            $this->addPoints(0);
            return 0;
        }
    }

    // Método para canjear puntos
    public function redeemPoints($points, $description) {
        // Verificar si el usuario tiene suficientes puntos
        $currentPoints = $this->getPoints();
        if ($currentPoints < $points) {
            return ["success" => false, "message" => "No tienes suficientes puntos para canjear esta recompensa."];
        }

        // Generar código único para el viaje gratis
        $voucherCode = strtoupper(substr(md5($this->id_usuario . time()), 0, 6));
        
        // Actualizar los puntos en la base de datos
        $newPoints = $currentPoints - $points;
        $sql = "UPDATE LoyaltyPoints SET total_points = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $newPoints, $this->id_usuario);

        if ($stmt->execute()) {
            // Registrar la transacción incluyendo el código del viaje gratis
            $transactionDesc = "Canje: " . $description . " (Código: " . $voucherCode . ")";
            $this->logTransaction($points, $transactionDesc);
            
            // Mensaje para el usuario con el código del viaje gratis
            $message = "<strong>¡Enhorabuena!</strong> Has canjeado exitosamente " . $points . " puntos por: " . $description . 
                       "<br><br><div class='alert alert-info'>Tu código de viaje gratis: <strong>" . $voucherCode . 
                       "</strong><br>Muestra este código al conductor para disfrutar de tu viaje.</div>" .
                       "<button onclick='window.print()' class='btn btn-sm btn-primary mt-2'>" .
                       "<i class='fas fa-print me-2'></i>Imprimir resguardo</button>";
            
            return ["success" => true, "message" => $message];
        } else {
            return ["success" => false, "message" => "Error al canjear puntos: " . $this->conn->error];
        }
    }

    // Método para agregar puntos
    // Modificar este método en LoyaltyPoints.php
    public function addPoints($points, $description = "Puntos añadidos") {
        $sql = "INSERT INTO LoyaltyPoints (id_usuario, total_points) VALUES (?, ?) ON DUPLICATE KEY UPDATE total_points = total_points + ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $this->id_usuario, $points, $points);

        if ($stmt->execute()) {
            if ($points > 0) {
                $this->logTransaction($points, $description);
                return ["success" => true, "message" => "Puntos añadidos exitosamente."];
            }
            return ["success" => true, "message" => ""];
        } else {
            return ["success" => false, "message" => "Error al añadir puntos: " . $this->conn->error];
        }
    }

    // Método para registrar transacciones
    private function logTransaction($points, $description) {
        $sql = "INSERT INTO LoyaltyTransactions (id_usuario, points, description, transaction_date) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $this->id_usuario, $points , $description);
        $stmt->execute(); 
    }

    // Método para obtener las opciones de canje
    public function getRedemptionOptions() {
        return [
            ['id' => 1, 'description' => 'Descuento de 1 viaje gratis', 'points_required' => 100],
            ['id' => 2, 'description' => 'Descuento de 2 viajes gratis', 'points_required' => 200],
            ['id' => 3, 'description' => 'Descuento de 3 viajes gratis', 'points_required' => 500],
            // Agrega más opciones según sea necesario
        ];
    }
    
    // Método para obtener el historial de transacciones
    public function getTransactionHistory($limit = 5) {
        $sql = "SELECT points, description, transaction_date FROM LoyaltyTransactions 
                WHERE id_usuario = ? ORDER BY transaction_date DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->id_usuario, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        
        return $transactions;
    }
}

// Inicializar la clase LoyaltyPoints
$loyalty = new LoyaltyPoints($conn, $id_usuario);

// Procesar formulario si se envió
$message = '';
$messageType = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['redeem']) && isset($_POST['option_id'])) {
        $optionId = $_POST['option_id'];
        $redemptionOptions = $loyalty->getRedemptionOptions();
        
        // Verificar que la opción seleccionada existe
        if (isset($redemptionOptions[$optionId - 1])) {
            $pointsRequired = $redemptionOptions[$optionId - 1]['points_required'];
            $description = $redemptionOptions[$optionId - 1]['description'];
            $result = $loyalty->redeemPoints($pointsRequired, $description);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'danger';
        } else {
            $message = "Opción de canje inválida.";
            $messageType = 'danger';
        }
    }
}

// Obtener puntos actuales y historial de transacciones
$currentPoints = $loyalty->getPoints();
$transactionHistory = $loyalty->getTransactionHistory();

// Calcular porcentaje para el siguiente nivel (ejemplo básico)
$nextLevelPoints = 1000; // Ejemplo: Siguiente nivel a los 1000 puntos
$progressPercent = min(($currentPoints / $nextLevelPoints) * 100, 100);

// Determinar nivel actual (ejemplo básico)
$userLevel = floor($currentPoints / 100) + 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puntos de Fidelización - MoveSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            color: var(--gray-800);
        }

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

        .container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-top: 30px;
            margin-bottom: 30px;
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

        .points-summary {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff);
            color: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .points-summary::before {
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

        .points-display {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 20px 0;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(58, 123, 213, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(58, 123, 213, 0.4);
            background: linear-gradient(to right, #336db5, #00b8d9);
        }

        .btn-secondary {
            background-color: var(--gray-600);
            border: none;
            padding: 10px 20px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            background-color: var(--gray-700);
        }

        .redemption-option {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid var(--gray-200);
        }

        .redemption-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .redemption-option.selected {
            border-color: var(--primary-color);
            background-color: rgba(58, 123, 213, 0.05);
        }

        .redemption-option .form-check-input:checked ~ .form-check-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .points-progress {
            height: 10px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            overflow: hidden;
            margin: 15px 0;
        }

        .points-progress-bar {
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
        }

        .transaction-history {
            margin-top: 30px;
        }

        .transaction-item {
            padding: 15px;
            border-bottom: 1px solid var(--gray-200);
            transition: background-color 0.3s;
        }

        .transaction-item:hover {
            background-color: var(--gray-100);
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .transaction-date {
            color: var(--gray-600);
            font-size: 0.85rem;
        }

        .transaction-description {
            font-weight: 500;
        }

        .transaction-points {
            font-weight: 600;
            text-align: right;
        }

        .transaction-points.positive {
            color: var(--success-color);
        }

        .transaction-points.negative {
            color: var(--danger-color);
        }

        .level-badge {
            display: inline-block;
            padding: 5px 12px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }

        .level-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        @media print {
            .navbar, .btn, form, .points-summary, .col-lg-4, .row.mt-4, .mt-4 {
                display: none !important;
            }
            h2, .col-lg-8, .section-title {
                display: none !important;
            }
            .alert-info {
                border: 2px dashed #3a7bd5;
                padding: 20px;
                margin: 20px auto;
                max-width: 500px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<!-- Barra de navegación -->
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
                    <a class="nav-link active" href="LoyaltyPoints.php"><i class="fas fa-star"></i> Puntos</a>
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

<div class="container">
    <h2 class="text-center mb-4">Programa de Fidelización</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Resumen de Puntos -->
    <div class="points-summary">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="level-badge">Nivel <?php echo $userLevel; ?></span>
                <h3 class="mb-3">Tu balance de puntos</h3>
                <div class="points-display">
                    <i class="fas fa-star me-3"></i><?php echo $currentPoints; ?> puntos
                </div>
                <div class="points-progress">
                    <div class="points-progress-bar" style="width: <?php echo $progressPercent; ?>%;"></div>
                </div>
                <div class="level-info">
                    <span>Progreso: <?php echo number_format($progressPercent, 1); ?>%</span>
                    <span>Siguiente nivel: <?php echo $nextLevelPoints; ?> pts</span>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div style="background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 120px; height: 120px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-medal fa-4x" style="color: white;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <h4 class="section-title"><i class="fas fa-gift me-2"></i>Canjea tus puntos</h4>
            <form method="POST" id="redeemForm">
                <div class="redemption-options">
                    <?php foreach ($loyalty->getRedemptionOptions() as $option): ?>
                        <div class="redemption-option">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option_id" id="option<?php echo $option['id']; ?>" value="<?php echo $option['id']; ?>" required>
                                <label class="form-check-label d-flex justify-content-between align-items-center" for="option<?php echo $option['id']; ?>">
                                    <div>
                                        <strong><?php echo $option['description']; ?></strong>
                                        <p class="mb-0 text-muted"><?php echo $option['points_required']; ?> puntos</p>
                                    </div>
                                    <span class="badge bg-primary rounded-pill"><?php echo $option['points_required']; ?> pts</span>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="redeem" class="btn btn-primary mt-3 w-100">
                    <i class="fas fa-exchange-alt me-2"></i>Canjear Puntos
                </button>
            </form>
        </div>
        
        <div class="col-lg-4">
            <h4 class="section-title"><i class="fas fa-history me-2"></i>Historial</h4>
            <div class="card">
                <div class="card-body p-0">
                    <?php if (empty($transactionHistory)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                            <p>No hay transacciones recientes.</p>
                        </div>
                    <?php else: ?>
                        <div class="transaction-history">
                            <?php foreach ($transactionHistory as $transaction): ?>
                                <div class="transaction-item">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="transaction-description">
                                                <?php echo htmlspecialchars($transaction['description']); ?>
                                            </div>
                                            <div class="transaction-date">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($transaction['transaction_date'])); ?>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="transaction-points <?php echo (strpos($transaction['description'], 'Canje') === 0) ? 'negative' : 'positive'; ?>">
                                                <?php echo (strpos($transaction['description'], 'Canje') === 0) ? '-' : '+'; ?><?php echo $transaction['points']; ?> pts
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <a href="#" class="btn btn-sm btn-outline-primary w-100">Ver historial completo</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <h4 class="section-title"><i class="fas fa-info-circle me-2"></i>Acerca del programa</h4>
            <div class="card">
                <div class="card-body">
                    <h5>¿Cómo funciona el programa de fidelización?</h5>
                    <p>Nuestro programa de fidelización recompensa tu lealtad y uso continuo de nuestro servicio. Acumula puntos con cada viaje, reportes de incidencias y otras actividades en la plataforma.</p>
                    
                    <h5>¿Cómo puedo ganar más puntos?</h5>
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-route fa-2x text-primary mb-3"></i>
                                    <h6>Completa rutas</h6>
                                    <p class="small">Gana 10 puntos por cada viaje completado</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-flag fa-2x text-warning mb-3"></i>
                                    <h6>Reporta incidencias</h6>
                                    <p class="small">Gana 20 puntos por cada reporte útil</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x text-success mb-3"></i>
                                    <h6>Invita amigos</h6>
                                    <p class="small">Gana 50 puntos por cada amigo que se una</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="../dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar la selección de opciones de canje
        const redemptionOptions = document.querySelectorAll('.redemption-option');
        redemptionOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            option.addEventListener('click', function() {
                radio.checked = true;
                redemptionOptions.forEach(o => o.classList.remove('selected'));
                option.classList.add('selected');
            });
        });
        
        // Auto ocultar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    });
</script>
</body>
</html>