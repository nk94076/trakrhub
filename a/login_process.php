<?php
session_start();
require_once '../required/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        die("❌ Email and password are required.");
    }

    $stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $db_email, $db_password, $role);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $db_email;
            $_SESSION['role'] = $role;

            // ✅ Redirect to role-based first page
            header("Location: https://app.trakrhub.com/a/redirect-user.php");
exit();
        } else {
            die("❌ Invalid password.");
        }
    } else {
        die("❌ No user found with that email.");
    }

    $stmt->close();
    $conn->close();
}
?>
