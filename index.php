<?php

session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: inicio.php');
}

include 'vista/notificaciones.php'; // Incluir el archivo de notificaciones
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro y control de asistencias</title>
  <link rel="stylesheet" href="Stilos/styles_login-register.css"/>
  <link rel="stylesheet" href="fontawesome/fontawesome-free-6.7.2-web/css/all.css">
  <script src="Java/notificaciones.js" defer></script>
</head>
<body>
  <main>
    <div class="Contenedor__login-register">
      <div class="login-container">
        <div class="login-form">
          <img src="imagen/Picsart_25-03-31_14-46-19-016.png" alt="Logo" class="logo" />
          <!-- Inicio de sesion -->
          <h2>Inicio de sesion</h2>
          <p>Por favor introduce tu usuario</p>
          <form action="login_usuario_be.php" class="formulario__login" method="post" id="form-login" autocomplete="off" novalidate>
            <input type="hidden" name="data_tipo" value="login" />
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="user" placeholder="Introduzca el usuario" />
            <span class="error" id="error-login-usuario"></span>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Introduzca la contraseña" />
            <span class="error" id="error-login-password"></span>

            <div class="remember">
              <input type="checkbox" id="remember" />
              <label for="remember">¿Recordar?</label>
            </div>

            <button type="submit">Entrar</button>
            <br>
            <h4>¿No tienes cuenta?</h4> <a id="" href="register.php">Da click aqui para registrarte</a>
            <h4>¿Olvidaste tu contraseña?</h4> <a href="recovery.php">Recuperar contraseña</a>
          </form>
        </div>
        <div class="login-bg"></div>
      </div>
    </div>
    
  </main>
  <div class="manual-link">
    <a href="archivos/Manual de usuarios.pdf" download class="manual-linka">
        <div class="icon"><i class="fa-solid fa-circle-question"></i></div>
    </a>
  </div>
    
  <?php
  // Mostrar el toast dependiendo del parámetro en la URL
  if (isset($_GET['status']) && isset($_GET['data_tipo'])) {
      $status = $_GET['status'];
      $data_tipo = $_GET['data_tipo'];

      $tipo = $status === 'success' ? 'exito' : ($status === 'error' ? 'error' : 'error2');
      $titulo = $status === 'success' ? '¡Éxito!' : ($status === 'error' ? '¡Error!' : '¡Error!');
      $descripcion = $status === 'success' 
          ? 'Usuario registrado correctamente.' 
          : ($status === 'error' 
              ? 'No se pudo registrar el usuario.' 
              : 'Usuario o contraseña incorrecta.');

      echo "<script>
          document.addEventListener('DOMContentLoaded', () => {
              agregarToast({
                  tipo: '$tipo',
                  titulo: '$titulo',
                  descripcion: '$descripcion',
                  autoCierre: true
              });
          });
      </script>";
  }

  if (isset($_GET['toast_tipo']) && isset($_GET['toast_titulo']) && isset($_GET['toast_descripcion'])) {
    $toast_tipo = htmlspecialchars($_GET['toast_tipo']);
    $toast_titulo = htmlspecialchars($_GET['toast_titulo']);
    $toast_descripcion = htmlspecialchars($_GET['toast_descripcion']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            agregarToast({
                tipo: '$toast_tipo',
                titulo: '$toast_titulo',
                descripcion: '$toast_descripcion',
                autoCierre: true
            });
        });
    </script>";
}
  ?>

  <script src="Java/Script.js"></script>
  <script>
  document.getElementById('form-login').addEventListener('submit', function(e) {
      let valido = true;

      // Limpiar errores previos
      document.getElementById('error-login-usuario').style.display = 'none';
      document.getElementById('error-login-password').style.display = 'none';
      document.getElementById('usuario').classList.remove('error-input');
      document.getElementById('password').classList.remove('error-input');

      // Usuario
      const usuario = document.getElementById('usuario');
      if (!usuario.value.trim()) {
          mostrarErrorLogin('error-login-usuario', 'El usuario es requerido.', usuario);
          valido = false;
      }

      // Contraseña
      const password = document.getElementById('password');
      if (!password.value) {
          mostrarErrorLogin('error-login-password', 'La contraseña es requerida.', password);
          valido = false;
      }

      if (!valido) e.preventDefault();
  });

  function mostrarErrorLogin(id, mensaje, input) {
      const error = document.getElementById(id);
      error.textContent = mensaje;
      error.style.display = 'block';
      if (input) input.classList.add('error-input');
  }

  // Validación automática al salir de cada input
  document.getElementById('usuario').addEventListener('blur', function() {
      if (!this.value.trim()) {
          mostrarErrorLogin('error-login-usuario', 'El usuario es requerido.', this);
      } else {
          limpiarErrorLogin('error-login-usuario', this);
      }
  });
  document.getElementById('password').addEventListener('blur', function() {
      if (!this.value) {
          mostrarErrorLogin('error-login-password', 'La contraseña es requerida.', this);
      } else {
          limpiarErrorLogin('error-login-password', this);
      }
  });
  function limpiarErrorLogin(id, input) {
      const error = document.getElementById(id);
      error.textContent = '';
      error.style.display = 'none';
      if (input) input.classList.remove('error-input');
  }
  </script>
</body>
</html>