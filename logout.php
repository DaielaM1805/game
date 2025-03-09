<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la cookie de sesión también
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Establecer una cookie para indicar que el usuario ha cerrado sesión
setcookie('logged_out', 'true', time() + 3600, '/');

// Redirigir al usuario a la página de inicio de sesión con un parámetro
header("Location: index.php?logout=true");
exit();
?>