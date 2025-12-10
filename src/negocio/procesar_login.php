<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';

require_once __DIR__ . '/../data/DBUsuarios.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_url . 'login.php');
    exit();
}

$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

$usuario_autenticado = autenticarUsuario($email, $password);

if ($usuario_autenticado) {
    
    $_SESSION['usuario'] = [
        'id' => $usuario_autenticado['id'],
        'nombre' => $usuario_autenticado['nombre'],
        'rol' => $usuario_autenticado['rol']
    ];


    if ($usuario_autenticado['rol'] === 'administrador') {

        header('Location: ' . $base_url . 'admin/dashboard.php');
    } else {

        header('Location: ' . $base_url . 'index.php');
    }
    exit();
} else {

    header('Location: ' . $base_url . 'login.php?error=credenciales_invalidas');
    exit();
}
?>