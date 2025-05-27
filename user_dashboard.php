<?php
session_start();
include "db.php";

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "usuario") {
    header("Location: login.php");
    exit;
}

$ruc = $_SESSION["ruc"];
$sql = "SELECT id FROM usuarios WHERE ruc='$ruc'";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$user_id = $row["id"];

$result = $conn->query("SELECT * FROM documentos WHERE user_id=$user_id");

// Guardamos los nombres de archivos para sugerencias
$archivos_nombres = [];
while ($doc = $result->fetch_assoc()) {
    $archivos_nombres[] = $doc;
}
// Volvemos a consultar para la lista (porque ya usamos el result)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #searchInput {
            max-width: 400px;
            margin: 0 auto 0.5rem;
            position: relative;
        }

        .archivo-item {
            transition: opacity 0.7s ease, transform 0.7s ease;
            opacity: 1;
        }

        .archivo-item.oculto {
            opacity: 0;
            transform: scaleY(0);
            height: 0;
            padding: 0;
            margin: 0;
            overflow: hidden;
            pointer-events: none;
        }

        /* Sugerencias */
        #suggestions {
            max-width: 400px;
            margin: 0 auto 20px;
            border: 1px solid #ced4da;
            border-top: none;
            background: white;
            top: 100%; /* Justo debajo del input */
            position: absolute;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 0 0 4px 4px;
        }

        #suggestions div {
            padding: 8px 12px;
            cursor: pointer;
        }

        #suggestions div:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5 position-relative">
    <h2 class="text-center mb-4">📂 Tus Archivos PDF</h2>

    <!-- Buscador -->
    <div class="d-flex justify-content-center position-relative">
        <input type="text" id="searchInput" class="form-control" placeholder="Buscar archivo..." autocomplete="off" />
        <div id="suggestions" style="display:none;"></div>
    </div>

    <?php if (count($archivos_nombres) > 0): ?>
        <ul class="list-group mt-3" id="fileList">
            <?php foreach ($archivos_nombres as $doc): ?>
                <li class="list-group-item archivo-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="archivo-nombre">📄 <?= htmlspecialchars($doc['archivo_nombre']) ?></span>
                        <a href="<?= htmlspecialchars($doc['ruta']) ?>" class="btn btn-sm btn-primary" target="_blank">Ver PDF</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-warning text-center mt-4" role="alert">
            No tienes archivos disponibles.
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const suggestionsBox = document.getElementById('suggestions');
    const items = document.querySelectorAll('.archivo-item');

    // Array de nombres para sugerencias
    const nombresArchivos = Array.from(items).map(item => 
        item.querySelector('.archivo-nombre').textContent.trim()
    );

   
    searchInput.addEventListener('input', function () {
        const valor = this.value.toLowerCase().trim();

        if (valor === '') {
            suggestionsBox.style.display = 'none';
            filterItems('');
            return;
        }

        const sugerencias = nombresArchivos.filter(nombre => 
            nombre.toLowerCase().includes(valor)
        ).slice(0, 5); 

        if (sugerencias.length === 0) {
            suggestionsBox.style.display = 'none';
        } else {
            suggestionsBox.innerHTML = '';
            sugerencias.forEach(sug => {
                const div = document.createElement('div');
                div.textContent = sug;
                div.addEventListener('click', () => {
                    searchInput.value = sug;
                    filterItems(sug.toLowerCase());
                    suggestionsBox.style.display = 'none';
                });
                suggestionsBox.appendChild(div);
            });
            suggestionsBox.style.display = 'block';
        }

        filterItems(valor);
    });


    searchInput.addEventListener('blur', () => {
        setTimeout(() => { 
            suggestionsBox.style.display = 'none';
        }, 200);
    });

    function filterItems(filtro) {
        items.forEach(item => {
            const nombre = item.querySelector('.archivo-nombre').textContent.toLowerCase();
            if (nombre.includes(filtro)) {
                item.classList.remove('oculto');
            } else {
                item.classList.add('oculto');
            }
        });
    }
</script>

</body>
</html>
