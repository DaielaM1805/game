<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

if (!isset($_POST['id_sala']) || !isset($_POST['id_jugador'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$id_sala = $_POST['id_sala'];
$id_jugador = $_POST['id_jugador'];

try {
    // Obtener información de todos los jugadores en la sala
    $query = "SELECT sj.id_jugador, u.user_name, a.nom_arma, a.dano,
                     COALESCE(sj.vida, 100) as vida,
                     COALESCE(sj.pos_x, RAND() * 80 + 10) as pos_x,
                     COALESCE(sj.pos_y, RAND() * 80 + 10) as pos_y
              FROM sala_jugadores sj
              JOIN usuario u ON sj.id_jugador = u.id_usuario
              LEFT JOIN sala_armas sa ON sj.id_sala = sa.id_sala AND sj.id_jugador = sa.id_jugador
              LEFT JOIN arma a ON sa.id_arma = a.id_arma
              WHERE sj.id_sala = :id_sala";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_sala', $id_sala);
    $stmt->execute();
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener la vida del jugador actual
    $query = "SELECT COALESCE(vida, 100) as vida 
              FROM sala_jugadores 
              WHERE id_sala = :id_sala 
              AND id_jugador = :id_jugador";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_sala', $id_sala);
    $stmt->bindParam(':id_jugador', $id_jugador);
    $stmt->execute();
    $vida = $stmt->fetch(PDO::FETCH_ASSOC)['vida'];

    echo json_encode([
        'success' => true,
        'vida' => $vida,
        'jugadores' => $jugadores
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
} 