<?php
session_start();
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['carpeta'])) {
    echo json_encode(['success' => false, 'error' => 'Falta el nombre de la carpeta']);
    exit;
}

$carpeta = basename($data['carpeta']);  
$dir = 'files/' . $carpeta;

if (!is_dir($dir)) {
    echo json_encode(['success' => false, 'error' => 'La carpeta no existe']);
    exit;
}

function borrarDirectorio($dir) {
    foreach(scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        $ruta = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($ruta)) {
            borrarDirectorio($ruta);
        } else {
            unlink($ruta);
        }
    }
    rmdir($dir);
}

try {
    borrarDirectorio($dir);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar la carpeta']);
}
?>
