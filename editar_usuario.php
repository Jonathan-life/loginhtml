<?php
// Incluir conexión a la base de datos
require_once 'db.php'; // este define $conn

// Verificar que se recibió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre']);

    // Preparar y ejecutar la consulta
    $sql = "UPDATE usuarios SET nombre = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en prepare: " . $conn->error);
    }

    $stmt->bind_param("si", $nombre, $id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard?mensaje=nombre_actualizado");
        exit();
    } else {
        echo "Error al actualizar el nombre: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
