<?php
require_once '../required/auth.php';
require_once '../required/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}

$id = (int)$_GET['id'];

// Delete from campaigns table
if (($_SESSION['role'] ?? '') === 'admin') {
    $stmt = $conn->prepare("DELETE FROM campaigns WHERE id = ?");
    $stmt->bind_param("i", $id);
} else {
    $stmt = $conn->prepare("DELETE FROM campaigns WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
}
if ($stmt->execute()) {
    header("Location: manage-link.php?msg=deleted");
    exit;
} else {
    echo "Error deleting record: " . $stmt->error;
}
?>
