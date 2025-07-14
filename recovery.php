<?php
session_start();
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
        <form action="recovery_be.php" method="POST">
            <h1>Ingresa tu Correo</h1>
            <h3>Correo electrónico:</h3>
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" placeholder="Ingresa tu correo electrónico" >
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
const inputEmail = document.getElementById('email');

function validarEmail() {
    // Limpiar errores previos
    let errorEmail = document.getElementById('error-email');
    if (errorEmail) errorEmail.remove();
    inputEmail.classList.remove('error-input');

    // Validar email
    const email = inputEmail.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        mostrarError('El correo electrónico es requerido.', inputEmail);
        return false;
    } else if (!emailRegex.test(email)) {
        mostrarError('Ingrese un correo electrónico válido.', inputEmail);
        return false;
    }
    return true;
}

function mostrarError(mensaje, input) {
    let errorSpan = document.createElement('span');
    errorSpan.id = 'error-email';
    errorSpan.className = 'error';
    errorSpan.textContent = mensaje;
    input.parentNode.insertBefore(errorSpan, input.nextSibling);
    input.classList.add('error-input');
}

// Validar al enviar el formulario
document.querySelector('form[action="recovery_be.php"]').addEventListener('submit', function(e) {
    if (!validarEmail()) e.preventDefault();
});

// Validar al salir del campo (blur)
inputEmail.addEventListener('blur', validarEmail);
</script>
</body>
</html>