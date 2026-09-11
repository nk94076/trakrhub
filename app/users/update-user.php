<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../required/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id        = $_POST['id'];
    $company   = $_POST['company'] ?? '';
    $username  = $_POST['username'] ?? '';
    $email     = $_POST['email'] ?? '';
    $phone     = $_POST['phone'] ?? '';
    $address   = $_POST['address'] ?? '';
    $city      = $_POST['city'] ?? '';
    $postal    = $_POST['postal_code'] ?? '';
    $role      = $_POST['role'] ?? 'client';
    $status    = isset($_POST['status']) ? 1 : 0;
    $about     = $_POST['about_me'] ?? '';
    $password  = $_POST['password'] ?? '';

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("UPDATE users SET company_name = ?, username = ?, email = ?, password = ?, phone = ?, address = ?, city = ?, postal_code = ?, role = ?, status = ?, about_me = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssssi", $company, $username, $email, $hashedPassword, $phone, $address, $city, $postal, $role, $status, $about, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET company_name = ?, username = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, role = ?, status = ?, about_me = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssi", $company, $username, $email, $phone, $address, $city, $postal, $role, $status, $about, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('✅ User updated successfully.'); window.location.href='manage-users.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating user: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

?>
