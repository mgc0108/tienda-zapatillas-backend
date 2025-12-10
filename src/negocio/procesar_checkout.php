<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';

require_once __DIR__ . '/../data/DBPedidos.php';
require_once __DIR__ . '/../data/DBProductos.php';

$compra_exitosa = false;
$total_pedido = 0;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['carrito'])) {
    header('Location: ' . $base_url . 'carrito.php');
    exit();
}

if (!isset($_SESSION['usuario']['id'])) {

    header('Location: ' . $base_url . 'login.php?error=Debes_iniciar_sesion_para_comprar');
    exit();
}

$user_id = $_SESSION['usuario']['id'];
$carrito = $_SESSION['carrito'];


$ids = array_keys($carrito);
$productos_a_comprar = obtenerProductosPorIds($ids);

if (empty($productos_a_comprar)) {
    header('Location: ' . $base_url . 'carrito.php?error=productos_no_encontrados');
    exit();
}

foreach ($productos_a_comprar as $producto) {
    $cantidad = $carrito[$producto['id']];
    $total_pedido += ($producto['precio'] * $cantidad);
    
    if ($producto['stock'] < $cantidad) {
        header('Location: ' . $base_url . 'checkout.php?status=error&mensaje=Stock_agotado');
        exit();
    }
}

$pedido_id = guardarPedido($user_id, $carrito, $total_pedido);

if ($pedido_id) {

    $compra_exitosa = true;
    
    unset($_SESSION['carrito']);
    
    header('Location: ' . $base_url . 'checkout.php?status=success&total=' . urlencode($total_pedido));
    exit();
} else {

    header('Location: ' . $base_url . 'checkout.php?status=error');
    exit();
}
?>