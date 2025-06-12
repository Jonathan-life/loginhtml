<?php
session_start();
include "db.php";  // Conexión a la base de datos

// Verificar que el usuario sea administrador
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}

// Variables de sesión para control
$_SESSION['admin'] = true;
$_SESSION['user'] = true;

// Consulta de usuarios con sus archivos, incluyendo estado
$sql = "SELECT u.id, u.ruc, u.nombre, u.password, u.tipo, u.fecha_creacion, u.estado,
               GROUP_CONCAT(d.archivo_nombre SEPARATOR ', ') AS archivos
        FROM usuarios u
        LEFT JOIN documentos d ON u.id = d.user_id
        GROUP BY u.id";
$result = $conn->query($sql);

// Total de usuarios
$consulta_total = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
$total_usuarios = $consulta_total->fetch_assoc()['total'];

// Manejo de mensajes de contraseña
$mensaje = null;
$tipoAlerta = null;
$mostrarModal = false;

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'] === 'misma_contraseña'
        ? "La nueva contraseña no puede ser igual a la actual."
        : "La contraseña se actualizó correctamente.";

    $tipoAlerta = $_SESSION['mensaje'] === 'misma_contraseña' ? 'warning' : 'success';
    $mostrarModal = true;

    unset($_SESSION['mensaje']); // Limpiar sesión
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel Asescon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>

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

      <!-- Administrativo -->
      <a href="admin_dashboard.php" class="boton-menu">
        <img src="archivos.png" class="icono-img" alt="Admin" />
        Administrativo
      </a>

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
  </div>

  <!-- Contenido principal -->
  <div class="main-content">

    <!-- Encabezado -->
      <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
          <h2 class="fw-bold">USUARIOS</h2>
          <p><?= $total_usuarios ?> usuarios registrados</p>
        </div>
        <div>
          <a href="crear_usuario.php" class="btn btn-primary-custom text-decoration-none">
            <i class="bi bi-plus-lg"></i>  Crear nuevo usuario
          </a>
        </div>
    </div>

<!-- Barra de búsqueda -->
<div class="input-group mb-2">
  <span class="input-group-text bg-white border-end-0">
    <img src="icone-loupe-gris.png" alt="Buscar" style="width: 16px; height: 16px;">
  </span>
  <input type="text" id="buscador-ruc" class="form-control border-start-0" placeholder="Buscar por N° de RUC">
</div>

<!-- Tabla de usuarios -->
<div class="table-responsive mt-4 mb-3 d-flex justify-content-center">
  <table class="table custom-table">
    <thead>
      <tr>
        <th>RUC</th>
        <th>Nombre</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="tabla-usuarios">
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td class="ruc-cell"><?= htmlspecialchars($row['ruc'] ?? '') ?></td>
          <td><?= htmlspecialchars($row['nombre'] ?? '') ?></td>
          <td>
            <img src="<?= $row['estado'] == 'activo' ? 'chaeck.png' : 'inactvo.png' ?>" 
                alt="<?= $row['estado'] ?>" 
                title="<?= ucfirst($row['estado']) ?>" 
                style="width: 20px; height: 20px; margin-right: 6px;">
            <?= ucfirst($row['estado']) ?>
          </td>
          <td>
            <div class="acciones-box">
              <!-- Visualizar archivos -->
              <form action="visualizar_archivos.php" method="GET" target="_blank" class="m-0">
                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                <button type="submit" class="btn-visualizar">Visualizar</button>
              </form>

              <!-- Eliminar usuario -->
              <button type="button" class="icon-btn text-danger"
                      onclick="abrirModalEliminar(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nombre']) ?>')"
                      title="Eliminar usuario">
                <img src="basura.png" alt="Eliminar" style="width: 16px; height: 16px;">
              </button>

              <!-- Menú desplegable -->
              <div class="dropdown">
                <button type="button" class="btn btn-subir dropdown-toggle dropdown-toggle-split rounded-pill"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        style="padding-left: 0.5rem; padding-right: 0.5rem;">
                  <img src="opcciones.png" alt="Icono" style="width: 20px; height: 20px; object-fit: contain;">
                </button>
                <ul class="dropdown-menu dropdown-menu-start">
                  <li>
                    <a class="dropdown-item" href="#" onclick="abrirModalNombre(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nombre']) ?>')">
                      Editar nombre
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#" onclick="abrirModalPassword(<?= $row['id'] ?>)">
                      Cambiar contraseña
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item d-flex align-items-center <?= $row['estado'] == 'activo' ? 'text-danger' : 'text-success' ?>"
                      href="#"
                      onclick="toggleEstadoUsuario(<?= $row['id'] ?>, '<?= $row['estado'] ?>')">
                      <img src="<?= $row['estado'] == 'activo' ? 'inactvo.png' : 'chaeck.png' ?>" 
                          alt="<?= $row['estado'] == 'activo' ? 'Desactivar' : 'Activar' ?>" 
                          style="width: 16px; height: 16px; margin-right: 8px;">
                      <span><?= $row['estado'] == 'activo' ? 'Desactivar cuenta ' : 'Activar cuenta ' ?></span>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>




