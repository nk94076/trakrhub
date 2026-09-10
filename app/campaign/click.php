<?php
// === Step 1: Collect and Validate Parameters ===
$offer_id = isset($_GET['offer_id']) ? (int)$_GET['offer_id'] : 0;
$aff_id   = isset($_GET['aff_id']) ? (int)$_GET['aff_id'] : 0;
$gclid    = $_GET['gclid'] ?? null;
$fbclid   = $_GET['fbclid'] ?? null;
$sub1     = $_GET['sub1'] ?? null;
$ip       = $_SERVER['REMOTE_ADDR'];
$referrer = $_SERVER['HTTP_REFERER'] ?? '';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$click_time = date('Y-m-d H:i:s');

if (!$offer_id || !$aff_id) {
    die("❌ Missing required parameters.");
}

// === Step 2: Fetch Campaign ===
$stmt = $conn->prepare("SELECT affiliate_url FROM campaigns WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $offer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Invalid or inactive campaign.");
}
$campaign = $result->fetch_assoc();
$redirect_url = $campaign['affiliate_url'];

// === Step 3: Track Click ===
$stmt = $conn->prepare("INSERT INTO clicks (offer_id, aff_id, gclid, fbclid, sub1, ip_address, referrer, user_agent, click_time, click_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$click_type = ($gclid || $fbclid || $sub1) ? 'real' : 'blank';
$stmt->bind_param("iissssssss", $offer_id, $aff_id, $gclid, $fbclid, $sub1, $ip, $referrer, $user_agent, $click_time, $click_type);
$stmt->execute();

// === Step 4: Redirect ===
header("Location: $redirect_url");
exit();
?>
