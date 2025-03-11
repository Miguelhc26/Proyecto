<?php
include('config/db_config.php');
include('includes/header.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['usuario'];
    $monto = $_POST['monto'];
    $metodo_pago = $_POST['metodo_pago'];

    $sql = "INSERT INTO Pagos (id_usuario, monto, metodo_pago) VALUES ($id_usuario, $monto, '$metodo_pago')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Pago realizado correctamente.');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<div class="container mt-5">
    <h1 class="text-center">Realizar Pago</h1>
    <form method="POST" class="mt-4">
        <label>Monto a Pagar:</label>
        <input type="number" name="monto" class="form-control" required>
        <label>Método de Pago:</label>
        <select name="metodo_pago" class="form-control" required>
            <option value="Tarjeta">Tarjeta</option>
            <option value="PayPal">PayPal</option>
        </select>
        <button type="submit" class="btn btn-success mt-3">Pagar</button>
    </form>
</div>
<?php include('includes/footer.php'); ?>
