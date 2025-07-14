<?php
include 'conexion_be.php';

$host = "localhost";
$user = "root";
$password = "";
$database = "registro";

// Restaurar desde backup existente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore_file'])) {
    $restore_file = basename($_POST['restore_file']);
    $backup_file = __DIR__ . "/uploads/" . $restore_file;

    if (file_exists($backup_file)) {
        $mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';
        $command = "\"$mysqlPath\" --host=$host --user=$user --password=$password $database < \"$backup_file\"";
        exec($command . " 2>&1", $output, $result);

        if ($result === 0) {
            // Registrar la restauración como un nuevo backup
            $nombreArchivo = $restore_file;
            $fecha = date('Y-m-d H:i:s');
            $enlace->query("INSERT INTO backup (nombre_archivo, fecha) VALUES ('$nombreArchivo', '$fecha')");

            // Eliminar el más antiguo si hay más de 5 backups
            $backups = $enlace->query("SELECT id FROM backup ORDER BY fecha DESC");
            if ($backups && $backups->num_rows > 5) {
                $ids = [];
                while ($row = $backups->fetch_assoc()) {
                    $ids[] = $row['id'];
                }
                $ids_a_eliminar = array_slice($ids, 5);
                if (!empty($ids_a_eliminar)) {
                    $ids_a_eliminar_str = implode(',', $ids_a_eliminar);
                    $enlace->query("DELETE FROM backup WHERE id IN ($ids_a_eliminar_str)");
                }
            }

            header("Location: settings.php?toast_tipo=exito&toast_titulo=Éxito&toast_descripcion=Base+de+datos+restaurada+exitosamente+desde+la+copia+seleccionada.");
            exit();
        } else {
            header("Location: settings.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=Error+al+restaurar+la+base+de+datos.+Verifica+el+archivo+SQL.");
            exit();
        }
    } else {
        header("Location: settings.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=El+archivo+de+respaldo+no+existe.");
        exit();
    }
}

// Restaurar desde archivo subido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sql_file'])) {
    if ($_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
        $uploaded_file = $_FILES['sql_file']['tmp_name'];
        $uploads_dir = __DIR__ . "/uploads";
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        $backup_file = $uploads_dir . "/" . $_FILES['sql_file']['name'];

        if (move_uploaded_file($uploaded_file, $backup_file)) {
            $mysqlPath = 'C:\\xampp\\mysql\\bin\\mysql.exe';
            $command = "\"$mysqlPath\" --host=$host --user=$user --password=$password $database < \"$backup_file\"";
            exec($command . " 2>&1", $output, $result);

            if ($result === 0) {
                $nombreArchivo = $_FILES['sql_file']['name'];
                $fecha = date('Y-m-d H:i:s');
                $enlace->query("INSERT INTO backup (nombre_archivo, fecha) VALUES ('$nombreArchivo', '$fecha')");

                $backups = $enlace->query("SELECT id FROM backup ORDER BY fecha DESC");
                if ($backups && $backups->num_rows > 5) {
                    $ids = [];
                    while ($row = $backups->fetch_assoc()) {
                        $ids[] = $row['id'];
                    }
                    $ids_a_eliminar = array_slice($ids, 5);
                    if (!empty($ids_a_eliminar)) {
                        $ids_a_eliminar_str = implode(',', $ids_a_eliminar);
                        $enlace->query("DELETE FROM backup WHERE id IN ($ids_a_eliminar_str)");
                    }
                }

                header("Location: settings.php?toast_tipo=exito&toast_titulo=Éxito&toast_descripcion=Base+de+datos+restaurada+exitosamente.");
                exit();
            } else {
                header("Location: settings.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=Error+al+restaurar+la+base+de+datos.+Verifica+el+archivo+SQL.");
                exit();
            }
        } else {
            header("Location: settings.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=Error+al+mover+el+archivo+subido.");
            exit();
        }
    } else {
        header("Location: settings.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=Error+al+subir+el+archivo.+Verifica+que+sea+un+archivo+válido.");
        exit();
    }
}
?>