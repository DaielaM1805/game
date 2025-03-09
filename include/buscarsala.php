<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Sesión no válida']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->conectar();
    
    $conn->beginTransaction();
    
    $id_jugador = $_SESSION['id_usuario'];
    $id_mundo = intval($_POST['id_mundo']);
    $id_arma = intval($_POST['id_arma']);
    
    error_log("Iniciando proceso para jugador: " . $id_jugador);
    
    // Limpiar datos antiguos del jugador
    $queries = [
        "DELETE FROM partida_jugadores WHERE id_jugador = :id_jugador",
        "DELETE FROM sala_jugadores WHERE id_jugador = :id_jugador",
        "DELETE FROM sala_armas WHERE id_jugador = :id_jugador"
    ];
    
    foreach ($queries as $query) {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_jugador', $id_jugador);
        $stmt->execute();
    }
    
    // Buscar sala disponible con el mismo mundo
    $stmt = $conn->prepare("
        SELECT s.id_sala 
        FROM sala s 
        LEFT JOIN sala_jugadores sj ON s.id_sala = sj.id_sala 
        WHERE s.id_mundo = :id_mundo 
        AND s.id_estado = 1 
        GROUP BY s.id_sala 
        HAVING COUNT(sj.id_jugador) < 5 
        LIMIT 1
    ");
    $stmt->bindParam(':id_mundo', $id_mundo);
    $stmt->execute();
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($sala) {
        $id_sala = $sala['id_sala'];
        error_log("Sala existente encontrada: " . $id_sala);
    } else {
        // Crear nueva sala si no hay disponibles
        $stmt = $conn->prepare("
            INSERT INTO sala (id_mundo, id_estado) 
            VALUES (:id_mundo, 1)
        ");
        $stmt->bindParam(':id_mundo', $id_mundo);
        $stmt->execute();
        $id_sala = $conn->lastInsertId();
        error_log("Nueva sala creada: " . $id_sala);
    }
    
    // Insertar jugador en la sala
    $stmt = $conn->prepare("
        INSERT INTO sala_jugadores (id_sala, id_jugador) 
        VALUES (:id_sala, :id_jugador)
    ");
    $stmt->bindParam(':id_sala', $id_sala);
    $stmt->bindParam(':id_jugador', $id_jugador);
    $stmt->execute();
    
    // Insertar arma seleccionada
    $stmt = $conn->prepare("
        INSERT INTO sala_armas (id_sala, id_jugador, id_arma) 
        VALUES (:id_sala, :id_jugador, :id_arma)
    ");
    $stmt->bindParam(':id_sala', $id_sala);
    $stmt->bindParam(':id_jugador', $id_jugador);
    $stmt->bindParam(':id_arma', $id_arma);
    $stmt->execute();
    
    $conn->commit();
    
    $_SESSION['id_sala'] = $id_sala;
    error_log("Proceso completado exitosamente para sala: " . $id_sala);
    
    echo json_encode([
        'success' => true,
        'mensaje' => 'Sala asignada correctamente',
        'redirect_url' => 'sala.php'
    ]);
    
} catch (Exception $e) {
    if ($conn) {
        $conn->rollBack();
    }
    error_log("Error en buscarsala.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error al crear sala: ' . $e->getMessage()
    ]);
}
?>