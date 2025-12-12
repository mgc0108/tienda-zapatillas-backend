<?php

function conectarDB() {
//parámetros de conexión a la base de datos local

    $host = "localhost";
    $usuario = "root";
    $password = ""; //contraseña vacía por defecto en entorno local
    $db = "tienda_zapatillas";
//crear una nueva instancia de la conexión MySQLi
    $conexion = new mysqli($host, $usuario, $password, $db);
//manejo de errores de conexión
//comprueba si hubo un error al intentar conectarse
    if ($conexion->connect_error) {
//termina la ejecución del script y muestra un mensaje de error detallado
        die("Error de conexión a la base de datos local (MySQL): " . $conexion->connect_error);
    }
//configuración de codificación
//establece el charset a UTF-8 para asegurar el correcto manejo de tildes, eñes y caracteres especiales
    $conexion->set_charset("utf8");
//devolver el objeto de conexión para ser usado en otras funciones
    return $conexion;
}