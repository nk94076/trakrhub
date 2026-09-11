<?php
require_once '../required/auth.php';
include '../required/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_id     = $_POST['campaign_id'];
    $campaign_name   = $_POST['campaign_name'];
    $main_url        = $_POST['url'];
    $safe_url        = $_POST['safe_url'];
    $google_pixel    = $_POST['google_pixel'] ?? '';
    $facebook_pixel  = $_POST['facebook_pixel'] ?? '';
    $note            = $_POST['note'] ?? '';
    $status          = isset($_POST['status']) ? 1 : 0;
    $blocked_params  = isset($_POST['blocked_params']) ? json_encode($_POST['blocked_params']) : '[]';

    $sql = "UPDATE campaigns
            SET campaign_name = ?, main_url = ?, safe_url = ?, google_pixel = ?, facebook_pixel = ?, blocked_params = ?, note = ?, status = ?
            WHERE id = ?";

    if (($_SESSION['role'] ?? '') !== 'admin') {
        $sql .= " AND user_id = ?";
    }

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        $stmt->bind_param("sssssssiii",
            $campaign_name,
            $main_url,
            $safe_url,
            $google_pixel,
            $facebook_pixel,
            $blocked_params,
            $note,
            $status,
            $campaign_id,
            $_SESSION['user_id']
        );
    } else {
        $stmt->bind_param("sssssssii",
            $campaign_name,
            $main_url,
            $safe_url,
            $google_pixel,
            $facebook_pixel,
            $blocked_params,
            $note,
            $status,
            $campaign_id
        );
    }

    if ($stmt->execute()) {
        echo "<script>alert('✅ Campaign updated successfully!'); window.location.href='manage-link.php';</script>";
    } else {
        echo "❌ Update failed: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "❌ Invalid request method.";
}
?>
