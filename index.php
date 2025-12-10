<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/src/data/DBProductos.php';

$productos = obtenerTodosLosProductos();

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda de Zapatillas - ZapasXpress</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .header { text-align: center; padding: 10px; background: white; border-bottom: 1px solid #ccc; margin-bottom: 30px; }
        .header a { margin: 0 15px; text-decoration: none; color: #333; }
        .header a:hover { color: #007bff; }
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .product-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .product-card h3 { color: #333; margin-top: 0; }
        .product-card p { color: #666; font-size: 0.9em; }
        .product-card .price { font-size: 1.5em; color: #d9534f; font-weight: bold; margin: 10px 0; }
        .product-card .stock { color: green; font-weight: bold; }
        .product-card .image {
            height: 150px;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-style: italic;
            color: #aaa;
        }
        .btn-cart {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }
        .btn-cart:hover { background-color: #0056b3; }
    </style>
</head>
<body>

    <div class="header">
        <h1>ZapasXpress 👟</h1>
        <nav>
            <a href="index.php">Inicio</a>
            
            <?php if (isset($_SESSION['usuario'])): ?>
                
                <?php if ($_SESSION['usuario']['rol'] === 'admin'): ?>
                    <a href="admin/dashboard.php">🔧 Panel Admin</a>
                <?php endif; ?>

                <span style="margin: 0 15px; color: #555;">
                    Hola, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>
                </span>
                <a href="logout.php">Cerrar Sesión</a>
                
            <?php else: ?>
                
                <a href="login.php">Acceder</a>
                <a href="registro.php">Registrarse</a>
                
            <?php endif; ?>

            <a href="carrito.php">
                🛒 Carrito (<?php
                    echo array_sum($_SESSION['carrito'] ?? []);
                ?>)
            </a>
        </nav>
    </div>

    <h2>Catálogo de Zapatillas</h2>

    <div class="product-grid">

        <?php
        
        if (count($productos) > 0) {
            foreach ($productos as $producto) {
        ?>
                <div class="product-card">
                    <div class="image">
                    <img src="img/zapatillas/<?php echo htmlspecialchars($producto['imagen']); ?>"
                            alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div> 
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <div class="price"><?php echo number_format($producto['precio'], 2, ',', '.'); ?> €</div>
                    <div class="stock">Stock: <?php echo $producto['stock']; ?> unidades</div>
                    
                    <a href="src/negocio/procesar_carrito.php?action=add&id=<?php echo $producto['id']; ?>" class="btn-cart">
                        Añadir al Carrito
                    </a>
                </div>
        <?php 
            }
        } else {
            
            echo "<p>Lo sentimos, no nos queda stock.</p>";
        }
        ?>
    </div>

</body>
</html>
// Esto es un cambio de prueba final