<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 320px;">
    <div class="modal-content text-center position-relative" style="border-radius: 20px; padding: 90px 20px 30px;">

      <!-- Ícono circular SALIENDO DEL MODAL -->
      <div class="position-absolute start-50 translate-middle-x" 
           style="top: -50px; background-color: #143B82; border-radius: 50%; width: 130px; height: 135px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <i class="bi bi-person-fill" style="font-size: 90px; color: white;"></i>
      </div>

      <!-- Título -->
      <h5 class="fw-bold mt-3 mb-3" style="color: #143B82; font-size: 25px;">ELIMINAR USUARIO</h5>

      <!-- Texto descriptivo -->
      <p id="textoUsuarioEliminar" class="mb-4 text-muted" style="font-size: 15px; padding: 0 15px;"></p>

      <!-- Formulario de eliminación -->
      <form action="eliminar_usuario.php" method="POST">
        <input type="hidden" name="user_id" id="userIdEliminar">
          <button type="submit" class="btn w-100 mb-2"
                style="padding: 5px; max-width: 204px; background-color: #143B82; color: white; border-radius: 30px; font-size: 22px; font-weight: bold; letter-spacing: 0.5px;">
          Eliminar
        </button>
        </button>
        <button type="button" class="btn btn-link text-muted w-100" data-bs-dismiss="modal" style="text-decoration: none; font-size: 14px;">
          Cancelar
        </button>
      </form>
    </div>
  </div>
</div>
<!-- Modal Editar Nombre -->
<div class="modal fade" id="modalEditarNombre" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 620px;">
    <div class="modal-content border-0 rounded-4 p-4 shadow-sm" style="border-radius: 20px;">
      
      <h5 class="fw-bold mb-3 text-primary text-center">Editar Nombre</h5>
      
      <form action="editar_usuario.php" method="POST">
        <input type="hidden" name="id" id="editarIdUsuario">

        <!-- Campo nombre -->
        <input type="text" name="nombre" class="form-control rounded-pill px-4 py-3 mb-4" 
               id="editarNombreUsuario" placeholder="Escriba el nuevo nombre" required>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-3">
          <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn text-white px-4" 
                  style="background-color: #1A3263; border-radius: 12px;">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="modalCambiarPassword" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 620px;">
    <div class="modal-content border-0 rounded-4 p-4 shadow-sm" style="border-radius: 20px;">
      
      <h5 class="fw-bold mb-3" style="color: #4F4F4F;">Cambiar la contraseña</h5>
      
      <form action="editar_password.php" method="POST">
        <input type="hidden" name="id" id="editarIdPassword">

        <!-- Mensaje con sesión -->
        <?php if (isset($mensaje)): ?>
          <div class="alert alert-<?= $tipoAlerta ?> alert-dismissible fade show" role="alert">
            <?= $mensaje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Campo contraseña -->
        <div class="position-relative mb-4">
          <input type="password" name="password" id="nuevaPassword" class="form-control rounded-pill px-4 py-3" placeholder="Introducir nueva contraseña" required style="padding-right: 50px;">
          <span onclick="togglePassword()" class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;">
            <i class="bi bi-eye" id="iconoVer"></i>
          </span>
        </div>

        <!-- Botones -->
        <div class="d-flex justify-content-end gap-3">
          <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn text-white px-4" style="background-color: #1A3263; border-radius: 12px;">Cambiar</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<!-- Scripts para abrir modales -->
