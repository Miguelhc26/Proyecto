<?php
class LoyaltyPoints {
    private $conn;
    private $user_id;

    public function __construct($dbConnection, $userId) {
        $this->conn = $dbConnection;
        $this->user_id = $userId;
    }

    // Obtener puntos de fidelidad del usuario
    public function getPoints() {
        $stmt = $this->conn->prepare("SELECT points FROM loyalty_points WHERE user_id = ?");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $pointsData = $result->fetch_assoc();
            return $pointsData['points'];
        } else {
            return 0; // Si no hay puntos, retornar 0
        }
    }

    // Canjear puntos
    public function redeemPoints($pointsToRedeem) {
        $currentPoints = $this->getPoints();

        if ($pointsToRedeem > $currentPoints) {
            return "No tienes suficientes puntos para canjear.";
        }

        // Actualizar los puntos en la base de datos
        $newPoints = $currentPoints - $pointsToRedeem;
        $stmt = $this->conn->prepare("UPDATE loyalty_points SET points = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $newPoints, $this->user_id);
        if ($stmt->execute()) {
            return "Canjeo exitoso. Te quedan $newPoints puntos.";
        } else {
            return "Error al canjear los puntos. Inténtalo de nuevo.";
        }
    }

    // Mostrar opciones de canje
    public function getRedemptionOptions() {
        return [
            "1. Canjea 100 puntos por un descuento del 10%",
            "2. Canjea 200 puntos por un descuento del 20%",
            "3. Canjea 500 puntos por un viaje gratis"
        ];
    }
}
?>