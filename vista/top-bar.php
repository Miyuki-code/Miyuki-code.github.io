<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    $host = "localhost";
    $user = "root"; // Cambia esto si tienes un usuario diferente
    $password = ""; // Cambia esto si tienes una contraseña
    $database = "registro"; // Cambia esto por el nombre de tu base de datos

    // Verificar si se subió un archivo
    if ($_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
        // Ruta temporal del archivo subido
        $uploaded_file = $_FILES['sql_file']['tmp_name'];

        // Comando para importar la base de datos
        $command = "mysql --host=$host --user=$user --password=$password $database < $uploaded_file";

        // Ejecutar el comando
        exec($command, $output, $result);

        if ($result === 0) {
            echo '<script>
                alert("Base de datos importada exitosamente.");
                window.location.href = window.location.href;
            </script>';
        } else {
            echo '<script>
                alert("Error al importar la base de datos. Verifica el archivo SQL.");
                window.location.href = window.location.href;
            </script>';
        }
    } else {
        echo '<script>
            alert("Error al subir el archivo. Verifica que sea un archivo válido.");
            window.location.href = window.location.href;
        </script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fontawesome/fontawesome-free-6.7.2-web/css/all.css">
    <link rel="stylesheet" href="Stilos/header.css">
    <link rel="stylesheet" href="Stilos/modal.css">
   

    <title>Inicio</title>
</head>
<body>
    <header>
        <div class="left">
            <div class="menu-container">
                <div class="menu" id="menu">
                    <div class="i"><i class="fa-solid fa-bars"></i></div>

                </div>
            </div>
        </div>

        <div class="right">
         <h2 id="fecha"></h2>
            <script>
                const fecha = new Date();
                const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const fechaFormateada = fecha.toLocaleDateString('es-ES', opciones);
                document.getElementById('fecha').textContent = fechaFormateada;
            </script>
        <h2 id="hora"></h2>
            <script>
                function actualizarHora() {
                    const fecha = new Date();
                    const horas = String(fecha.getHours()).padStart(2, '0');
                    const minutos = String(fecha.getMinutes()).padStart(2, '0');
                    const segundos = String(fecha.getSeconds()).padStart(2, '0');
                    document.getElementById('hora').textContent = `${horas}:${minutos}:${segundos}`;
                }
                setInterval(actualizarHora, 1000);
            </script>
            
            <!--<a href="notificacion.php">
                <div class="iconos"><i class="fa-solid fa-bell"></i></div>
            </a>-->

            <a href="cerrar_sesion.php" onclick="console.log('Cerrando sesión');">
                <div class="iconos"><i class="fa-solid fa-right-to-bracket"></i></div>
            </a>
        </div>
    </header>
    <div class="sidebar" id="sidebar">
        <ul>
            <li class="logo" style="--bg:#ffffff;">
                <a href="inicio.php">
                    <div class="icon"><img src="imagen/Picsart_25-03-31_14-46-19-016.png" alt=""></div>
                    <div class="text">C.E.I Simoncito Guayana</div>
                </a>
            </li>
            <div class="Menulist">
                <li style="--bg:#ff4d4d;" class="active">
                    <a href="inicio.php">
                        <div class="icon"><i class="fa-solid fa-house"></i></div>
                        <div class="text">Inicio</div>
                    </a>
                </li>
                <li style="--bg:#ff7e4d;">
                    <a href="asistencias.php">
                        <div class="icon"><i class="fa-solid fa-clipboard"></i></div>
                        <div class="text">Asistencias</div>
                    </a>
                </li>
                <li style="--bg:#ffdf4d;">
                    <a href="trabajadores.php">
                        <div class="icon"><i class="fa-solid fa-person"></i></div>
                        <div class="text">Trabajadores</div>
                    </a>
                </li>
                <li style="--bg:#4dff4d;">
                    <a href="reposo_medico.php">
                        <div class="icon"><i class="fa-solid fa-kit-medical"></i></div>
                        <div class="text">Reposo medico</div>
                    </a>
                </li>
                <li style="--bg:#884dff;">
                    <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1): // Solo Administradores ?>
                        <a href="usuarios.php">
                            <div class="icon"><i class="fa-solid fa-user"></i></div>
                            <div class="text">Usuario</div>
                        </a>
                    <?php endif; ?>
                </li>
                <li style="--bg:#ff4dff;">
                    <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1): // Solo Administradores ?>
                    <a href="settings.php">
                        <div class="icon"><i class="fa-solid fa-bars-progress"></i></div>
                        <div class="text">Configuracion</div>
                    </a>
                    <?php endif; ?>
                </li>

                <li style="--bg:#4dff4d;">
                    <a href="archivos/Manual de usuarios.pdf" download>
                        <div class="icon"><i class="fa-solid fa-circle-question"></i></div>
                        <div class="text">Manual de Usuarios</div>
                    </a>
                </li>

            </div>
        </ul>
    </div>

    <!-- Contenedor de notificaciones -->
    <div id="notification-container" class="notification-container">
        <div class="notification-header">
            <h4>Notificaciones</h4>
            <button id="close-notifications" class="close-btn">&times;</button>
        </div>
        <div class="notification-body">
            <!-- Aquí se cargarán las notificaciones dinámicamente -->
            <ul id="notification-list">
                <li>No hay notificaciones nuevas.</li>
            </ul>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menu = document.getElementById('menu');
        const sidebar = document.getElementById('sidebar');
        const Menulist = document.querySelectorAll('.Menulist > li');
        const mainContent = document.getElementById('main-content');

        // Alternar la visibilidad del menú lateral
        menu.addEventListener('click', () => {
            sidebar.classList.toggle('menu-toggle');
        });

        // Función para establecer el enlace activo basado en la URL actual
        function setActiveLink() {
            const currentPath = window.location.pathname.split('/').pop(); // Obtiene el nombre del archivo actual
            Menulist.forEach((item) => {
                const link = item.querySelector('a');
                if (link) {
                    const href = link.getAttribute('href').split('/').pop(); // Obtiene el nombre del archivo del href
                    if (href === currentPath) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                }
            });
        }

        // Llama a la función al cargar la página
        setActiveLink();

        // Agrega el evento de clic para cambiar el enlace activo manualmente
        Menulist.forEach((item) => {
            item.addEventListener('click', function () {
                Menulist.forEach((el) => el.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Carga dinámica del contenido de los módulos
        if (mainContent) {
            const links = document.querySelectorAll('.Menulist a');

            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault(); // Evita la recarga completa
                    const url = link.getAttribute('href');

                    // Carga el contenido del módulo
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text();
                        })
                        .then(data => {
                            mainContent.innerHTML = data; // Inserta el contenido en el contenedor
                            history.pushState(null, '', url); // Actualiza la URL sin recargar
                            setActiveLink(); // Actualiza el enlace activo
                        })
                        .catch(error => console.error('Error al cargar el módulo:', error));
                });
            });
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const sqlFileInput = document.getElementById('sql_file');
        const importModal = document.getElementById('importDbModal');

        if (sqlFileInput && importModal) {
            const importButton = document.querySelector('#importDbModal .btn-primary');

            sqlFileInput.addEventListener('change', () => {
                if (sqlFileInput.files.length > 0) {
                    importButton.disabled = false;
                } else {
                    importButton.disabled = true;
                }
            });

            importButton.addEventListener('click', (e) => {
                const confirmImport = confirm('¿Estás seguro de que deseas importar esta base de datos? Esto sobrescribirá los datos actuales.');
                if (!confirmImport) {
                    e.preventDefault();
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const notificationLink = document.querySelector('a[href="notificacion.php"]');
        const notificationContainer = document.getElementById('notification-container');
        const closeNotifications = document.getElementById('close-notifications');

        if (notificationLink) {
            // Mostrar el contenedor de notificaciones
            notificationLink.addEventListener('click', (e) => {
                e.preventDefault(); // Evitar redirección
                notificationContainer.style.display = "block"; // Mostrar contenedor
            });
        }

        if (closeNotifications) {
            // Cerrar el contenedor de notificaciones
            closeNotifications.addEventListener('click', () => {
                notificationContainer.style.display = "none"; // Ocultar contenedor
            });
        }
    });
</script>

</body>
</html>
