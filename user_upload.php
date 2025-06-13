<?php
session_start();
include "db.php";  // Conexión a la base de datos
// Si no existe la sesión o no es el tipo correcto, redirige al login
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}
$_SESSION['admin'] = true;   // Datos admin
$_SESSION['user'] = true;    // Datos usuario

// Consulta para obtener los usuarios tipo "usuario"
$sql = "SELECT id, ruc, nombre, tipo FROM usuarios WHERE tipo = 'usuario'";
$resultado = $conn->query($sql);

$usuarios = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel Asescon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>

body, html {
  height: 100%;
  background-color: #f8f9fa; /* ejemplo para bg claro */
}

/* Sidebar fijo */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 23%;
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
  padding-top: 60px;   /* baja solo el contenido interno */
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
  cursor: pointer;
  transition: background-color 0.3s ease;

  margin-top: 22px; /* <-- aquí bajas el botón */
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
  margin-top: 20px;
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
  padding: 0.5rem 1rem;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  width: 100%;
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
/* Estilo para quitar fondo azul en hover, focus y active */
.dropdown-menu .dropdown-item:hover,
.dropdown-menu .dropdown-item:focus,
.dropdown-menu .dropdown-item:active {
  background-color: transparent !important;
  color: #000 !important;
  text-decoration: none;
}

/* Estilo general del menú dropdown */
.dropdown-menu {
  min-width: 8rem;
  padding: 0.25rem 0;
  border-radius: 0.5rem;
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
  border: none;
}

/* Quitar flecha del botón dropdown-toggle */
.btn-subir.dropdown-toggle-split::after {
  display: none !important;
}

/* Opcional: eliminar cualquier fondo en el botón también */
.btn-subir {
  background-color: transparent !important;
  border: none !important;
  padding: 0.25rem;
  box-shadow: none !important;
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
  text-decoration: none; /* Quitar subrayado */
  display: inline-block;
}


  </style>
  <style>
  #tablaUsuarios thead th,
  #tablaUsuarios tbody td {
    background-color: #ffffff !important; /* Fondo blanco */
    color: #555 !important;              /* Texto gris oscuro */
    border: 1px solid #ddd !important;   /* Borde claro */
    text-align: center;
    vertical-align: middle;
  }

  #tablaUsuarios {
    border-collapse: collapse;
  }

  /* Opcional: fuerza el fondo blanco en filas si quedó alguna herencia */
  #tablaUsuarios tbody tr {
    background-color: #ffffff !important;
  }
</style>

