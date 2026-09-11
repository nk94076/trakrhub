<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===============================
// click.php — Google-ready Redirect Tracker
include '../required/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "✅ click.php is working!";


$campaignId = isset($_GET['offer_id']) ? (int) $_GET['offer_id'] : 0;
$userId = isset($_GET['aff_id']) ? (int) $_GET['aff_id'] : 0;

if (!$campaignId || !$userId) {
    die("Missing or invalid parameters");
}

// Fetch campaign details
$stmt = $conn->prepare("SELECT * FROM campaigns WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ii", $campaignId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$campaign = $result->fetch_assoc();

if (!$campaign) {
    header("Location: https://app.trakrhub.com/error.html");
    exit;
}

// If inactive, redirect to safe_url
if ((int)$campaign['status'] === 0) {
    header("Location: " . $campaign['safe_url']);
    exit;
}

// Blocked parameters (comma-separated string from DB)
$blockedParams = array_filter(array_map('trim', explode(',', $campaign['blocked_params'])));

// All trackable Google UTM and ad params
$trackParams = [
    'gclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
    'fbclid', 'session_id', 'click_id', 'campaign_code', 'custom_event'
];

// Filter: remove only blocked ones
$filteredParams = array_filter($_GET, function ($key) use ($blockedParams) {
    return !in_array($key, ['offer_id', 'aff_id']) && !in_array($key, $blockedParams);
}, ARRAY_FILTER_USE_KEY);

// Build destination URL
$finalUrl = $campaign['main_url'];
if (!empty($filteredParams)) {
    $queryString = http_build_query($filteredParams);
    $finalUrl .= (parse_url($finalUrl, PHP_URL_QUERY) ? '&' : '?') . $queryString;
}

// Capture user environment
$ip         = $_SERVER['REMOTE_ADDR'] ?? '';
$userAgent  = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referrer   = $_SERVER['HTTP_REFERER'] ?? '';
$language   = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
$timestamp  = date('Y-m-d H:i:s');

// Prepare data for logging
$logStmt = $conn->prepare("INSERT INTO click_logs (
    campaign_id, user_id, ip_address, user_agent, referrer, language,
    gclid, fbclid, utm_source, utm_medium, utm_campaign, utm_term, utm_content,
    session_id, click_id, campaign_code, custom_event, timestamp
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$logStmt->bind_param(
    "iisssssssssssssssss",
    $campaignId,
    $userId,
    $ip,
    $userAgent,
    $referrer,
    $language,
    $_GET['gclid'] ?? '',
    $_GET['fbclid'] ?? '',
    $_GET['utm_source'] ?? '',
    $_GET['utm_medium'] ?? '',
    $_GET['utm_campaign'] ?? '',
    $_GET['utm_term'] ?? '',
    $_GET['utm_content'] ?? '',
    $_GET['session_id'] ?? '',
    $_GET['click_id'] ?? '',
    $_GET['campaign_code'] ?? '',
    $_GET['custom_event'] ?? '',
    $timestamp
);
$logStmt->execute();

// Fire Pixels if present
echo "<script>\n";
if (!empty($campaign['facebook_pixel'])) {
    echo <<<FB
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){
    n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{$campaign['facebook_pixel']}');
    fbq('track', 'PageView');
FB;
}

if (!empty($campaign['google_pixel'])) {
    echo <<<GT
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{$campaign['google_pixel']}');
GT;
}
echo "</script>\n";

// Delay to allow pixels to fire
sleep(1);

// Final redirect
header("Location: $finalUrl");
exit;
?>
