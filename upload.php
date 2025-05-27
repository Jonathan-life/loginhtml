<?php
include "db.php";
session_start();

if ($_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}

$ruc = $_POST["ruc"];
$archivo = $_FILES["archivo"];

if ($archivo["type"] != "application/pdf") {
    die("Solo se permiten archivos PDF.");
}

// Buscar ID del usuario
$sql = "SELECT id FROM usuarios WHERE ruc='$ruc'";
$res = $conn->query($sql);

if ($res->num_rows == 1) {
    $row = $res->fetch_assoc();
    $user_id = $row["id"];

    // Crear carpeta si no existe
    $carpeta_usuario = "files/$ruc/";
    if (!file_exists($carpeta_usuario)) {
        mkdir($carpeta_usuario, 0777, true); // permisos completos
    }

    $nombre_archivo = basename($archivo["name"]);
    $ruta_archivo = $carpeta_usuario . time() . "_" . $nombre_archivo;


    if (move_uploaded_file($archivo["tmp_name"], $ruta_archivo)) {
        $stmt = $conn->prepare("INSERT INTO documentos (user_id, archivo_nombre, ruta) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $nombre_archivo, $ruta_archivo);
        $stmt->execute();

        echo "Archivo subido exitosamente y guardado en la carpeta del usuario.";
    } else {
        echo "Error al mover el archivo.";
    }
} else {
    echo "Usuario no encontrado.";
}
?>


