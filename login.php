<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ruc = $_POST["ruc"];
    $password = $_POST["password"];

    // Verificar usuario
    $sql = "SELECT * FROM usuarios WHERE ruc = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $ruc, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario) {
        if ($usuario['estado'] === 'inactivo') {
            $error = "Esta cuenta está desactivada. Contacte al administrador.";
        } else {
            $_SESSION["ruc"] = $usuario['ruc'];
            $_SESSION["tipo"] = "usuario";
            $_SESSION["usuario_id"] = $usuario['id'];
            $_SESSION["nombre"] = $usuario['nombre']; // ✅ Aquí guardamos el nombre
            header("Location: user_dashboard.php");
            exit;
        }
    } else {
        // Verificar admin si no es usuario
        $sql2 = "SELECT * FROM administradores WHERE usuario = ? AND password = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ss", $ruc, $password);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $admin = $result2->fetch_assoc();

        if ($admin) {
            $_SESSION["usuario"] = $admin['usuario'];
            $_SESSION["tipo"] = "admin";
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Credenciales incorrectas.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
      background: url('asescon-02.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: Arial, sans-serif;
    }

    .login-container {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(8px);
      padding: 1rem;
    }

    .login-card {
      background-color: white;
      border-radius: 28px;
      width: 100%;
      padding: 5rem;
      max-width: 500px;
      box-sizing: border-box;
    }

    .login-title {
      font-weight: bold;
      color:rgb(12, 67, 126);
      margin-bottom: 0.25rem;
      text-align: left;
      font-size: 2rem;
    }

    .login-subtitle {
      color: #333;
      margin-bottom: 2rem;
      text-align: left;
      font-size: 1rem;
    }

    .form-group {
      position: relative;
      margin-bottom: 2.5rem;
    }

    .form-control {
      width: 100%;
      border-radius: 12px;
      padding: 16px 14px 16px 14px;
      font-size: 1rem;
      border: 2px solid #0056b3;
      background: transparent;
    }

    .form-label {
      position: absolute;
      left: 16px;
      top: 19px;
      background: white;
      padding: 0 6px;
      color: #0056b3;
      font-weight: 600;
      transition: 0.2s ease all;
      pointer-events: none;
    }

    /* Cuando el input está enfocado o tiene texto, la etiqueta sube */
    .form-control:focus + .form-label,
    .form-control:not(:placeholder-shown) + .form-label {
      top: -10px;
      left: 12px;
      font-size: 0.85rem;
      background-color: white;
      color: #003d80;
    }

    .input-icon {
      position: absolute;
      right: 16px;
      top: 52%;
      transform: translateY(-50%);
      width: 22px;
      height: 23px;
    }

    .input-icon img {
      width: 100%;
      height: auto;
    }

    .btn-primary {
      background-color:rgb(19, 67, 119);
      border-color: #0056b3;
      border-radius: 12px;
      padding: 12px;
      font-weight: 600;
      font-size: 1.1rem;
      margin-top: 10px;
      width: 100%;
    }

    .form-check-label {
      font-size: 1rem;
      color: #333;
    }

    .alert {
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="login-card">
    <h2 class="login-title">BIENVENIDO</h2>
    <h5 class="login-subtitle">INICIAR SESIÓN</h5>

    <?php if(isset($error) && $error): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="form-group">
        <input type="text" class="form-control" id="ruc" name="ruc" required placeholder=" ">
        <label for="ruc" class="form-label">RUC</label>
      </div>

      <div class="form-group">
        <input type="password" class="form-control" id="password" name="password" required placeholder=" ">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-icon">
          <img src="contraseña-ojo.png" alt="Icono de contraseña">
        </div>
      </div>
      
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="recordarme" id="recordarme">
        <label class="form-check-label" for="recordarme">
          Recordarme
        </label>
      </div>

      <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
