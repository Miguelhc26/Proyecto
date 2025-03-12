<?php
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
            return 0; // Si no hay puntos, retornar 0
        }
    }

    // Método para canjear puntos
    public function redeemPoints($points) {
        // Verificar si el usuario tiene suficientes puntos
        $currentPoints = $this->getPoints();
        if ($currentPoints < $points) {
            return "No tienes suficientes puntos para canjear.";
        }

        // Actualizar los puntos en la base de datos
        $newPoints = $currentPoints - $points;
        $sql = "UPDATE LoyaltyPoints SET total_points = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $newPoints, $this->id_usuario);

        if ($stmt->execute()) {
            return "Puntos canjeados exitosamente.";
        } else {
            return "Error al canjear puntos: " . $this->conn->error;
        }
    }

    // Método para agregar puntos
    public function addPoints($points) {
        $sql = "INSERT INTO LoyaltyPoints (id_usuario, total_points) VALUES (?, ?) ON DUPLICATE KEY UPDATE total_points = total_points + ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $this->id_usuario, $points, $points);

        if ($stmt->execute()) {
            return "Puntos añadidos exitosamente.";
        } else {
            return "Error al añadir puntos: " . $this->conn->error;
        }
    }

    // Método para obtener las opciones de canje
    public function getRedemptionOptions() {
        // Aquí puedes definir las opciones de canje disponibles
        return [
            ['id' => 1, 'description' => 'Descuento de $10', 'points_required' => 100],
            ['id' => 2, 'description' => 'Descuento de $20', 'points_required' => 200],
            ['id' => 3, 'description' => 'Descuento de $50', 'points_required' => 500],
            // Agrega más opciones según sea necesario
        ];
    }
}
?>