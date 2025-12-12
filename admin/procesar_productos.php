<?php

session_start();

//Control de acceso y sesión
//verifica si el usuario está logeado y si su rol es admin si no lo redirige.
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php?error=Acceso denegado.');
    exit();
}
//incluir el archivo con las funciones de base de datos (CRUD)
require_once __DIR__ . '/../src/data/DBProductos.php';

//Lógica para la eliminación
//si la URL tiene ?action=delete, procesamos la eliminación del producto
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = $_GET['id'] ?? null;
    
//asegurarse de que el ID es un número antes de intentar eliminar
    if (is_numeric($id)) {
//llamada a la función de BD para eliminar y redirigir con un mensaje
        if (eliminarProducto((int)$id)) {
            header('Location: dashboard.php?msg=Producto eliminado con éxito.');
        } else {
            header('Location: dashboard.php?error=Error al eliminar el producto.');
        }
    } else {
        header('Location: dashboard.php?error=ID no válido para eliminar.');
    }
    exit();
}
//lógica para la creación/actualización
//procesa el formulario enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    //extracción de datos del formulario, usando el operador ?? para evitar errores si no existen
    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = $_POST['precio'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $descripcion = trim($_POST['descripcion'] ?? '');
    
    //alamcena el nombre de la imagen actual
    $imagen = $_POST['imagen_actual'] ?? null;
    
    //procesamiento de la subida de archivos
    //verifica si se ha subido un nuevo archivo de imagen sin errores
    if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
        
        $archivo_temp = $_FILES['imagen_archivo']['tmp_name'];
        $nombre_original = basename($_FILES['imagen_archivo']['name']);

        //generar un numero unico para la imagen
        //esto previene que se sobreescriban archivos con nombres iguales
        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $nombre_unico = time() . "_" . uniqid() . "." . $extension;
//definir la ruta final de guardado, apuntando a la carpeta de zapatillas
        $carpeta_destino = __DIR__ . '/../img/zapatillas/';
        $ruta_final = $carpeta_destino . $nombre_unico;
//Si la carpeta de destino no existe, la crea con permisos de escritura (0777)
        if (!is_dir($carpeta_destino)) {

            mkdir($carpeta_destino, 0777, true);
        }
//mover el archivo desde la ubicacion temporal a la carpeta final
        if (move_uploaded_file($archivo_temp, $ruta_final)) {
            //si tiene exito se actualiza la variable $imagen con el nombre unico
            $imagen = $nombre_unico;
            //error en la subida/movimiento del archivo
        } else {
            $msg = 'Error al guardar el archivo de imagen en el servidor. Revise permisos de carpeta.';
            header('Location: productos_form.php?id=' . $id . '&error=' . urlencode($msg));
            exit();
        }
    }
    //validación de datos obligatorios
    //si falta nombre, descripcion o los valores numericos son invalidos
    if (empty($nombre) || empty($descripcion) || $precio <= 0 || $stock < 0) {
        $msg = 'Todos los campos obligatorios deben estar rellenos y ser válidos.';
        header('Location: productos_form.php?id=' . $id . '&error=' . urlencode($msg));
        exit();
    }
    //crear el array de datos que se enviará a la funcion de la base de datos
    $datos_producto = [
        'id' => $id ? (int)$id : null,//si tiene ID, se convierte a entero
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'precio' => (float)$precio, //casteo a float
        'stock' => (int)$stock, //casteo a entero
        'imagen' => $imagen //nombre unico (nuevo) o nombre antiguo
    ];
//ejecutar la seccion de guardar
//llama a la función que inserta (si ID es null) o actualiza (si ID existe)
    if (guardarProducto($datos_producto)) {
        $mensaje = $id ? 'Producto actualizado con éxito.' : 'Producto creado con éxito.';
        header('Location: dashboard.php?msg=' . urlencode($mensaje));
    } else {
        $mensaje = $id ? 'Error al actualizar el producto.' : 'Error al crear el producto.';
        header('Location: productos_form.php?id=' . $id . '&error=' . urlencode($mensaje));
    }
    exit();
}
//redirección por defecto si el script se accede sin POST ni GET de eliminación
header('Location: dashboard.php');
exit();
?>