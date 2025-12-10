<?php

session_start();

require_once __DIR__ . '/src/data/DBProductos.php';

$carrito_vacio = true;
$productos_en_carrito = [];
$total_carrito = 0;

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    $carrito_vacio = false;
    
    $ids_productos = array_keys($_SESSION['carrito']);
    
    $productos_en_carrito = obtenerProductosPorIds($ids_productos);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tu Carrito de Compras</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .header { text-align: center; padding: 10px; background: white; border-bottom: 1px solid #ccc; margin-bottom: 30px; }
        .header a { margin: 0 15px; text-decoration: none; color: #333; }
        .header a:hover { color: #007bff; }
        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
        }
        .cart-item-details { flex-grow: 1; }
        .cart-item-details h3 { margin: 0; }
        .cart-item-details p { margin: 5px 0 0 0; color: #666; }
        .cart-actions { text-align: right; }
        .cart-total {
            text-align: right;
            font-size: 1.8em;
            font-weight: bold;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #333;
        }
        .btn-checkout {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        .btn-checkout:hover { background-color: #218838; }
        .btn-remove {
            color: #dc3545;
            text-decoration: none;
            margin-left: 15px;
            border: 1px solid #dc3545;
            padding: 5px;
            border-radius: 3px;
        }
        .btn-remove:hover { background-color: #dc3545; color: white; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Tu Carrito de Compras 🛒</h1>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="login.php">Acceder</a>
            <a href="registro.php">Registrarse</a>
            <a href="carrito.php">🛒 Carrito (<?php echo array_sum($_SESSION['carrito'] ?? []); ?>)</a>
        </nav>
    </div>

    <h2>Contenido de tu Pedido</h2>

    <?php if ($carrito_vacio): ?>
        <p>Tu carrito está vacío. ¡Añade algunas zapatillas!</p>
        <p><a href="index.php">Volver al catálogo</a></p>
    <?php else: ?>
        
        <?php foreach ($productos_en_carrito as $producto): ?>
            <div class="cart-item">
                <div class="cart-item-details">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <?php
                        $cantidad = $_SESSION['carrito'][$producto['id']];
                        $subtotal = $producto['precio'] * $cantidad;
                        $total_carrito += $subtotal; // Acumulamos el total
                    ?>
                    <p>Precio unitario: <?php echo number_format($producto['precio'], 2, ',', '.'); ?> €</p>
                    <p>Cantidad: <strong><?php echo $cantidad; ?></strong></p>
                </div>
                <div class="cart-actions">
                    <p style="font-size: 1.2em; font-weight: bold;">Subtotal: <?php echo number_format($subtotal, 2, ',', '.'); ?> €</p>
                    <a href="src/negocio/procesar_carrito.php?action=remove&id=<?php echo $producto['id']; ?>" class="btn-remove">Eliminar</a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="cart-total">
            Total del Pedido: <?php echo number_format($total_carrito, 2, ',', '.'); ?> €
        </div>

        <form action="src/negocio/procesar_checkout.php" method="POST" style="text-align: right;">
            <input type="hidden" name="total" value="<?php echo $total_carrito; ?>">

            <button type="submit" class="btn-checkout">Finalizar Compra</button>
        </form>

    <?php endif; ?>

</body>
</html>