<?php

session_start();

$base_url = '/Proyectos/TiendaZapatillas/';
//extracción de parámetros de la URL
//Obtener la acción (add/remove) y el ID del producto desde GET o POST
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$id_producto = $_GET['id'] ?? $_POST['id'] ?? null;

//validación de entrada
//si el ID del producto no es numérico o es inválido, redirigir al carrito para evitar errores
if (!is_numeric($id_producto) || $id_producto <= 0) {
    header('Location: ' . $base_url . 'carrito.php');
    exit();
}
//acción ADD (añadir al carrito)
if ($action === 'add') {
    $cantidad = 1; //se añade una unidad por defecto al hacer click
    //inicializar el carrito si no existe la sesión
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    //incremento: si el producto ya existe en el carrito, se suma 1
    $_SESSION['carrito'][$id_producto] = ($_SESSION['carrito'][$id_producto] ?? 0) + $cantidad;
//redirigir el index (página de la tienda) después de añadir para que el usuario siga comprando
    header("Location: " . $base_url . "index.php");
    exit();
}
//acción REMOVE (eliminar del carrito)
if ($action === 'remove') {
    //verifica si el producto existe en el array del carrito
    if (isset($_SESSION['carrito'][$id_producto])) {
        unset($_SESSION['carrito'][$id_producto]); //elimina la entrada del producto
        //mensaje de confirmación para mostrar en la siguiente vista
        $_SESSION['mensaje'] = "Producto eliminado del carrito.";
    }
}
//lógica de redirección inteligente (control de flujo)
//determina donde redirigir al usuario después de una acción (principalmente 'remove')
$destino_carrito = $base_url . 'carrito.php';
$destino_index = $base_url . 'index.php';
//si la página anterior (HTTP_REFERER) es el carrito, volver al carrito, si no volver al index
$destino = (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'carrito.php'))
? $destino_carrito
: $destino_index;

header("Location: $destino");
exit();
?>