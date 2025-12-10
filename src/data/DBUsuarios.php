<?php

require_once __DIR__ . '/ConexionDB.php';

function obtenerTodosLosUsuarios() {
    $db = conectarDB();
    if (!$db) {
        return [];
    }

    $sql = "SELECT id, nombre, email, rol FROM usuarios ORDER BY id ASC";
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

    $stmt = $db->prepare("SELECT id, nombre, email, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        if (password_verify($password, $usuario['password'])) {
            unset($usuario['password']);
            $stmt->close();
            $db->close();
            return $usuario;
        }
    }

    $stmt->close();
    $db->close();
    return false;
}

function crearUsuario(string $nombre, string $email, string $password_raw, string $rol = 'cliente') {
    $db = conectarDB();
    if (!$db) return false;

    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);

    if ($stmt) {
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

    $params = [$nombre, $email, $rol];
    $types = "sssi";
    $sql_parts = ["nombre = ?", "email = ?", "rol = ?"];

    if ($password_raw) {
        $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);
        $sql_parts[] = "password = ?";
        $params[] = $password_hashed;
        $types = "ssssi";
    }

    $sql = "UPDATE usuarios SET " . implode(", ", $sql_parts) . " WHERE id = ?";
    $params[] = $id;

    $stmt = $db->prepare($sql);
    
    if ($stmt) {
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