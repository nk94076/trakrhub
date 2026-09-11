<?php
require_once '../required/config.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    die('Access denied.');
}

// Get all form values
$company   = $_POST['company'] ?? '';
$username  = $_POST['username'] ?? '';
$email     = $_POST['email'] ?? '';
$password  = $_POST['password'] ?? 'Default@123'; // fallback
$phone     = $_POST['phone'] ?? '';
$address   = $_POST['address'] ?? '';
$city      = $_POST['city'] ?? '';
$postal    = $_POST['postal_code'] ?? '';
$role      = $_POST['role'] ?? 'client';
$status    = isset($_POST['status']) ? 1 : 0;
$about     = $_POST['about_me'] ?? '';
$position  = 'Client'; // static for now

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Use username as name
$name = $username;

// Check if email or username already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$check->bind_param("ss", $email, $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>alert('❌ Email or Username already exists!'); window.history.back();</script>";
    exit;
}

// Insert query
$stmt = $conn->prepare("INSERT INTO users 
  (name, username, email, password, phone, address, city, postal_code, company_name, position, role, status, about_me) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssssssssis", $name, $username, $email, $hashedPassword, $phone, $address, $city, $postal, $company, $position, $role, $status, $about);

if ($stmt->execute()) {
    echo "<script>alert('✅ User created successfully!'); window.location.href='add-user.php';</script>";
} else {
    echo "<script>alert('❌ Error: " . $stmt->error . "');</script>";
}
?>
