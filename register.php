<?php

session_start();

if(isset($_SESSION['usuario'])){
    header('Location: inicio.php');
}


?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro y control de asistencias</title>
  <link rel="stylesheet" href="Stilos/styles_login-register.css"/>
  <link rel="stylesheet" href="fontawesome/fontawesome-free-6.7.2-web/css/all.css">


</head>
<body>
  <main>
    <div class="Contenedor__login-register">          
      <!-- Registro-->
      <div class="register-container">
        <div class="register-form" id="contenedor-botones">
          <img src="imagen/Picsart_25-03-31_14-46-19-016.png" alt="Logo" class="logo" />
          <h2>Registro</h2>
          <p>Por favor introduce tus datos</p>
          <form action="registro_usuario_be.php" class="formulario__register" method="post" id="form-registro" autocomplete="off" novalidate>
            <input type="hidden" name="data_tipo" value="registro" />

            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre_completo" name="nombre_completo" placeholder="Introduzca su nombre" />
            <span class="error" id="error-nombre"></span>

            <label for="Correo_electronico">Correo electrónico</label>
            <input type="email" id="Correo_electronico" name="email" placeholder="Introduzca su Correo electronico" />
            <span class="error" id="error-email"></span>

            <label for="usuario-registro">Usuario</label>
            <input type="text" id="usuario-registro" name="user" placeholder="Introduzca el usuario" minlength="4" maxlength="15" />
            <span class="error" id="error-usuario"></span>

            <label for="password-registro">Contraseña</label>
            <input type="password" id="password-registro" name="password" placeholder="Introduzca la contraseña" />
            <span class="error" id="error-password"></span>

            <button type="submit" name="registro" id="btn__registrarse">Registrar</button>
            <br>
            <a id="" href="index.php">¿Ya tienes cuenta?</a>
          </form>
        </div>
      <div class="register-bg"></div>
      </div>
    </div>
  </main>
    <div class="manual-link">
        <a href="archivos/Manual de usuarios.pdf" download class="manual-linkb">
            <div class="icon"><i class="fa-solid fa-circle-question"></i></div>
        </a>
    </div>
    
  <script src="Java/Script.js"></script>
  <script>
document.getElementById('form-registro').addEventListener('submit', function(e) {
    let valido = true;

    // Limpiar errores previos
    document.querySelectorAll('.error').forEach(el => el.style.display = 'none');
    document.querySelectorAll('input').forEach(el => el.classList.remove('error-input'));

    // Nombre completo
    const nombre = document.getElementById('nombre_completo');
    if (!nombre.value.trim()) {
        mostrarError('error-nombre', 'El nombre completo es requerido.', nombre);
        valido = false;
    }

    // Email
    const email = document.getElementById('Correo_electronico');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim()) {
        mostrarError('error-email', 'El correo electrónico es requerido.', email);
        valido = false;
    } else if (!emailRegex.test(email.value.trim())) {
        mostrarError('error-email', 'Ingrese un correo electrónico válido.', email);
        valido = false;
    }

    // Usuario
    const usuario = document.getElementById('usuario-registro');
    if (!usuario.value.trim()) {
        mostrarError('error-usuario', 'El usuario es requerido.', usuario);
        valido = false;
    } else if (usuario.value.length < 4 || usuario.value.length > 15) {
        mostrarError('error-usuario', 'El usuario debe tener entre 4 y 15 caracteres.', usuario);
        valido = false;
    }

    // Contraseña
    const password = document.getElementById('password-registro');
    // No ñ, {}, ´´,  /, [], etc.
    const forbidden = /[ñÑ{}\[\]\/´`]/;
    const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9ñÑ{}\[\]\/´`]).{6,}$/;
    if (!password.value) {
        mostrarError('error-password', 'La contraseña es requerida.', password);
        valido = false;
    } else if (password.value.length < 6) {
        mostrarError('error-password', 'La contraseña debe tener al menos 6 caracteres.', password);
        valido = false;
    } else if (!passRegex.test(password.value) || forbidden.test(password.value)) {
        mostrarError('error-password', 'Debe tener al menos una mayúscula, una minúscula y un caracter especial permitido (sin ñ, {}, [], /, ´, `).', password);
        valido = false;
    }

    if (!valido) e.preventDefault();
});

// Validación automática al salir de cada input
document.getElementById('nombre_completo').addEventListener('blur', function() {
    const nombre = this;
    const errorId = 'error-nombre';
    if (!nombre.value.trim()) {
        mostrarError(errorId, 'El nombre completo es requerido.', nombre);
    } else {
        limpiarError(errorId, nombre);
    }
});

document.getElementById('Correo_electronico').addEventListener('blur', function() {
    const email = this;
    const errorId = 'error-email';
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim()) {
        mostrarError(errorId, 'El correo electrónico es requerido.', email);
    } else if (!emailRegex.test(email.value.trim())) {
        mostrarError(errorId, 'Ingrese un correo electrónico válido.', email);
    } else {
        limpiarError(errorId, email);
    }
});

document.getElementById('usuario-registro').addEventListener('blur', function() {
    const usuario = this;
    const errorId = 'error-usuario';
    if (!usuario.value.trim()) {
        mostrarError(errorId, 'El usuario es requerido.', usuario);
    } else if (usuario.value.length < 4 || usuario.value.length > 15) {
        mostrarError(errorId, 'El usuario debe tener entre 4 y 15 caracteres.', usuario);
    } else {
        limpiarError(errorId, usuario);
    }
});

document.getElementById('password-registro').addEventListener('blur', function() {
    const password = this;
    const errorId = 'error-password';
    const forbidden = /[ñÑ{}\[\]\/´`]/;
    const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9ñÑ{}\[\]\/´`]).{6,}$/;
    if (!password.value) {
        mostrarError(errorId, 'La contraseña es requerida.', password);
    } else if (password.value.length < 6) {
        mostrarError(errorId, 'La contraseña debe tener al menos 6 caracteres.', password);
    } else if (!passRegex.test(password.value) || forbidden.test(password.value)) {
        mostrarError(errorId, 'Debe tener al menos una mayúscula, una minúscula y un caracter especial permitido (sin ñ, {}, [], /, ´, `).', password);
    } else {
        limpiarError(errorId, password);
    }
});

// Función para limpiar el error
function limpiarError(id, input) {
    const error = document.getElementById(id);
    error.textContent = '';
    error.style.display = 'none';
    if (input) input.classList.remove('error-input');
}

function mostrarError(id, mensaje, input) {
    const error = document.getElementById(id);
    error.textContent = mensaje;
    error.style.display = 'block';
    if (input) input.classList.add('error-input');
}
</script>
</body>
</html>

