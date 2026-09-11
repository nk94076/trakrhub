<?php
require_once '../required/auth.php'; // ✅ handles session + login check
require_once '../required/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// rest of the code same...


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id     = $_POST['user_id'] ?? 0;
    $domain_name = trim($_POST['domain_name'] ?? '');
    $domain_url  = trim($_POST['domain_url'] ?? '');
    $api_path    = trim($_POST['api_path'] ?? '');
    $note        = trim($_POST['note'] ?? '');
    $status      = isset($_POST['status']) ? 'active' : 'inactive';
    $created_at  = date('Y-m-d H:i:s');
    $token       = md5(uniqid(rand(), true));

    // Validation
    if (empty($domain_name) || empty($domain_url) || empty($api_path)) {
        echo "<script>alert('❌ Please fill in all required fields.'); window.history.back();</script>";
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO domains (user_id, name, domain_url, api_path, token,note, status, created_at) VALUES (?, ?, ?,?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user_id, $domain_name, $domain_url, $api_path, $token,$note, $status, $created_at);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Domain added successfully!'); window.location.href = 'manage-domains.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to add domain.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request'); window.location.href = 'add-domain.php';</script>";
    exit;
}
?>
