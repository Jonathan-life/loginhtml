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
 /* Reseteo general */
body, html {
  margin: 0;
  padding: 0;
  height: 100%;
  background-color: #f8f9fa; /* ejemplo para bg claro */
}

/* Sidebar fijo */
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
  align-items: flex-start;
  z-index: 1000;
}

/* Contenido principal a la derecha del sidebar */
.main-content {
  margin-left: 23%;    /* deja espacio para sidebar fijo */
  padding-top: 100px;   /* baja solo el contenido interno */
  padding-left: 50px;
  padding-right: 70px;
  background-color: white;
  min-height: 100vh;   /* opcional, que ocupe al menos toda la pantalla */
}

.btn-primary-custom {
  background-color: rgb(24, 76, 131); /* azul */
  color: white;
  border-radius: 12px;
  border: none;
  padding: 7px 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;

  margin-top: 30px; /* <-- aquí bajas el botón */
}
.btn-primary-custom:hover {
  background-color: rgb(27, 49, 71);
  color: white;
}



/* Otros estilos sidebar que tenías */
.flecha-toggle {
  width: 100%;
  padding-right: 6rem;
  padding-left: 5px;
  margin-top: auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.titulo {
  width: 100%;
  text-align: center;
  margin-top: 40px;
  letter-spacing: 4px;
  font-size: 1.8rem;
  font-weight: bold;
  margin-bottom: 2rem;
}

.linea-titulo {
  width: 100%;
  margin: 0 auto 4rem auto;
  margin-top: 15px;
  border: none;
  border-bottom: 3px solid #ccc;
}

.contenedor-menu {
  width: 100%;
  padding-left: 32px;
  padding-right: 1rem;
  margin-top: -2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  align-items: flex-start;
}

.boton-menu,
.btn-toggle,
.sub-opcion {
  color: white;
  font-size: 1rem;
  font-weight: 400;
  text-align: left;
  background: none;
  letter-spacing: 1px;
  border: none;
  padding: 10px;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  width: 90%;
  text-decoration: none;
  cursor: pointer;
}

.btn-toggle {
  justify-content: space-between;
  border-radius: 8px;
}

.btn-toggle span {
  display: flex;
  align-items: center;
  gap: 0.7rem;
}

.boton-menu:hover,
.btn-toggle:hover,
.sub-opcion:hover,
.cerrar-sesion:hover {
  background-color: #124b7f;
  border-radius: 8px;
}

.submenu {
  padding-left: 2rem;
  margin-top: 0.3rem;
}

.cerrar-sesion {
  color: white;
  text-decoration: none;
  padding: 0.5rem;
  width: 100%;
  font-size: 20px;
  text-align: left;
  border: none;
  margin-right: 50px;
  background: none;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  cursor: pointer;
}

.icono-menu {
  font-size: 1rem;
  user-select: none;
}

.icono-img {
  width: 25px;
  height: 25px;
  object-fit: contain;
}

/* Si quieres, aquí el estilo para el icono dentro del botón */
.btn-primary-custom i {
  font-size: 1.2rem;
}

        .breadcrumb-item + .breadcrumb-item::before {
      content: ">";
    }

    .table thead th {
      background-color: #f1f1f1;
      text-align: center;
    }

    .table td {
      vertical-align: middle;
      text-align: center;
    }

    .btn-subir {
      background-color: #0c4f92;
      color: white;
    }

    .btn-subir:hover {
      background-color: #093c71;
    }

    .icon-btn {
      border: none;
      background: none;
      cursor: pointer;
      padding: 0.25rem;
    }

    .icon-btn i {
      font-size: 1rem;
    }



  .btn-primary-custom:hover {
    background-color: #0056b3;
    color: white;
  }

  /* Borde redondeado para el input y el span del buscador */
  .input-group .input-group-text {
    border-radius: 25px 0 0 25px;
    border: 1px solid #ced4da;
  }
  .input-group input.form-control {
    border-radius: 0 25px 25px 0;
    border: 1px solid #ced4da;
  }

.custom-table {
  border-collapse: separate;
  border-spacing: 0 12px;
  width: 100%; /* Ahora usa todo el ancho disponible */
  max-width: 1080px; /* Máximo ancho definido */
  margin: 0 auto; /* Centra horizontalmente */
  text-align: center;
}

.custom-table thead th {
  background-color: #f0f0f0;
  color: #6c757d;
  border: none;
  padding: 10px 14px;
  font-size: 14px;
  text-align: center;
}

.custom-table tbody tr {
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  height: 50px;
}

.custom-table td {
  border: none;
  padding: 10px 14px;
  vertical-align: middle;
  font-size: 14px;
  text-align: center;
}

.custom-table tbody tr td:first-child {
  border-top-left-radius: 8px;
  border-bottom-left-radius: 8px;
}

.custom-table tbody tr td:last-child {
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
}

/* Botón de visualizar */
.btn-visualizar {
  background-color: rgb(27, 69, 131);
  color: white;
  padding: 5px 10px;
  border: none;
  border-radius: 20px;
  font-size: 13px;
  min-width: 120px;
  text-align: center;
}

/* Botón de icono simple */
.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
}

/* Botón para subir con imagen (sin fondo) */
.btn-subir {
  background-color: transparent;
  border: none;
  cursor: pointer;
}

/* Estilo para quitar fondo azul en hover y active */
.dropdown-menu .dropdown-item:hover,
.dropdown-menu .dropdown-item:focus,
.dropdown-menu .dropdown-item:active {
  background-color: transparent !important;
  color: #000 !important;
}

/* Estilo general del menú dropdown */
.dropdown-menu {
  min-width: 8rem;
  padding: 0.25rem 0;
  border-radius: 0.5rem;
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
}

/* Quitar flecha del botón dropdown-toggle */
.btn-subir.dropdown-toggle-split::after {
  display: none !important;
}

/* Contenedor de acciones centrado y alineado */
.acciones-box {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-right: 0; /* Eliminar el margin-right excesivo */
}

/* Ajustes adicionales para el botón con imagen */
.btn-subir.dropdown-toggle-split {
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  cursor: pointer;
}
/* Forzar dropdown hacia la izquierda del botón */
.dropdown .dropdown-menu {
  left: auto !important;
  right: 0 !important;
}


  </style>
</head>
<body class="bg-light">

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

    </div> <!-- FIN contenedor-menu -->
  </div> <!-- FIN sidebar -->

  <!-- Contenido principal -->
  <div class="container mt-5 position-relative">
    <h2 class="text-center mb-4">📂 Tus Archivos PDF</h2>

    <!-- Buscador -->
    <div class="d-flex justify-content-center position-relative">
      <input type="text" id="searchInput" class="form-control" placeholder="Buscar archivo..." autocomplete="off" />
      <div id="suggestions" style="display:none;"></div>
    </div>

    <!-- Lista de archivos -->
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

    <!-- Botón de cerrar sesión -->
    <div class="text-center mt-4">
      <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>
  </div> <!-- FIN container -->

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
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
