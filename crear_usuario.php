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

.main-content {
  margin-left: 23%; /* Deja espacio para el sidebar fijo */
  width: 77%; /* Ocupa el espacio restante */
  padding:  40px;
  box-sizing: border-box;
  min-height: 100vh;
  background-color: white;
}


.encabezado-nuevo-usuario {
  background-color: #ffffff; /* Fondo blanco */
  border-radius: 12px;
}

.titulo-nuevo-usuario {
  text-align: center;
  font-weight: 700;
  color: #555; /* Gris oscuro */
  margin-top: 1rem;
  margin-bottom: 1rem;
}

.subtitulo-nuevo-usuario {
  text-align: center;
  color: black; /* Gris más claro */
  font-size: 15px;
  margin-bottom: 2rem;
  line-height: 1.5;
}

.form-card {
  background-color: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  width: 100%;
  max-width: 850px; /* Limita el ancho */
  margin: 0 auto; /* Centra horizontalmente */
}

.form-card-header {
  background-color: rgb(255, 255, 255); /* Azul oscuro como relleno */
  padding: 15px 20px;
  font-weight: 700;
  font-size: 25px;
  color: #555; /* Letra blanca */
  border-bottom: none; /* Opcional: sin borde inferior */
  border-radius: 12px 12px 0 0; /* Bordes redondeados arriba */
}



.form-card-body {
  padding: 20px;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  margin-bottom: 16px;
  border: 1px solid rgb(23, 58, 95); /* Borde azul */
  border-radius: 12px;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
  color: #666; /* Texto gris */
  background-color:rgb(255, 255, 255); /* Fondo claro */
  transition: border-color 0.2s ease;
}

.form-input::placeholder {
  color: #999; /* Placeholder gris claro */
}

.form-input:focus {
  border-color: rgb(16, 58, 102); /* Azul más fuerte al enfocar */
}

.password-wrapper {
  position: relative;

}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 16px;
  transform: translateY(-50%);
  cursor: pointer;
  color: #666;
}

.submit-btn {
  display: block;
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 12px;
  background-color:rgb(23, 58, 95);
  color: white;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.submit-btn:hover {
  background-color: #0056b3;
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
          Contable
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


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content">
<div class="encabezado-nuevo-usuario">
  <h2 class="titulo-nuevo-usuario">CREA UN NUEVO USUARIO</h2>
  <p class="subtitulo-nuevo-usuario">
    Completa los siguientes datos para registrar un<br>
    nuevo usuario en el sistema.
  </p>
</div>



    <div class="form-card">
      <div class="form-card-header">Información del usuario</div>
      <div class="form-card-body">
        <form action="guardar_usuario.php" method="POST" id="createUserForm">
          <input type="text" name="nombre" class="form-input" placeholder="NOMBRE" required>
          <input type="text" name="ruc" class="form-input" placeholder="RUC" required>


          <div class="password-wrapper">
            <input type="password" name="password" class="form-input" placeholder="CONTRASEÑA" id="password" required>
            <i class="bi bi-eye-slash toggle-password" onclick="togglePassword('password', this)"></i>
          </div>

          <div class="password-wrapper">
            <input type="password" name="confirm_password" class="form-input" placeholder="CONFIRMAR CONTRASEÑA" id="confirm_password" required>
            <i class="bi bi-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
          </div>

          <button type="submit" class="submit-btn">Registrar Usuario</button>
        </form>
      </div>
    </div>
  </div>

<!-- Bootstrap Icons (para el ojito de contraseña) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">



  <!-- TOAST -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastMessage" class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div id="toastBody" class="toast-body">Mensaje</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
      </div>
    </div>
  </div>

  <!-- BOOTSTRAP Y JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SCRIPT FUNCIONAL -->
  <script>
    // Mostrar/Ocultar contraseña
    function togglePassword(id, el) {
      const input = document.getElementById(id);
      const icon = el;

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      } else {
        input.type = "password";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      }
    }

    // Mostrar mensaje tipo toast
    function showToast(message, type = "success") {
      const toastEl = document.getElementById("toastMessage");
      const toastBody = document.getElementById("toastBody");
      toastBody.innerText = message;
      toastEl.className = `toast align-items-center text-bg-${type} border-0`;
      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    }

    // Carga de scripts al DOM
    document.addEventListener("DOMContentLoaded", () => {
      // Formulario CREAR USUARIO
      const form = document.getElementById("createUserForm");
      form.addEventListener("submit", function (e) {
        e.preventDefault();

        if (!confirm("¿Deseas crear este usuario?")) return;

        const password = form.querySelector('input[name="password"]').value;
        const confirmPassword = form.querySelector('input[name="confirm_password"]').value;

        if (password !== confirmPassword) {
          showToast("Las contraseñas no coinciden", "danger");
          return;
        }

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
          })
          .catch((err) => {
            showToast("Error al enviar el formulario", "danger");
            console.error(err);
          });
      });
    });
  </script>
</body>
</html>