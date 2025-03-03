<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Buscar una sala con menos de 5 jugadores
$sql = "SELECT id FROM salas WHERE jugadores < 5 ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $sala = $result->fetch_assoc();
    $sala_id = $sala['id'];
    $conn->query("UPDATE salas SET jugadores = jugadores + 1 WHERE id = $sala_id");
} else {
    // Crear nueva sala
    $conn->query("INSERT INTO salas (jugadores) VALUES (1)");
    $sala_id = $conn->insert_id;
}

// Asignar al usuario a la sala
$conn->query("UPDATE usuarios SET sala_id = $sala_id WHERE id = $user_id");

echo json_encode(['sala_id' => $sala_id]);

$conn->close();
?>