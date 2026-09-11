<?php
require_once '../required/auth.php';
require_once '../required/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id           = $_SESSION['user_id'] ?? 0;
    $campaign_name     = $_POST['campaign_name'] ?? '';
    $preview_url       = $_POST['preview_url'] ?? '';
    $affiliate_url     = $_POST['affiliate_url'] ?? '';
    $landing_url       = $_POST['landing_url'] ?? '';
    $description       = $_POST['description'] ?? '';
    $offer_type        = $_POST['offer_type'] ?? '';
    $daily_cap         = $_POST['daily_cap'] ?? 0;
    $total_cap         = $_POST['total_cap'] ?? 0;
    $notes             = $_POST['notes'] ?? '';
    $status            = isset($_POST['status']) ? 'active' : 'inactive';
    $referer_tracking  = $_POST['referer_tracking'] ?? 'track'; // new field

    // Simple validation
    if (!$campaign_name || !$preview_url || !$affiliate_url || !$offer_type) {
        echo "<script>alert('❌ Required fields missing.'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO campaigns 
        (user_id, campaign_name, preview_url, affiliate_url, landing_url, description, offer_type, daily_cap, total_cap, notes, status, referer_tracking)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("issssssddsss", 
        $user_id, $campaign_name, $preview_url, $affiliate_url, $landing_url, $description, $offer_type, 
        $daily_cap, $total_cap, $notes, $status, $referer_tracking
    );

    if ($stmt->execute()) {
        echo "<script>alert('✅ Campaign created successfully!'); window.location.href='manage-offers.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to create campaign.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('❌ Invalid request method.'); window.history.back();</script>";
}
?>
