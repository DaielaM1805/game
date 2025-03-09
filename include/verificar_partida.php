<?php
require_once(__DIR__ . "/../config/database.php");

session_start();
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["id_partida"])) {
    echo json_encode(["partida_lista" => false, "error" => "No hay sesión activa"]);
    exit;
}

try {
    $db = new Database();
    $conn = $db->conectar();
    
    // Verificar que la partida exista y esté en estado 'en curso' (5)
    $query = "SELECT p.id_partida, COUNT(pj.id_jugador) as num_jugadores
              FROM partidas p 
              JOIN partida_jugadores pj ON p.id_partida = pj.id_partida
              WHERE p.id_partida = :id_partida 
              AND p.id_estado = 5
              GROUP BY p.id_partida";
              
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_partida", $_SESSION["id_partida"]);
    $stmt->execute();
    
    $partida = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($partida && $partida['num_jugadores'] >= 2) {
        echo json_encode(["partida_lista" => true]);
    } else {
        echo json_encode(["partida_lista" => false, "error" => "La partida no está lista"]);
    }

} catch (PDOException $e) {
    error_log("Error en verificar_partida.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(["partida_lista" => false, "error" => "Error al verificar la partida"]);
}
?>
