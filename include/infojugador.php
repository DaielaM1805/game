<?php
require_once(__DIR__ . "/../config/database.php");

$db = new Database();
$conn = $db->conectar();

session_start();
if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

try {
    $query = "SELECT u.user_name AS username, a.nom_avatar AS avatar 
              FROM usuario u 
              LEFT JOIN avatar a ON u.id_avatar = a.id_avatar 
              WHERE u.id_usuario  = :id_usuario";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        $avatarPath = "http://localhost/game/img/";
        $avatarName = $resultado['avatar']; // Nombre del avatar desde la base de datos
        $avatarExtensions = ['png', 'webp']; // Extensiones posibles

        // Verificar cuál de las extensiones existe en la carpeta /img/
        foreach ($avatarExtensions as $ext) {
            if (file_exists(__DIR__ . "/../img/" . $avatarName . "." . $ext)) {
                $resultado['avatar'] = $avatarPath . $avatarName . "." . $ext;
                break;
            }
        }

        echo json_encode($resultado);
    } else {
        echo json_encode(["error" => "Usuario no encontrado"]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
