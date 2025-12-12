<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';
//control de acceso
//solo el administrador puede ejecutar las accisones de este script
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ' . $base_url . 'login.php?error=Acceso denegado.');
    exit();
}
//incluir las funciones de la BD (crear, modificar, eliminar)
require_once __DIR__ . '/../src/data/DBUsuarios.php';
//inicialización y manejo de acciones
$action = $_REQUEST['action'] ?? '';
$mensaje_exito = '';
$mensaje_error = '';
//logica para la creación/modificación
//procesa los datos enviados desde el formulario de usuarios_form.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $rol = trim($_POST['rol'] ?? 'cliente');
    $password = $_POST['password'] ?? null;
//validación de campos
    if (empty($nombre) || empty($email) || empty($rol)) {
        $mensaje_error = 'Error de validación: Complete los campos de nombre, email y rol.';
    } elseif ($id === null && empty($password)) {
        $mensaje_error = 'La contraseña es obligatoria para un nuevo usuario.';
    } else {
        $exito = false;
//lógica de modificación
        if ($id) {
            //llama a la función que gestiona la actualización y el hashing condicional de la contraseña
            $exito = modificarUsuario((int)$id, $nombre, $email, $rol, $password);
            $accion_texto = 'actualizado';
            //lógica de creación
        } else {
            //llama a la función que crea el usuario y hashea la contraseña 
            $exito = crearUsuario($nombre, $email, $password, $rol);
            $accion_texto = 'creado';
        }
        
        if ($exito) {
            $mensaje_exito = 'Usuario ' . $accion_texto . ' exitosamente.';
        } else {
            $mensaje_error = 'Error al guardar el usuario en la base de datos (posiblemente email duplicado).';
        }
    }
//lógica para la eliminación
//procesa la solicitud de eliminación desde admin_usuarios.php
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'delete') {
    
    $id = (int)$_GET['id'] ?? 0;
    
    if ($id > 0) {
        //validación de seguridad para evitar que el administrador se autoelimine
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
//redirige al dashboard de usuarios con el mensaje de éxito o error
$url_redireccion = 'admin_usuarios.php';

if ($mensaje_exito) {
    header('Location: ' . $url_redireccion . '?success=' . urlencode($mensaje_exito));
} elseif ($mensaje_error) {
    header('Location: ' . $url_redireccion . '?error=' . urlencode($mensaje_error));
} else {
    //redirección por defecto si no se hizo ninguna acción valida
    header('Location: ' . $url_redireccion);
}

exit();