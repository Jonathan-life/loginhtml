<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ruc = $_POST["ruc"];
    $password = $_POST["password"];

    // Verificar usuario
    $sql = "SELECT * FROM usuarios WHERE ruc='$ruc' AND password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $_SESSION["ruc"] = $ruc;
        $_SESSION["tipo"] = "usuario";
        header("Location: user_dashboard.php");
        exit;
    }

    // Verificar admin
    $sql2 = "SELECT * FROM administradores WHERE usuario='$ruc' AND password='$password'";
    $result2 = $conn->query($sql2);
    if ($result2->num_rows == 1) {
        $_SESSION["usuario"] = $ruc;
        $_SESSION["tipo"] = "admin";
        header("Location: admin_dashboard.php");
        exit;
    }

    $error = "Credenciales incorrectas.";
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
      font-size: 3rem;
    }

    .login-subtitle {
      color: #333;
      margin-bottom: 2rem;
      text-align: left;
      font-size: 1.4rem;
    }

    .form-group {
      position: relative;
      margin-bottom: 1.5rem;
    }

    .form-label {
      position: absolute;
      top: 9px;
      left: 16px;
      background: white;
      padding: 0 6px;
      font-size: 0.95rem;
      color: #0056b3;
      font-weight: 600;
      border-radius: 4px;
      z-index: 1;
    }

.form-control {
  border-radius: 12px;
  padding: 14px 42px 14px 14px; /* espacio a la derecha para la imagen */
  font-size: 1.1rem;
  border: 2px solid #0056b3;
}

.input-icon {
  position: absolute;
  right: 12px; /* ahora el icono está a la derecha */
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
}

.input-icon img {
  width: 110%;
  height: 65%;
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
        <label for="ruc" class="form-label">RUC:</label>
        <input type="text" class="form-control" id="ruc" name="ruc" required>
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-icon">
          <img src="contraseña-ojo.png" alt="Icono de contraseña">
        </div>
        <input type="password" class="form-control" id="password" name="password" required>
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
