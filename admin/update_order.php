<?php
require_once '../required/auth.php';
include '../required/config.php';

if (isset($_POST['order']) && is_array($_POST['order'])) {
    foreach ($_POST['order'] as $position => $id) {
        $stmt = $conn->prepare("UPDATE offers SET position = ? WHERE id = ?");
        $stmt->bind_param("ii", $position, $id);
        $stmt->execute();
    }
    echo "success";
} else {
    echo "error";
}
?>
