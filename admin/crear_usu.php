<?php
session_start();
require_once('../config/database.php');

$conex = new database;
$con = $conex->conectar();

if (isset($_POST['enviar'])) {   
    $doc = $_POST['id_usuario'];    
    $user_name = $_POST['user_name'];
    $passw = $_POST['contra'];
    $correo = $_POST['email'];
    $estado = $_POST['estado']; // ID del estado
    $rol = $_POST['rol'];       // ID del rol

    // Encriptar la contraseña
    $pasw_enc = password_hash($passw, PASSWORD_DEFAULT);

    // Verificar si el usuario ya existe
    $sql1 = $con->prepare("SELECT * FROM usuario WHERE id_usuario = :doc");
    $sql1->bindParam(":doc", $doc, PDO::PARAM_INT);
    $sql1->execute();
    $fila = $sql1->fetch(PDO::FETCH_ASSOC);

    if ($fila) {
        // Si el usuario ya existe, se actualizan sus datos
        $update = $con->prepare("UPDATE usuario 
                                 SET user_name = :user_name, 
                                     contra = :passw, 
                                     email = :correo, 
                                     id_estado = :estado, 
                                     id_rol = :rol
                                 WHERE id_usuario = :doc");
        $update->bindParam(":doc", $doc, PDO::PARAM_INT);
        $update->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $update->bindParam(":passw", $pasw_enc, PDO::PARAM_STR);
        $update->bindParam(":correo", $correo, PDO::PARAM_STR);
        $update->bindParam(":estado", $estado, PDO::PARAM_INT);
        $update->bindParam(":rol", $rol, PDO::PARAM_INT);

        if ($update->execute()) {
            echo '<script>alert("Usuario actualizado correctamente")</script>';
        } else {
            echo '<script>alert("Error al actualizar el usuario")</script>';
        }
    } else {
        // Insertar nuevo usuario
        $insert = $con->prepare("INSERT INTO usuario (id_usuario, user_name, contra, email, id_estado, id_rol) 
                                 VALUES (:doc, :user_name, :passw, :correo, :estado, :rol)");
        $insert->bindParam(":doc", $doc, PDO::PARAM_INT);
        $insert->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $insert->bindParam(":passw", $pasw_enc, PDO::PARAM_STR);
        $insert->bindParam(":correo", $correo, PDO::PARAM_STR);
        $insert->bindParam(":estado", $estado, PDO::PARAM_INT);
        $insert->bindParam(":rol", $rol, PDO::PARAM_INT);

        if ($insert->execute()) {
            echo '<script>alert("Registro insertado correctamente")</script>';
        } else {
            echo '<script>alert("Error al registrar el usuario")</script>';
        }
    }

    echo '<script>window.location = "../admin/admin.php"</script>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet">
    <title>Crear Usuario</title>
</head>
<body>
    <h1>Crear o actualizar un usuario</h1>
    <form action="" method="post">
        <!-- Documento -->
        <label for="id_usuario">Documento</label>
        <input type="number" name="id_usuario" id="id_usuario" required placeholder="Ingrese su documento">

        <!-- Username -->
        <label for="user_name">Nombre de usuario</label>
        <input type="text" name="user_name" id="user_name" required placeholder="Ingrese su username">

        <!-- Contraseña -->
        <label for="contra">Contraseña</label>
        <input type="password" name="contra" id="contra" required placeholder="Ingrese su contraseña">

        <!-- Correo -->
        <label for="email">Correo</label>
        <input type="email" name="email" id="email" required placeholder="Ingrese su correo">

        <!-- Estado -->
        <label for="estado">Estado</label>
        <select name="estado" id="estado" required>
            <option value="1" selected>Activo</option>
            <option value="0">Inactivo</option>
        </select>

        <!-- Rol -->
        <label for="rol">Rol</label>
        <select name="rol" id="rol" required>
            <option value="1">Administrador</option>
            <option value="2" selected>Usuario</option>
        </select>

        <!-- Botón de registro -->
        <input type="submit" name="enviar" value="Registrar" id="enviar">
    </form>
</body>
</html>
