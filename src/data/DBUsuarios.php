<?php
//archivo de conexión
require_once __DIR__ . '/ConexionDB.php';

function obtenerTodosLosUsuarios() {
    $db = conectarDB();
    if (!$db) {
        return [];
    }

    $sql = "SELECT id, nombre, email, rol FROM usuarios ORDER BY id ASC";
    //usamos query() porque no hay entrada de usuario, por lo que no es necesaria la preparación
    $resultado = $db->query($sql);
    
    $usuarios = [];
    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $usuarios[] = $fila;
        }
    }
    
    $db->close();
    return $usuarios;
}

function obtenerUsuarioPorId(int $id) {
    $db = conectarDB();
    if (!$db) return null;
//consulta preparada para enlazar el ID
    $stmt = $db->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $usuario = $resultado->fetch_assoc();
    
    $stmt->close();
    $db->close();
    return $usuario;
}

function autenticarUsuario($email, $password) {
    $db = conectarDB();
    if (!$db) return false;
//buscar al usuario por email (consulta preparada)
    $stmt = $db->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email); //'s' para string (email)
    $stmt->execute();
    $resultado = $stmt->get_result();
//verifica la existencia del usuario
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
//verificación de la contraseña
//password_verify() compara la contraseña en texto plano con el hash almacenado
        if (password_verify($password, $usuario['password'])) {
            unset($usuario['password']); //eliminar el hash de la contraseña antes de devolver los datos
            $stmt->close();
            $db->close();
            return $usuario; //autenticación exitosa
        }
    }

    $stmt->close();
    $db->close();
    return false; //autenticación fallida
}

function crearUsuario(string $nombre, string $email, string $password_raw, string $rol = 'cliente') {
    $db = conectarDB();
    if (!$db) return false;
//hashing de la contraseña
//PASSWORD_DEFAULT utiliza el algoritmo más fuerte disponible
    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);

    if ($stmt) {
        //enlazar 4 parámetros string ('ssss')
        $stmt->bind_param("ssss", $nombre, $email, $password_hashed, $rol);
        $resultado = $stmt->execute();
        $stmt->close();
        $db->close();
        return $resultado;
    }
    
    $db->close();
    return false;
}

function modificarUsuario(int $id, string $nombre, string $email, string $rol, string $password_raw = null) {
    $db = conectarDB();
    if (!$db) return false;
//lógica para construir la consulta UPDATE dinámicamente
    $params = [$nombre, $email, $rol];
    $types = "sssi";
    $sql_parts = ["nombre = ?", "email = ?", "rol = ?"];

    if ($password_raw) {
        //si se proporciona una contraseña, se hashea y se añade a la consulta
        $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);
        $sql_parts[] = "password = ?";
        $params[] = $password_hashed;
        $types = "ssssi"; //añadimos una 's' al inicio para la nueva contraseña hasheada
    }

    $sql = "UPDATE usuarios SET " . implode(", ", $sql_parts) . " WHERE id = ?";
    $params[] = $id;

    $stmt = $db->prepare($sql);
    
    if ($stmt) {
        //los parámetros y los tipos se enlazan de forma dinámica
        $stmt->bind_param($types, ...$params);
        $resultado = $stmt->execute();
        $stmt->close();
        $db->close();
        return $resultado;
    }

    $db->close();
    return false;
}

function eliminarUsuario(int $id) {
    $db = conectarDB();
    if (!$db) return false;

    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $db->prepare($sql); //consulta preparada

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