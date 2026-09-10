<?php
session_start(); // Needed for session_destroy to work
session_unset();
session_destroy();
setcookie("user_email", "", time() - 3600, "/");
header("Location: https://app.trakrhub.com/a/login.php");
exit();
?>
