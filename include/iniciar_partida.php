<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_sala'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$db = new Database();
$conn = $db->conectar();

try {
    // Iniciar transacción
    $conn->beginTransaction();

    // Verificar que la sala existe y tiene al menos 2 jugadores
    $query = "SELECT COUNT(*) as num_jugadores FROM sala_jugadores WHERE id_sala = :id_sala";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_sala', $_POST['id_sala']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['num_jugadores'] < 2) {
        throw new Exception('No hay suficientes jugadores para iniciar la partida');
    }

    // Crear nueva partida
    $query = "INSERT INTO partidas (id_sala, id_estado, tiempo_inicio, jugadores_min, jugadores_max) 
              VALUES (:id_sala, 5, NOW(), 2, 5)"; // estado 5 = en curso
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_sala', $_POST['id_sala']);
    $stmt->execute();
    $id_partida = $conn->lastInsertId();

    // Obtener todos los jugadores de la sala
    $query = "SELECT sj.id_jugador 
              FROM sala_jugadores sj 
              WHERE sj.id_sala = :id_sala";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_sala', $_POST['id_sala']);
    $stmt->execute();
    $jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agregar jugadores a la partida con vida inicial
    foreach ($jugadores as $jugador) {
        $query = "INSERT INTO partida_jugadores (id_partida, id_jugador, vida, estado) 
                  VALUES (:id_partida, :id_jugador, 100, 'vivo')";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_partida', $id_partida);
        $stmt->bindParam(':id_jugador', $jugador['id_jugador']);
        $stmt->execute();
    }

    // Actualizar estado de la sala a "en curso"
    $query = "UPDATE sala SET id_estado = 5 WHERE id_sala = :id_sala"; // estado 5 = en curso
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_sala', $_POST['id_sala']);
    $stmt->execute();

    // Guardar id_partida en la sesión
    $_SESSION['id_partida'] = $id_partida;

    // Confirmar transacción
    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollBack();
    error_log("Error en iniciar_partida.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>