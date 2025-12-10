<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';

$status = $_GET['status'] ?? null;
$total = $_GET['total'] ?? '0.00';

if ($status === 'success') {
    $compra_exitosa = true;
    $mensaje = '¡Gracias por tu compra! Tu pedido ha sido procesado con éxito. Total: ' . number_format($total, 2, ',', '.') . ' €';
} elseif ($status === 'error') {
    $compra_exitosa = false;
    $mensaje = 'Error al procesar el pedido. Por favor, inténtalo de nuevo.';
    
} else {
    header('Location: ' . $base_url . 'carrito.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Pedido</title>
    </head>
<body>
    <div class="box">
        <?php if ($compra_exitosa): ?>
            <h2 style="color: green;">✅ Pedido Realizado</h2>
        <?php else: ?>
            <h2 style="color: red;">❌ Error al Procesar</h2>
        <?php endif; ?>

        <p><?php echo htmlspecialchars($mensaje); ?></p>
        
        <a href="index.php">Volver a la Tienda</a>
    </div>
</body>
</html>