<?php
include 'conexion_be.php'; // Asegúrate de que este archivo contiene la conexión a la base de datos
include 'validar_sesion.php';
include 'validar_level_user.php';

// Obtener el filtro seleccionado
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';
$filtro_fecha = isset($_GET['filtro_fecha']) ? $_GET['filtro_fecha'] : 'hoy';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Construir la consulta SQL según el filtro de fecha
if ($filtro_fecha === 'hoy') {
    $consulta_fecha = "DATE(hora) = CURDATE()";
} elseif ($filtro_fecha === 'ayer') {
    $consulta_fecha = "DATE(hora) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
} elseif ($filtro_fecha === 'ultimos_7_dias') {
    $consulta_fecha = "DATE(hora) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filtro_fecha === 'personalizado' && $fecha_inicio && $fecha_fin) {
    $consulta_fecha = "DATE(hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
} else {
    $consulta_fecha = "1"; // Mostrar todos los registros si no hay filtro válido
}

// Construir la consulta SQL según el filtro
if ($filtro === 'todos') {
    $consulta = "
        SELECT id, nombre, apellido, cedula, tipo_trabajador, tipo, hora
        FROM asistencias
        WHERE $consulta_fecha
        ORDER BY hora DESC
    ";
} else {
    $consulta = "
        SELECT id, nombre, apellido, cedula, tipo_trabajador, tipo, hora
        FROM asistencias
        WHERE tipo_trabajador = '$filtro' AND $consulta_fecha
        ORDER BY hora DESC
    ";
}

// Ejecutar la consulta
$resultado = $enlace->query($consulta);

// Verificar si la consulta fue exitosa
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}

