<?php

include 'conexion_be.php';

if (!$enlace) {
    die("Error en la conexión: " . mysqli_connect_error());
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['registro'])) {
    $data_tipo = $_POST['data_tipo']; // Recoge el valor del campo oculto
    $nombre_completo = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $usuario = $_POST['user'];
    $contraseña = $_POST['password'];

    // Encriptar contraseña
    $contraseña = password_hash($contraseña, PASSWORD_BCRYPT);

    // Verificar que el correo no se repita en la base de datos
    $verificar_correo = mysqli_query($enlace, "SELECT * FROM usuarios WHERE email = '$email'");
    if (mysqli_num_rows($verificar_correo) > 0) {
        header("Location: index.php?status=error&data_tipo=$data_tipo"); // Redirige con error
        exit();
    }

    // Verificar que el usuario no se repita en la base de datos
    $verificar_usuario = mysqli_query($enlace, "SELECT * FROM usuarios WHERE user = '$usuario'");
    if (mysqli_num_rows($verificar_usuario) > 0) {
        header("Location: index.php?status=error&data_tipo=$data_tipo"); // Redirige con error
        exit();
    }

    // Insertar el usuario en la base de datos
    $stmt = "INSERT INTO usuarios (nombre_completo, email, user, password) VALUES ('$nombre_completo', '$email', '$usuario', '$contraseña')";
    $ejecutarInsertar = mysqli_query($enlace, $stmt);

    if ($ejecutarInsertar) {
        header("Location: index.php?status=success&data_tipo=$data_tipo"); // Redirige con éxito
        exit();
    } else {
        header("Location: index.php?status=error&data_tipo=$data_tipo"); // Redirige con error
        exit();
    }

    mysqli_close($enlace);
}

?>