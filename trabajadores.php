<?php
include 'conexion_be.php'; // Asegúrate de que este archivo contiene la conexión a la base de datos
include 'validar_sesion.php';
include 'validar_level_user.php';

// Permitir acceso a todos los roles, pero restringir acciones para el rol Usuario (rol_id = 3)
$solo_visualizar = ($_SESSION['rol_id'] == 3); // Si es Usuario, solo puede visualizar


include 'validar_acceso.php';

// Solo Administradores (rol_id = 1) y Moderadores (rol_id = 2) pueden agregar/editar trabajadores
if ($_SESSION['rol'] == 3) { // Usuario
    echo '<script>
        alert("No tienes permiso para agregar o editar trabajadores.");
        window.location.href = "inicio.php";
    </script>';
    exit();
}

// Incluir la conexión a la base de datos



// Manejar la adición de un nuevo trabajador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $data_tipo = $_POST['data_tipo'];
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellido = htmlspecialchars(trim($_POST['apellido']));
    $cedula = htmlspecialchars(trim($_POST['cedula']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $cargos = intval($_POST['cargos']);

    $hay_error = false;

    // Validar campos vacíos
    if (empty($nombre) || empty($apellido) || empty($cedula) || empty($telefono) || empty($cargos)) {
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

    // Validar formato de cédula
    if (!preg_match('/^\d{7,8}$/', $cedula)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'La cédula debe contener entre 7 y 8 dígitos.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }

    // Validar formato de teléfono
    if (!preg_match('/^\d{11}$/', $telefono)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'El teléfono debe contener exactamente 11 dígitos.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }

    // Validar longitud de nombre y apellido
    if (strlen($nombre) > 50 || strlen($apellido) > 50) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'El nombre y el apellido no deben exceder los 50 caracteres.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }

    // Verificar si la cédula ya existe en cualquier cargo
    $verificar_cedula = $enlace->prepare("SELECT id_trabajador FROM trabajadores WHERE cedula = ?");
    $verificar_cedula->bind_param("s", $cedula);
    $verificar_cedula->execute();
    $verificar_cedula->store_result();

    if ($verificar_cedula->num_rows > 0) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'La cédula ya está registrada.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }
    $verificar_cedula->close();

    // Solo registrar si NO hay errores
    if (!$hay_error) {
        $stmt = $enlace->prepare("INSERT INTO trabajadores (nombre, apellido, cedula, telefono, cargos) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nombre, $apellido, $cedula, $telefono, $cargos);
        if ($stmt->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    agregarToast({
                        tipo: 'exito',
                        titulo: 'Éxito',
                        descripcion: 'Trabajador registrado correctamente',
                        autoCierre: true
                    });
                });
            </script>";
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    agregarToast({
                        tipo: 'error',
                        titulo: 'Error',
                        descripcion: 'Ocurrió un error al registrar el trabajador.',
                        autoCierre: true
                    });
                });
            </script>";
        }
        $stmt->close();
    }
}

// Manejar la eliminación de un trabajador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id_trabajador = intval($_POST['id']);
    $stmt = $enlace->prepare("DELETE FROM trabajadores WHERE id_trabajador = ?");
    $stmt->bind_param("i", $id_trabajador);
    $stmt->execute();
    $stmt->close();

    // Redirigir para evitar el reenvío de datos
    header('Location: trabajadores.php');
    exit();
}

