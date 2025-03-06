<?php
require_once('../config/database.php');
$conex = new database();
$con = $conex->conectar();
session_start();

$usuario_id = $_POST['usuario_id'];
$nuevo_estado = ($_POST['estado'] == 'on' || $_POST['estado'] == '1') ? '1' : '2'; // 1 = Activo, 2 = Inactivo

$sql = "UPDATE usuario SET id_estado = '$nuevo_estado' WHERE id_usuario = '$usuario_id'";
$con->exec($sql); // Ejecutar la consulta directamente

// Redirige o realiza otras acciones según sea necesario
header("Location: admin.php");
exit();
?>