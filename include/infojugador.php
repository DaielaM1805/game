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
              WHERE u.id_usuario = :id_usuario";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        $avatarName = $resultado['avatar'];
        $rutaBase = dirname(__DIR__) . "/img/avatares/";
        $ruta_completa = $rutaBase . $avatarName;
        
        if (file_exists($ruta_completa)) {
            $resultado['avatar'] = $avatarName;
            $avatarEncontrado = true;
        } else {
            $resultado['error'] = "No se encontró la imagen del avatar seleccionado";
            $resultado['avatar'] = null;
            $resultado['debug'] = [
                'nombre_avatar' => $avatarName,
                'ruta_intentada' => $ruta_completa,
                'directorio_existe' => is_dir($rutaBase),
                'ruta_base' => $rutaBase
            ];
        }

        echo json_encode($resultado);
    } else {
        echo json_encode(["error" => "Usuario no encontrado"]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>