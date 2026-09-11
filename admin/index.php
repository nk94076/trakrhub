<?php
session_start();

// Agar already logged in hai
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Agar not logged in, send to login page
header("Location: https://" . $_SERVER['HTTP_HOST'] . "/a/login.php");
exit();
