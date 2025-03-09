<?php

    session_start();
    require_once('../config/database.php');
    // require_once('../include/inactivity.php');
    include('../include/validar_sesion.php');
    include 'menu.html';
    $conex=new database();
    $con=$conex->conectar();   
?>
<?php
$username=$_GET['id_usuario'];
$delete= $con->prepare("DELETE FROM usuario WHERE id_usuario ='$username'");
    $delete->execute();
    echo '<script>alert("Eliminado con exito");</script>';
    echo '<script>window.location="index.php"</script>';
    
?>