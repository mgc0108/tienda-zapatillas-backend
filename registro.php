<?php

session_start();
//captura el parámetro 'error' que proviene del script de procesamiento (procesar_registro.php)
//si la validación falla, por ejemplo, campos vacíos, contraseñas diferentes,etc.
$mensaje_error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f4f4f4; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover { background-color: #0056b3; }
        .error-message { color: red; text-align: center; margin-bottom: 15px; }
        .link-login { text-align: center; margin-top: 15px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Nuevo Cliente</h2>
        
        <?php if ($mensaje_error): ?>
            <p class="error-message">Error: <?php echo htmlspecialchars($mensaje_error); ?></p>
        <?php endif; ?>

        <form action="src/negocio/procesar_registro.php" method="POST">
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="password2">Repetir Contraseña:</label>
            <input type="password" id="password2" name="password2" required>

            <button type="submit">Registrarme</button>
        </form>
        <div class="link-login">
            ¿Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a>
        </div>
    </div>
</body>
</html>