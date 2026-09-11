<?php
require_once '../required/config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Prepare & execute delete
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ User deleted successfully.'); window.location.href='manage-users.php';</script>";
    } else {
        echo "<script>alert('❌ Error deleting user.'); window.location.href='manage-users.php';</script>";
    }
} else {
    echo "<script>alert('❌ Invalid user ID.'); window.location.href='manage-users.php';</script>";
}
?>
