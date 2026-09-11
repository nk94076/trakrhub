<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB connection
require_once 'required/config.php';

// Get offer & affiliate IDs
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
$stmt->close();

if (!$campaign) {
    die("Invalid or expired tracking link.");
}

// Only active campaigns redirect live traffic; pending/paused go to the safe URL
if (($campaign['status'] ?? 'paused') !== 'active') {
    header("Location: " . $campaign['safe_url']);
    exit;
}

// Handle blocked parameters
$blockedParams = array_filter(array_map('trim', explode(',', $campaign['blocked_params'] ?? '')));
$filteredParams = array_filter($_GET, function ($key) use ($blockedParams) {
    return !in_array($key, ['offer_id', 'aff_id']) && !in_array($key, $blockedParams);
}, ARRAY_FILTER_USE_KEY);

// Build redirect URL — a "custom" redirect type sends traffic to a
// pre-lander/blog post about the offer instead of straight to the main URL.
$finalUrl = ($campaign['redirect_type'] ?? '302') === 'custom' && !empty($campaign['custom_url'])
    ? $campaign['custom_url']
    : $campaign['main_url'];
if (!empty($filteredParams)) {
    $queryString = http_build_query($filteredParams);
    $finalUrl .= (parse_url($finalUrl, PHP_URL_QUERY) ? '&' : '?') . $queryString;
}

// Capture environment data
$ip         = $_SERVER['REMOTE_ADDR'] ?? '';
$userAgent  = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referrer   = $_SERVER['HTTP_REFERER'] ?? '';
$language   = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
$timestamp  = date('Y-m-d H:i:s');

function getOS($userAgent) {
    $osArray = [
        // Android and iOS checked first: their user agents also contain
        // "Linux"/"Mac OS X" respectively, which would otherwise shadow them.
        '/android/i' => 'Android',
        '/iphone|ipad|ipod/i' => 'iPhone',
        '/windows nt 10/i' => 'Windows 10',
        '/macintosh|mac os x/i' => 'Mac OS',
        '/linux/i' => 'Linux',
    ];
    foreach ($osArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) return $value;
    }
    return 'Unknown';
}

function getBrowser($userAgent) {
    $browsers = [
        '/chrome/i' => 'Chrome',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/msie|trident/i' => 'Internet Explorer',
    ];
    foreach ($browsers as $regex => $browser) {
        if (preg_match($regex, $userAgent)) return $browser;
    }
    return 'Unknown';
}

function getDevice($userAgent) {
    if (preg_match('/mobile/i', $userAgent)) return 'Mobile';
    elseif (preg_match('/tablet/i', $userAgent)) return 'Tablet';
    else return 'Desktop';
}

$os      = getOS($userAgent);
$browser = getBrowser($userAgent);
$device  = getDevice($userAgent);

function isBot($agent) {
    return preg_match('/bot|crawl|slurp|spider|mediapartners/i', $agent);
}
$isBot = isBot($userAgent) ? 1 : 0;
$realClick = $isBot ? 0 : 1;

$location = '';
$geo = @json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
if ($geo && isset($geo->city)) {
    $location = $geo->city . ', ' . ($geo->region ?? '') . ', ' . ($geo->country ?? '');
}

// Assign tracking params (only if not blocked)
function safeParam($key, $blockedParams) {
    return in_array($key, $blockedParams) ? '' : ($_GET[$key] ?? '');
}

$gclid         = safeParam('gclid', $blockedParams);
$fbclid        = safeParam('fbclid', $blockedParams);
$utm_source    = safeParam('utm_source', $blockedParams);
$utm_medium    = safeParam('utm_medium', $blockedParams);
$utm_campaign  = safeParam('utm_campaign', $blockedParams);
$utm_term      = safeParam('utm_term', $blockedParams);
$utm_content   = safeParam('utm_content', $blockedParams);
$session_id    = safeParam('session_id', $blockedParams);
$click_id      = safeParam('click_id', $blockedParams);
$campaign_code = safeParam('campaign_code', $blockedParams);
$custom_event  = safeParam('custom_event', $blockedParams);

$logStmt = $conn->prepare("INSERT INTO click_logs (
    campaign_id, user_id, ip_address, user_agent, referrer, language,
    gclid, fbclid, utm_source, utm_medium, utm_campaign, utm_term, utm_content,
    session_id, click_id, campaign_code, custom_event, timestamp,
    browser, os, device_type, location, is_bot, is_real
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$logStmt->bind_param(
    "iissssssssssssssssssssii",
    $campaignId, $userId, $ip, $userAgent, $referrer, $language,
    $gclid, $fbclid, $utm_source, $utm_medium, $utm_campaign, $utm_term, $utm_content,
    $session_id, $click_id, $campaign_code, $custom_event, $timestamp,
    $browser, $os, $device, $location, $isBot, $realClick
);

$logStmt->execute();
$insertedId = $conn->insert_id; // ← CHANGE 1: log_id capture kiya
$logStmt->close();

// Device/OS targeting: mismatched traffic is logged above but sent to the safe URL
$allowedDevices = ($campaign['devices'] ?? 'all') === 'all' ? null : explode(',', $campaign['devices']);
if ($allowedDevices !== null && !in_array(strtolower($device), $allowedDevices, true)) {
    header("Location: " . $campaign['safe_url']);
    exit;
}

$osTargetMap = ['Windows 10' => 'windows', 'Mac OS' => 'macos', 'Linux' => 'linux', 'Android' => 'android', 'iPhone' => 'ios'];
$allowedOs = $campaign['os'] ?? 'all';
if ($allowedOs !== 'all' && ($osTargetMap[$os] ?? null) !== $allowedOs) {
    header("Location: " . $campaign['safe_url']);
    exit;
}

// ← CHANGE 2: landing page tracking ke liye adtrackr_lid append karo
$finalUrl .= (parse_url($finalUrl, PHP_URL_QUERY) ? '&' : '?') . 'adtrackr_lid=' . $insertedId;

$has_pixel = !empty($campaign['facebook_pixel']) || !empty($campaign['google_pixel']);

if ($has_pixel) {
    $fbPixelJs = json_encode((string) $campaign['facebook_pixel']);
    $gaPixelJs = json_encode((string) $campaign['google_pixel']);
    $finalUrlJs = json_encode($finalUrl);

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
fbq('init', {$fbPixelJs});
fbq('track', 'PageView');
FB;
    }
    if (!empty($campaign['google_pixel'])) {
        echo <<<GT
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', {$gaPixelJs});
GT;
    }
    echo "setTimeout(function(){ window.location.href = {$finalUrlJs}; }, 1000);";
    echo "</script>";
    exit;
} else {
    $redirectType = $campaign['redirect_type'] ?? '302';

    if (in_array($redirectType, ['302_hrf', '200_hrf'], true)) {
        header('Referrer-Policy: no-referrer');
    }

    if ($redirectType === '200' || $redirectType === '200_hrf') {
        http_response_code(200);
        echo "<script>window.location.href = " . json_encode($finalUrl) . ";</script>";
    } else {
        header("Location: $finalUrl", true, 302);
    }
    exit;
}
?>