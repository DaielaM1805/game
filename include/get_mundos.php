<?php
require_once(__DIR__ . "/../config/database.php");

session_start();
if (!isset($_SESSION["id_usuario"])) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

$db = new Database();
$conn = $db->conectar();

try {
    header('Content-Type: application/json');
    
    // Obtener los mundos de la base de datos
    $query = "SELECT id_mundo, nom_mundo, img_mundo FROM mundos ORDER BY id_mundo ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $mundos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log detallado de cada mundo
    foreach ($mundos as $mundo) {
        error_log("Mundo encontrado - ID: {$mundo['id_mundo']}, Nombre: {$mundo['nom_mundo']}, Imagen: {$mundo['img_mundo']}");
    }
    
    // Asegurarnos de que las rutas sean correctas
    $mundos = array_map(function($mundo) {
        // Si la ruta no comienza con 'img/', agregarla
        if (!$mundo['img_mundo']) {
            error_log("ADVERTENCIA: Imagen vacía para el mundo ID: {$mundo['id_mundo']}");
            $mundo['img_mundo'] = 'img/mundos/default.png';
        } else if (strpos($mundo['img_mundo'], 'img/') !== 0) {
            error_log("Ajustando ruta de imagen para mundo ID {$mundo['id_mundo']}: {$mundo['img_mundo']} -> img/mundos/{$mundo['img_mundo']}");
            $mundo['img_mundo'] = 'img/mundos/' . $mundo['img_mundo'];
        }
        return $mundo;
    }, $mundos);
    
    // Log final de la respuesta
    error_log("Respuesta JSON final: " . json_encode($mundos));
    
    echo json_encode($mundos);
    
} catch (PDOException $e) {
    error_log("Error en get_mundos.php: " . $e->getMessage());
    echo json_encode(["error" => "Error al obtener los mundos: " . $e->getMessage()]);
}
?> 