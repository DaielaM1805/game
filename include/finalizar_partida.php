<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_partida'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Sesión no válida']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->conectar();

    // Obtener el motivo de finalización
    $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : 'desconocido';

    // Obtener el ganador (jugador con más vida)
    $query = "SELECT id_jugador, vida 
             FROM partida_jugadores 
             WHERE id_partida = :id_partida 
             ORDER BY vida DESC 
             LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_partida', $_SESSION['id_partida']);
    $stmt->execute();
    $ganador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Actualizar estado de la partida
    $stmt = $conn->prepare("UPDATE partidas SET id_estado = 6, id_ganador = :id_ganador WHERE id_partida = :id_partida");
    $stmt->bindParam(':id_partida', $_SESSION['id_partida']);
    $stmt->bindParam(':id_ganador', $ganador['id_jugador']);
    $stmt->execute();

    // Actualizar puntos del ganador
    if ($ganador) {
        $stmt = $conn->prepare("UPDATE usuario SET puntos = puntos + 100 WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $ganador['id_jugador']);
        $stmt->execute();
    }

    // Guardar el ganador en la sesión para mostrarlo en resultado.php
    $_SESSION['ganador'] = $ganador['id_jugador'];
    $_SESSION['motivo_fin'] = $motivo;

    echo json_encode([
        'success' => true,
        'mensaje' => 'Partida finalizada',
        'ganador' => $ganador['id_jugador']
    ]);

} catch (Exception $e) {
    error_log("Error en finalizar_partida.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'mensaje' => 'Error al finalizar la partida']);
}
?> 