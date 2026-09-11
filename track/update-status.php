<?php
require_once '../required/auth.php';
require_once '../required/config.php';

$id = (int) ($_POST['id'] ?? 0);
$status = in_array($_POST['status'] ?? '', ['active', 'pending', 'paused'], true) ? $_POST['status'] : null;

if ($id && $status !== null) {
    if (($_SESSION['role'] ?? '') === 'admin') {
        $stmt = $conn->prepare("UPDATE campaigns SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
    } else {
        $stmt = $conn->prepare("UPDATE campaigns SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $status, $id, $_SESSION['user_id']);
    }
    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status.";
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid request.";
}
?>
