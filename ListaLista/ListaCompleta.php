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

// Configuración de caracteres
$conexion->set_charset("utf8");

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los valores seleccionados
$ciudad = isset($_GET['ciudad']) ? $conexion->real_escape_string($_GET['ciudad']) : '';
$id_asamblea = isset($_GET['id']) ? intval($_GET['id']) : null;

// Consulta principal para mostrar la lista de localidades
$query = "SELECT ID, Nombre, Estado, Ciudad FROM asambleas_de_venezuela";
if (!empty($ciudad)) {
    $query .= " WHERE Ciudad = '$ciudad'";
}
$resultado = $conexion->query($query);

// Obtener detalles de una asamblea específica
$detalle_asamblea = null;
if ($id_asamblea) {
    $detalle_query = "SELECT Nombre, Numero, Estado, Ciudad, Direccion, Domingo, Lunes, Martes, Miercoles, 
                      Jueves, Viernes, Sabado, Obras, GoogleMaps 
                      FROM asambleas_de_venezuela 
                      WHERE ID = $id_asamblea";
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
    <title>Directorio de Locales</title>

    

    <style>
        /* General */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Encabezado */
        /* Encabezado */
        header {
            display: flex; /* Usamos flexbox para organizar los elementos */
            justify-content: space-between; /* Distribuimos los elementos a los lados */
            align-items: center; /* Alineamos verticalmente los elementos */
            background-color: #003366;
            color: white;
            padding: 15px 10px;
            border-radius: 8px 8px 0 0;
        }

        header h1 {
            flex: 1; /* Toma el espacio restante para centrarse */
            text-align: center;
            margin: 0;
            font-size: 1.8em;
        }

        .menu-button {
            text-decoration: none;
            color: white;
            background-color: #0056b3;
            padding: 10px 20px;
            border-radius: 5px;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .menu-button:hover {
            background-color: #003f7f;
        }

        form {
            margin: 0;
        }

        form label {
            margin-right: 5px;
            font-weight: bold;
        }

        form select {
            padding: 5px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }



        /* Formulario */
        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        form label {
            margin-right: 10px;
            font-weight: bold;
        }

        form select {
            padding: 5px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #ddd;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #003366;
            color: white;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #dbe7f0;
        }

        /* Scroll para la tabla */
        table tbody {
            display: block;
            max-height: 300px; /* Ajusta la altura según sea necesario */
            overflow-y: auto;
            overflow-x: hidden;
        }

        table thead, table tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        /* Detalles */
        .details {
            background-color: #eef6fc;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            border: 1px solid #d1e7f0;
        }

        .details h2 {
            margin-top: 0;
        }

        .details p {
            margin: 8px 0;
        }

        .details a {
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }

        .details a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #003366;
            color: white;
            border-radius: 0 0 8px 8px;
            font-size: 0.9em;
        }


        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        .details {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }
        .details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">

        <header>
            <!-- Botón para volver al menú -->
            <a href="../index.html" class="menu-button">&#8592; Menú</a>
            <h1>Directorio de Locales</h1>

            <!-- Formulario para filtrar por ciudad -->
            <form method="GET" action="">
                <label for="ciudad">Selecciona una ciudad:</label>
                <select name="ciudad" id="ciudad" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <?php
                    $ciudades_query = "SELECT DISTINCT Ciudad FROM asambleas_de_venezuela";
                    $ciudades_resultado = $conexion->query($ciudades_query);
                    while ($fila = $ciudades_resultado->fetch_assoc()) {
                        $selected = ($ciudad === $fila['Ciudad']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($fila['Ciudad']) . "' $selected>" . htmlspecialchars($fila['Ciudad']) . "</option>";
                    }
                    ?>
                </select>
            </form>
        </header>

       

        

        <!-- Tabla para mostrar localidades -->
        <h2>Lista de Localidades</h2>
        <table>
            <thead>
                <tr>
                    <th>Asamblea</th>
                    <th>Estado</th>
                    <th>Ciudad</th>
                    <th>Detalles</th>
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
                        echo "<td><a href='?id=" . $fila['ID'] . "'>Ver detalles</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No se encontraron resultados.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Mostrar detalles de la asamblea seleccionada -->
        <?php if ($detalle_asamblea): ?>
            <div class="details">
                <h2>Detalles de la Asamblea</h2>
                <h3>Asamblea</h3>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($detalle_asamblea['Nombre']) ?></p>
                <p><strong>Numero:</strong> <?= htmlspecialchars($detalle_asamblea['Numero']) ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($detalle_asamblea['Estado']) ?></p>
                <p><strong>Ciudad:</strong> <?= htmlspecialchars($detalle_asamblea['Ciudad']) ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($detalle_asamblea['Direccion']) ?></p>
                <h3>Horario</h3>
                <p><strong>Domingo:</strong> <?= htmlspecialchars($detalle_asamblea['Domingo']) ?></p>
                <p><strong>Lunes:</strong> <?= htmlspecialchars($detalle_asamblea['Lunes']) ?></p>
                <p><strong>Martes:</strong> <?= htmlspecialchars($detalle_asamblea['Martes']) ?></p>
                <p><strong>Miércoles:</strong> <?= htmlspecialchars($detalle_asamblea['Miercoles']) ?></p>
                <p><strong>Jueves:</strong> <?= htmlspecialchars($detalle_asamblea['Jueves']) ?></p>
                <p><strong>Viernes:</strong> <?= htmlspecialchars($detalle_asamblea['Viernes']) ?></p>
                <p><strong>Sábado:</strong> <?= htmlspecialchars($detalle_asamblea['Sabado']) ?></p>
                <h3>Extra</h3>
                <p><strong>Obras:</strong> <?= htmlspecialchars($detalle_asamblea['Obras']) ?></p>
                <p><strong>GoogleMaps:</strong> <a href="<?= htmlspecialchars($detalle_asamblea['GoogleMaps']) ?>" target="_blank">Ver ubicación</a></p>
            </div>
        <?php endif; ?>

        <footer>
            <p>&copy; 2024 Directorio de Locales</p>
        </footer>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conexion->close();
?>
