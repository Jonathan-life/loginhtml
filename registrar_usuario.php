<?php
include "db.php";
session_start();

if ($_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}

$ruc = $_POST["ruc"];
$password = $_POST["password"];

// Verificar si ya existe el usuario
$sql = "SELECT * FROM usuarios WHERE ruc='$ruc'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo " Ya existe un usuario con ese RUC.";
    echo "<br><a href='admin_dashboard.php'>Volver</a>";
    exit;
}

// Insertar nuevo usuario
$stmt = $conn->prepare("INSERT INTO usuarios (ruc, password) VALUES (?, ?)");
$stmt->bind_param("ss", $ruc, $password);
$stmt->execute();

echo "Usuario creado correctamente.";

?>
