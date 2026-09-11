<?php
require '../required/config.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    die(json_encode(['error' => 'Access denied.']));
}

$user_id = (int) ($_GET['user_id'] ?? 0);
$stmt = $conn->prepare("SELECT permission FROM user_permissions WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$permissions = [];
while ($row = $result->fetch_assoc()) {
  $permissions[] = $row['permission'];
}
echo json_encode($permissions);