</head>
<body class="bg-light">

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="titulo">ASESCON</div>
    <hr class="linea-titulo" />

    <div class="contenedor-menu" id="menuAccordion">

      <!-- Administrativo -->
      <a href="admin_dashboard.php" class="boton-menu">
        <img src="archivos.png" class="icono-img" alt="Admin" />
        Administrativo
      </a>
      <!-- Solo redirecciona a upload_file.php -->
      <a href="upload_file.php" class="btn-toggle d-inline-block">
        <span>
          <img src="contilidad.png" class="icono-img" alt="Contabilidad" />
          Subir archivos
        </span>
      </a>


      <!-- Cerrar sesión -->
      <a href="logout.php" class="cerrar-sesion">
        <img src="secion.png" class="icono-img" alt="Cerrar sesión" />
        Cerrar sesión
      </a>

    </div> <!-- FIN contenedor-menu -->
  </div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content">
  <div class="container-fluid p-4">

    <!-- Encabezado -->
    <div class="d-flex justify-content-end align-items-center mb-3 flex-wrap gap-3">
      <div class="fs-4 fw-bold me-auto">Seleccionar</div>

      <!-- Buscador (form separado porque es GET y no afecta selección) -->
      <form action="buscar_ruc.php" method="get" class="position-relative" style="min-width: 310px;">
        <input type="text" name="ruc" id="buscador" class="form-control ps-5 rounded-pill shadow-sm"
               placeholder="Buscar por N° de RUC" required>
        <img src="icone-loupe-gris.png" alt="Buscar"
             class="position-absolute top-50 start-0 translate-middle-y ms-3"
             style="width: 20px; height: 20px; object-fit: contain;">
      </form>
       <div class="d-flex gap-3 align-items-center mb-3">
        <!-- Botón SUBIR (ya no es submit del formulario, sino botón independiente) -->
        <button type="button" id="botonSubirSeleccionados" class="btn rounded-pill px-4"
          style="background-color: rgb(27, 69, 131); color: #ffffff;">
          Subir
        </button>


        <!-- Menú desplegable -->
        <div class="dropdown">
          <button type="button" class="btn btn-subir dropdown-toggle dropdown-toggle-split rounded-pill"
                  data-bs-toggle="dropdown" aria-expanded="false">
            <img src="opcciones.png" alt="Icono"
                 style="width: 20px; height: 20px; object-fit: contain;">
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" id="seleccionarTodos">Seleccionar todos</a></li>
          </ul>
        </div>
      </div>

    </div>

    <!-- FORMULARIO ÚNICO que incluye el botón Subir y la tabla -->
    <form action="upload_file.php" method="post" id="formSubir">

      <!-- Tabla con checkboxes -->
      <div class="table-responsive mt-4">
        <table class="table table-bordered" id="tablaUsuarios" style="background-color: white;">
          <thead class="text-center" style="background-color: #f8f9fa; color: #495057;">
            <tr>
              <th><input type="checkbox" id="checkTodos"></th>
              <th>RUC</th>
              <th>Nombre</th>
              <th>Usuario</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($usuarios as $usuario): ?>
              <tr>
                <td class="text-center">
                  <input type="checkbox" class="checkFila" name="rucs[]" value="<?= htmlspecialchars($usuario['ruc']) ?>">
                </td>
                <td style="background-color: white; color: #6c757d;"><?= htmlspecialchars($usuario['ruc']) ?></td>
                <td style="background-color: white; color: #6c757d;"><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td style="background-color: white; color: #6c757d;"><?= htmlspecialchars($usuario['tipo']) ?></td>
                <td style="background-color: white;">
                  <a href="upload_file.php?ruc=<?= urlencode($usuario['ruc']) ?>" class="btn-visualizar" style="text-decoration: none;">
                    Subir
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </form>

  </div>
</div>

<script>
  // Subir archivos a múltiples RUCs por GET
  document.getElementById('botonSubirSeleccionados').addEventListener('click', function () {
    const checkboxes = document.querySelectorAll('.checkFila:checked');
    if (checkboxes.length === 0) {
      alert('Por favor, selecciona al menos un usuario para subir archivos.');
      return;
    }

    // Construir URL con los RUCs seleccionados
    const rucs = Array.from(checkboxes).map(cb => cb.value);
    const url = 'upload_file.php?rucs=' + encodeURIComponent(rucs.join(','));

    // Redirigir
    window.location.href = url;
  });
</script>


<!-- Script para seleccionar/deseleccionar todos y validación -->
<script>
  // Seleccionar/deseleccionar todos los checkboxes
  document.getElementById('checkTodos').addEventListener('change', function () {
    const checked = this.checked;
    document.querySelectorAll('.checkFila').forEach(chk => chk.checked = checked);
  });

  // Seleccionar todos desde el menú desplegable
  document.getElementById('seleccionarTodos').addEventListener('click', function (e) {
    e.preventDefault();
    document.getElementById('checkTodos').checked = true;
    document.querySelectorAll('.checkFila').forEach(chk => chk.checked = true);
  });

  // Validar que se seleccione al menos un checkbox antes de enviar
  document.getElementById('formSubir').addEventListener('submit', function (e) {
    const seleccionados = document.querySelectorAll('.checkFila:checked');
    if (seleccionados.length === 0) {
      e.preventDefault();
      alert('Por favor, selecciona al menos un usuario para subir archivos.');
    }
  });
</script>


puede hacer qeu al menos haber selecciono a avario ususarios y al darle a subir envie la url de todos los rucc para poder enviar archivo a eso?



<!-- JS para búsqueda dinámica -->
<script>
  document.getElementById('buscador').addEventListener('keyup', function () {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaUsuarios tbody tr');
    filas.forEach(fila => {
      const texto = fila.innerText.toLowerCase();
      fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
  });
</script>

    

  
</body>
</html>
