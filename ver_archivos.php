<?php
session_start();
include "db.php";

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}

$dir = 'files/';  // Carpeta raíz que quieres listar
$_SESSION['admin'] = true;   // Datos admin
$_SESSION['user'] = true;    // Datos usuario

function listarCarpetasConArchivos($path, $parentId = '') {
    if (!is_dir($path)) {
        echo "<p>La carpeta no existe.</p>";
        return;
    }

    $items = scandir($path);
    $carpetas = array_filter($items, fn($item) => is_dir($path . DIRECTORY_SEPARATOR . $item) && $item != '.' && $item != '..');

    if (count($carpetas) == 0) {
        echo "<p>No hay carpetas disponibles.</p>";
        return;
    }

    echo '<div class="accordion" id="accordionFolders">';
    $index = 0;
    foreach ($carpetas as $carpeta) {
        $index++;
        $collapseId = "collapse$parentId$index";
        $headingId = "heading$parentId$index";
        $folderPath = $path . DIRECTORY_SEPARATOR . $carpeta;

        // Listar archivos dentro de esta carpeta
        $archivos = array_filter(scandir($folderPath), fn($f) => !is_dir($folderPath . DIRECTORY_SEPARATOR . $f) && $f != '.' && $f != '..');

        // Contenedor con clase para búsqueda y botones para eliminar
        echo <<<HTML
        <div class="accordion-item carpeta-item" data-carpeta="$carpeta">
            <h2 class="accordion-header d-flex justify-content-between align-items-center" id="$headingId">
                <button class="accordion-button collapsed flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#$collapseId" aria-expanded="false" aria-controls="$collapseId">
                    📁 <span class="nombre-carpeta">$carpeta</span>
                </button>
                <button class="btn btn-danger btn-sm ms-2 btn-eliminar-carpeta" data-carpeta="$carpeta" title="Eliminar carpeta">🗑️</button>
            </h2>
            <div id="$collapseId" class="accordion-collapse collapse" aria-labelledby="$headingId" data-bs-parent="#accordionFolders">
                <div class="accordion-body">
HTML;

        if (count($archivos) > 0) {
            echo '<ul class="list-group">';
            foreach ($archivos as $archivo) {
                $rutaArchivo = $folderPath . DIRECTORY_SEPARATOR . $archivo;
                $urlArchivo = htmlspecialchars($rutaArchivo);
                $nombreArchivo = htmlspecialchars($archivo);
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>📄 <a href='$urlArchivo' target='_blank'>$nombreArchivo</a> <button class='btn btn-danger btn-sm btn-eliminar-archivo' data-carpeta='$carpeta' data-archivo='$archivo' title='Eliminar archivo'>🗑️</button></li>";
            }
            echo '</ul>';
        } else {
            echo '<p>No hay archivos en esta carpeta.</p>';
        }

        echo <<<HTML
                </div>
            </div>
        </div>
HTML;
    }
    echo '</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Carpetas y Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #searchInput {
            max-width: 400px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Carpetas en <?= htmlspecialchars($dir) ?></h2>

    <!-- Buscador -->
    <input type="text" id="searchInput" class="form-control" placeholder="Buscar carpeta por nombre o RUC...">

    <?php listarCarpetasConArchivos($dir); ?>

    <div class="mt-4">
        <a href="admin_dashboard.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        const carpetas = document.querySelectorAll('.carpeta-item');

        carpetas.forEach(carpeta => {
            const nombre = carpeta.querySelector('.nombre-carpeta').textContent.toLowerCase();

            if (nombre.includes(filtro)) {
                carpeta.style.display = '';
            } else {
                carpeta.style.display = 'none';
            }
        });
    });

    // Eliminar carpeta
    document.addEventListener('click', e => {
        if(e.target.classList.contains('btn-eliminar-carpeta')){
            const carpeta = e.target.getAttribute('data-carpeta');
            if (!confirm(`¿Seguro que quieres eliminar la carpeta "${carpeta}" y todo su contenido?`)) return;

            fetch('eliminar_carpeta.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ carpeta })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    alert('Carpeta eliminada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
    });

    // Eliminar archivo
    document.addEventListener('click', e => {
        if(e.target.classList.contains('btn-eliminar-archivo')){
            const carpeta = e.target.getAttribute('data-carpeta');
            const archivo = e.target.getAttribute('data-archivo');
            if (!confirm(`¿Seguro que quieres eliminar el archivo "${archivo}"?`)) return;

            fetch('eliminar_archivo.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ carpeta, archivo })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    alert('Archivo eliminado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }
    });
</script>
</body>
</html>
