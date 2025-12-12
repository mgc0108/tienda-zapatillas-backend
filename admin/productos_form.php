<?php

session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php?error=Acceso denegado.');
    exit();
}

require_once __DIR__ . '/../src/data/DBProductos.php';

$producto = [
    'id' => null,
    'nombre' => '',
    'descripcion' => '',
    'precio' => '',
    'stock' => '',
    'imagen' => ''
];
$titulo_pagina = 'Añadir Nuevo Producto';

$producto_id = $_GET['id'] ?? null;

if ($producto_id && is_numeric($producto_id)) {
    $datos_producto = obtenerProductoPorId((int)$producto_id);
    
    if ($datos_producto) {
        $producto = $datos_producto;
        $titulo_pagina = 'Editar Producto: ' . htmlspecialchars($producto['nombre']);
    } else {
        header('Location: dashboard.php?error=Producto no encontrado.');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titulo_pagina; ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8f9fa; }
        .header { background-color: #343a40; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .header a { color: #ffc107; text-decoration: none; margin-right: 20px; }
        h1 { color: #343a40; }
        form { background: white; padding: 25px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea { resize: vertical; }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover { background-color: #218838; }
        .btn-back { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <div class="header">
        <a href="dashboard.php">← Volver al Panel de Control</a>
    </div>

    <h1><?php echo $titulo_pagina; ?></h1>

    <form action="procesar_productos.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">
        
        <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($producto['imagen']); ?>">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>

        <label for="precio">Precio (€):</label>
        <input type="number" id="precio" name="precio" step="0.01" min="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
        
        <label for="imagen_archivo">Seleccionar Imagen:</label>
        <input type="file" id="imagen_archivo" name="imagen_archivo">

        <button type="submit"><?php echo $producto['id'] ? 'Guardar Cambios' : 'Crear Producto'; ?></button>
    </form>
    </body>
</html>