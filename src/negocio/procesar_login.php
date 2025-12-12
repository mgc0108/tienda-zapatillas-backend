<?php
//iniciar o reanudar la sesión. necesario para guardar los datos del usuario logeado
session_start();

//definir la URL base del proyecto
$base_url = '/Proyectos/TiendaZapatillas/';

//incluir las funciones de base de datos para buscar y verificar usuarios
require_once __DIR__ . '/../data/DBUsuarios.php';
//control de método de solicitud
//si la solicitud no es POST redirige a la pagina de login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_url . 'login.php');
    exit();
}
//limpiar y normalizar el email: trim() elimina espacios extra; strtolower() convierte a minúsculas
$email = trim(strtolower($_POST['email'] ?? ''));
//la contraseña se obtiene tal cual, ya que la verificación de hash la maneja autenticarUsuario()
$password = $_POST['password'] ?? '';

//autenticación general
//llamar a la función que consulta la BD, verifica el hash de la contraseña y devuelve el usuario si es valido
$usuario_autenticado = autenticarUsuario($email, $password);

//gestión de sesión y redirección
if ($usuario_autenticado) {

//si la autenticación es exitosa, se crea el array de sesión
//guarda solo los datos esenciales (id, nombre, rol) por seguridad
    $_SESSION['usuario'] = [
        'id' => $usuario_autenticado['id'],
        'nombre' => $usuario_autenticado['nombre'],
        'rol' => $usuario_autenticado['rol']
    ];

//redirección basada en el rol del usuario
    if ($usuario_autenticado['rol'] === 'administrador') {
//redirigir al panel de administración
        header('Location: ' . $base_url . 'admin/dashboard.php');
    } else {
//redirigir al inicio de la tienda para clientes normales
        header('Location: ' . $base_url . 'index.php');
    }
    exit();
} else {
//autenticación fallida
//si la autenticación falla, redirige al login con un código de error específico
    header('Location: ' . $base_url . 'login.php?error=credenciales_invalidas');
    exit();
}
?>