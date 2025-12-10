<?php

require_once __DIR__ . '/ConexionDB.php';
require_once __DIR__ . '/DBProductos.php';

function guardarPedido(int $id_usuario, array $carrito, float $total_pedido) {
    $db = conectarDB();
    if (!$db) {
        return false;
    }
    
    $db->autocommit(FALSE);
    $exito = true;
    $id_pedido = null;
    
    $fecha = date('Y-m-d H:i:s');
    $estado = 'Pendiente'; 
    
    $sql_pedido = "INSERT INTO pedidos (id_usuario, fecha_pedido, total, estado_pedido) VALUES (?, ?, ?, ?)";
    $stmt_pedido = $db->prepare($sql_pedido);
    
    if ($stmt_pedido) {
        $stmt_pedido->bind_param("isds", $id_usuario, $fecha, $total_pedido, $estado);
        
        if (!$stmt_pedido->execute()) {
            $exito = false;
        }
        $id_pedido = $db->insert_id;
        $stmt_pedido->close();
    } else {
        $exito = false;
    }
    
    if ($exito && $id_pedido) {
        $ids_productos = array_keys($carrito);
        $productos_info = obtenerProductosPorIds($ids_productos);
        $productos_hash = array_column($productos_info, null, 'id');
        
        $sql_detalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unidad) VALUES (?, ?, ?, ?)";
        $stmt_detalle = $db->prepare($sql_detalle);

        if ($stmt_detalle) {
            foreach ($carrito as $id_producto => $cantidad) {
                $id_producto = (int)$id_producto;
                if (isset($productos_hash[$id_producto])) {
                    $precio = $productos_hash[$id_producto]['precio'];
                    
                    $stmt_detalle->bind_param("iiid", $id_pedido, $id_producto, $cantidad, $precio);
                    
                    if (!$stmt_detalle->execute()) {
                        $exito = false;
                        break;
                    }

                    if (!restarStockProducto($db, $id_producto, $cantidad)) {
                        $exito = false;
                        break;
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

    if ($exito) {
        $db->commit();
    } else {
        $db->rollback();
    }

    $db->autocommit(TRUE);
    $db->close();
    return $exito;
}