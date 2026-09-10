<?php
include '../required/auth.php';
include '../required/config.php';

header('Content-Type: application/json');

// Optional: Check user role here if needed
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain_id = $_POST['id'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;

    if (!$domain_id || !$user_id) {
        echo json_encode(['status' => 'error', 'message' => '❌ Invalid request']);
        exit;
    }

    // ✅ Secure delete with user ownership check (optional)
    $stmt = $conn->prepare("DELETE FROM domains WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $domain_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => '✅ Domain deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Failed to delete']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => '❌ Invalid request method']);
}
?>
