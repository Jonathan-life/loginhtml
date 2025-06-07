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
  border-radius: 25px;
  border: none;
  padding: 4px 12px;
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


.input-group .input-group-text {
  border-radius: 25px 0 0 25px;
  border: 1px solid #999999; /* borde gris */
  background-color: white;
}

.input-group input.form-control {
  border-radius: 0 25px 25px 0;
  border: 1px solid #999999; /* borde gris */
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
        <p>158 USUARIOS ACTIVOS</p>
      </div>
      <div>
        <button class="btn-primary-custom">
          <i class="bi bi-plus-lg"></i>+ Crear nuevo usuario
        </button>

      </div>
    </div>

    <!-- Barra de búsqueda -->
<div class="input-group mb-2">
  <span class="input-group-text bg-white border-end-0">
    <img src="icone-loupe-gris.png" alt="Buscar" style="width: 16px; height: 16px;">
  </span>
  <input type="text" class="form-control border-start-0" placeholder="Buscar por N° de RUC">
</div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


  <script>

    // Formulario crear usuario
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

    // Función mostrar toast
    function showToast(message, type = "success") {
      const toastEl = document.getElementById("toastMessage");
      const toastBody = document.getElementById("toastBody");
      toastBody.innerText = message;
      toastEl.className = `toast align-items-center text-bg-${type} border-0`;
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    }

    // Toggle menú lateral
    document.querySelectorAll('.btn-toggle').forEach(button => {
      button.addEventListener('click', () => {
        const targetId = button.getAttribute('data-target');
        const target = document.querySelector(targetId);
        const isVisible = target.classList.contains('show');

        // Cerrar todos los submenús
        document.querySelectorAll('.submenu').forEach(menu => {
          menu.classList.remove('show');
        });

        // Resetear todos los íconos a flecha abajo
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
  
</body>
</html>
