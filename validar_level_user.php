<?php

$roles_permitidos = ['Administrador', 'Moderador', 'Usuario']; // Define los roles permitidos

    if(!array_key_exists('rol', $_SESSION) || !in_array($_SESSION['rol'], $roles_permitidos)) {
        echo '<script>
            alert("No tienes un rol permitido");
            window.location = "index.php";
        </script>';
        session_destroy();
        die();
    }
?>