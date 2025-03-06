<?php
session_start();
require_once('config/database.php');

$conex = new database();
$con = $conex->conectar();

if (!isset($_GET['token'])) {
    echo '<script>alert("Acceso no autorizado.");</script>';
    echo '<script>window.location = "../reset_password/recovery.php";</script>';
    exit;
}

$token = $_GET['token'];

// Buscar el usuario en la tabla `recu_contra` usando el token
$query = $con->prepare("
    SELECT u.id_usuario, u.email 
    FROM usuario u 
    JOIN recu_contra r ON u.id_usuario = r.id_usuario 
    WHERE r.token = ? AND r.expiracion_t > NOW()
");
$query->execute([$token]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo '<script>alert("El token es inválido o ha expirado.");</script>';
    echo '<script>window.location = "../reset_password/recovery.php";</script>';
    exit;
}

$id_usuario = $user['id_usuario'];
$email = $user['email'];

if (isset($_POST['submit'])) {
    $contra = $_POST['contra'];
    $contra2 = $_POST['contra2'];

    if (strlen($contra) < 6) {
        echo '<script>alert("La contraseña debe tener al menos 6 caracteres.");</script>';
    } elseif ($contra !== $contra2) {
        echo '<script>alert("Las contraseñas no coinciden.");</script>';
    } else {
        $hashedPassword = password_hash($contra, PASSWORD_DEFAULT, array("cost" => 12));

        // Actualizar la contraseña en la tabla `usuario`
        $update = $con->prepare("UPDATE usuario SET contra = ? WHERE id_usuario = ?");
        $update->execute([$hashedPassword, $id_usuario]);

        // Eliminar el token después de usarlo
        $deleteToken = $con->prepare("DELETE FROM recu_contra WHERE id_usuario = ?");
        $deleteToken->execute([$id_usuario]);

        if ($update->rowCount() > 0) {
            echo '<script>alert("Contraseña actualizada exitosamente.");</script>';
            echo '<script>window.location = "login.php";</script>';
        } else {
            echo '<script>alert("Error al actualizar la contraseña.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Kumite Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Restablecer Contraseña</h2>
        <form method="POST" action="" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Nueva Contraseña</label>
                <input type="password" id="contra" name="contra" class="form-control" placeholder="Ingresa la nueva contraseña" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" id="contra2" name="contra2" class="form-control" placeholder="Confirma la nueva contraseña" required>
            </div>
            <p class="text-danger" id="coincide" style="display: none;">¡Las contraseñas no coinciden!</p>
            <button name="submit" type="submit" class="btn btn-primary w-100">Confirmar</button>
        </form>
    </div>

    <script>
        document.getElementById("contra2").addEventListener("input", function() {
            const pass1 = document.getElementById("contra").value;
            const pass2 = this.value;
            const coincideMsg = document.getElementById("coincide");

            if (pass1 !== pass2) {
                coincideMsg.style.display = "block";
            } else {
                coincideMsg.style.display = "none";
            }
        });
    </script>
</body>
</html>
