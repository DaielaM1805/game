<?php
session_start();
require '../config/database.php';

$db = new Database();
$con = $db->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $contra = trim($_POST['contra']);

    $sql = "SELECT id_usuario, user_name, email, contra FROM usuario WHERE user_name = ? OR email = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$usuario, $usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($contra, $user['contra'])) {
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['user_name'] = $user['user_name'];

        echo json_encode(["status" => "success", "message" => "Inicio de sesión exitoso."]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Usuario o contraseña incorrectos."]);
        exit;
    }
}
?>
