<?php
session_start();
session_unset();
session_destroy();
unset($_SESSION["admin"]);
unset($_SESSION["user"]);
unset($_SESSION["id"]);
unset($_SESSION["password"]);
header("location: user_login.php");
exit();
?>
