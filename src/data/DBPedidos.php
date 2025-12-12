<?php

require_once __DIR__ . '/ConexionDB.php';
require_once __DIR__ . '/DBProductos.php';

function guardarPedido(int $id_usuario, array $carrito, float $total_pedido) {
    $db = conectarDB();
    if (!$db) {
        return false;
    }
    //inicio de la transacción, dshabilita el autocommit
    //esto asegur que los cambios no se guarden permanentemente hasta que se llame a commit()
    $db->autocommit(FALSE);
    $exito = true;
    $id_pedido = null;
    
    $fecha = date('Y-m-d H:i:s');
    $estado = 'Pendiente'; //estado inicial del pedido
    
    $sql_pedido = "INSERT INTO pedidos (id_usuario, fecha_pedido, total, estado_pedido) VALUES (?, ?, ?, ?)";
    $stmt_pedido = $db->prepare($sql_pedido);
    
    if ($stmt_pedido) {
        //enlazar parámetros: intenger, string, double, string (isds)
        $stmt_pedido->bind_param("isds", $id_usuario, $fecha, $total_pedido, $estado);
        
        if (!$stmt_pedido->execute()) {
            $exito = false;
        }
        $id_pedido = $db->insert_id; //obtener el ID generado para usarlo en los detalles
        $stmt_pedido->close();
    } else {
        $exito = false;
    }
    
    if ($exito && $id_pedido) {
        //obtener la info de precio de los productos antes de insertar
        $ids_productos = array_keys($carrito);
        $productos_info = obtenerProductosPorIds($ids_productos); //llama a la función de DBProductos
        $productos_hash = array_column($productos_info, null, 'id'); //reindexar por ID para acceso rápido
        
        $sql_detalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unidad) VALUES (?, ?, ?, ?)";
        $stmt_detalle = $db->prepare($sql_detalle);

        if ($stmt_detalle) {
            foreach ($carrito as $id_producto => $cantidad) {
                $id_producto = (int)$id_producto;
                //asegurarse que el producto existe y obtener su precio actual
                if (isset($productos_hash[$id_producto])) {
                    $precio = $productos_hash[$id_producto]['precio'];
                    //insertar el detalle (un producto en el pedido)
                    //enlazar parámetros: integer, integer, integer, double (iiid)
                    $stmt_detalle->bind_param("iiid", $id_pedido, $id_producto, $cantidad, $precio);
                    
                    if (!$stmt_detalle->execute()) {
                        $exito = false;
                        break; //detener el bucle si la inserción falla
                    }
                    //restar stock
                    if (!restarStockProducto($db, $id_producto, $cantidad)) {
                        $exito = false;
                        break; //detener si el stock no se puede restar
                    }
                }
            }
            $stmt_detalle->close();
        } else {
            $exito = false;
        }
    } else {
        $exito = false;
    }
//finalización de la transacción
    if ($exito) {
        $db->commit(); //si todo fue bien, guarda los cambios de forma permanente
    } else {
        $db->rollback(); //si algo falló, deshacer todos los cambios (el pedido no se guardará ni se restará el stock)
    }
//restablecer el autocommit a TRUE y cerrar la conexión
    $db->autocommit(TRUE);
    $db->close();
    return $exito;
}