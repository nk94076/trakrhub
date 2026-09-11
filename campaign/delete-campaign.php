<?php
require_once '../required/config.php';
require_once '../required/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? 0;
    $user_id = $_SESSION['user_id'];

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID missing']);
        exit;
    }

    // Only delete if campaign belongs to the user
    $stmt = $conn->prepare("DELETE FROM campaigns WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not deleted']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
