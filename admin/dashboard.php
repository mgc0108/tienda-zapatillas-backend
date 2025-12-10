<?php

session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php?error=Acceso denegado. Se requiere ser Administrador.');
    exit();
}

require_once __DIR__ . '/../src/data/DBProductos.php';

$productos = obtenerTodosLosProductos();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Gestión de Productos</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8f9fa; }
        .header { background-color: #343a40; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .header a { color: #ffc107; text-decoration: none; margin-right: 20px; }
        .header a:hover { color: #fff; }
        h1 { color: #343a40; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background-color: #e9ecef; }
        .btn { padding: 5px 10px; border-radius: 3px; text-decoration: none; margin-right: 5px; font-size: 0.9em; }
        .btn-edit { background-color: #ffc107; color: #343a40; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-new { background-color: #28a745; color: white; margin-bottom: 15px; display: inline-block; padding: 10px 15px; }
        .header .active-link { color: white; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        Bienvenido, Administrador <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?> | 
        
        <span class="active-link" style="margin-right: 20px;">Gestión de Productos</span>
        
        <a href="admin_usuarios.php">Gestión de Usuarios</a>
        
        <a href="../index.php">Volver a la Tienda</a>
        <a href="../logout.php">Cerrar Sesión</a>
    </div>

    <h1>Gestión de Productos</h1>
    
    <a href="productos_form.php" class="btn-new">➕ Añadir Nuevo Producto</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($productos)) {
                foreach ($productos as $producto) {
            ?>
                    <tr>
                        <td><?php echo $producto['id']; ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo number_format($producto['precio'], 2, ',', '.'); ?> €</td>
                        <td><?php echo $producto['stock']; ?></td>
                        <td>
                            <a href="productos_form.php?id=<?php echo $producto['id']; ?>" class="btn btn-edit">Editar</a>
                            <a href="procesar_productos.php?action=delete&id=<?php echo $producto['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">Eliminar</a>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="5">No hay productos registrados.</td></tr>';
            }
            ?>
        </tbody>
    </table>

</body>
</html>