<?php
include 'conexion_be.php';
include 'validar_sesion.php';
include 'validar_level_user.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Base de Datos</title>
    <link rel="stylesheet" href="Stilos/css/bootstrap.min.css"> <!-- Opcional: agrega estilos a las tablas -->
</head>
<body>
    <!-- Modal para subir el archivo SQL -->
    <div class="modal fade" id="importDbModal" tabindex="-1" aria-labelledby="importDbModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importDbModalLabel">Importar Base de Datos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="sql_file" class="form-label">Selecciona un archivo SQL:</label>
                            <input type="file" name="sql_file" id="sql_file" class="form-control" accept=".sql" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Importar</button>
                        <a href="inicio.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="Java/js/bootstrap.bundle.min.js"></script>
    <script>
        // Abrir el modal automáticamente al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            const importDbModal = new bootstrap.Modal(document.getElementById('importDbModal'));
            importDbModal.show(); // Mostrar el modal automáticamente
        });
    </script>
</body>
</html>

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
        $command = "C:\\xampp\\mysql\\bin\\mysql --host=$host --user=$user --password=$password $database < $uploaded_file";

        // Ejecutar el comando
        exec($command, $output, $result);

        if ($result === 0) {
            echo '<script>
                alert("Base de datos importada exitosamente.");
                window.location.href = "inicio.php";
            </script>';
        } else {
            echo '<script>
                alert("Error al importar la base de datos. Verifica el archivo SQL.");
                window.location.href = "inicio.php";
            </script>';
        }
    } else {
        echo '<script>
            alert("Error al subir el archivo. Verifica que sea un archivo válido.");
            window.location.href = "inicio.php";
        </script>';
    }
}
?>
