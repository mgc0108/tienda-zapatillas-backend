<?php

require_once 'src/data/ConexionDB.php';

$db = conectarDB();

if ($db) {

    echo "<h1>✅ ¡Conexión Exitosa con la Base de Datos!</h1>";
    echo "<p>Base de Datos: " . DB_NAME . "</p>";
    
    $db->close();
} else {
    
    echo "<h1>❌ ERROR: La conexión falló.</h1>";
    echo "<p>Revisa que Apache y MySQL estén en verde en el Panel de Control de XAMPP.</p>";
}
?>