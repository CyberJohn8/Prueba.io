<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Conexión a la base de datos con UTF-8 habilitado
        $pdo = new PDO('mysql:host=localhost;dbname=asambleasvenezuela;charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Manejo de archivos
        $fileName = $_POST['DocumentoActual'];
        $uploadDir = "ruta_donde_se_guardan_los_archivos/"; // Ruta donde se guardan los archivos
        if (isset($_POST['EliminarDocumento']) && $_POST['EliminarDocumento'] === '1') {
            // Eliminar documento actual
            if ($fileName && file_exists($uploadDir . $fileName)) {
                unlink($uploadDir . $fileName);
            }
            $fileName = null;
        } elseif (!empty($_FILES['Documento']['name'])) {
            // Validar el archivo subido
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png']; // Tipos permitidos
            $maxFileSize = 2 * 1024 * 1024; // Tamaño máximo: 2MB
            $fileType = $_FILES['Documento']['type'];
            $fileSize = $_FILES['Documento']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Tipo de archivo no permitido.');
            }
            if ($fileSize > $maxFileSize) {
                throw new Exception('El archivo supera el tamaño máximo permitido.');
            }

            // Reemplazar archivo actual con uno nuevo
            if ($fileName && file_exists($uploadDir . $fileName)) {
                unlink($uploadDir . $fileName);
            }
            $fileName = time() . "_" . basename($_FILES['Documento']['name']);
            move_uploaded_file($_FILES['Documento']['tmp_name'], $uploadDir . $fileName);
        }

        // Actualización de datos en la base de datos
        $sql = "UPDATE anuncios SET Titulo = :Titulo, Descripcion = :Descripcion, Vocero = :Vocero, 
                Asamblea = :Asamblea, Estado = :Estado, Ciudad = :Ciudad, Documento = :Documento WHERE ID = :ID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':Titulo' => htmlspecialchars($_POST['Titulo']),
            ':Descripcion' => htmlspecialchars($_POST['Descripcion']),
            ':Vocero' => htmlspecialchars($_POST['Vocero']),
            ':Asamblea' => htmlspecialchars($_POST['Asamblea']),
            ':Estado' => htmlspecialchars($_POST['Estado']),
            ':Ciudad' => htmlspecialchars($_POST['Ciudad']),
            ':Documento' => $fileName,
            ':ID' => intval($_POST['ID'])
        ]);

        echo "Anuncio actualizado correctamente. <a href='crud.php'>Volver</a>";
    } catch (Exception $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
} else {
    try {
        // Conexión a la base de datos con UTF-8 habilitado
        $pdo = new PDO('mysql:host=localhost;dbname=asambleasvenezuela;charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Obtener datos del anuncio
        $sql = "SELECT * FROM anuncios WHERE ID = :ID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ID' => intval($_GET['id'])]);
        $anuncio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$anuncio) {
            throw new Exception("Anuncio no encontrado.");
        }
    } catch (Exception $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
        exit;
    }

    // Mostrar formulario
    echo '
        <div class="container">
            <a href="crud.php" class="btn">Volver</a>
            <form action="editar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="ID" value="' . htmlspecialchars($anuncio['ID']) . '">
                <input type="hidden" name="DocumentoActual" value="' . htmlspecialchars($anuncio['Documento']) . '">

                <label for="Titulo">Título:</label>
                <input type="text" name="Titulo" value="' . htmlspecialchars($anuncio['Titulo']) . '" required>

                <label for="Descripcion">Descripción:</label>
                <textarea name="Descripcion" required>' . htmlspecialchars($anuncio['Descripcion']) . '</textarea>
                
                <label for="Vocero">Vocero:</label>
                <input type="text" name="Vocero" value="' . htmlspecialchars($anuncio['Vocero']) . '" required>
                
                <label for="Asamblea">Asamblea:</label>
                <input type="text" name="Asamblea" value="' . htmlspecialchars($anuncio['Asamblea']) . '" required>
                
                <label for="Estado">Estado:</label>
                <input type="text" name="Estado" value="' . htmlspecialchars($anuncio['Estado']) . '" required>
                
                <label for="Ciudad">Ciudad:</label>
                <input type="text" name="Ciudad" value="' . htmlspecialchars($anuncio['Ciudad']) . '" required>

                <label for="Documento">Documento Actual:</label>
                ' . ($anuncio['Documento'] ? '<a href="' . $uploadDir . htmlspecialchars($anuncio['Documento']) . '" target="_blank">' . htmlspecialchars($anuncio['Documento']) . '</a>' : 'No hay documento cargado') . '
                
                <label for="Documento">Nuevo Documento:</label>
                <input type="file" name="Documento">
                
                <label>
                    <input type="checkbox" name="EliminarDocumento" value="1"> Eliminar documento actual
                </label>

                <button type="submit">Actualizar</button>
            </form>
        </div>
    ';
}
?>



<style>
/* Contenedor principal */
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 20px;
}

/* Botón Volver */
.btn {
    display: block;
    width: 50%; /* Ajusta el ancho del botón según sea necesario */
    padding: 10px;
    font-size: 18px;
    color: white;
    background-color: #007bff;
    border: none;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 20px; /* Espacio entre el botón y el formulario */
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #0056b3;
}

/* Formulario */
form {
    width: 100%;
    max-width: 500px;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

form label {
    font-size: 16px;
    color: #555;
    display: block;
    margin-bottom: 5px;
}

form input, form select, form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

form input:focus, form select:focus, form textarea:focus {
    border-color: #007bff;
    outline: none;
    background-color: #fff;
}

form button {
    width: 100%;
    padding: 10px;
    font-size: 18px;
    color: white;
    background-color: #007bff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #0056b3;
}
</style>
