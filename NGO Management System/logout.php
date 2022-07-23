<?php
session_start();
unset($_SESSION["id"]);

header("Location:login.html");
session_destroy();
 
?>