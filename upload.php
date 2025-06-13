<?php
include "db.php";
session_start();

if ($_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}

// Validar archivos
$archivos = $_FILES["archivos"] ?? null;
if (!$archivos || !isset($archivos["name"])) {
    die("No se recibieron archivos.");
}

// Obtener lista de RUCs
$rucs = [];
if (!empty($_POST["rucs"])) {
    $rucs = json_decode($_POST["rucs"], true);
}
$rucs = array_filter(array_unique($rucs));

if (count($rucs) === 0) {
    die("No se recibieron RUCs válidos.");
}

$total_subidos = 0;
$total_archivos = count($archivos["name"]);

// Procesar cada archivo
for ($i = 0; $i < $total_archivos; $i++) {
    if ($archivos["type"][$i] != "application/pdf") {
        continue;
    }

    $nombre_archivo = basename($archivos["name"][$i]);
    $tmp_path = $archivos["tmp_name"][$i];

    foreach ($rucs as $ruc) {
        $ruc = trim($ruc);

        // Obtener ID del usuario
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE ruc = ?");
        $stmt->bind_param("s", $ruc);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $user_id = $row["id"];

            // Crear carpeta
            $carpeta_usuario = "files/$ruc/";
            if (!file_exists($carpeta_usuario)) {
                mkdir($carpeta_usuario, 0777, true);
            }

            $nombre_final = time() . "_" . $nombre_archivo;
            $ruta_archivo = $carpeta_usuario . $nombre_final;

            // Copiar archivo
            if (copy($tmp_path, $ruta_archivo)) {
                $stmt = $conn->prepare("INSERT INTO documentos (user_id, archivo_nombre, ruta) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $nombre_archivo, $ruta_archivo);
                $stmt->execute();
                $total_subidos++;
            }
        }
    }
}

// Mensaje final
if ($total_subidos > 0) {
    echo "✅ Se subieron $total_subidos archivo(s) a los usuarios seleccionados.";
} else {
    echo "❌ No se pudo subir ningún archivo.";
}
?>
