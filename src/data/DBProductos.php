<?php
//incluir el archivo de conexión
require_once 'ConexionDB.php';

function obtenerTodosLosProductos() {
    $conexion = conectarDB();
//consulta SQL simple para seleccionar todas las columnas necesarias
    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen FROM productos";
//Ejecución de consulta. Usamos query() porque no hay parámetros de usuario.
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $productos = [];
    //Recorrer el resultado y guardar cada fila como un array asociativo.
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }
        $resultado->close();
        return $productos;
    } else {
        return []; //retorna array vacío si no hay productos.
    }
}

function obtenerProductoPorId(int $id) {
    $db = conectarDB();

    if (!$db) {
        return null;
    }
//Preparar la consulta previene la inyección SQL.
    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen FROM productos WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    if ($stmt) {
        //Enlazar el parámetro 'i' indica que el valor $id es un entero
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_assoc();
        
        $stmt->close();
        $db->close();
        
        return $producto;
    }
    
    $db->close();
    return null;
}

function obtenerProductosPorIds(array $ids) {
    if (empty($ids)) {
        return []; //Si no hay IDs, retorna un array vacío de inmediato
    }
    
    $db = conectarDB();
    if (!$db) {
        return [];
    }
//Construir los placeholders '?' dinámicamente según la cantidad de IDs.
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen FROM productos WHERE id IN ($placeholders)";
    
    $stmt = $db->prepare($sql);
    //Tipos de datos 'i' por cada ID
    $types = str_repeat('i', count($ids));

    if ($stmt) {
//Usar ...$ids para desempaquetar el array de IDs en argumentos separados para bind_param
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }
        $stmt->close();
        $db->close();
        return $productos;
    }
    $db->close();
    return [];
}

function restarStockProducto($db, int $id_producto, int $cantidad) {
//La condición 'AND stock >= ?' asegura que haya suficiente stock antes de restar
    $sql = "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?";
    $stmt = $db->prepare($sql);
    
    if ($stmt) {
//enlazar 3 parámetros enteros ('iii'): cantidad, id, cantidad (otra vez).
        $stmt->bind_param("iii", $cantidad, $id_producto, $cantidad);
        
        $resultado = $stmt->execute();
        $filas_afectadas = $stmt->affected_rows;
        $stmt->close();
//debe ser exactamente 1 fila afectada para confirmar la actualización
        return $resultado && $filas_afectadas === 1;
    }
    return false;
}

function guardarProducto(array $datos) {
    $db = conectarDB();
    if (!$db) {
        return false;
    }
//extracción y casteo
    $id = $datos['id'] ?? null;
    $nombre = $datos['nombre'] ?? null;
    $descripcion = $datos['descripcion'] ?? null;
    $precio = (float)($datos['precio'] ?? 0.0); //(float) para números decimales
    $stock = (int)($datos['stock'] ?? 0); //(int) para números enteros

    $imagen = $datos['imagen'] ?? $datos['imagen_url'] ?? null;
    
    $resultado = false;

    if ($id) {
//Lógica de Actualización (UPDATE)
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        if ($stmt) {

            $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $stock, $imagen, $id);
            $resultado = $stmt->execute();
            $stmt->close();
        }
    } else {
//lógica de Inserción (INSERT) Producto Nuevo
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $imagen);
            $resultado = $stmt->execute();
            $stmt->close();
        }
    }

    $db->close();
    return $resultado;
}

function eliminarProducto(int $id) {
    $db = conectarDB();
    if (!$db) {
        return false;
    }

    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $db->prepare($sql); //consulta preparada por seguridad

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $filas_afectadas = $stmt->affected_rows;
        $stmt->close();
        $db->close();
//verifica que la ejecución haya sido exitosa Y que se haya afectado 1 sola fila
        return $resultado && $filas_afectadas === 1;
    }

    $db->close();
    return false;
}