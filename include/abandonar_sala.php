<?php
require_once(__DIR__ . "/../config/database.php");

session_start();
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

if (!isset($_GET['id_sala'])) {
    echo json_encode(["error" => "ID de sala no proporcionado"]);
    exit;
}

$db = new Database();
$conn = $db->conectar();
$id_sala = $_GET['id_sala'];
$id_usuario = $_SESSION["id_usuario"];

try {
    // Iniciar transacción
    $conn->beginTransaction();

    // Marcar al jugador como inactivo en la sala
    $query = "UPDATE sala_jugadores 
              SET estado = 'inactivo' 
              WHERE id_sala = :id_sala 
              AND id_jugador = :id_usuario";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_sala", $id_sala);
    $stmt->bindParam(":id_usuario", $id_usuario);
    $stmt->execute();

    // Eliminar el arma seleccionada
    $query = "DELETE FROM sala_armas 
              WHERE id_sala = :id_sala 
              AND id_jugador = :id_usuario";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_sala", $id_sala);
    $stmt->bindParam(":id_usuario", $id_usuario);
    $stmt->execute();

    // Verificar si quedan jugadores activos en la sala
    $query = "SELECT COUNT(*) as total 
              FROM sala_jugadores 
              WHERE id_sala = :id_sala 
              AND estado = 'activo'";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_sala", $id_sala);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no quedan jugadores, cerrar la sala
    if ($result['total'] == 0) {
        $query = "UPDATE sala 
                  SET estado = 'cerrada' 
                  WHERE id_sala = :id_sala";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id_sala", $id_sala);
        $stmt->execute();
    }

    // Confirmar transacción
    $conn->commit();
    
    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    // Revertir cambios si hay error
    $conn->rollBack();
    echo json_encode([
        "success" => false,
        "error" => "Error al abandonar la sala: " . $e->getMessage()
    ]);
}
?> 