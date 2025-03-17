<?php
session_start();
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

        // Actualizar los puntos en la base de datos
        $newPoints = $currentPoints - $points;
        $sql = "UPDATE LoyaltyPoints SET total_points = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $newPoints, $this->id_usuario);

        if ($stmt->execute()) {
            // Registrar la transacción
            $this->logTransaction($points, "Canje: " . $description);
            return ["success" => true, "message" => "¡Enhorabuena! Has canjeado exitosamente " . $points . " puntos por: " . $description];
        } else {
            return ["success" => false, "message" => "Error al canjear puntos: " . $this->conn->error];
        }
    }

    // Método para agregar puntos
    public function addPoints($points) {
        $sql = "INSERT INTO LoyaltyPoints (id_usuario, total_points) VALUES (?, ?) ON DUPLICATE KEY UPDATE total_points = total_points + ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $this->id_usuario, $points, $points);

        if ($stmt->execute()) {
            if ($points > 0) {
                $this->logTransaction($points, "Puntos añadidos");
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
}

// Inicializar la clase LoyaltyPoints
$loyalty = new LoyaltyPoints($conn, $id_usuario);

// Procesar formulario si se envió
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['redeem']) && isset($_POST['option_id'])) {
        $optionId = $_POST['option_id'];
        $pointsRequired = $loyalty->getRedemptionOptions()[$optionId - 1]['points_required'];
        $description = $loyalty->getRedemptionOptions()[$optionId - 1]['description'];
        $result = $loyalty->redeemPoints($pointsRequired, $description);
        $message = $result['message'];
    }
}

// Obtener puntos actuales
$currentPoints = $loyalty->getPoints();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puntos de Fidelización - Versión Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
        }
        .container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-top: 60px;
        }
        h1, h2 {
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }
        .alert {
            margin-top: 20px;
        }
        .list-group-item {
            transition: background-color 0.3s, transform 0.2s;
        }
        .list-group-item:hover {
            background-color: #e2e6ea;
            transform: scale(1.02);
        }
        .btn-primary {
            width: 100%;
            font-size: 1.2em;
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .points-display {
            font-size: 1.5em;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin-bottom: 30px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Puntos de Fidelización</h1>
    <p class="points-display">Tienes un total de <strong><?php echo $currentPoints; ?></strong> puntos.</p>
    
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <h2>Gasta tus puntos</h2>
    <form method="POST">
        <div class="list-group">
            <?php foreach ($loyalty->getRedemptionOptions() as $option): ?>
                <div class="list-group-item">
                    <input type="radio" name="option_id" value="<?php echo $option['id']; ?>" required>
                    <strong><?php echo $option['description']; ?></strong> (Requiere <strong><?php echo $option['points_required']; ?></strong> puntos)
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" name="redeem" class="btn btn-primary mt-3">Canjear Puntos</button>
    </form>
    <br>
    <a href="../dashboard.php" class="btn btn-secondary">Volver</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>