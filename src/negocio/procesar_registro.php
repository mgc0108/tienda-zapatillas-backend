<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';

require_once __DIR__ . '/../data/DBUsuarios.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_url . 'registro.php');
    exit();
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim(strtolower($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

if (empty($nombre) || empty($email) || empty($password) || empty($password_confirm)) {
    header('Location: ' . $base_url . 'registro.php?error=campos_obligatorios');
    exit();
}

if ($password !== $password_confirm) {
    header('Location: ' . $base_url . 'registro.php?error=contrasenas_no_coinciden');
    exit();
}

$rol = 'cliente';

if (crearUsuario($nombre, $email, $password, $rol)) {
    header('Location: ' . $base_url . 'login.php?success=Registro exitoso. Por favor, inicie sesión.');
    exit();
} else {
    header('Location: ' . $base_url . 'registro.php?error=Error al registrar el usuario o email ya existe.');
    exit();
}