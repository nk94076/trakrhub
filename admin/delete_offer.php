<?php
include '../required/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $offerId = intval($_POST['id']); // Prevent SQL injection

    $query = "DELETE FROM offers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $offerId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "invalid_request";
}
?>
