<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';
//inclusión de la capa de datos
require_once __DIR__ . '/../data/DBUsuarios.php';
//control de metodo de solicitud
//solo se permite la ejecucion si el formulario de registro se envió via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_url . 'registro.php');
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
//validación. Campos obligatorios
if (empty($nombre) || empty($email) || empty($password) || empty($password_confirm)) {
    header('Location: ' . $base_url . 'registro.php?error=campos_obligatorios');
    exit();
}
//Validación. Coincidencia de contraseñas
//Verificación para asegurar que el usuario ingresó la misma contraseña dos veces
if ($password !== $password_confirm) {
    header('Location: ' . $base_url . 'registro.php?error=contrasenas_no_coinciden');
    exit();
}

$rol = 'cliente'; //asignación de rol por defecto para nuevos registros públicos
//ejecución de la creación de usuario
//llama a la función que hashea la contraseña y guarda el usuario en la BD
if (crearUsuario($nombre, $email, $password, $rol)) {
    //registro exitoso, redirige al login para que inicie sesión
    header('Location: ' . $base_url . 'login.php?success=Registro exitoso. Por favor, inicie sesión.');
    exit();
} else {
    //error en la BD, por ejemplo el email duplicado porque es clave única
    header('Location: ' . $base_url . 'registro.php?error=Error al registrar el usuario o email ya existe.');
    exit();
}