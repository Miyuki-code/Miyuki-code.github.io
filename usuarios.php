<?php
include 'conexion_be.php';
include 'validar_sesion.php';
include 'validar_level_user.php';
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) { // Solo Administradores (rol_id = 1)
    echo '<script>
        alert("No tienes permiso para acceder a esta página.");
        window.location.href = "inicio.php";
    </script>';
    exit();
}

include 'validar_acceso.php';

// Solo los administradores (rol_id = 1) pueden acceder
validar_acceso([1]);




// Manejar la eliminación de un usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id_usuario = intval($_POST['id']);
    $stmt = $enlace->prepare("DELETE FROM usuarios WHERE ID = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();

    // Redirigir para evitar el reenvío de datos
    header('Location: usuarios.php');
    exit();
}

// Validar campos vacíos al actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $id_usuario = intval($_POST['id']);
    $nombre_completo = htmlspecialchars(trim($_POST['nombre_completo']));
    $email = htmlspecialchars(trim($_POST['email']));
    $user = htmlspecialchars(trim($_POST['user']));
    $rol_id = intval($_POST['rol']);

    // Validar campos vacíos
    if (empty($nombre_completo) || empty($email) || empty($user) || empty($rol_id)) {
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
    } else {
        // Actualizar el usuario en la base de datos
        $stmt = $enlace->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, user = ?, rol_id = ? WHERE ID = ?");
        $stmt->bind_param("sssii", $nombre_completo, $email, $user, $rol_id, $id_usuario);

        if ($stmt->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    agregarToast({
                        tipo: 'exito',
                        titulo: 'Éxito',
                        descripcion: 'Usuario actualizado correctamente.',
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
                        descripcion: 'Ocurrió un error al actualizar el usuario.',
                        autoCierre: true
                    });
                });
            </script>";
        }
        $stmt->close();
    }
}

// Obtener todos los registros de la tabla usuarios con el nombre del rol
$rol_filtro = isset($_GET['rol']) ? $_GET['rol'] : 'todos';

if ($rol_filtro !== 'todos' && !is_numeric($rol_filtro)) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', () => {
            agregarToast({
                tipo: 'error',
                titulo: 'Error',
                descripcion: 'Filtro de rol no válido.',
                autoCierre: true
            });
        });
    </script>";
    $rol_filtro = 'todos';
}

if ($rol_filtro === 'todos') {
    $resultado = $enlace->query("
        SELECT usuarios.ID, usuarios.nombre_completo, usuarios.email, usuarios.user, level_user.roles AS rol 
        FROM usuarios
        INNER JOIN level_user ON usuarios.rol_id = level_user.id
    ");
} else {
    $stmt = $enlace->prepare("
        SELECT usuarios.ID, usuarios.nombre_completo, usuarios.email, usuarios.user, level_user.roles AS rol 
        FROM usuarios
        INNER JOIN level_user ON usuarios.rol_id = level_user.id
        WHERE usuarios.rol_id = ?
    ");
    $stmt->bind_param("i", $rol_filtro);
    $stmt->execute();
    $resultado = $stmt->get_result();
}

if (!$resultado) {
    die("Error en la consulta: " . $enlace->error);
}

// Obtener todos los registros de la tabla level_user
$rol_resultado = $enlace->query("SELECT id, roles FROM level_user");

// Obtener todos los registros de la tabla level_user para el modal
$rol_array = [];
while ($rol = $rol_resultado->fetch_assoc()) {
    $rol_array[] = $rol;
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
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="Stilos/usuarios.css">
    <link rel="stylesheet" href="Stilos/css/bootstrap.min.css">  <!--Opcional: agrega estilos a las tablas -->
    <link rel="stylesheet" href="Stilos/styles_tablas.css">  <!--Opcional: agrega estilos a las tablas -->
    <link rel="stylesheet" href="fontawesome/fontawesome-free-6.7.2-web/css/all.css">">
    <link rel="stylesheet" href="Stilos/jquery.dataTables.min.css">
    <script src="Java/jquery.min.js"></script>
    <script src="Java/jquery.dataTables.min.js"></script>    
    <script src="Java/main.js"></script>
    <script src="Java/notificaciones.js" defer></script>
</head>
<body class="trab-body">
    <?php include 'vista/top-bar.php'; ?>

    <div class="container3">
        <h1>Registro de usuarios</h1>
        </div>
    <div class="container4">
        <form method="GET" action="usuarios.php" class="mb-3 d-flex align-items-center from-rigth">
            <label for="filtro-rol" class="form-label me-2">Filtrar Rol:</label>
            <select name="rol" id="filtro-rol" class="form-select me-3 sel2" onchange="this.form.submit()">
                <option value="todos" <?php echo (!isset($_GET['rol']) || $_GET['rol'] === 'todos') ? 'selected' : ''; ?>>Todos</option>
                <?php
                $roles_resultado_filtro = $enlace->query("SELECT id, roles FROM level_user");
                while ($rol = $roles_resultado_filtro->fetch_assoc()):
                ?>
                    <option value="<?php echo $rol['id']; ?>" <?php echo (isset($_GET['rol']) && $_GET['rol'] == $rol['id']) ? 'selected' : ''; ?>>
                        <?php echo $rol['roles']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <h2>Lista de usuarios</h2>
        <table class="table table-striped table-hover" id="data-tables">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Nombre Completo</th>
                    <th>Correo Electrónico</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usuarios-list">
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php $contador = 1; // <-- Agrega esta línea ?>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $contador++; ?></td> <!-- Cambia esto para mostrar el número consecutivo -->
                        <td><?php echo $fila['nombre_completo']; ?></td>
                        <td><?php echo $fila['email']; ?></td>
                        <td><?php echo $fila['user']; ?></td>
                        <td><?php echo $fila['rol']; ?></td>
                        <td>
                            <?php if ($fila['ID'] != $_SESSION['id_usuario']): ?>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $fila['ID']; ?>">
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal<?php echo $fila['ID']; ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="submit" name="eliminar" class="btn btn-danger" onclick="return advertencia()">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Activo</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Modal para actualizar un trabajador -->
                    <div class="modal fade" id="exampleModal<?php echo $fila['ID']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?php echo $fila['ID']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel<?php echo $fila['ID']; ?>">Editar Trabajador</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <!-- Campo oculto para enviar el ID del trabajador -->
                                        <input type="hidden" name="id" value="<?php echo $fila['ID']; ?>">

                                        <!-- Campos para editar los datos del trabajador -->
                                        <div class="mb-3">
                                            <label for="nombre_completo" class="form-label">Nombre Completo</label>
                                            <input type="text" class="form-control" name="nombre_completo" value="<?php echo $fila['nombre_completo']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Correo Electrónico</label>
                                            <input type="text" class="form-control" name="email" value="<?php echo $fila['email']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user" class="form-label">Usuario</label>
                                            <input type="text" class="form-control" name="user" value="<?php echo $fila['user']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rol" class="form-label">Rol</label>
                                            <select class="form-select" name="rol" required>
                                                <option value="" disabled>Seleccione un Rol</option>
                                                <?php foreach ($rol_array as $rol): ?>
                                                    <option value="<?php echo $rol['id']; ?>" 
                                                        <?php echo ($rol['id'] == $fila['rol']) ? 'selected' : ''; ?>>
                                                        <?php echo $rol['roles']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Botones del modal -->
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron registros.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="Java/js/bootstrap.bundle.min.js"></script>
    <script src="Java/js.js"></script>
     <script>
        $(document).ready(function() {
            $('#data-tables').DataTable()
        });
                
     </script>
    
</body>
</html>