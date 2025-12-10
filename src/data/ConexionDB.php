<?php

function conectarDB() {
    
    $databaseUrl = getenv('DATABASE_URL');

    if (!$databaseUrl) {
        $host = "localhost";
        $usuario = "root";
        $password = "";
        $db = "tienda_zapatillas";
        
        die("Error: La variable DATABASE_URL de Render no se encontró. No se puede conectar a la BD remota.");
    }
    $urlParts = parse_url($databaseUrl);

    $host = $urlParts['host'];
    $usuario = $urlParts['user'];
    $password = $urlParts['pass'];
    $db = substr($urlParts['path'], 1);

    try {
        $dsn = "pgsql:host=$host;dbname=$db;user=$usuario;password=$password";
        
        $conexion = new PDO($dsn);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $conexion;

    } catch (PDOException $e) {
        echo "Error de conexión a la base de datos de Render: " . $e->getMessage();
        die();
    }
}