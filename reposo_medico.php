<?php
include 'conexion_be.php'; // Conexión a la base de datos
include 'validar_sesion.php';
include 'validar_level_user.php';

// Permitir acceso a todos los roles, pero restringir acciones para el rol Usuario (rol_id = 3)
$solo_visualizar = ($_SESSION['rol_id'] == 3); // Si es Usuario, solo puede visualizar

include 'validar_acceso.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    // Validar que el campo 'trabajadores' esté definido
    if (!isset($_POST['trabajadores']) || empty($_POST['trabajadores'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'Por favor, seleccione un trabajador.',
                    autoCierre: true
                });
            });
        </script>";
        return;
    }

    $id_trabajador = intval($_POST['trabajadores']);
    $expedicion = $_POST['expedicion']; // Fecha de expedición
    $vence = $_POST['vence']; // Fecha de vencimiento

    $hay_error = false;

    // Validar que los campos no estén vacíos
    if (empty($expedicion) || empty($vence)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'Por favor, complete todos los campos.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }

    // Validar que la fecha de expedición no sea mayor que la de vencimiento
    if (!$hay_error && $expedicion > $vence) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'La fecha de expedición no puede ser mayor que la de vencimiento.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }

    // Validar si el trabajador ya tiene un reposo vigente
    if (!$hay_error) {
        $fecha_actual = date('Y-m-d');
        $verificar_reposo = $enlace->prepare("
            SELECT id FROM medical_rest 
            WHERE id_trabajador = ? AND vence >= ?
            LIMIT 1
        ");
        $verificar_reposo->bind_param("is", $id_trabajador, $fecha_actual);
        $verificar_reposo->execute();
        $verificar_reposo->store_result();

        if ($verificar_reposo->num_rows > 0) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    agregarToast({
                        tipo: 'error',
                        titulo: 'Error',
                        descripcion: 'Este trabajador ya tiene un reposo vigente.',
                        autoCierre: true
                    });
                });
            </script>";
            $hay_error = true;
        }
        $verificar_reposo->close();
    }

    // Solo registrar si NO hay errores
    if (!$hay_error) {
        $insert_query = "
            INSERT INTO medical_rest (id_trabajador, expedicion, vence)
            VALUES (?, ?, ?)
        ";

        $stmt = $enlace->prepare($insert_query);
        $stmt->bind_param("iss", $id_trabajador, $expedicion, $vence);

        if ($stmt->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    agregarToast({
                        tipo: 'exito',
                        titulo: 'Éxito',
                        descripcion: 'Reposo registrado correctamente.',
                        autoCierre: true
                    });
                });
            </script>";
            // Opcional: puedes recargar la página después de un tiempo si quieres
            // echo '<script>setTimeout(() => { window.location.href = "reposo_medico.php"; }, 1500);</script>';
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    agregarToast({
                        tipo: 'error',
                        titulo: 'Error',
                        descripcion: 'Error al registrar el reposo.',
                        autoCierre: true
                    });
                });
            </script>";
        }
        $stmt->close();
    }
}

// Obtener los filtros seleccionados
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';
$filtro_fecha = isset($_GET['filtro_fecha']) ? $_GET['filtro_fecha'] : 'todos';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Construir la consulta SQL según los filtros
$consulta = "
    SELECT 
        m.id, 
        t.nombre, 
        t.apellido, 
        t.cedula, 
        c.cargo AS tipo_trabajador, 
        m.expedicion AS fecha_expedicion, 
        m.vence AS fecha_vencimiento
    FROM medical_rest m
    INNER JOIN trabajadores t ON m.id_trabajador = t.id_trabajador
    INNER JOIN cargos c ON t.cargos = c.id_cargo
    WHERE 1 = 1
";

// Filtro por tipo de trabajador
if ($filtro !== 'todos') {
    $consulta .= " AND c.cargo = '$filtro'";
}

