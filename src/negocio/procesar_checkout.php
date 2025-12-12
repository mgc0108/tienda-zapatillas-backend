<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';
//inclusión de la capa de datos
//necesario apra guardar el pedido y obtener información de los productos
require_once __DIR__ . '/../data/DBPedidos.php';
require_once __DIR__ . '/../data/DBProductos.php';

$compra_exitosa = false;
$total_pedido = 0;
//validación inicial de flujo y carrito
//debe ser una solicitud POST (enviada desde el formulario de checout) y el carrito no debe estar vacío
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['carrito'])) {
    header('Location: ' . $base_url . 'carrito.php');
    exit();
}
//verificación de autenticación
//asegura que el usuario haya iniciado sesión antes de poder finalizar la compra
if (!isset($_SESSION['usuario']['id'])) {

    header('Location: ' . $base_url . 'login.php?error=Debes_iniciar_sesion_para_comprar');
    exit();
}

$user_id = $_SESSION['usuario']['id'];
$carrito = $_SESSION['carrito'];


$ids = array_keys($carrito);
//obtener la información fresca y actual de los productos desde la base de datos
$productos_a_comprar = obtenerProductosPorIds($ids);

if (empty($productos_a_comprar)) {
    header('Location: ' . $base_url . 'carrito.php?error=productos_no_encontrados');
    exit();
}
//bucle de validación de stock y cáculo del total
foreach ($productos_a_comprar as $producto) {
    $cantidad = $carrito[$producto['id']];
    $total_pedido += ($producto['precio'] * $cantidad);
    //validación final de stock, impedir la compra si el stock actual es insuficiente
    if ($producto['stock'] < $cantidad) {
        header('Location: ' . $base_url . 'checkout.php?status=error&mensaje=Stock_agotado');
        exit();
    }
}
//ejecución de la transacción
//se llama a la función de la BD que maneja la lógiva compleja
//1 guarda el pedido, 2 guarda los detalles, 3 resta el stock (todo en una transacción)
$pedido_id = guardarPedido($user_id, $carrito, $total_pedido);
//finalización y redirección
if ($pedido_id) {

    $compra_exitosa = true;
    
    unset($_SESSION['carrito']); //limpiar el carrito de la sesión después de la compra
    //redirigir a la página de confirmación con el estado y el total 
    header('Location: ' . $base_url . 'checkout.php?status=success&total=' . urlencode($total_pedido));
    exit();
} else {
//si la función guardarPedido falla, redirigir con error
    header('Location: ' . $base_url . 'checkout.php?status=error');
    exit();
}
?>