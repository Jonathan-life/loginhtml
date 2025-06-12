<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;
    $estado = $_POST['estado'] ?? null;

    if ($id && ($estado === 'activo' || $estado === 'inactivo')) {
        $stmt = $conn->prepare("UPDATE usuarios SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $estado, $id);

        if ($stmt->execute()) {
            echo "ok";
        } else {
            echo "Error al actualizar estado";
        }

        $stmt->close();
    } else {
        echo "Datos inválidos";
    }
} else {
    echo "Método inválido";
}
?>
