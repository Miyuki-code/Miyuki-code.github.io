<?php
include 'conexion_be.php';
include 'validar_sesion.php';
include 'validar_level_user.php';

// settings.php
// Este archivo contiene la configuración de la base de datos y otras configuraciones necesarias para el funcionamiento del sistema.
// Configuración de la base de datos
$servidor = "localhost"; // Cambia esto si tu servidor de base de datos es diferente
$usuario = "root"; // Cambia esto si tu usuario de base de datos es diferente
$contraseña = ""; // Cambia esto si tu contraseña de base de datos es diferente
$base_datos = "registro"; // Cambia esto si tu base de datos es diferente


// Configuración de la zona horaria
date_default_timezone_set('America/Caracas'); // Cambia esto si tu zona horaria es diferente
// Configuración de la codificación de caracteres
$enlace->set_charset("utf8"); // Cambia esto si tu codificación de caracteres es diferente

include 'vista/notificaciones.php'; // Incluir el archivo de notificaciones

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Stilos/inicio.css">
    <script src="Java/jquery.dataTables.min.js"></script>
    <script src="Java/jquery.dataTables.min.js"></script>
    <script src="Java/notificaciones.js" defer></script>


</head>
<body>
    <?php include 'vista/top-bar.php'; ?>
<meta>
    <div class="container-settings">
        <div class="nav-settings">
            <div class="titulo-settings"><h2>Configuración de la Base de Datos</h2></div>
            <div class="nav">
                <ul>
                    <li id="import" onclick="mostrar_importar();">
                        <div>
                            Importar<i class="fa-solid fa-upload"></i>
                        </div>
                        
                    </li>
                    <li id="export" onclick="mostrar_exportar();">
                        <div>
                            Exportar<i class="fa-solid fa-download"></i>
                        </div>
                    </li>
                </ul>
                <div class="nav-content">
                    <div class="import" id="importar">
                        <h5 class="modal-title" id="importDbModalLabel">Importar Base de Datos</h5>
                        <div class="modal-body">
                            <form action="restore_db.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="sql_file" class="form-label">Selecciona un archivo SQL:</label>
                                    <input type="file" name="sql_file" id="sql_file" class="form-control" accept=".sql" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Importar</button>
                            </form>
                        </div>
                        <?php
                        $backups = $enlace->query("SELECT * FROM backup ORDER BY fecha DESC");
                        ?>
                        <h3>Copias de Seguridad Recientes</h3>
                        <table id="tabla-backups" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Nombre del Archivo</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; while($row = $backups->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre_archivo']); ?></td>
                                    <td><?php echo $row['fecha']; ?></td>
                                    <td>
                                        <form action="restore_db.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="restore_file" value="<?php echo htmlspecialchars($row['nombre_archivo']); ?>">
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('¿Restaurar esta copia de seguridad?')">
                                                <i class="fa-solid fa-rotate-left"></i> Restaurar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="export" id="exportar">
                        <h3>Exportar Base de Datos</h3>
                        <p>Haz clic en el botón de abajo para exportar la base de datos actual.</p>
                        <button class="btn btn-primary" onclick="window.location.href='backup_db.php'">
                            <div class="icon"><i class="fa-solid fa-download"></i></div>
                            Exportar Base de Datos

                        </button>
                </div>
                
            </div>
            </div>
        </div>
    </div>
</meta>

<script>
    function mostrar_exportar(){
        document.getElementById('importar').style.display = 'none';
        document.getElementById('exportar').style.display = 'flex';
    }
    function mostrar_importar(){
        document.getElementById('importar').style.display = 'flex';
        document.getElementById('exportar').style.display = 'none';
    }
</script>

<script>
$(document).ready(function() {
    $('#tabla-backups').DataTable({
        "pageLength": 5,
        "lengthChange": false,
        "ordering": false,
        "info": false,
        "searching": false,
        "paging": false // No paginación, solo muestra los 5 más recientes
    });
});
</script>

<?php
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

</body>
</html>