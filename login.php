<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_message = '';
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    if ($error === 'credenciales_invalidas') {
        $error_message = 'Credenciales incorrectas. Inténtalo de nuevo.';
    } elseif ($error === 'Debes_iniciar_sesion_para_comprar') {
        $error_message = 'Debes iniciar sesión para continuar con la compra.';
    } else {
        $error_message = 'Ha ocurrido un error durante el inicio de sesión.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - ZapasXpress</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 25px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            display: inline-block;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .btn-submit {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .register-link {
            margin-top: 20px;
            display: block;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>👋 Iniciar Sesión</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="src/negocio/procesar_login.php" method="POST">
            
            <div class="form-group">
                <label for="email">📧 Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-submit">Acceder</button>
        </form>

        <a href="registro.php" class="register-link">¿No tienes cuenta? Regístrate aquí</a>
    </div>

</body>
</html>