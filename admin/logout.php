<?php
require_once '../required/auth.php';

// Destroy session
session_unset();
session_destroy();

// Remove cookie
setcookie("user_email", "", time() - 3600, "/"); 

// ✅ Correct redirect
header("Location: https://adhook.adtrackr.org/a/login.php");
exit();
?>
