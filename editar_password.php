<?php
session_start(); // Asegúrate de iniciar sesión
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = $_POST['id'];
  $nuevaPassword = trim($_POST['password']);

  // Obtener la contraseña actual
  $sql = "SELECT password FROM usuarios WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado && $resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $passwordActual = $row['password'];

    if ($passwordActual === $nuevaPassword) {
      $_SESSION['mensaje'] = 'misma_contraseña';
      $_SESSION['id_modal'] = $id;
      header("Location: admin_dashboard.php");
      exit();
    }

    $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevaPassword, $id);

    if ($stmt->execute()) {
      $_SESSION['mensaje'] = 'contraseña_actualizada';
      $_SESSION['id_modal'] = $id;
      header("Location: admin_dashboard.php");
      exit();
    } else {
      echo "Error al actualizar la contraseña: " . $stmt->error;
    }

    $stmt->close();
  } else {
    echo "Usuario no encontrado.";
  }

  $conn->close();
}

?>
