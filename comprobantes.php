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

  body, html {
    height: 100%;
    margin: 0;
  }

  .sidebar {
    height: 100vh;
    width: 22%;
    background-color: rgb(8, 60, 112);
    color: white;
    position: fixed;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding-top: 2rem;
    z-index: 1000; /* Para asegurar que la barra quede encima */
  }

  .main-content {
    margin-left: 22%; /* desplazamos el contenido para que no esté debajo */
    padding: 2rem;
  }
  .flecha-toggle {
      width: 100%;
      padding-left: 20px;
      padding-right: 6rem;
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
      font-size: 2.3rem;
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
      padding-left: 60px;
      padding-right: 1rem;
      margin-top: -2rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .boton-menu,
    .btn-toggle,
    .sub-opcion {
      color: white;
      font-size: 1.2rem;
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
      /* Quitar fondo y borde al botón con imagen */
  .btn-subir.dropdown-toggle-split {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
  }

  /* Quitar la flecha del dropdown-toggle */
  .btn-subir.dropdown-toggle-split::after {
    display: none !important;
  }

  /* Opcional: cambiar el cursor para que parezca clickeable */
  .btn-subir.dropdown-toggle-split {
    cursor: pointer;
  }
  </style>
</head>
<body class="bg-light">

  <div class="sidebar">
    <div class="titulo">ASESCON</div>
    <hr class="linea-titulo" />

    <div class="contenedor-menu" id="menuAccordion">

      <!-- Administrativo -->
      <a href="#" class="boton-menu" onclick="cambiarRuta('Administrativo')">
        <img src="archivos.png" class="icono-img" alt="Admin" />
        Administrativo
      </a>

      <!-- Contabilidad -->
      <button class="btn-toggle" data-bs-toggle="collapse" data-bs-target="#contaCollapse" aria-expanded="true">
        <span onclick="cambiarRuta('Contabilidad')">
          <img src="contilidad.png" class="icono-img" alt="Contabilidad" />
          Contabilidad
        </span>
        <span class="icono-menu flecha-toggle">▲</span>
      </button>
      <div id="contaCollapse" class="collapse show submenu" data-bs-parent="#menuAccordion">
        <a href="#" class="sub-opcion" onclick="cambiarRuta('Contabilidad', 'Cuentas Contables')">Cuentas Contables</a>
      </div>

      <!-- Comprobantes contables -->
      <button class="btn-toggle" data-bs-toggle="collapse" data-bs-target="#comproCollapse" aria-expanded="false">
        <span onclick="cambiarRuta('Contabilidad', 'Comprobantes contables')">
          Comprobantes contables
        </span>
        <span class="icono-menu">▲</span>
      </button>
      <div id="comproCollapse" class="collapse submenu" data-bs-parent="#menuAccordion">
        <a href="#" class="sub-opcion" onclick="cambiarRuta('Contabilidad', 'Libro diario')">Libro diario</a>
      </div>

      <a href="logout.php" class="cerrar-sesion">
        <img src="secion.png" class="icono-img" alt="Cerrar sesión" />
        Cerrar sesión
      </a>
    </div>
  </div>
<!-- CONTENIDO PRINCIPAL -->
<div class="main-content mt-4">
  <div class="container-fluid p-4">

    <!-- Fila de encabezado alineado horizontalmente -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

      <!-- Título principal -->
      <div class="fs-4 fw-bold">MINERA SANTA EMMA</div>

      <!-- Breadcrumb dinámico -->
      <div id="breadcrumb-dinamico" class="ms-2">
        <span>Contabilidad</span> &gt;
        <a href="#" class="text-primary">Comprobantes contables</a>
      </div>

      <!-- Contenedor de buscador y botones -->
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <!-- Buscador -->
        <div class="position-relative" style="min-width: 310px;">
          <input type="text" class="form-control ps-5 rounded-pill shadow-sm" placeholder="Buscar por N° de comprobante">
          <img src="icone-loupe-gris.png" alt="Buscar"
            class="position-absolute top-50 start-0 translate-middle-y ms-3"
            style="width: 20px; height: 20px; object-fit: contain;">
        </div>

        <!-- Botones -->
        <div class="btn-group">
          <button class="btn btn-subir rounded-pill px-4">Subir</button>
          <button type="button" class="btn btn-subir dropdown-toggle dropdown-toggle-split rounded-pill"
            data-bs-toggle="dropdown" aria-expanded="false"
            style="padding-left: 0.5rem; padding-right: 0.5rem; padding-top: 5px; padding-bottom: 5px;">
            <img src="op." alt="Icono"
              style="width: 20px; height: 20px; object-fit: contain;">
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Opción 1</a></li>
            <li><a class="dropdown-item" href="#">Opción 2</a></li>
          </ul>
        </div>

        <!-- Botón adicional -->
        <button class="btn btn-light rounded-circle">
          <i class="bi bi-three-dots-vertical"></i>
        </button>
      </div>

    </div> <!-- fin fila encabezado -->

  </div>
</div>


  <!-- JS para actualizar breadcrumb -->
  <script>
    function cambiarRuta(seccion, sub = null) {
      const breadcrumb = document.getElementById("breadcrumb-dinamico");
      if (sub) {
        breadcrumb.innerHTML = `<span>${seccion}</span> &gt; <a href="#" class="text-primary">${sub}</a>`;
      } else {
        breadcrumb.innerHTML = `<span>${seccion}</span>`;
      }
    }
  </script>
</body>

        <!-- Tabla alineada a la derecha -->
        <div class="table-responsive mt-12 mb-8 d-flex">
          <table class="table table-bordered" style="width: 1150px; margin-left: 457px; margin-top: 10px;" >
            <thead>
              <tr>
                <th><input type="checkbox"></th>
                <th>Número</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Total</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="checkbox"></td>
                <td>E001-204</td>
                <td>25-05-2025</td>
                <td>Venta</td>
                <td>Factura de venta de productos a tienda XXX</td>
                <td>S/ 105.50</td>
                <td>
                  <button class="icon-btn text-danger"><i class="bi bi-trash"></i></button>
                  <button class="icon-btn text-primary"><i class="bi bi-pencil-square"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


  <script>
    // Formulario subir archivo
    document.getElementById("uploadForm").addEventListener("submit", function (e) {
      e.preventDefault();
      if (!confirm("¿Estás seguro de subir este archivo?")) return;

      const form = this;
      const formData = new FormData(form);

      fetch("upload.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.text())
        .then((msg) => {
          showToast(msg, msg.includes("exitosamente") ? "success" : "danger");
          if (msg.includes("exitosamente")) {
            form.reset();
          }
        });
    });

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
