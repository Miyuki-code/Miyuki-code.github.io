<?php
require('fpdf/fpdf.php');
include 'conexion_be.php'; // Conexión a la base de datos

// Obtener los filtros de la URL
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

// Construir la consulta SQL según el filtro de tipo de trabajador
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
    die("Error en la consulta: " . $enlace->error);
}

// Crear el PDF

// Clase PDF personalizada para encabezado repetido
class PDF extends FPDF
{
    function Header()
    {
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 16); // Fuente más grande para el título principal
        $this->Cell(0, 20, utf8_decode('C.E.I. Simoncito Guayana'), 0, 1, 'C');
        $this->Ln(5); // Espacio debajo del título principal

        // Imagen del cintillo (ajusta la ruta si es necesario)
        $this->Image('imagen/cintillo.jpg', 0, -3, 190, 27);

        // Imagen del logo (ajusta la ruta si es necesario)
        $this->Image('imagen/Picsart_25-03-31_14-46-19-016.png', 188, 2, 17);

        $this->SetFont('Arial', 'B', 14); // Fuente más pequeña para el subtítulo
        $this->Cell(0, 10, utf8_decode('Registro de asistencias'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, date('d/m/Y H:i'), 0, 1, 'C');
        $this->Ln(3);

        // Agregar encabezados de la tabla
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(10, 10, utf8_decode('N°'), 1, 0, 'C');
        $this->Cell(30, 10, utf8_decode('Nombre'), 1, 0, 'C');
        $this->Cell(30, 10, utf8_decode('Apellido'), 1, 0, 'C');
        $this->Cell(25, 10, utf8_decode('Cédula'), 1, 0, 'C');
        $this->Cell(40, 10, utf8_decode('Tipo Trabajador'), 1, 0, 'C');
        $this->Cell(25, 10, utf8_decode('Tipo'), 1, 0, 'C');
        $this->Cell(30, 10, utf8_decode('Hora'), 1, 0, 'C');
        $this->Ln();
    }

    function Footer()
    {
        // Posición a 1.5 cm del final de la página
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        // Número de página centrado
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();

    

// Agregar los datos al PDF
$pdf->SetFont('Arial', '', 10); // Sin negrita
$gris1 = [230, 230, 230]; // Gris claro
$gris2 = [245, 245, 245]; // Gris aún más claro
$contador = 1;
while ($fila = $resultado->fetch_assoc()) {
    // Alternar color según si la fila es par o impar
    if ($contador % 2 == 0) {
        $pdf->SetFillColor($gris1[0], $gris1[1], $gris1[2]);
    } else {
        $pdf->SetFillColor($gris2[0], $gris2[1], $gris2[2]);
    }

    $pdf->Cell(10, 10, $contador, 1, 0, 'C', true);
    $pdf->Cell(30, 10, utf8_decode($fila['nombre']), 1, 0, 'C', true);
    $pdf->Cell(30, 10, utf8_decode($fila['apellido']), 1, 0, 'C', true);
    $pdf->Cell(25, 10, utf8_decode($fila['cedula']), 1, 0, 'C', true);
    $pdf->Cell(40, 10, utf8_decode($fila['tipo_trabajador']), 1, 0, 'C', true);
    $pdf->Cell(25, 10, utf8_decode($fila['tipo']), 1, 0, 'C', true);
    $fecha = strtotime($fila['hora']);
    $pdf->Cell(30, 10, date('d/m/Y, H:i', $fecha), 1, 1, 'C', true);

    $contador++;
}

// Salida del PDF
$pdf->Output('D', 'asistencias_filtradas.pdf'); // Descarga directa del archivo
exit();
?>