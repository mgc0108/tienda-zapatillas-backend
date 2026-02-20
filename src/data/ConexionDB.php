<?php

function conectarDB() {
    // Leemos las variables que configuramos en la pestaña Environment de Render
    $host = getenv('DB_HOST');
    $usuario = getenv('DB_USER');
    $password = getenv('DB_PASS'); 
    $db = getenv('DB_NAME');

    // Crear la conexión usando las variables de la nube
    $conexion = new mysqli($host, $usuario, $password, $db);

    // Manejo de errores
    if ($conexion->connect_error) {
        die("Error de conexión a la base de datos en la nube: " . $conexion->connect_error);
    }

    // Configuración de codificación para tildes y eñes
    $conexion->set_charset("utf8");

    return $conexion;
}
