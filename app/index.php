<?php
// Get the current domain dynamically
$currentDomain = $_SERVER['HTTP_HOST'];

// Define the target URL dynamically
$redirectUrl = "https://" . $currentDomain . "/a/login.php";

// Redirect the user
header("Location: $redirectUrl");
exit();
?>