// Manejar la actualización de un trabajador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $id_trabajador = intval($_POST['id']);
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellido = htmlspecialchars(trim($_POST['apellido']));
    $cedula = htmlspecialchars(trim($_POST['cedula']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $cargos = intval($_POST['cargos']);

    $hay_error = false;

    // Validar campos vacíos
    if (empty($nombre) || empty($apellido) || empty($cedula) || empty($telefono) || empty($cargos)) {
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

    // Validar formato de cédula y teléfono
    if (!preg_match('/^\d{7,8}$/', $cedula)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'La cédula debe contener entre 7 y 8 dígitos.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }
    if (!preg_match('/^\d{11}$/', $telefono)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'El teléfono debe contener exactamente 11 dígitos.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }

    // Validar longitud de nombre y apellido
    if (strlen($nombre) > 50 || strlen($apellido) > 50) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                agregarToast({
                    tipo: 'error',
                    titulo: 'Error',
                    descripcion: 'El nombre y el apellido no deben exceder los 50 caracteres.',
                    autoCierre: true
                });
            });
        </script>";
        $hay_error = true;
    }

    // Actualizar el trabajador en la base de datos
    if (!$hay_error) {
        $stmt = $enlace->prepare("UPDATE trabajadores SET nombre = ?, apellido = ?, cedula = ?, telefono = ?, cargos = ? WHERE id_trabajador = ?");
        $stmt->bind_param("ssssii", $nombre, $apellido, $cedula, $telefono, $cargos, $id_trabajador);

        if ($stmt->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    agregarToast({
                        tipo: 'exito',
                        titulo: 'Éxito',
                        descripcion: 'Trabajador actualizado correctamente',
                        autoCierre: true
                    });
                });
            </script>";
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    agregarToast({
                        tipo: 'error',
                        titulo: 'Error',
                        descripcion: 'Ocurrió un error al actualizar el trabajador.',
                        autoCierre: true
                    });
                });
            </script>";
        }
        $stmt->close();
    }
}

