<?php
require_once '../required/config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? 0;
$status = $data['status'] ?? '';

if ($id && in_array($status, ['active', 'inactive'])) {
    $stmt = $conn->prepare("UPDATE domains SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    echo json_encode(['success' => $stmt->execute()]);
} else {
    echo json_encode(['success' => false]);
}
?>
