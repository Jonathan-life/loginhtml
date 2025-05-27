<?php
session_start();
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['carpeta']) || empty($data['archivo'])) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos']);
    exit;
}

$carpeta = basename($data['carpeta']);
$archivo = basename($data['archivo']);
$rutaArchivo = 'files/' . $carpeta . DIRECTORY_SEPARATOR . $archivo;

if (!file_exists($rutaArchivo)) {
    echo json_encode(['success' => false, 'error' => 'El archivo no existe']);
    exit;
}

try {
    unlink($rutaArchivo);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar el archivo']);
}
?>
