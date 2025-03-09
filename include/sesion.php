<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    
    echo '<script>alert("Credenciales incorrectas.")</script>';
    echo '<script>window.location = "../login.php"</script>';
    exit();
}

?>