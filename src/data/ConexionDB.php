<?php

function conectarDB() {

    $host = "localhost";
    $usuario = "root";
    $password = "";
    $db = "tienda_zapatillas";
    
    $conexion = new mysqli($host, $usuario, $password, $db);
    
    if ($conexion->connect_error) {

        die("Error de conexión a la base de datos local (MySQL): " . $conexion->connect_error);
    }

    $conexion->set_charset("utf8");

    return $conexion;
}