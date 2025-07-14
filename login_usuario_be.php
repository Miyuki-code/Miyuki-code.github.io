<?php
session_start();
include 'conexion_be.php';

if (isset($_POST['user']) && isset($_POST['password'])) {
    $data_tipo = $_POST['data_tipo'];
    $usuario = $_POST['user'];
    $contraseña = $_POST['password'];

    // Buscar el usuario en la base de datos
    $query = "SELECT u.ID, u.user, u.password, l.roles as rol, u.rol_id 
              FROM usuarios u 
              LEFT JOIN level_user l ON u.rol_id = l.id 
              WHERE u.user = '$usuario' LIMIT 1";
    $resultado = mysqli_query($enlace, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = $resultado->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($contraseña, $row['password'])) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['rol_id'] = $row['rol_id'];
            $_SESSION['id_usuario'] = $row['ID'];
            header("Location: inicio.php?status=info&data_tipo=$data_tipo");
            exit();
        }
    }
    // Si llega aquí, usuario o contraseña incorrectos
    header("Location: index.php?status=error2&data_tipo=$data_tipo");
    exit();
} else {
    header("Location: index.php?status=error&data_tipo=login");
    exit();
}
?>