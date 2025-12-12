<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';
//control de acceso y verificación para vistas de administrador

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ' . $base_url . 'login.php?error=Acceso denegado.');
    exit();
}

require_once __DIR__ . '/../src/data/DBUsuarios.php';
//inicialización del estado (modo crear)
$usuario = [
    'id' => null, //indica que es un nuevo registro
    'nombre' => '',
    'email' => '',
    'rol' => 'cliente'
];
$accion = 'Crear';
//lógiva para modo edición (si hay un ID en la  URL)
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $usuario_db = obtenerUsuarioPorId($id); //consultar el usuario de la BD
    
    if ($usuario_db) {
        $usuario = $usuario_db; //cargar datos si se encuentra
        $accion = 'Editar'; //cambiar la acción del formulario
    } else {
        //redirigir si el ID no corresponde a un usuario existente
        header('Location: admin_usuarios.php?error=usuario_no_encontrado');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $accion; ?> Usuario</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #007bff; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], select, input[type="password"] {
            width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box;
        }
        .btn-submit { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-submit:hover { background-color: #0056b3; }
        .btn-cancel { background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; margin-left: 10px;}
    </style>
</head>
<body>

    <div class="container">
        <h1><?php echo $accion; ?> Usuario</h1>

        <form action="procesar_usuarios.php" method="POST">
            
            <?php if ($usuario['id']): ?>
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="rol">Rol</label>
                <select id="rol" name="rol" required>
                    <option value="cliente" <?php echo $usuario['rol'] === 'cliente' ? 'selected' : ''; ?>>Cliente</option>
                    <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                </select>
            </div>

            <p style="margin-top: 25px; font-style: italic; color: #6c757d;">
                <?php echo $usuario['id'] ? 'Dejar la contraseña en blanco para no modificarla.' : 'Contraseña obligatoria para el nuevo usuario.'; ?>
            </p>
            
            <div class="form-group">
                <label for="password">Contraseña (Nueva o Vacía)</label>
                <input type="password" id="password" name="password" <?php echo $usuario['id'] ? '' : 'required'; ?>>
            </div>
            
            <button type="submit" class="btn-submit"><?php echo $accion; ?></button>
            <a href="admin_usuarios.php" class="btn-cancel">Cancelar</a>
            
        </form>
    </div>

</body>
</html>