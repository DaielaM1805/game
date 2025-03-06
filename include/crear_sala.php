<?php
session_start();
require_once(__DIR__ . "/../config/database.php");

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];
$db = new Database();
$conn = $db->conectar();

try {
    // 1️⃣ Buscar una sala con menos de 5 jugadores
    $stmt = $conn->prepare("SELECT s.id_sala 
                            FROM salas s 
                            LEFT JOIN sala_jugadores sj ON s.id_sala = sj.id_sala 
                            GROUP BY s.id_sala 
                            HAVING COUNT(sj.id_jugador) < 5 
                            LIMIT 1");
    $stmt->execute();
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sala) {
        $id_sala = $sala['id_sala'];
    } else {
        // 2️⃣ Crear una nueva sala si no hay disponible
        $stmt = $conn->prepare("INSERT INTO salas () VALUES ()");
        $stmt->execute();
        $id_sala = $conn->lastInsertId();
    }

    // 3️⃣ Agregar al usuario a la sala
    $stmt = $conn->prepare("INSERT INTO sala_jugadores (id_sala, id_jugador) VALUES (:id_sala, :id_jugador)");
    $stmt->bindParam(":id_sala", $id_sala, PDO::PARAM_INT);
    $stmt->bindParam(":id_jugador", $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true, "id_sala" => $id_sala]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
