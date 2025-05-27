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
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4">Iniciar Sesión</h3>

        <?php if($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="ruc" class="form-label">RUC o Usuario</label>
                <input type="text" class="form-control" id="ruc" name="ruc" placeholder="Ingrese RUC o Usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
