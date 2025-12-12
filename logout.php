<?php
//inicio de sesión
//si no se inicia la sesión no se puede acceder a la información de la sesión activa
//y destruirla correctamente
session_start();
//destruir todas las variables de sesión
//limpia el array $_SESSION. Esto borra la información del usuario
$_SESSION = array();
//Borra la cookie de sesión. Es opcional
//si la sesión utiliza cookies, se envia una cookie con una fecha de caducidad en el pasado
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, //hace que la cookie expire inmediatamente
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
//destruir la sesión en el servidor
//elimina los datos de la sesión almacenados en el servidor o disco
session_destroy();
//redirige al usuario a la página de inicio o login después de cerrar la sesión
header("Location: index.php");
exit;
?>