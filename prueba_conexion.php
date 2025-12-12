<?php

require_once 'src/data/ConexionDB.php';
//ejecución de la prueba
$db = conectarDB(); //la función intenta establecer la conexión con la BD
//manejo de exito o fracaso
if ($db) {
    //exito, la conexión se ha establecido correctamente
    echo "<h1>✅ ¡Conexión Exitosa con la Base de Datos!</h1>";
    echo "<p>Base de Datos: " . DB_NAME . "</p>";
    
    $db->close(); //cerrar la conexión nada mas se termina de usar
} else {
    //fracaso, la conexión no se ha podido establecer
    echo "<h1>❌ ERROR: La conexión falló.</h1>";
    echo "<p>Revisa que Apache y MySQL estén en verde en el Panel de Control de XAMPP.</p>";
}
?>