<?php
session_start();

$base_url = '/Proyectos/TiendaZapatillas/';
//control de acceso
//solo permite el acceso si la sesión está activa y el rol es admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ' . $base_url . 'login.php?error=Acceso denegado. Se requiere ser Administrador.');
    exit();
}
//conexión a la capa de datos
require_once __DIR__ . '/../src/data/DBUsuarios.php';
//para obtener los datos llama a la función de la BD para obtener el listado de usuarios
$usuarios = obtenerTodosLosUsuarios();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - Gestión de Usuarios</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f8f9fa; }
        .header { background-color: #343a40; color: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .header a { color: #ffc107; text-decoration: none; margin-right: 20px; }
        .header a:hover { color: #fff; }
        h1 { color: #343a40; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        th { background-color: #e9ecef; }
        .btn { padding: 5px 10px; border-radius: 3px; text-decoration: none; margin-right: 5px; font-size: 0.9em; }
        .btn-edit { background-color: #ffc107; color: #343a40; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-new { background-color: #28a745; color: white; margin-bottom: 15px; display: inline-block; padding: 10px 15px; }
    </style>
</head>
<body>

    <div class="header">
        Bienvenido, Administrador <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?> |
        <a href="dashboard.php">Gestión de Productos</a>
        <span class="active-link" style="color: white; font-weight: bold; margin-right: 20px;">Gestión de Usuarios</span>
        <a href="../index.php">Volver a la Tienda</a>
        <a href="../logout.php">Cerrar Sesión</a>
    </div>

    <h1>Gestión de Usuarios</h1>
    
    <a href="usuarios_form.php" class="btn-new">➕ Añadir Nuevo Usuario</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($usuarios)) {
                //itera sobre el array de usuarios obtenido de la base de datos
                foreach ($usuarios as $usuario) {
            ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                        <td>
                            <a href="usuarios_form.php?id=<?php echo $usuario['id']; ?>" class="btn btn-edit">Editar</a>
                            <a href="procesar_usuarios.php?action=delete&id=<?php echo $usuario['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">Eliminar</a>
                        </td>
                    </tr>
            <?php
                }
            } else {
                //mensaje si no hay usuarios en la base de datos
                echo '<tr><td colspan="5">No hay usuarios registrados.</td></tr>';
            }
            ?>
        </tbody>
    </table>

</body>
</html>