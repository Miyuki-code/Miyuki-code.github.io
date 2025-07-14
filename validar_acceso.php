<?php
function validar_acceso($roles_permitidos) {

    // Verificar si el usuario tiene un rol asignado
    if (!isset($_SESSION['rol_id'])) {
        echo '<script>
            alert("No tienes permiso para acceder a esta página.");
            window.location.href = "inicio.php";
        </script>';
        exit();
    }

    // Verificar si el rol del usuario está en los roles permitidos
    if (!in_array($_SESSION['rol_id'], $roles_permitidos)) {
        echo '<script>
            alert("No tienes permiso para acceder a esta página.");
            window.location.href = "inicio.php";
        </script>';
        exit();
    }
}
?>