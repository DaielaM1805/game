<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_partida'])) {
    echo json_encode(['success' => false, 'error' => 'No hay una partida activa']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->conectar();

    // Obtener estado actual de todos los jugadores en la partida
    $query = "SELECT 
                pj.id_jugador,
                pj.vida,
                pj.estado,
                u.user_name,
                ta.dano
              FROM partida_jugadores pj
              JOIN usuario u ON pj.id_jugador = u.id_usuario
              JOIN partidas p ON pj.id_partida = p.id_partida
              JOIN sala s ON p.id_sala = s.id_sala
              JOIN sala_armas sa ON s.id_sala = sa.id_sala AND pj.id_jugador = sa.id_jugador
              JOIN arma a ON sa.id_arma = a.id_arma
              JOIN tipo_arma ta ON a.id_tipo = ta.id_tipo
              WHERE pj.id_partida = :id_partida";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_partida', $_SESSION['id_partida']);
    $stmt->execute();
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay algún jugador muerto
    $jugadoresMuertos = array_filter($jugadores, function($jugador) {
        return $jugador['estado'] === 'muerto';
    });

    // Si hay jugadores muertos, actualizar el estado de la partida
    if (!empty($jugadoresMuertos)) {
        // Obtener el jugador ganador (el que sigue vivo)
        $ganador = array_filter($jugadores, function($jugador) {
            return $jugador['estado'] === 'vivo';
        });
        $ganador = reset($ganador); // Obtener el primer (y único) jugador vivo

        // Actualizar estado de la partida a terminada (id_estado = 6)
        $query = "UPDATE partidas SET id_estado = 6 WHERE id_partida = :id_partida";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_partida', $_SESSION['id_partida']);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'jugadores' => $jugadores,
            'partida_terminada' => true,
            'ganador' => $ganador['user_name']
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'jugadores' => $jugadores,
            'partida_terminada' => false
        ]);
    }

} catch (Exception $e) {
    error_log("Error en get_estado_partida.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
