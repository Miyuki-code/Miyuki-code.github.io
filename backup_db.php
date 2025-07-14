<?php
// filepath: c:\xampp\htdocs\AppRegistroyControl\backup_db.php
$host = "localhost";
$user = "root"; // Cambia esto si tienes un usuario diferente
$password = ""; // Cambia esto si tienes una contraseña
$database = "registro"; // Cambia esto por el nombre de tu base de datos

// Formato de nombre: nombreBD_YYYY-MM-DD_HH-MM-SS.sql
$fecha_hora = date('Y-m-d_H-i-s');
$nombre_archivo = "{$database}_{$fecha_hora}.sql";
$ruta_archivo = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nombre_archivo;

// Comando para exportar la base de datos
$command = "C:\\xampp\\mysql\\bin\\mysqldump --host=$host --user=$user --password=$password $database > \"$ruta_archivo\"";

// Ejecutar el comando
exec($command, $output, $result);

if ($result === 0 && file_exists($ruta_archivo)) {
    // Forzar descarga del archivo al usuario
    header('Content-Description: File Transfer');
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($ruta_archivo));
    readfile($ruta_archivo);
    // Eliminar el archivo temporal después de la descarga
    unlink($ruta_archivo);
    exit();
} else {
    // Redirigir con toast de error
    header("Location: inicio.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=Error+al+crear+el+respaldo.+Verifica+la+configuración+de+mysqldump.");
    exit();
}
?>