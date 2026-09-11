<?php
require_once '../required/auth.php';
require_once '../required/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}

$id = (int)$_GET['id'];

// Delete from campaigns table
$stmt = $conn->prepare("DELETE FROM campaigns WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: manage-link.php?msg=deleted");
    exit;
} else {
    echo "Error deleting record: " . $stmt->error;
}
?>