// Filtro por fecha
if ($filtro_fecha === 'hoy') {
    $consulta .= " AND DATE(m.vence) = CURDATE()";
} elseif ($filtro_fecha === 'ayer') {
    $consulta .= " AND DATE(m.vence) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
} elseif ($filtro_fecha === 'ultimos_7_dias') {
    $consulta .= " AND DATE(m.vence) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filtro_fecha === 'personalizado' && $fecha_inicio && $fecha_fin) {
    $consulta .= " AND DATE(m.vence) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

// Ordenar por fecha de vencimiento
$consulta .= " ORDER BY m.vence DESC";

// Ejecutar la consulta
$resultado = $enlace->query($consulta);

// Verificar si la consulta fue exitosa
if (!$resultado) {
    die("Error en la consulta: " . $enlace->error);
}

include 'vista/notificaciones.php'; // Incluir el archivo de notificaciones

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reposos</title>
    <link rel="stylesheet" href="Stilos/reposo.css">
    <link rel="stylesheet" href="Stilos/styles_tablas.css">
    <link rel="stylesheet" href="Stilos/css/bootstrap.min.css">
    <link rel="stylesheet" href="Stilos/jquery.dataTables.min.css">
    <script src="Java/jquery.min.js"></script>
    <script src="Java/jquery.dataTables.min.js"></script>
    <script src="Java/main.js"></script>
    <script src="Java/notificaciones.js" defer></script>
</head>
<body>

    <?php include 'vista/top-bar.php'; ?>
    <div class="container">
        <div class="contenedor2">
            <h1>Gestión de Reposos</h1>
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
                    <option value="todos" <?php echo $filtro_fecha === 'todos' ? 'selected' : ''; ?>>Todos</option>
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

                <?php if (!$solo_visualizar): // Mostrar solo si no es Usuario ?>
                    <button type="button" class="btn btn-primary btn-pad" data-bs-toggle="modal" data-bs-target="#addWorkerModal">
                        <i class="fa-solid fa-plus"></i> Agregar Reposo
                    </button>
                <?php endif; ?>
                <?php if (!$solo_visualizar): // Mostrar solo si no es Usuario ?>
                    <a href="descargar_reposos.php?filtro=<?php echo $filtro; ?>&filtro_fecha=<?php echo $filtro_fecha; ?>&fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>" class="btn btn-primary btn-pad">
                        <i class="fa-solid fa-download"></i> Descargar PDF
                    </a>
                <?php endif; ?>
            </form>
            </div>
    

        <!-- Modal para agregar Nuevo Reposo -->
        <div class="modal fade" id="addWorkerModal" tabindex="-1" aria-labelledby="addWorkerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addWorkerModalLabel">Agregar Reposo</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="reposo_medico.php">
                            <div class="mb-3">
                                <label for="trabajadores" class="form-label">Seleccione un trabajador</label>
                                <select name="trabajadores" id="trabajadores" class="form-control" <?php echo $solo_visualizar ? 'disabled' : ''; ?> >
                                    <option value="" disabled selected>Seleccione un trabajador</option>
                                    <?php
                                    // Consulta para obtener los trabajadores
                                    $trabajadores_query = "SELECT id_trabajador, nombre, apellido FROM trabajadores";
                                    $trabajadores_resultado = $enlace->query($trabajadores_query);

                                    // Verificar si la consulta fue exitosa
                                    if (!$trabajadores_resultado) {
                                        die("Error en la consulta: " . $enlace->error);
                                    }

                                    // Mostrar los trabajadores en el select
                                    while ($trabajador = $trabajadores_resultado->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $trabajador['id_trabajador']; ?>">
                                            <?php echo $trabajador['nombre'] . ' ' . $trabajador['apellido']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <span class="error"></span>
                            </div>
                            <div class="mb-3">
                                <label for="expedicion" class="form-label">Fecha de Expedición</label>
                                <input type="date" class="form-control" name="expedicion" <?php echo $solo_visualizar ? 'disabled' : ''; ?> >
                                <span class="error"></span>
                            </div>
                            <div class="mb-3">
                                <label for="vence" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" name="vence" <?php echo $solo_visualizar ? 'disabled' : ''; ?> >
                                <span class="error"></span>
                            </div>
                            <?php if (!$solo_visualizar): // Mostrar botones solo si no es Usuario ?>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" name="agregar" class="btn btn-primary">Registrar</button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="tabla">
         <div class="tabla-contenedor">
                <h1>Lista de Reposos</h1>
            <table class="table table-striped table-hover" id="data-tables">
                <thead>
                  <tr>
                    <th scope="col">N°</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Cédula</th>
                    <th scope="col">Tipo de Trabajador</th>
                    <th scope="col">Fecha de Expedición</th>
                    <th scope="col">Fecha de Vencimiento</th>
                    <th scope="col">Estado</th>
                  </tr>
                </thead>
                <tbody>
                    <?php 
                    $numero_fila = 1; // Inicializa el contador de filas
                    $fecha_actual = date('Y-m-d'); // Obtiene la fecha actual del sistema
                    while ($fila = $resultado->fetch_assoc()): 
                    ?>
                        <tr>
                            <td scope="row"><?php echo $numero_fila++; ?></td>
                            <td><?php echo $fila['nombre']; ?></td>
                            <td><?php echo $fila['apellido']; ?></td>
                            <td><?php echo $fila['cedula']; ?></td>
                            <td><?php echo $fila['tipo_trabajador']; ?></td> <!-- Tipo de trabajador -->
                            <td><?php echo date('d/m/Y', strtotime($fila['fecha_expedicion'])); ?></td> <!-- Fecha de expedición -->
                            <td><?php echo date('d/m/Y', strtotime($fila['fecha_vencimiento'])); ?></td> <!-- Fecha de vencimiento -->
                            <td>
                                <?php if ($fila['fecha_vencimiento'] < $fecha_actual): ?>
                                    <span class="badge bg-danger">Vencido</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Vigente</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
          </div>
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

        // Validación para el formulario de agregar reposo médico
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#addWorkerModal form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let valido = true;
        limpiarErroresModal(form);

        // Trabajador
        const trabajador = form.querySelector('[name="trabajadores"]');
        if (!trabajador.value) {
            mostrarErrorModal(trabajador, 'Debe seleccionar un trabajador.');
            valido = false;
        }

        // Fecha de expedición
        const expedicion = form.querySelector('[name="expedicion"]');
        if (!expedicion.value) {
            mostrarErrorModal(expedicion, 'La fecha de expedición es requerida.');
            valido = false;
        }

        // Fecha de vencimiento
        const vence = form.querySelector('[name="vence"]');
        if (!vence.value) {
            mostrarErrorModal(vence, 'La fecha de vencimiento es requerida.');
            valido = false;
        }

        // Validar que expedición no sea mayor que vencimiento
        if (expedicion.value && vence.value && expedicion.value > vence.value) {
            mostrarErrorModal(vence, 'La fecha de expedición no puede ser mayor que la de vencimiento.');
            valido = false;
        }

        if (!valido) e.preventDefault();
    });

    // Validación al salir de cada input
    [
        {campo: 'trabajadores', mensaje: 'Debe seleccionar un trabajador.'},
        {campo: 'expedicion', mensaje: 'La fecha de expedición es requerida.'},
        {campo: 'vence', mensaje: 'La fecha de vencimiento es requerida.'}
    ].forEach(function(obj) {
        const input = form.querySelector(`[name="${obj.campo}"]`);
        if (input) {
            input.addEventListener('blur', function() {
                limpiarErrorModal(input);
                if (!input.value) {
                    mostrarErrorModal(input, obj.mensaje);
                }
                // Validar fechas
                if (obj.campo === 'vence') {
                    const expedicion = form.querySelector('[name="expedicion"]');
                    if (expedicion.value && input.value && expedicion.value > input.value) {
                        mostrarErrorModal(input, 'La fecha de expedición no puede ser mayor que la de vencimiento.');
                    }
                }
            });
        }
    });

    // Funciones auxiliares
    function mostrarErrorModal(input, mensaje) {
        let error = input.parentElement.querySelector('.error');
        if (!error) {
            error = document.createElement('span');
            error.className = 'error';
            input.parentElement.appendChild(error);
        }
        error.textContent = mensaje;
        error.style.display = 'block';
        input.classList.add('error-input');
    }

    function limpiarErrorModal(input) {
        let error = input.parentElement.querySelector('.error');
        if (error) {
            error.textContent = '';
            error.style.display = 'none';
        }
        input.classList.remove('error-input');
    }

    function limpiarErroresModal(form) {
        form.querySelectorAll('.error').forEach(function(e) {
            e.textContent = '';
            e.style.display = 'none';
        });
        form.querySelectorAll('.error-input').forEach(function(e) {
            e.classList.remove('error-input');
        });
    }
});
    </script>

    <script src="Java/js/bootstrap.bundle.min.js"></script>
    <script src="Java/js.js"></script>
    <script>
        $(document).ready(function() {
            $('#data-tables').DataTable();
        });
    </script>
</body>
</html>