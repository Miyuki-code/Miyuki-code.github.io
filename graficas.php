<?php
include 'conexion_be.php'; // Conexión a la base de datos

// Obtener la cédula del trabajador desde la URL
$cedula = isset($_GET['cedula']) ? $_GET['cedula'] : null;

if (!$cedula) {
    echo '<script>
        alert("No se proporcionó una cédula válida.");
        window.location.href = "trabajadores.php";
    </script>';
    exit();
}

// Consultar las asistencias del trabajador
$query_mensual = "
    SELECT MONTH(hora) AS mes, COUNT(*) AS total
    FROM asistencias
    WHERE cedula = ?
    GROUP BY MONTH(hora)
    ORDER BY MONTH(hora)
";

$query_anual = "
    SELECT YEAR(hora) AS anio, COUNT(*) AS total
    FROM asistencias
    WHERE cedula = ?
    GROUP BY YEAR(hora)
    ORDER BY YEAR(hora)
";

$stmt_mensual = $enlace->prepare($query_mensual);
$stmt_mensual->bind_param("s", $cedula);
$stmt_mensual->execute();
$resultado_mensual = $stmt_mensual->get_result();

$stmt_anual = $enlace->prepare($query_anual);
$stmt_anual->bind_param("s", $cedula);
$stmt_anual->execute();
$resultado_anual = $stmt_anual->get_result();

// Convertir los resultados en datos para las gráficas
$datos_mensuales = [];
while ($fila = $resultado_mensual->fetch_assoc()) {
    $datos_mensuales[] = $fila;
}

$datos_anuales = [];
while ($fila = $resultado_anual->fetch_assoc()) {
    $datos_anuales[] = $fila;
}

$stmt_mensual->close();
$stmt_anual->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficas de Asistencias</title>
    <link rel="stylesheet" href="Stilos/styles_graficas.css">
    <script src="Java/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Gráficas de Asistencias</h1>
        <h2>Cédula: <?php echo htmlspecialchars($cedula); ?></h2>

        <div class="grafica">
            <h3>Asistencias Mensuales</h3>
            <canvas id="graficaMensual"></canvas>
        </div>

        <div class="grafica">
            <h3>Asistencias Anuales</h3>
            <canvas id="graficaAnual"></canvas>
        </div>

        <button onclick="window.location.href='trabajadores.php'" class="btn btn-primary">Volver</button>
    </div>

    <script>
        // Datos para la gráfica mensual
        const datosMensuales = <?php echo json_encode($datos_mensuales); ?>;
        const etiquetasMensuales = datosMensuales.map(d => `Mes ${d.mes}`);
        const valoresMensuales = datosMensuales.map(d => d.total);

        // Configuración de la gráfica mensual
        const ctxMensual = document.getElementById('graficaMensual').getContext('2d');
        new Chart(ctxMensual, {
            type: 'bar',
            data: {
                labels: etiquetasMensuales,
                datasets: [{
                    label: 'Asistencias Mensuales',
                    data: valoresMensuales,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Datos para la gráfica anual
        const datosAnuales = <?php echo json_encode($datos_anuales); ?>;
        const etiquetasAnuales = datosAnuales.map(d => `Año ${d.anio}`);
        const valoresAnuales = datosAnuales.map(d => d.total);

        // Configuración de la gráfica anual
        const ctxAnual = document.getElementById('graficaAnual').getContext('2d');
        new Chart(ctxAnual, {
            type: 'line',
            data: {
                labels: etiquetasAnuales,
                datasets: [{
                    label: 'Asistencias Anuales',
                    data: valoresAnuales,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>