<?php
$conn = new mysqli("localhost", "root", "", "sistema_archivos");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (!isset($_POST['user_id'])) {
    die("No se recibió el ID del usuario.");
}

$user_id = intval($_POST['user_id']);

// Obtener RUC o nombre de carpeta del usuario
$stmt = $conn->prepare("SELECT ruc FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($ruc);
$stmt->fetch();
$stmt->close();

// Eliminar archivos físicos
$stmt = $conn->prepare("SELECT ruta FROM documentos WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($f = $result->fetch_assoc()) {
    if (file_exists($f['ruta'])) {
        unlink($f['ruta']);
    }
}
$stmt->close();

// Eliminar registros de la base de datos
$stmt1 = $conn->prepare("DELETE FROM documentos WHERE user_id = ?");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$stmt1->close();

$stmt2 = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$stmt2->close();

// Eliminar carpeta del usuario (si existe)
$carpeta = "files/" . $ruc;
if (is_dir($carpeta)) {
    rmdir($carpeta); // solo funciona si está vacía
}

// Redirigir
header("Location: admin_dashboard.php");
exit;
?>
