<?php
require 'PHPMAILER/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
require 'config/database.php'; // Conexión a la base de datos

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        echo '<script>alert("El correo no puede estar vacío");</script>';
        echo '<script>window.location = "recovery.php";</script>';
        exit;
    }

    // Conectar a la base de datos
    $conex = new database();
    $con = $conex->conectar();

    // Buscar el usuario por email y obtener su ID
    $stmt = $con->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo '<script>alert("Correo incorrecto");</script>';
        echo '<script>window.location = "recovery.php";</script>';
        exit;
    }

    $id_usuario = $user['id_usuario'];
    $token = bin2hex(random_bytes(50));
    $creacion_t = date("Y-m-d H:i:s");
    $expiracion_t = date("Y-m-d H:i:s", strtotime("+1 hour")); // Expira en 1 hora

    // Verificar si ya existe un registro de recuperación para este usuario
    $stmt = $con->prepare("SELECT id_recu FROM recu_contra WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Si existe, actualizar el token y la fecha de expiración
        $stmt = $con->prepare("UPDATE recu_contra SET token = ?, creacion_t = ?, expiracion_t = ? WHERE id_usuario = ?");
        $stmt->execute([$token, $creacion_t, $expiracion_t, $id_usuario]);
    } else {
        // Si no existe, insertar un nuevo registro
        $stmt = $con->prepare("INSERT INTO recu_contra (id_usuario, token, creacion_t, expiracion_t) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_usuario, $token, $creacion_t, $expiracion_t]);
    }

    // Configurar PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'freefirecorreo297@gmail.com';
        $mail->Password = 'kcdh ngap cigl ajvv'; // ⚠️ Mejor usa variables de entorno en lugar de exponer contraseñas.
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('freefirecorreo297@gmail.com', 'Soporte Free_Fire');
        $mail->addAddress($email);
        $mail->Subject = 'Recuperación de contraseña - Free_Fire';

        // Enlace de recuperación
        $reset_link = "http://localhost/free_fire/change.php?token=" . urlencode($token);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Body = "<h2>Recuperación de contraseña</h2>
                       <p>Hola, has solicitado recuperar tu contraseña.</p>
                       <p>Haz clic en el siguiente enlace para restablecerla:</p>
                       <p><a href='$reset_link' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer contraseña</a></p>
                       <p>Si no solicitaste este cambio, ignora este mensaje.</p>
                       <p>Este enlace expira en 1 hora.</p>";

        // Enviar correo
        $mail->send();
        echo '<script>alert("Revisa tu correo para restablecer la contraseña.");</script>';
        echo '<script>window.location = "login.php";</script>';
    } catch (Exception $e) {
        echo '<script>alert("Error al enviar el correo: ' . $mail->ErrorInfo . '");</script>';
    }
}
?>