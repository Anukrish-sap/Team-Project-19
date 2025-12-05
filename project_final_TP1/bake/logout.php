<?php
session_start();
session_unset();
session_destroy();
$_SESSION['logout'] = "You have logged out of your account. You are now browsing as a guest.";
header("Location: home.php"); 
exit(); 
?>
