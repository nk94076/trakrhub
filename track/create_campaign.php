<?php
include '../required/auth.php';
include '../required/config.php';

// Sanitize input
function clean($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}

// Gather POST data
$campaign_name     = clean($conn, $_POST['campaign_name'] ?? '');
$description       = clean($conn, $_POST['description'] ?? '');
$category          = clean($conn, $_POST['category'] ?? '');
$main_url          = clean($conn, $_POST['url'] ?? '');
$safe_url          = clean($conn, $_POST['safe_url'] ?? '');
$google_pixel      = clean($conn, $_POST['google_pixel'] ?? '');
$facebook_pixel    = clean($conn, $_POST['facebook_pixel'] ?? '');
$note              = clean($conn, $_POST['note'] ?? '');
$kpi               = clean($conn, $_POST['kpi'] ?? '');
$terms_conditions  = clean($conn, $_POST['terms_conditions'] ?? '');
$require_tnc       = isset($_POST['require_tnc']) ? 1 : 0;
$redirect_type     = in_array($_POST['redirect_type'] ?? '', ['302', '302_hrf', '200', '200_hrf'], true) ? $_POST['redirect_type'] : '302';
$os                = in_array($_POST['os'] ?? '', ['all', 'windows', 'macos', 'linux', 'android', 'ios'], true) ? $_POST['os'] : 'all';
$allowedDevices    = ['desktop', 'mobile', 'tablet'];
$selectedDevices   = array_values(array_intersect($_POST['devices'] ?? [], $allowedDevices));
$devices           = empty($selectedDevices) || count($selectedDevices) === count($allowedDevices) ? 'all' : implode(',', $selectedDevices);
$status            = in_array($_POST['status'] ?? '', ['active', 'pending', 'paused'], true) ? $_POST['status'] : 'pending';
$user_id           = $_SESSION['user_id'] ?? 0;

// Collect all selected checkboxes for blocked params
$blocked_params_array = [];
$all_params = ['gclid', 'fbclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'session_id', 'click_id', 'campaign_code', 'custom_event', 'referrer', 'language', 'timestamp', 'ip_address', 'user_agent', 'browser', 'os', 'location'];

foreach ($all_params as $param) {
    if (isset($_POST[$param])) {
        $blocked_params_array[] = $param;
    }
}
$blocked_params = implode(',', $blocked_params_array);

// Validate mandatory fields
if (empty($campaign_name) || empty($main_url) || $user_id == 0) {
    die("Missing required fields.");
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO campaigns
    (user_id, campaign_name, description, main_url, safe_url, google_pixel, facebook_pixel, blocked_params, note, category, kpi, terms_conditions, require_tnc, devices, os, redirect_type, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "issssssssssssssss",
    $user_id, $campaign_name, $description, $main_url, $safe_url, $google_pixel, $facebook_pixel, $blocked_params, $note,
    $category, $kpi, $terms_conditions, $require_tnc, $devices, $os, $redirect_type, $status
);

if ($stmt->execute()) {
    $offer_id = $stmt->insert_id;
    $tracking_link = "https://app.trakrhub.com/click.php?aff_id=$user_id&offer_id=$offer_id";

    echo "<script>
        alert('✅ Campaign created successfully!\\n\\n🔗 Tracking Link:\\n$tracking_link');
        window.location.href = 'manage-link.php';
    </script>";
} else {
    echo "❌ Failed to save campaign: " . $stmt->error;
}
?>
