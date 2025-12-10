<?php

function conectarDB() {
    $servidor = "localhost";
    $usuario = "root";
    $password = "";
    $base_datos = "tienda_zapatillas";
    $puerto = 3306;
    $db = new mysqli($servidor, $usuario, $password, $base_datos, $puerto); 
    
    if ($db->connect_error) {;
        return null; 
    }
    
    $db->set_charset("utf8");
    return $db;
}