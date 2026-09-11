<?php
require_once '../required/auth.php';
include '../required/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_id       = (int) $_POST['campaign_id'];
    $campaign_name     = $_POST['campaign_name'];
    $description       = $_POST['description'] ?? '';
    $category          = $_POST['category'] ?? '';
    $main_url          = $_POST['url'];
    $safe_url          = $_POST['safe_url'];
    $google_pixel      = $_POST['google_pixel'] ?? '';
    $facebook_pixel    = $_POST['facebook_pixel'] ?? '';
    $note              = $_POST['note'] ?? '';
    $kpi               = $_POST['kpi'] ?? '';
    $terms_conditions  = $_POST['terms_conditions'] ?? '';
    $require_tnc       = isset($_POST['require_tnc']) ? 1 : 0;
    $redirect_type     = in_array($_POST['redirect_type'] ?? '', ['302', '302_hrf', '200', '200_hrf'], true) ? $_POST['redirect_type'] : '302';
    $os                = in_array($_POST['os'] ?? '', ['all', 'windows', 'macos', 'linux', 'android', 'ios'], true) ? $_POST['os'] : 'all';
    $allowedDevices    = ['desktop', 'mobile', 'tablet'];
    $selectedDevices   = array_values(array_intersect($_POST['devices'] ?? [], $allowedDevices));
    $devices           = empty($selectedDevices) || count($selectedDevices) === count($allowedDevices) ? 'all' : implode(',', $selectedDevices);
    $status            = in_array($_POST['status'] ?? '', ['active', 'pending', 'paused'], true) ? $_POST['status'] : 'pending';
    $blocked_params    = implode(',', $_POST['blocked_params'] ?? []);

    $sql = "UPDATE campaigns
            SET campaign_name = ?, description = ?, category = ?, main_url = ?, safe_url = ?, google_pixel = ?, facebook_pixel = ?,
                blocked_params = ?, note = ?, kpi = ?, terms_conditions = ?, require_tnc = ?, devices = ?, os = ?, redirect_type = ?, status = ?
            WHERE id = ?";

    $isAdmin = ($_SESSION['role'] ?? '') === 'admin';
    if (!$isAdmin) {
        $sql .= " AND user_id = ?";
    }

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $params = [
        $campaign_name, $description, $category, $main_url, $safe_url, $google_pixel, $facebook_pixel,
        $blocked_params, $note, $kpi, $terms_conditions, $require_tnc, $devices, $os, $redirect_type, $status,
        $campaign_id,
    ];
    $types = 'ssssssssssisssssi'; // 15 s/i for the SET columns (require_tnc is i) + i for WHERE id

    if (!$isAdmin) {
        $params[] = $_SESSION['user_id'];
        $types .= 'i';
    }

    $stmt->bind_param($types, ...$params);

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
