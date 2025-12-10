<?php

session_start();

$base_url = '/Proyectos/TiendaZapatillas/';

$action = $_GET['action'] ?? $_POST['action'] ?? null;
$id_producto = $_GET['id'] ?? $_POST['id'] ?? null;

if (!is_numeric($id_producto) || $id_producto <= 0) {
    header('Location: ' . $base_url . 'carrito.php');
    exit();
}

if ($action === 'add') {
    $cantidad = 1;
    
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    $_SESSION['carrito'][$id_producto] = ($_SESSION['carrito'][$id_producto] ?? 0) + $cantidad;
    
    header("Location: " . $base_url . "index.php");
    exit();
}

if ($action === 'remove') {
    if (isset($_SESSION['carrito'][$id_producto])) {
        unset($_SESSION['carrito'][$id_producto]);
        
        $_SESSION['mensaje'] = "Producto eliminado del carrito.";
    }
}

$destino_carrito = $base_url . 'carrito.php';
$destino_index = $base_url . 'index.php';

$destino = (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'carrito.php'))
? $destino_carrito
: $destino_index;

header("Location: $destino");
exit();
?>