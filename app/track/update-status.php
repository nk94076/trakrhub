<?php
require_once '../required/config.php';

$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? 0;

if ($id) {
    $stmt = $conn->prepare("UPDATE campaigns SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);
    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status.";
    }
    $stmt->close();
}
?>
