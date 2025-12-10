<?php

function conectarDB() {
    
    $databaseUrl = getenv('DATABASE_URL');

    if (!$databaseUrl) {
        // Bloque de desarrollo local: No deberías estar aquí en Render
        $host = "localhost";
        $usuario = "root";
        $password = "";
        $db = "tienda_zapatillas";
        
        // El die() aquí evita que el código intente conectarse localmente
        die("Error: La variable DATABASE_URL de Render no se encontró. No se puede conectar a la BD remota.");
    }

    // --- Extracción de Partes (Corrección de Puertos y Path) ---
    $urlParts = parse_url($databaseUrl);

    // Si parse_url falla, es un problema con el formato de la URL
    if ($urlParts === false) {
        die("Error: No se pudo analizar la DATABASE_URL. Verifique el formato.");
    }
    
    $host = $urlParts['host'];
    $usuario = $urlParts['user'];
    $password = $urlParts['pass'];
    
    // **CORRECCIÓN 1: Extraer el puerto**
    $port = $urlParts['port'] ?? 5432; 
    
    // **CORRECCIÓN 2: Usar ltrim en path para obtener el nombre de la BD**
    $db = ltrim($urlParts['path'], '/'); 

    // **CORRECCIÓN 3: Extraer el parámetro SSL**
    $query = $urlParts['query'] ?? '';
    parse_str($query, $query_params);
    $sslmode = $query_params['sslmode'] ?? '';

    // --- Cadena de Conexión DSN para PDO ---
    // Incluir el puerto en el DSN
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$usuario;password=$password";

    // Añadir sslmode si existe (es necesario para la URL pública)
    if ($sslmode) {
        $dsn .= ";sslmode=$sslmode";
    }

    try {
        // Crear la conexión PDO
        $conexion = new PDO($dsn);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $conexion;

    } catch (PDOException $e) {
        echo "Error de conexión a la base de datos de Render: " . $e->getMessage();
        die();
    }
    //h
}