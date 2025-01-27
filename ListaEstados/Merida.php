<?php
// Activar el reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Datos de conexión a la base de datos
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "asambleasvenezuela";

// Crear conexión
$conexion = new mysqli($host, $usuario, $password, $base_datos);

//comando para permitir los protocolos de acentos
$conexion->set_charset("utf8");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener la ciudad seleccionada (si existe)
$ciudad = isset($_GET['ciudad']) ? $_GET['ciudad'] : '';

// Obtener el ID de la asamblea seleccionada (si existe)
$id_asamblea = isset($_GET['id']) ? intval($_GET['id']) : null;

// Consulta para la tabla principal
$query = "SELECT ID, Nombre, Estado, Ciudad FROM asambleas_de_venezuela WHERE Estado = 'Mérida'";
if (!empty($ciudad)) {     //----------revisar estado----------//
    $query .= " AND Ciudad = '" . $conexion->real_escape_string($ciudad) . "'";
}
$resultado = $conexion->query($query);

// Consulta para obtener detalles de una Asamblea específica
$detalle_asamblea = null;
if ($id_asamblea) {
    $detalle_query = "SELECT Nombre, Numero, Estado, Ciudad, Direccion, Domingo, Lunes, Martes, Miercoles,
     Jueves, Viernes, Sabado, Obras, GoogleMaps 
    FROM asambleas_de_venezuela 
    WHERE ID = " . $conexion->real_escape_string($id_asamblea);
    $detalle_resultado = $conexion->query($detalle_query);
    if ($detalle_resultado && $detalle_resultado->num_rows > 0) {
        $detalle_asamblea = $detalle_resultado->fetch_assoc();
    }
    
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mérida</title><!--//----------revisar estado----------//-->
    <link rel="stylesheet" href="../CSS/estados.css">
    <style>
        .map-section { position: relative; text-align: center; }
        .map-section img { max-width: 100%; height: auto; }
        .location-button { position: absolute; background-color: #007BFF; color: white; border: none; border-radius: 50%; padding: 10px; cursor: pointer; font-size: 12px; transform: translate(-50%, -50%); }
        .location-button:hover { background-color: #0056b3; }
        #puerto_ayacucho { top: 50%; left: 60%; }
        #san_fernando { top: 70%; left: 40%; }
    </style>
</head>
<body>
    <header>
        <a href="Mapa.html" class="menu-button">&#8592; Mapa</a>
        <h1>Directorio de Locales - Mérida</h1><!--//----------revisar estado----------//-->
    </header>

    <main class="content">
        <section class="map-section">
            <h2>Estado Amazonas</h2>   <!--//----------revisar estado----------//-->
            <img src="../assets/Merida.png" alt="Mapa del estado Mérida"> 
            <button class="location-button" id="puerto_ayacucho" onclick="filterByCity('Puerto Ayacucho')">Puerto Ayacucho</button>
            <button class="location-button" id="san_fernando" onclick="filterByCity('San Fernando')">San Fernando</button>
            <div class="city-buttons">
                <form method="GET" action="">
                    <button type="submit" name="ciudad" value="">Mostrar todas</button>
                </form>
            </div>
        </section>

        <section class="table-section">
            <h2>Localidades</h2>
            <table>
                <thead>
                    <tr>
                        <th>Asamblea</th>
                        <th>Estado</th>
                        <th>Ciudad</th>
                        <th>Consultar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado->num_rows > 0) {
                        while ($fila = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($fila['Nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['Estado']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['Ciudad']) . "</td>";
                            echo "<td>";
                            echo "<form method='GET' action=''>";
                            echo "<button type='submit' name='id' value='" . $fila['ID'] . "' class='detalles-btn'>Ver detalles</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No se encontraron resultados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <hr class="divider">

        <section id="detalle" class="grid-container"> 
            <h2>Detalles de la Asamblea</h2>
            <div id="detalle-content">
                <?php if ($detalle_asamblea): ?>
                    <h3>Asamblea</h3>
                    <p><strong>Nombre:</strong> <?= htmlspecialchars($detalle_asamblea['Nombre'] ?? 'No disponible') ?></p>
                    <p><strong>Numero:</strong> <?= htmlspecialchars($detalle_asamblea['Numero'] ?? 'No disponible') ?></p>
                    <p><strong>Estado:</strong> <?= htmlspecialchars($detalle_asamblea['Estado'] ?? 'No disponible') ?></p>
                    <p><strong>Ciudad:</strong> <?= htmlspecialchars($detalle_asamblea['Ciudad'] ?? 'No disponible') ?></p>
                    <p><strong>Dirección:</strong> <?= htmlspecialchars($detalle_asamblea['Direccion'] ?? 'No disponible') ?></p>
                    <h3>Horario</h3>
                    <p><strong>Domingo:</strong> <?= htmlspecialchars($detalle_asamblea['Domingo'] ?? 'No disponible') ?></p>
                    <p><strong>Lunes:</strong> <?= htmlspecialchars($detalle_asamblea['Lunes'] ?? 'No disponible') ?></p>
                    <p><strong>Martes:</strong> <?= htmlspecialchars($detalle_asamblea['Martes'] ?? 'No disponible') ?></p>
                    <p><strong>Miércoles:</strong> <?= htmlspecialchars($detalle_asamblea['Miercoles'] ?? 'No disponible') ?></p>
                    <p><strong>Jueves:</strong> <?= htmlspecialchars($detalle_asamblea['Jueves'] ?? 'No disponible') ?></p>
                    <p><strong>Viernes:</strong> <?= htmlspecialchars($detalle_asamblea['Viernes'] ?? 'No disponible') ?></p>
                    <p><strong>Sábado:</strong> <?= htmlspecialchars($detalle_asamblea['Sabado'] ?? 'No disponible') ?></p>
                    <h3>Extra</h3>
                    <p><strong>Obras:</strong> <?= htmlspecialchars($detalle_asamblea['Obras'] ?? 'No disponible') ?></p>
                    <p><strong>GoogleMaps:</strong> <?= htmlspecialchars($detalle_asamblea['GoogleMaps'] ?? 'No disponible') ?></p>
                <?php else: ?>
                    <p>Selecciona una Asamblea para ver sus detalles.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        function filterByCity(city) {
            const url = new URL(window.location.href);
            url.searchParams.set('ciudad', city);
            window.location.href = url;
        }
    </script>

    <footer>
        <p>&copy; 2024 Directorio de Locales</p>
    </footer>
</body>
</html>

<?php
// Cerrar la conexión
$conexion->close();
?>
