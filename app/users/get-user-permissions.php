<?php
require '../required/config.php';
$user_id = $_GET['user_id'];
$stmt = $conn->prepare("SELECT permission FROM user_permissions WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$permissions = [];
while ($row = $result->fetch_assoc()) {
  $permissions[] = $row['permission'];
}
echo json_encode($permissions);
