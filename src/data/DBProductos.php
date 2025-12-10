<?php

require_once 'ConexionDB.php';

function obtenerTodosLosProductos() {
    $conexion = conectarDB();
    
    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen FROM productos";
    
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }
        $resultado->close();
        return $productos;
    } else {
        return [];
    }
}

function obtenerProductoPorId(int $id) {
    $db = conectarDB();
    if (!$db) {
        return null;
    }

    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen_url FROM productos WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    if ($stmt) {
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
        return [];
    }
    
    $db = conectarDB();
    if (!$db) {
        return [];
    }
    
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen_url FROM productos WHERE id IN ($placeholders)";
    
    $stmt = $db->prepare($sql);
    $types = str_repeat('i', count($ids));

    if ($stmt) {
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

    $sql = "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?";
    $stmt = $db->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("iii", $cantidad, $id_producto, $cantidad);
        
        $resultado = $stmt->execute();
        $filas_afectadas = $stmt->affected_rows;
        $stmt->close();
        
        return $resultado && $filas_afectadas === 1;
    }
    return false;
}

function guardarProducto(array $datos) {
    $db = conectarDB();
    if (!$db) {
        return false;
    }

    $id = $datos['id'] ?? null;
    $nombre = $datos['nombre'];
    $descripcion = $datos['descripcion'];
    $precio = (float)$datos['precio'];
    $stock = (int)$datos['stock'];
    $imagen_url = $datos['imagen_url'] ?? $datos['imagen'] ?? null;

    $resultado = false;

    if ($id) {
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen_url = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $stock, $imagen_url, $id);
            $resultado = $stmt->execute();
            $stmt->close();
        }
    } else {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $imagen_url);
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
    $stmt = $db->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $filas_afectadas = $stmt->affected_rows;
        $stmt->close();
        $db->close();
        
        return $resultado && $filas_afectadas === 1;
    }

    $db->close();
    return false;
}