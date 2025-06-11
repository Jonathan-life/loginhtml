<?php
include 'db.php';

if (!isset($_GET['user_id'])) {
    echo "ID de usuario no proporcionado.";
    exit;
}

$user_id = intval($_GET['user_id']);

// Obtener el RUC del usuario desde la base de datos
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

echo "<h2>🧑‍💼 Archivos del usuario: $nombre (RUC: $ruc)</h2>";

// Mostrar archivos desde la base de datos
$sql = "SELECT archivo_nombre, ruta FROM documentos WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>📦 Archivos en base de datos:</h3><ul>";
$encontrado = false;
while ($row = $result->fetch_assoc()) {
    $encontrado = true;
    echo "<li><a href='{$row['ruta']}' target='_blank'>{$row['archivo_nombre']}</a></li>";
}
if (!$encontrado) {
    echo "<li>❌ No hay archivos registrados en la base de datos.</li>";
}
echo "</ul>";

// Mostrar archivos desde la carpeta del usuario
$carpeta = "files/$ruc/";

echo "<h3>📂 Archivos en la carpeta: $carpeta</h3>";
if (is_dir($carpeta)) {
    $archivos = scandir($carpeta);
    echo "<ul>";
    $hayArchivos = false;
    foreach ($archivos as $archivo) {
        if ($archivo === '.' || $archivo === '..') continue;
        $hayArchivos = true;
        echo "<li><a href='{$carpeta}{$archivo}' target='_blank'>{$archivo}</a></li>";
    }
    if (!$hayArchivos) {
        echo "<li>❌ La carpeta está vacía.</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ No existe la carpeta del usuario.</p>";
}
?>
