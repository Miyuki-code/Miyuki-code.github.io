<?php

include 'conexion_be.php';

if (!$enlace) {
    die("Error en la conexión: " . mysqli_connect_error());
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validar si los datos necesarios están presentes
if (isset($_POST['ID']) && isset($_POST['new_password'])) {
    $ID = intval($_POST['ID']); // Convertir el ID a un número entero
    $new_password = $_POST['new_password'];
    $new_password = password_hash($new_password, PASSWORD_BCRYPT); // Encriptar la nueva contraseña

    // Actualizar la contraseña en la base de datos
    $stmt = "UPDATE usuarios SET password = '$new_password' WHERE ID = $ID";
    $ejecutar = mysqli_query($enlace, $stmt);

    if ($ejecutar) {
        header("Location: index.php?toast_tipo=exito&toast_titulo=Éxito&toast_descripcion=Contraseña+actualizada+exitosamente.");
        exit();
    } else {
        header("Location: index.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=Error+al+actualizar+la+contraseña.");
        exit();
    }

    mysqli_close($enlace);
} else if (isset($_POST['token']) && isset($_POST['new_password'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT); // Encriptar la nueva contraseña

    $query = mysqli_query($enlace, "SELECT user_id FROM password_resets WHERE token = '$token' AND expires_at > NOW()");
    if ($row = mysqli_fetch_assoc($query)) {
        $user_id = $row['user_id'];
        // Cambiar la contraseña del usuario
        mysqli_query($enlace, "UPDATE usuarios SET password = '$new_password_hashed' WHERE ID = $user_id");
        // Eliminar el token para que no se pueda reutilizar
        mysqli_query($enlace, "DELETE FROM password_resets WHERE token = '$token'");
        header("Location: index.php?toast_tipo=exito&toast_titulo=Éxito&toast_descripcion=Contraseña+actualizada+exitosamente.");
        exit();
    } else {
        // Redirigir con toast de error: enlace inválido o expirado
        header("Location: index.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=Enlace+inválido+o+expirado.");
        exit();
    }
} else {
    header("Location: index.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=Datos+incompletos+para+cambiar+la+contraseña.");
    exit();
}
?>