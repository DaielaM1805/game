<?php
session_start();
require_once('config/database.php');
$conex = new database();
$con = $conex->conectar();

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo '<script>alert("Ningún dato puede estar vacío");</script>';
    } else {
        // Verificar si el usuario existe
        $sql = $con->prepare("SELECT * FROM usuario WHERE email = ?");
        $sql->execute([$email]);
        $fila = $sql->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            $_SESSION['email'] = $fila['email'];
            // Redirigir con los datos usando POST
            echo '<form id="sendForm" action="enviar_recuperacion.php" method="POST">
                      <input type="hidden" name="email" value="' . htmlspecialchars($email) . '">
                  </form>
                  <script>document.getElementById("sendForm").submit();</script>';
            exit;
        } else {
            echo '<script>alert("Correo incorrecto");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="css/styles.css">
</head> 
<body>
    <div class="container">
        <div class="login">
            <h2>¿Olvidaste tu contraseña?</h2>
            <p>No te preocupes, restableceremos tu contraseña, <br>
            solo dinos con qué dirección de e-mail te registraste <br>
            en el juego.</p>
            <form action="" method="POST" autocomplete="off">
                <label for="documentId">Correo electrónico</label>
                <input type="email" id="email" name="email" placeholder="Ingresa tu correo electrónico" required>

                <div class="buttons">
                    <a href="login.php"><button type="button" class="secondary-btn">Regresar</button></a>
                    <button type="submit" class="primary-btn" name="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 

