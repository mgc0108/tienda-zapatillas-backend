<?php

function conectarDB() {
    $databaseUrl = getenv('DATABASE_URL');

    if (!$databaseUrl) {
        $host = "localhost";
        $usuario = "root";
        $password = "";
        $db = "tienda_zapatillas";
        
        die("Error: La variable DATABASE_URL no se encontró. Necesaria para la conexión remota.");
    }

    $urlParts = parse_url($databaseUrl);

    if ($urlParts === false) {
        die("Error: No se pudo analizar la DATABASE_URL. Verifique el formato.");
    }
    
    $host = $urlParts['host'];
    $usuario = $urlParts['user'];
    $password = $urlParts['pass'];
    $port = $urlParts['port'] ?? 5432;
    $db = ltrim($urlParts['path'], '/');

    $query = $urlParts['query'] ?? '';
    parse_str($query, $query_params);
    $sslmode = $query_params['sslmode'] ?? '';

    $dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$usuario;password=$password;sslmode=require";

    try {
        $conexion = new PDO($dsn);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $conexion;

    } catch (PDOException $e) {
        echo "Error de conexión a la base de datos de Render: " . $e->getMessage();
        die();
    }
}