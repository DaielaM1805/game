<?php
session_start();
unset($_SESSION['id_documento']);
unset($_SESSION['tipo']);
unset($_SESSION['estado']);
session_destroy();
session_write_close();

header("location: ../index.php") ;
?>