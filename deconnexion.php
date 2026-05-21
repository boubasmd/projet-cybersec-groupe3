<?php
session_start();
session_destroy();
header("Location: interface_connexion.php");
exit();
?>