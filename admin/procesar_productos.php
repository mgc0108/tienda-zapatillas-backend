<?php

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php?error=Acceso denegado.');
    exit();
}

require_once __DIR__ . '/../src/data/DBProductos.php';

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = $_GET['id'] ?? null;
    
    if (is_numeric($id)) {
        if (eliminarProducto((int)$id)) {
            header('Location: dashboard.php?msg=Producto eliminado con éxito.');
        } else {
            header('Location: dashboard.php?error=Error al eliminar el producto.');
        }
    } else {
        header('Location: dashboard.php?error=ID no válido para eliminar.');
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    if (empty($nombre) || empty($descripcion) || $precio <= 0 || $stock < 0) {
        $msg = 'Todos los campos obligatorios deben estar rellenos y ser válidos.';
        header('Location: productos_form.php?id=' . $id . '&error=' . urlencode($msg));
        exit();
    }

    $datos_producto = [
        'id' => $id ? (int)$id : null,
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'precio' => (float)$precio,
        'stock' => (int)$stock,
        'imagen' => $imagen
    ];

    if (guardarProducto($datos_producto)) {
        $mensaje = $id ? 'Producto actualizado con éxito.' : 'Producto creado con éxito.';
        header('Location: dashboard.php?msg=' . urlencode($mensaje));
    } else {
        $mensaje = $id ? 'Error al actualizar el producto.' : 'Error al crear el producto.';
        header('Location: productos_form.php?id=' . $id . '&error=' . urlencode($mensaje));
    }
    exit();
}

header('Location: dashboard.php');
exit();
?>