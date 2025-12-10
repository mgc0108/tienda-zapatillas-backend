<?php
// Asegúrate de que este archivo contiene la función conectarDB() que devuelve un objeto PDO
require_once 'ConexionDB.php';

/**
 * Obtiene todos los productos de la base de datos.
 * Versión PDO compatible.
 */
function obtenerTodosLosProductos() {
    $conexion = conectarDB();
    if (!$conexion) {
        return [];
    }

    $sql = "SELECT id, nombre, descripcion, precio, imagen_url FROM productos";
    
    try {
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // DEPURACIÓN: Contar cuántos resultados se obtuvieron (usando count() en lugar de num_rows)
        if (count($productos) > 0) {
            return $productos;
        } else {
            // Este mensaje se mostrará si la conexión es exitosa pero la tabla está vacía.
            echo "DEPURACIÓN: La consulta se ejecutó, pero devolvió 0 productos. Revisa tu tabla 'productos' en DBeaver o en el panel de Render.";
            return [];
        }
    } catch (PDOException $e) {
        error_log("Error al obtener productos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene un producto por su ID.
 * Versión PDO compatible.
 */
function obtenerProductoPorId(int $id) {
    $db = conectarDB();
    if (!$db) {
        return null;
    }

    // Nota: Usamos imagen_url en lugar de imagen (asumiendo que así está en la BD)
    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen_url FROM productos WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    if ($stmt) {
        // Ejecución con parámetros pasados como array
        $stmt->execute([$id]);
        
        // fetch() para un solo resultado, equivalente a fetch_assoc()
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $producto ?: null; // Devuelve el producto o null si no se encuentra
    }
    
    return null;
}

/**
 * Obtiene varios productos por un array de IDs.
 * Versión PDO compatible.
 */
function obtenerProductosPorIds(array $ids) {
    if (empty($ids)) {
        return [];
    }
    
    $db = conectarDB();
    if (!$db) {
        return [];
    }
    
    // Crear los placeholders (?) para la cláusula IN
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen_url FROM productos WHERE id IN ($placeholders)";
    
    $stmt = $db->prepare($sql);
    
    if ($stmt) {
        // Los IDs se pasan directamente al execute()
        $stmt->execute($ids); 
        
        // Obtener todos los resultados
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $productos;
    }
    return [];
}

/**
 * Resta stock a un producto.
 * Versión PDO compatible (usa rowCount()).
 */
function restarStockProducto($db, int $id_producto, int $cantidad) {
    // Nota: $db debe ser el objeto PDO ya conectado

    $sql = "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?";
    $stmt = $db->prepare($sql);
    
    if ($stmt) {
        // Los parámetros se pasan a execute()
        $resultado = $stmt->execute([$cantidad, $id_producto, $cantidad]);
        
        // PDO usa rowCount() para saber las filas afectadas
        $filas_afectadas = $stmt->rowCount(); 
        
        return $resultado && $filas_afectadas === 1;
    }
    return false;
}

/**
 * Guarda o actualiza un producto.
 * Versión PDO compatible.
 */
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
    // Se usa 'imagen_url' como campo, aunque se podría recibir como 'imagen'
    $imagen_url = $datos['imagen_url'] ?? $datos['imagen'] ?? null; 

    $resultado = false;

    if ($id) {
        // UPDATE (Actualizar)
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen_url = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        if ($stmt) {
            $resultado = $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen_url, $id]);
        }
    } else {
        // INSERT (Insertar nuevo)
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt) {
            $resultado = $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen_url]);
        }
    }

    return $resultado;
}

/**
 * Elimina un producto por su ID.
 * Versión PDO compatible.
 */
function eliminarProducto(int $id) {
    $db = conectarDB();
    if (!$db) {
        return false;
    }

    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $db->prepare($sql);

    if ($stmt) {
        $resultado = $stmt->execute([$id]);
        
        // PDO usa rowCount()
        $filas_afectadas = $stmt->rowCount(); 
        
        return $resultado && $filas_afectadas === 1; 
    }

    return false;
}