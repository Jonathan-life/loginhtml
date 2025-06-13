<?php
session_start();
include "db.php";

// Verificar sesión
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "usuario") {
    header("Location: login.php");
    exit;
}

// Recuperar datos de sesión
$nombre = isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : "Usuario";
$ruc = isset($_SESSION["ruc"]) ? $_SESSION["ruc"] : "Sin RUC";

// Obtener ID del usuario
$sql = "SELECT id FROM usuarios WHERE ruc = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ruc);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$user_id = $row["id"] ?? 0;

// Obtener documentos del usuario
$archivos_nombres = [];
if ($user_id > 0) {
    $result = $conn->query("SELECT * FROM documentos WHERE user_id = $user_id");
    while ($doc = $result->fetch_assoc()) {
        $archivos_nombres[] = $doc;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Archivos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- ✅ Bootstrap JS (solo uno, y completo) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      background-color: #f8f9fa;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 22%;
      background-color: rgb(8, 60, 112);
      color: white;
      padding-top: 2rem;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      z-index: 1000;
    }

    .main-wrapper {
      margin-left: 22%;
      min-height: 100vh;
      background-color: #fff;
      padding: 0;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 30px 40px;
      background-color: white;
    }

    .title {
      font-size: 28px;
      font-weight: 750;
      margin-top: 100px;
      color: #1a3b85;
    }

.company-box {
  background-color: #f0f0f0;
  padding: 5px 5px;
  margin-top: -55px;
  padding-left: 30px;
  font-size: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.company-box .text-content {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.company-box strong {
  font-weight: 600;
  color: #333;
    letter-spacing: 1px;
  font-size: 16px;
}

.company-box .ruc {
  color: #555;
  font-size: 15px;
}

.icon {
  font-size: 2.2rem;
  padding: 10px;
  color: rgb(49, 49, 49);
  transform: scaleY(1); /* ← Esto lo estira verticalmente */

}




    .archivo-item {
      transition: all 0.4s ease;
    }

    .btn-visualizar {
      background-color: rgb(27, 69, 131);
      color: white;
      padding: 5px 10px;
      border: none;
      border-radius: 20px;
      font-size: 13px;
      min-width: 120px;
    }

.titulo {
  width: 100%;
  text-align: center;
  margin-top: 40px;
  letter-spacing: 4px;
  font-size: 1.6rem;
  font-weight: bold;
  margin-bottom: 2rem;
}
.linea-titulo {
  width: 100%;
  margin-top: 14px;
  border: none;
  border-bottom: 3px solid #ccc;
}
    .contenedor-menu {
      padding-left: 30px;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .btn-toggle,
    .sub-opcion,
    .cerrar-sesion {
      background: none;
      border: none;
      color: white;
      font-size: 1rem;
      text-align: left;
      padding: 10px;
      width: 100%;
      display: flex;
      align-items: center;
      gap: 0.7rem;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-toggle:hover,
    .sub-opcion:hover,
    .cerrar-sesion:hover {
      background-color: #124b7f;
      border-radius: 6px;
    }

    .submenu {
      padding-left: 1.5rem;
    }

    .icono-img {
      width: 25px;
      height: 25px;
    }

    .list-group-item {
      border-radius: 10px;
      margin-bottom: 12px;
      padding: 15px 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
/* Estilo para el contenedor principal */
.main-content {
  max-width: 1400px;
  margin: 0 auto;
}

/* Buscador */
.input-group .input-group-text {
  border-radius: 25px 0 0 25px;
  border: 1px solid #ced4da;
}
.input-group input.form-control {
  border-radius: 0 25px 25px 0;
  border: 1px solid #ced4da;
}

/* Tabla */
.custom-table {
  width: 90%;
  margin: 0 auto; /* Centrado */
  border-collapse: collapse;
  font-family: 'Segoe UI', sans-serif;
  font-size: 14px;
  color: #333;
}
.custom-table thead {
  background-color: #f2f2f2;
}
.custom-table thead th,
.custom-table tbody td {
  padding: 12px;
  border: 1px solid #ddd;
  vertical-align: middle;
  text-align: center; /* Centrar texto */
}

/* Botón Visualizar */
.btn-descargar {
  background-color: #1b4583;
  color: white;
  font-size: 13px;
  padding: 6px 12px;
  border-radius: 6px;
  border: none;
  text-decoration: none;
  display: inline-block;
  text-align: center;
}
.btn-descargar:hover {
  background-color: #163968;
  color: #fff;
  text-decoration: none;
}
.buscador-wrapper {
  width: 100%;
  padding-left: 50px;
  max-width: 600px;
}

  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="titulo">ASESCON</div>
    <hr class="linea-titulo" />

    <div class="contenedor-menu" id="menuAccordion">
      <!-- Contabilidad -->
      <button class="btn-toggle" data-bs-toggle="collapse" data-bs-target="#contaCollapse" aria-expanded="true">
        <span>
          <img src="contilidad.png" class="icono-img" alt="Contabilidad" />
          Contabilidad
        </span>
        <span class="icono-menu flecha-toggle">▲</span>
      </button>
      <div id="contaCollapse" class="collapse show submenu" data-bs-parent="#menuAccordion">
        <a href="#" class="sub-opcion">Cuentas Contables</a>
      </div>

      <!-- Cerrar sesión -->
      <a href="logout.php" class="cerrar-sesion">
        <img src="secion.png" class="icono-img" alt="Cerrar sesión" />
        Cerrar sesión
      </a>
    </div>
  </div>

  <!-- Main Wrapper -->
  <div class="main-wrapper">

    <!-- Header -->
    <div class="header">
      <div class="title">Comprobantes contables</div>
      <div class="company-box">
        <div class="text-content">
          <strong><?= htmlspecialchars($nombre) ?></strong>
          <div class="ruc">RUC: <?= htmlspecialchars($ruc) ?></div>
        </div>
        <i class="bi bi-person-fill icon"></i>
      </div>
    </div>

<!-- Main Content -->
<div class="main-content text-center">

  <!-- Buscador -->
<div class="mb-4">
  <div class="buscador-wrapper">
    <div class="input-group">
      <span class="input-group-text"><i class="bi bi-search"></i></span>
      <input type="text" id="searchInput" class="form-control" placeholder="Buscar archivo o descripción..." autocomplete="off">
    </div>
  </div>
</div>

  <?php if (count($archivos_nombres) > 0): ?>
    <div class="table-responsive">
      <table class="table custom-table" id="pdfTable">
        <thead>
          <tr>
            <th>N° doc</th>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($archivos_nombres as $index => $doc): ?>
            <tr>
              <td>#000<?= $index + 1 ?></td>
              <td><?= date("d-m-Y", strtotime($doc['fecha_subida'])) ?></td>
              <td><?= htmlspecialchars($doc['descripcion'] ?? pathinfo($doc['archivo_nombre'], PATHINFO_FILENAME)) ?></td>
              <td>
                <a href="<?= htmlspecialchars($doc['ruta']) ?>" class="btn-descargar" target="_blank">Visualizar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center mt-4" role="alert">
      No tienes archivos disponibles.
    </div>
  <?php endif; ?>
</div> <!-- Fin main-content -->

<script>
  document.getElementById("searchInput").addEventListener("keyup", function () {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll("#pdfTable tbody tr");

    rows.forEach(row => {
      const texto = row.textContent.toLowerCase();
      row.style.display = texto.includes(value) ? "" : "none";
    });
  });
</script>

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