// Código para registrar asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener la cédula y el tipo del formulario
    $cedula = $_POST['cedula'];
    $tipo = $_POST['tipo']; // Ejemplo: entrada o salida
    $hora = date('Y-m-d H:i:s'); // Hora actual

    // Verificar si la cédula existe en la tabla trabajadores
    $query_trabajador = "SELECT t.nombre, t.apellido, t.cedula, c.cargo AS tipo_trabajador
                         FROM trabajadores t
                         INNER JOIN cargos c ON t.cargos = c.id_cargo
                         WHERE t.cedula = '$cedula'";
    $resultado_trabajador = $conexion->query($query_trabajador);

    if ($resultado_trabajador->num_rows > 0) {
        // Obtener los datos del trabajador
        $trabajador = $resultado_trabajador->fetch_assoc();
        $nombre = $trabajador['nombre'];
        $apellido = $trabajador['apellido'];
        $tipo_trabajador = $trabajador['tipo_trabajador'];

        // Insertar los datos en la tabla asistencias
        $query_asistencia = "INSERT INTO asistencias (nombre, apellido, cedula, tipo_trabajador, tipo, hora)
                             VALUES ('$nombre', '$apellido', '$cedula', '$tipo_trabajador', '$tipo', '$hora')";

        if ($conexion->query($query_asistencia) === TRUE) {
            echo "Asistencia registrada exitosamente.";
        } else {
            echo "Error al registrar la asistencia: " . $conexion->error;
        }
    } else {
        echo "La cédula no existe en la tabla trabajadores.";
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asistencias</title>
    <link rel="stylesheet" href="Stilos/styles_asistencias.css">
    <link rel="stylesheet" href="Stilos/styles_tablas.css">
    <link rel="stylesheet" href="Stilos/css/bootstrap.min.css">
    <link rel="stylesheet" href="Stilos/jquery.dataTables.min.css">
    <script src="Java/jquery.min.js"></script>
    <script src="Java/jquery.dataTables.min.js"></script>
    <script src="Java/main.js"></script>
</head>
<body>

    <?php include 'vista/top-bar.php'; ?>
    <div class="container">
        <div class="contenedor">
            <h1>Gestión de Asistencias</h1>
        </div>

        <div class="filtros">
            <h2>Filtros</h2>
            <!-- Filtros combinados -->
            <form method="GET" action="">
                <!-- Filtro por tipo de trabajador -->
                <label for="filtro">Filtrar por tipo de trabajador:</label>
                <select name="filtro" id="filtro">
                    <option value="todos" <?php echo $filtro === 'todos' ? 'selected' : ''; ?>>Todos</option>
                    <option value="Cocinero" <?php echo $filtro === 'Cocinero' ? 'selected' : ''; ?>>Cocinero</option>
                    <option value="Vigilante" <?php echo $filtro === 'Vigilante' ? 'selected' : ''; ?>>Vigilante</option>
                    <option value="Maestro" <?php echo $filtro === 'Maestro' ? 'selected' : ''; ?>>Maestro</option>
                    <option value="Obrero" <?php echo $filtro === 'Obrero' ? 'selected' : ''; ?>>Obrero</option>
                </select>

                <!-- Filtro por rango de fechas -->
                <label for="filtro_fecha">Filtrar por fecha:</label>
                <select name="filtro_fecha" id="filtro_fecha" onchange="toggleFechaPersonalizada()">
                    <option value="hoy" <?php echo $filtro_fecha === 'hoy' ? 'selected' : ''; ?>>Hoy</option>
                    <option value="ayer" <?php echo $filtro_fecha === 'ayer' ? 'selected' : ''; ?>>Ayer</option>
                    <option value="ultimos_7_dias" <?php echo $filtro_fecha === 'ultimos_7_dias' ? 'selected' : ''; ?>>Últimos 7 días</option>
                    <option value="personalizado" <?php echo $filtro_fecha === 'personalizado' ? 'selected' : ''; ?>>Personalizado</option>
                </select>

                <!-- Campos para rango de fechas personalizado -->
                <div id="fechas_personalizadas" style="display: <?php echo $filtro_fecha === 'personalizado' ? 'block' : 'none'; ?>;">
                    <label for="fecha_inicio">Desde:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
                    <label for="fecha_fin">Hasta:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="<?php echo $fecha_fin; ?>">
                </div>

                <!-- Botón para aplicar los filtros -->
                <button type="submit">Filtrar</button>

                <!-- Botón para descargar PDF -->
                <button type="button" class="btn btn-pad" data-bs-toggle="modal" data-bs-target="#addWorkerModal">
                    <a href="descargar_asistencias.php?filtro=<?php echo $filtro; ?>&filtro_fecha=<?php echo $filtro_fecha; ?>&fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>" class="dowl">Descargar</a>
                </button>
            </form>
        </div>

        <div class="tabla">
            <!-- Tabla para mostrar los registros -->
            <h2>Lista de Registros</h2>
            <table class="table table-striped table-hover" id="data-tables">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Cédula</th>
                        <th>Tipo de Trabajador</th>
                        <th>Tipo</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $numero_fila = 1; // Inicializa el contador de filas
                    while ($fila = $resultado->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?php echo $numero_fila++; ?></td>
                            <td><?php echo $fila['nombre']; ?></td>
                            <td><?php echo $fila['apellido']; ?></td>
                            <td><?php echo $fila['cedula']; ?></td>
                            <td><?php echo $fila['tipo_trabajador']; ?></td>
                            <td><?php echo $fila['tipo']; ?></td>
                            <td>
                                <?php
                                    $fecha = strtotime($fila['hora']);
                                    echo date('d/m/Y, H:i', $fecha);
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleFechaPersonalizada() {
            const filtroFecha = document.getElementById('filtro_fecha').value;
            const fechasPersonalizadas = document.getElementById('fechas_personalizadas');
            if (filtroFecha === 'personalizado') {
                fechasPersonalizadas.style.display = 'block';
            } else {
                fechasPersonalizadas.style.display = 'none';
            }
        }
    </script>

    <script src="Java/js/bootstrap.bundle.min.js"></script>
    <script src="Java/js.js"></script>
    <script>
        $(document).ready(function() {
            $('#data-tables').DataTable()
        });
                
     </script>
</body>
</html>