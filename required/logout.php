<?php
session_start();

// Remove session and cookie
session_unset();
session_destroy();

// Remove "Remember Me" cookie
setcookie("user_email", "", time() - 3600, "/"); 

// Redirect to login page
header("Location: login.php");
exit();
?>
