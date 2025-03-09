<?php
session_start();
require_once('../config/database.php');
include '../include/procesarlogin.php';

$conex = new database;
$con = $conex->conectar();

// 🔍 Verificar si hay sesión iniciada
if (!isset($fila['user_name']) || empty($fila['user_name'])) {
    die('<script>alert("No se proporcionó un usuario válido."); window.location="../index.php";</script>');
}

if (!isset($_SESSION['id_rol'])) {
    die('<script>alert("Error: No se puede determinar el rol del usuario."); window.location="admin.php";</script>');
}

$user_name = $_SESSION['user_name'];

// 📌 Obtener datos del usuario
$sql = $con->prepare("SELECT * FROM usuario 
                      INNER JOIN rol ON usuario.id_rol = rol.id_rol 
                      INNER JOIN estado ON usuario.id_estado = estado.id_estado
                      WHERE user_name = :user_name");

$sql->bindParam(':user_name', $user_name, PDO::PARAM_STR);
$sql->execute();
$fila = $sql->fetch(PDO::FETCH_ASSOC);

// 🛑 Si el usuario no existe, redirigir
if (!$fila) {
    die('<script>alert("El usuario no existe en la base de datos."); window.location="admin.php";</script>');
}

// // 🚫 Bloquear modificación del administrador
// if ($_SESSION['id_rol'] == 1) {
//     die('<script>alert("No puedes modificar los datos del administrador."); window.location="admin.php";</script>');
// }

// 📌 Procesar actualización del usuario
if (isset($_POST["actualizar"])) {
    $nuevo_user_name = $_POST["user_name"];
    $nuevo_rol = $_POST["id_rol"];
    $nuevo_estado = $_POST["id_estado"];
    $nuevo_email = $_POST["email"];
    $documento = $_POST["id_usuario"];

    $update = $con->prepare("UPDATE usuario 
                             SET user_name = :user_name, 
                                 id_rol = :id_rol, 
                                 id_estado = :id_estado,
                                 email = :email
                             WHERE id_usuario = :id_usuario");

    $update->bindParam(':user_name', $nuevo_user_name, PDO::PARAM_STR);
    $update->bindParam(':id_rol', $nuevo_rol, PDO::PARAM_INT);
    $update->bindParam(':id_estado', $nuevo_estado, PDO::PARAM_INT);
    $update->bindParam(':email', $nuevo_email, PDO::PARAM_STR);
    $update->bindParam(':id_usuario', $documento, PDO::PARAM_INT);

    if ($update->execute()) {
        echo '<script>alert("Actualización exitosa."); window.location="admin.php";</script>';
    } else {
        echo '<script>alert("Error en la actualización.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Usuario</title>
</head>
<body>
    <h2>Actualizar Usuario</h2>
    <form action="" method="post">
        <table border="2">
            <tr>
                <td>Nombre de usuario</td>
                <td>Email</td>
                <td>Rol</td>
                <td>Estado</td>
            </tr>
            <tr>
                <td><input name="user_name" value="<?php echo htmlspecialchars($fila['user_name']); ?>" readonly></td>
                <td><input name="email" value="<?php echo htmlspecialchars($fila['email']); ?>" required></td>

                <!-- Select Rol -->
                <td>
                    <select name="id_rol">
                        <option value="<?php echo $fila['id_rol']; ?>">Actualmente: <?php echo htmlspecialchars($fila['nom_rol']); ?></option>
                        <?php
                        $sql = $con->prepare("SELECT * FROM rol WHERE id_rol != 1"); // Evita seleccionar administrador
                        $sql->execute();
                        while ($fila1 = $sql->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $fila1['id_rol'] . "'>" . htmlspecialchars($fila1['nom_rol']) . "</option>";
                        }
                        ?>
                    </select>
                </td>

                <!-- Select Estado -->
                <td>
                    <select name="id_estado">
                        <option value="<?php echo $fila['id_estado']; ?>">Actualmente: <?php echo htmlspecialchars($fila['estado']); ?></option>
                        <?php
                        $sql = $con->prepare("SELECT * FROM estado");
                        $sql->execute();
                        while ($fila2 = $sql->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $fila2['id_estado'] . "'>" . htmlspecialchars($fila2['estado']) . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <br>
        <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($fila['id_usuario']); ?>">
        <input type="submit" value="Actualizar" name="actualizar">
    </form>
</body>
</html>
