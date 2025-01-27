<?php
try {
    // Configurar conexión con UTF-8
    $pdo = new PDO('mysql:host=localhost;dbname=asambleasvenezuela;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los anuncios
    $sql = "SELECT * FROM anuncios ORDER BY Fecha_creacion DESC";
    $stmt = $pdo->query($sql);

    // Construcción del HTML
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CRUD de Anuncios</title>
        <link rel="stylesheet" href="../CSS/crud.css">
    </head>
    <body>
        <header>
            <a href="cartelera.php" class="dropdown-button">&#8592; Volver</a>
            <h1>Gestión de Anuncios</h1>
            <a href="crear.php" class="dropdown-button">Crear Anuncio</a>
        </header>
        <main>
            <section class="table-section">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
    
    // Validar si hay resultados y construir las filas de la tabla
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>
                <td>' . htmlspecialchars($row['ID']) . '</td>
                <td>' . htmlspecialchars($row['Titulo']) . '</td>
                <td>' . htmlspecialchars($row['Descripcion']) . '</td>
                <td>' . htmlspecialchars($row['Fecha_creacion']) . '</td>
                <td>
                    <a href="editar.php?id=' . htmlspecialchars($row['ID']) . '" class="btn">Editar</a>
                    <a href="eliminar.php?id=' . htmlspecialchars($row['ID']) . '" class="btn btn-danger" onclick="return confirm(\'¿Estás seguro de eliminar este anuncio?\')">Eliminar</a>
                </td>
            </tr>';
        }
    } else {
        echo '<tr><td colspan="5">No hay anuncios disponibles.</td></tr>';
    }

    echo '      </tbody>
                </table>
            </section>
        </main>
        <footer>
            <p>&copy; ' . date("Y") . ' Gestión de Anuncios. Todos los derechos reservados.</p>
        </footer>
    </body>
    </html>';
} catch (PDOException $e) {
    // Mostrar error de conexión
    echo "Error al conectar con la base de datos: " . htmlspecialchars($e->getMessage());
}
?>
