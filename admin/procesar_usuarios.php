<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ' . $base_url . 'login.php?error=Acceso denegado.');
    exit();
}

require_once __DIR__ . '/../src/data/DBUsuarios.php';

$action = $_REQUEST['action'] ?? '';
$mensaje_exito = '';
$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $rol = trim($_POST['rol'] ?? 'cliente');
    $password = $_POST['password'] ?? null;

    if (empty($nombre) || empty($email) || empty($rol)) {
        $mensaje_error = 'Error de validación: Complete los campos de nombre, email y rol.';
    } elseif ($id === null && empty($password)) {
        $mensaje_error = 'La contraseña es obligatoria para un nuevo usuario.';
    } else {
        $exito = false;

        if ($id) {
            $exito = modificarUsuario((int)$id, $nombre, $email, $rol, $password);
            $accion_texto = 'actualizado';
        } else {
            $exito = crearUsuario($nombre, $email, $password, $rol);
            $accion_texto = 'creado';
        }
        
        if ($exito) {
            $mensaje_exito = 'Usuario ' . $accion_texto . ' exitosamente.';
        } else {
            $mensaje_error = 'Error al guardar el usuario en la base de datos (posiblemente email duplicado).';
        }
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'delete') {
    
    $id = (int)$_GET['id'] ?? 0;
    
    if ($id > 0) {
        if ($id === (int)$_SESSION['usuario']['id']) {
            $mensaje_error = 'No puedes eliminar tu propia cuenta de administrador.';
        } else {
            $exito = eliminarUsuario($id);
            
            if ($exito) {
                $mensaje_exito = 'Usuario eliminado exitosamente.';
            } else {
                $mensaje_error = 'Error al eliminar el usuario.';
            }
        }
    } else {
        $mensaje_error = 'ID de usuario inválido para eliminar.';
    }
}

$url_redireccion = 'admin_usuarios.php';

if ($mensaje_exito) {
    header('Location: ' . $url_redireccion . '?success=' . urlencode($mensaje_exito));
} elseif ($mensaje_error) {
    header('Location: ' . $url_redireccion . '?error=' . urlencode($mensaje_error));
} else {
    header('Location: ' . $url_redireccion);
}

exit();