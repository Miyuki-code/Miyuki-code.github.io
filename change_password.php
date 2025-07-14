<?php
session_start();
include 'conexion_be.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Obtener token de la URL
$token = isset($_GET['token']) ? $_GET['token'] : null;

if (!$token) {
    die("Enlace inválido.");
}

// Buscar token en la base de datos y verificar expiración
$query = mysqli_query($enlace, "SELECT user_id FROM password_resets WHERE token = '$token' AND expires_at > NOW()");
if ($row = mysqli_fetch_assoc($query)) {
    $user_id = $row['user_id'];
    // Mostrar formulario para cambiar contraseña (con campo oculto para el token)
} else {
    die("El enlace ha expirado o no es válido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="Stilos/stylos_recuperar_contrasena.css">
    <link rel="stylesheet" href="fontawesome/fontawesome-free-6.7.2-web/css/all.css">

</head>
<body>
    <div class="form">
        <form action="change_password_be.php" method="POST">
            <h1>Cambiar Contraseña</h1>
            <label for="password">Nueva Contraseña</label>
            <input type="password" id="password" name="new_password" placeholder="Contraseña">
            <!-- Campo oculto para enviar el token -->
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <button type="submit">Recuperar Contraseña</button>
            <p>¿Ya tienes cuenta? <a href="index.php">Iniciar sesión</a></p>
            <p>¿No tienes cuenta? <a href="register.php">Registrarse</a></p>
        </form>
    </div>
    <div class="manual-link">
        <a href="archivos/Manual de usuarios.pdf" download class="manual-linka">
            <div class="icon"><i class="fa-solid fa-circle-question"></i></div>
        </a>
    </div>
    
    <script>
const inputPassword = document.getElementById('password');

function validarPassword() {
    // Limpiar error previo
    let errorSpan = document.getElementById('error-password');
    if (errorSpan) errorSpan.remove();
    inputPassword.classList.remove('error-input');

    // Validaciones
    const password = inputPassword.value;
    const forbidden = /[ñÑ{}\[\]\/´`]/;
    const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9ñÑ{}\[\]\/´`]).{6,}$/;

    if (!password) {
        mostrarError('La contraseña es requerida.', inputPassword);
        return false;
    } else if (password.length < 6) {
        mostrarError('La contraseña debe tener al menos 6 caracteres.', inputPassword);
        return false;
    } else if (!passRegex.test(password) || forbidden.test(password)) {
        mostrarError('Debe tener al menos una mayúscula, una minúscula y un caracter especial permitido (sin ñ, {}, [], /, ´, `).', inputPassword);
        return false;
    }
    return true;
}

function mostrarError(mensaje, input) {
    let errorSpan = document.createElement('span');
    errorSpan.id = 'error-password';
    errorSpan.className = 'error';
    errorSpan.textContent = mensaje;
    input.parentNode.insertBefore(errorSpan, input.nextSibling);
    input.classList.add('error-input');
}

// Validar al enviar el formulario
document.querySelector('form[action="change_password_be.php"]').addEventListener('submit', function(e) {
    if (!validarPassword()) e.preventDefault();
});

// Validar al salir del campo (blur)
inputPassword.addEventListener('blur', validarPassword);
</script>
</body>
</html>