<script>
  function abrirModalEliminar(id, nombre) {
    document.getElementById("userIdEliminar").value = id;
    document.getElementById("textoUsuarioEliminar").innerText = `Estás a punto de eliminar al usuario ${nombre}. Vas a eliminar sus datos de forma permanente.`;
    const modal = new bootstrap.Modal(document.getElementById("modalEliminarUsuario"));
    modal.show();
  }

  function abrirModalNombre(id, nombre) {
    document.getElementById('editarIdUsuario').value = id;
    document.getElementById('editarNombreUsuario').value = nombre;
    new bootstrap.Modal(document.getElementById('modalEditarNombre')).show();
  }

  function abrirModalPassword(id) {
    document.getElementById('editarIdPassword').value = id;
    new bootstrap.Modal(document.getElementById('modalCambiarPassword')).show();
  }

  function setIdPassword(id) {
    document.getElementById('editarIdPassword').value = id;
  }
</script>
<?php if (isset($mostrarModal) && $mostrarModal): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = new bootstrap.Modal(document.getElementById('modalCambiarPassword'));
      modal.show();

      <?php if (isset($_SESSION['id_modal'])): ?>
        document.getElementById('editarIdPassword').value = "<?= $_SESSION['id_modal'] ?>";
      <?php unset($_SESSION['id_modal']); endif; ?>
    });
  </script>
<?php endif; ?>

<script>
  function togglePassword() {
    const passwordInput = document.getElementById("nuevaPassword");
    const icon = document.getElementById("iconoVer");
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      icon.classList.remove("bi-eye");
      icon.classList.add("bi-eye-slash");
    } else {
      passwordInput.type = "password";
      icon.classList.remove("bi-eye-slash");
      icon.classList.add("bi-eye");
    }
  }
</script>


<!-- Script para autodescartar alertas -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const alertEl = document.querySelector(".alert");
    if (alertEl) {
      setTimeout(() => {
        const alertInstance = bootstrap.Alert.getOrCreateInstance(alertEl);
        alertInstance.close();
      }, 4000); // 4 segundos
    }
  });
</script>

<!-- Script para mostrar modal automáticamente si viene por URL -->
<?php if (isset($_GET['mensaje']) && isset($_GET['id_modal'])): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    setIdPassword(<?php echo $_GET['id_modal']; ?>);
    const modal = new bootstrap.Modal(document.getElementById("modalCambiarPassword"));
    modal.show();
  });
</script>
<?php endif; ?>

<!-- Script para crear usuario vía fetch -->
<script>
  document.getElementById("createUserForm").addEventListener("submit", function (e) {
    e.preventDefault();
    if (!confirm("¿Deseas crear este usuario?")) return;

    const form = this;
    const formData = new FormData(form);

    fetch("registrar_usuario.php", {
      method: "POST",
      body: formData,
    })
    .then((res) => res.text())
    .then((msg) => {
      showToast(msg, msg.includes("creado") ? "success" : "danger");
      if (msg.includes("creado")) {
        form.reset();
      }
    });
  });

  function showToast(message, type = "success") {
    const toastEl = document.getElementById("toastMessage");
    const toastBody = document.getElementById("toastBody");
    toastBody.innerText = message;
    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
  }
</script>

<!-- Script para toggle menú lateral -->
<script>
  document.querySelectorAll('.btn-toggle').forEach(button => {
    button.addEventListener('click', () => {
      const targetId = button.getAttribute('data-target');
      const target = document.querySelector(targetId);
      const isVisible = target.classList.contains('show');

      // Cerrar todos los submenús
      document.querySelectorAll('.submenu').forEach(menu => {
        menu.classList.remove('show');
      });

      // Resetear íconos
      document.querySelectorAll('.btn-toggle .icono-menu').forEach(icon => {
        icon.textContent = '▼';
      });

      if (!isVisible) {
        target.classList.add('show');
        const icon = button.querySelector('.icono-menu');
        icon.textContent = '▲';
      }
    });
  });
</script>

<!-- Script para búsqueda de RUC -->
<script>
  document.getElementById("buscador-ruc").addEventListener("input", function() {
    const filtro = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll("#tabla-usuarios tr");

    filas.forEach(fila => {
      const ruc = fila.querySelector(".ruc-cell")?.textContent.toLowerCase() || "";
      fila.style.display = ruc.includes(filtro) ? "" : "none";
    });
  });

</script>
<script>
function toggleEstadoUsuario(id, estadoActual) {
  const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';

  if (!confirm(`¿Estás seguro de que deseas cambiar el estado a ${nuevoEstado}?`)) {
    return;
  }

  fetch('cambiar_estado.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id=${id}&estado=${nuevoEstado}`
  })
  .then(res => res.text())
  .then(data => {
    if (data === 'ok') {
      location.reload(); // Recarga la página para reflejar el cambio
    } else {
      alert('Error al cambiar estado');
    }
  });
}
</script>



</body>
</html>