// Obtener todos los registros de la tabla trabajadores with el nombre del cargo
$resultado = $enlace->query("
    SELECT trabajadores.id_trabajador, trabajadores.nombre, trabajadores.apellido, trabajadores.cedula, trabajadores.telefono, cargos.cargo, trabajadores.cargos 
    FROM trabajadores
    INNER JOIN cargos ON trabajadores.cargos = cargos.id_cargo
");

// Obtener todos los registros de la tabla cargos
$cargos_resultado = $enlace->query("SELECT id_cargo, cargo FROM cargos");

// Obtener todos los registros de la tabla cargos para el modal
$cargos_resultado_modal = $enlace->query("SELECT id_cargo, cargo FROM cargos");
$cargos_array = [];
while ($cargo = $cargos_resultado_modal->fetch_assoc()) {
    $cargos_array[] = $cargo;
}

include 'vista/notificaciones.php'; // Incluir el archivo de notificaciones

?>

<script>
    function advertencia() {
        var not = confirm("¿Está seguro de que desea eliminar este trabajador?");
        return not;
    }
</script>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Trabajadores</title>
    <link rel="stylesheet" href="Stilos/styles_trabajadores.css">
    <link rel="stylesheet" href="Stilos/css/bootstrap.min.css">  <!--Opcional: agrega estilos a las tablas -->
    <link rel="stylesheet" href="Stilos/styles_tablas.css">  <!--Opcional: agrega estilos a las tablas -->
    <link rel="stylesheet" href="fontawesome/fontawesome-free-6.7.2-web/css/all.css">
    <link rel="stylesheet" href="Stilos/jquery.dataTables.min.css">
    <script src="Java/jquery.min.js"></script>
    <script src="Java/jquery.dataTables.min.js"></script>
    <script src="Java/main.js"></script>
    <script src="Java/notificaciones.js" defer></script>

</head>
<body class="trab-body">
    <?php include 'vista/top-bar.php'; ?>

    <div class="containerh3">
        <h1>Gestión de Trabajadores</h1>
        </div>

        <div class="tabla-termination">
        <!-- Formulario para filtrar por cargo -->
        <form method="GET" action="trabajadores.php" class="mb-3 d-flex align-items-center from-rigth">
            <label for="filtro-cargo" class="form-label me-2">Filtrar Cargo:</label>
            <select name="cargo" id="filtro-cargo" class="form-select me-3 sel2" onchange="this.form.submit()">
                <option value="todos" <?php echo (!isset($_GET['cargo']) || $_GET['cargo'] === 'todos') ? 'selected' : ''; ?>>Todos</option>
                <?php
                // Obtener los cargos de la base de datos
                $cargos_resultado_filtro = $enlace->query("SELECT id_cargo, cargo FROM cargos");
                while ($cargo = $cargos_resultado_filtro->fetch_assoc()):
                ?>
                    <option value="<?php echo $cargo['id_cargo']; ?>" <?php echo (isset($_GET['cargo']) && $_GET['cargo'] == $cargo['id_cargo']) ? 'selected' : ''; ?>>
                        <?php echo $cargo['cargo']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <?php if (!$solo_visualizar): // Mostrar solo si no es Usuario ?>
                <button type="button" class="btn btn-primary btn-pad" data-bs-toggle="modal" data-bs-target="#addWorkerModal">
                    <i class="fa-solid fa-plus"></i> Agregar Trabajador
                </button>
            <?php endif; ?>
            <a href="descargar.php?cargo=<?php echo isset($_GET['cargo']) ? $_GET['cargo'] : 'todos'; ?>" class="btn btn-primary btn-pad">
                <i class="fa-solid fa-download"></i> Descargar
            </a>
        </form>

        <!-- Modal para agregar un nuevo trabajador -->
        <div class="modal fade" id="addWorkerModal" tabindex="-1" aria-labelledby="addWorkerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addWorkerModalLabel">Agregar Trabajador</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <input type="hidden" name="data_tipo" value="agregar" />
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" placeholder="Agregue el Nombre" >
                                <span class="error"></span>
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" name="apellido" placeholder="Agregue el Apellido" >
                                <span class="error"></span>
                            </div>
                            <div class="mb-3">
                                <label for="cedula" class="form-label">Cédula</label>
                                <input type="text" class="form-control" name="cedula" placeholder="Agregue la Cédula" >
                                <span class="error"></span>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" placeholder="Agregue el numero de Teléfono" >
                                <span class="error"></span>
                            </div>
                            <div class="mb-3">
                                <label for="cargos" class="form-label">Cargo</label>
                                <select class="form-select" name="cargos" >
                                    <option value="" disabled selected>Seleccione un cargo</option>
                                    <?php while ($cargo = $cargos_resultado->fetch_assoc()): ?>
                                        <option value="<?php echo $cargo['id_cargo']; ?>"><?php echo $cargo['cargo']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <span class="error"></span>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" name="agregar" class="btn btn-primary">Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h2>Lista de Trabajadores</h2>
        <table class="table table-striped table-hover" id="data-tables">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Cargo</th>
                    <?php if (!$solo_visualizar): // Solo si NO es usuario nivel 3 ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody id="trabajadores-list">
                <?php 
                // Inicializar el contador de filas
                $numero_fila = 1;

                // Obtener el filtro de cargo
                $cargo_filtro = isset($_GET['cargo']) ? $_GET['cargo'] : 'todos';

                // Modificar la consulta según el filtro
                if ($cargo_filtro === 'todos') {
                    $resultado = $enlace->query("
                        SELECT trabajadores.id_trabajador, trabajadores.nombre, trabajadores.apellido, trabajadores.cedula, trabajadores.telefono, cargos.cargo 
                        FROM trabajadores
                        INNER JOIN cargos ON trabajadores.cargos = cargos.id_cargo
                    ");
                } else {
                    $stmt = $enlace->prepare("
                        SELECT trabajadores.id_trabajador, trabajadores.nombre, trabajadores.apellido, trabajadores.cedula, trabajadores.telefono, cargos.cargo 
                        FROM trabajadores
                        INNER JOIN cargos ON trabajadores.cargos = cargos.id_cargo
                        WHERE trabajadores.cargos = ?
                    ");
                    $stmt->bind_param("i", $cargo_filtro);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                }
                
                // Mostrar los resultados en la tabla
                while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $numero_fila++; ?></td> <!-- Mostrar el número de fila -->
                    <td><?php echo $fila['nombre']; ?></td>
                    <td><?php echo $fila['apellido']; ?></td>
                    <td><?php echo $fila['cedula']; ?></td>
                    <td><?php echo $fila['telefono']; ?></td>
                    <td><?php echo $fila['cargo']; ?></td> <!-- Mostrar el nombre del cargo -->
                    <?php if (!$solo_visualizar): // Solo si NO es usuario nivel 3 ?>
                    <td>
                        <!-- Botón para eliminar, editar y abrir estadísticas -->
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $fila['id_trabajador']; ?>">
                            <!--<button type="button" class="btn btn-success" onclick="window.location.href='graficas.php?cedula=<?php echo $fila['cedula']; ?>'">
                                <i class="fa-solid fa-square-poll-vertical"></i>
                            </button>-->  
                            <button type="button" class="btn btn-warning " data-bs-toggle="modal" data-bs-target="#exampleModal<?php echo $fila['id_trabajador']; ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>     
                            <button type="submit" name="eliminar" class="btn btn-danger " onclick="return advertencia()"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>

                <!-- Modal para actualizar un trabajador -->
                <div class="modal fade" id="exampleModal<?php echo $fila['id_trabajador']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?php echo $fila['id_trabajador']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel<?php echo $fila['id_trabajador']; ?>">Editar Trabajador</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo $fila['id_trabajador']; ?>">

                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" name="nombre" value="<?php echo $fila['nombre']; ?>" <?php echo $solo_visualizar ? 'readonly' : ''; ?>>
                                        <span class="error"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">Apellido</label>
                                        <input type="text" class="form-control" name="apellido" value="<?php echo $fila['apellido']; ?>" <?php echo $solo_visualizar ? 'readonly' : ''; ?>>
                                        <span class="error"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cedula" class="form-label">Cédula</label>
                                        <input type="text" class="form-control" name="cedula" value="<?php echo $fila['cedula']; ?>" <?php echo $solo_visualizar ? 'readonly' : ''; ?>>
                                        <span class="error"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" name="telefono" value="<?php echo $fila['telefono']; ?>" <?php echo $solo_visualizar ? 'readonly' : ''; ?>>
                                        <span class="error"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cargos" class="form-label">Cargo</label>
                                        <select class="form-select" name="cargos" <?php echo $solo_visualizar ? 'disabled' : ''; ?>>
                                            <option value="" disabled>Seleccione un cargo</option>
                                            <?php foreach ($cargos_array as $cargo): ?>
                                                <option value="<?php echo $cargo['id_cargo']; ?>" 
                                                    <?php echo (isset($fila['cargos']) && $cargo['id_cargo'] == $fila['cargos']) ? 'selected' : ''; ?>>
                                                    <?php echo $cargo['cargo']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="error"></span>
                                    </div>

                                    <?php if (!$solo_visualizar): // Mostrar botones solo si no es Usuario ?>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
// Validación para el formulario de agregar trabajador
document.addEventListener('DOMContentLoaded', function() {
    // Agregar Trabajador
    const formAdd = document.querySelector('#addWorkerModal form');
    if (formAdd) {
        formAdd.addEventListener('submit', function(e) {
            let valido = true;

            limpiarErroresModal(formAdd);

            // Nombre
            const nombre = formAdd.querySelector('[name="nombre"]');
            if (!nombre.value.trim()) {
                mostrarErrorModal(nombre, 'El nombre es requerido.');
                valido = false;
            } else if (nombre.value.length > 50) {
                mostrarErrorModal(nombre, 'El nombre no debe exceder 50 caracteres.');
                valido = false;
            }

            // Apellido
            const apellido = formAdd.querySelector('[name="apellido"]');
            if (!apellido.value.trim()) {
                mostrarErrorModal(apellido, 'El apellido es requerido.');
                valido = false;
            } else if (apellido.value.length > 50) {
                mostrarErrorModal(apellido, 'El apellido no debe exceder 50 caracteres.');
                valido = false;
            }

            // Cédula
            const cedula = formAdd.querySelector('[name="cedula"]');
            if (!cedula.value.trim()) {
                mostrarErrorModal(cedula, 'La cédula es requerida.');
                valido = false;
            } else if (!/^\d{7,8}$/.test(cedula.value)) {
                mostrarErrorModal(cedula, 'La cédula debe contener entre 7 y 8 dígitos.');
                valido = false;
            }

            // Teléfono
            const telefono = formAdd.querySelector('[name="telefono"]');
            if (!telefono.value.trim()) {
                mostrarErrorModal(telefono, 'El teléfono es requerido.');
                valido = false;
            } else if (!/^\d{11}$/.test(telefono.value)) {
                mostrarErrorModal(telefono, 'El teléfono debe contener exactamente 11 dígitos.');
                valido = false;
            }

            // Cargo
            const cargos = formAdd.querySelector('[name="cargos"]');
            if (!cargos.value) {
                mostrarErrorModal(cargos, 'Debe seleccionar un cargo.');
                valido = false;
            }

            if (!valido) e.preventDefault();
        });

        // Validación al salir de cada input
        ['nombre', 'apellido', 'cedula', 'telefono', 'cargos'].forEach(function(campo) {
            const input = formAdd.querySelector(`[name="${campo}"]`);
            if (input) {
                input.addEventListener('blur', function() {
                    limpiarErrorModal(input);
                    if (!input.value.trim()) {
                        mostrarErrorModal(input, (campo.charAt(0).toUpperCase() + campo.slice(1)) + ' es requerido.');
                    } else if (campo === 'cedula' && !/^\d{7,8}$/.test(input.value)) {
                        mostrarErrorModal(input, 'La cédula debe contener entre 7 y 8 dígitos.');
                    } else if (campo === 'telefono' && !/^\d{11}$/.test(input.value)) {
                        mostrarErrorModal(input, 'El teléfono debe contener exactamente 11 dígitos.');
                    } else if ((campo === 'nombre' || campo === 'apellido') && input.value.length > 50) {
                        mostrarErrorModal(input, 'No debe exceder 50 caracteres.');
                    }
                });
            }
        });
    }

    // Editar Trabajador (para todos los modales de edición)
    document.querySelectorAll('form[action=""]').forEach(function(formEdit) {
        if (formEdit.closest('.modal')) {
            formEdit.addEventListener('submit', function(e) {
                let valido = true;
                limpiarErroresModal(formEdit);

                // Nombre
                const nombre = formEdit.querySelector('[name="nombre"]');
                if (!nombre.value.trim()) {
                    mostrarErrorModal(nombre, 'El nombre es requerido.');
                    valido = false;
                } else if (nombre.value.length > 50) {
                    mostrarErrorModal(nombre, 'El nombre no debe exceder 50 caracteres.');
                    valido = false;
                }

                // Apellido
                const apellido = formEdit.querySelector('[name="apellido"]');
                if (!apellido.value.trim()) {
                    mostrarErrorModal(apellido, 'El apellido es requerido.');
                    valido = false;
                } else if (apellido.value.length > 50) {
                    mostrarErrorModal(apellido, 'El apellido no debe exceder 50 caracteres.');
                    valido = false;
                }

                // Cédula
                const cedula = formEdit.querySelector('[name="cedula"]');
                if (!cedula.value.trim()) {
                    mostrarErrorModal(cedula, 'La cédula es requerida.');
                    valido = false;
                } else if (!/^\d{7,8}$/.test(cedula.value)) {
                    mostrarErrorModal(cedula, 'La cédula debe contener entre 7 y 8 dígitos.');
                    valido = false;
                }

                // Teléfono
                const telefono = formEdit.querySelector('[name="telefono"]');
                if (!telefono.value.trim()) {
                    mostrarErrorModal(telefono, 'El teléfono es requerido.');
                    valido = false;
                } else if (!/^\d{11}$/.test(telefono.value)) {
                    mostrarErrorModal(telefono, 'El teléfono debe contener exactamente 11 dígitos.');
                    valido = false;
                }

                // Cargo
                const cargos = formEdit.querySelector('[name="cargos"]');
                if (!cargos.value) {
                    mostrarErrorModal(cargos, 'Debe seleccionar un cargo.');
                    valido = false;
                }

                if (!valido) e.preventDefault();
            });

            // Validación al salir de cada input
            ['nombre', 'apellido', 'cedula', 'telefono', 'cargos'].forEach(function(campo) {
                const input = formEdit.querySelector(`[name="${campo}"]`);
                if (input) {
                    input.addEventListener('blur', function() {
                        limpiarErrorModal(input);
                        if (!input.value.trim()) {
                            mostrarErrorModal(input, (campo.charAt(0).toUpperCase() + campo.slice(1)) + ' es requerido.');
                        } else if (campo === 'cedula' && !/^\d{7,8}$/.test(input.value)) {
                            mostrarErrorModal(input, 'La cédula debe contener entre 7 y 8 dígitos.');
                        } else if (campo === 'telefono' && !/^\d{11}$/.test(input.value)) {
                            mostrarErrorModal(input, 'El teléfono debe contener exactamente 11 dígitos.');
                        } else if ((campo === 'nombre' || campo === 'apellido') && input.value.length > 50) {
                            mostrarErrorModal(input, 'No debe exceder 50 caracteres.');
                        }
                    });
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
            $('#data-tables').DataTable()
        });
                
     </script>
    
</body>
</html>