<?php
include 'db.php';

if (!isset($_GET['user_id'])) {
    echo "ID de usuario no proporcionado.";
    exit;
}

$user_id = intval($_GET['user_id']);

// Obtener el RUC y nombre del usuario
$sql = "SELECT ruc, nombre FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Usuario no encontrado.";
    exit;
}

$user = $result->fetch_assoc();
$ruc = $user['ruc'];
$nombre = $user['nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Archivos del Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      background-color: #f8f9fa;
    }
    .main-wrapper {
      margin: auto;
      padding: 30px;
      max-width: 1200px;
    }
    .titulo {
      font-size: 28px;
      font-weight: bold;
      color: #1a3b85;
      margin-bottom: 20px;
      text-align: center;
    }
    .section-title {
      font-size: 20px;
      color: #333;
      margin-top: 40px;
    }
    ul {
      list-style: none;
      padding-left: 0;
    }
    li {
      margin-bottom: 10px;
    }
    a {
      color: #1b4583;
      text-decoration: none;
      font-weight: 500;
    }
    a:hover {
      text-decoration: underline;
    }
    .no-archivos {
      color: #888;
    }
    .card-box {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      padding: 25px 35px;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

<div class="main-wrapper">
  <div class="titulo">🧑‍💼 Archivos del usuario: <?= htmlspecialchars($nombre) ?> <br>(RUC: <?= htmlspecialchars($ruc) ?>)</div>

  <!-- Archivos en base de datos -->
  <div class="card-box">
    <div class="section-title">📦 Archivos registrados en la base de datos</div>
    <ul>
      <?php
      $sql = "SELECT archivo_nombre, ruta FROM documentos WHERE user_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      $hayDB = false;
      while ($row = $result->fetch_assoc()) {
          $hayDB = true;
          echo "<li><a href='{$row['ruta']}' target='_blank'><i class='bi bi-file-earmark'></i> {$row['archivo_nombre']}</a></li>";
      }
      if (!$hayDB) {
          echo "<li class='no-archivos'>❌ No hay archivos en la base de datos.</li>";
      }
      ?>
    </ul>
  </div>

  <!-- Archivos en carpeta -->
  <div class="card-box">
    <div class="section-title">📂 Archivos en la carpeta del servidor: <code>files/<?= htmlspecialchars($ruc) ?>/</code></div>
    <ul>
      <?php
      $carpeta = "files/$ruc/";
      $hayCarpeta = false;
      if (is_dir($carpeta)) {
          $archivos = scandir($carpeta);
          foreach ($archivos as $archivo) {
              if ($archivo === '.' || $archivo === '..') continue;
              $hayCarpeta = true;
              echo "<li><a href='{$carpeta}{$archivo}' target='_blank'><i class='bi bi-folder2-open'></i> {$archivo}</a></li>";
          }
          if (!$hayCarpeta) {
              echo "<li class='no-archivos'>❌ La carpeta está vacía.</li>";
          }
      } else {
          echo "<li class='no-archivos'>❌ La carpeta no existe.</li>";
      }
      ?>
    </ul>
  </div>
</div>

</body>
</html>
