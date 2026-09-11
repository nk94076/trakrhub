<?php
header('Content-Type: application/json');

// === Auto-detect current domain
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$my_domain = $protocol . $host;

// === Tracker API Config
$tracker_api_url = "https://adhook.adtrackr.org/api/fetch-token.php";
$domain_check_url = "https://adhook.adtrackr.org/api/check-domain-status.php";
$master_key = "8853662979Ma@";

// === Step 1: Fetch token from tracker
$token_url = $tracker_api_url . "?domain=" . urlencode($my_domain) . "&auth=" . $master_key;
$token_response = @file_get_contents($token_url);
$response_data = json_decode($token_response, true);
$fetched_token = $response_data['token'] ?? '';

// === Step 2: Match token
$sent_token = $_GET['token'] ?? '';
if ($sent_token !== $fetched_token) {
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "message" => "❌ Invalid token",
        "sent_token" => $sent_token,
        "fetched_token" => $fetched_token
    ]);
    exit;
}

// === Optional: Test endpoint
if ($_GET['action'] === 'test') {
    echo json_encode([
        "status" => "success",
        "message" => "✅ Token verified",
        "your_domain" => $my_domain
    ]);
    exit;
}

// === Step 3: Check if domain is active
$status_url = $domain_check_url . "?domain=" . urlencode($my_domain) . "&auth=" . $master_key;
$status_response = @file_get_contents($status_url);
$status_data = json_decode($status_response, true);
if (!$status_data || $status_data['status'] !== 'active') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "❌ Domain is not active or connected"]);
    exit;
}

// === Step 4: Handle installation
$action = $_GET['action'] ?? '';
$template_url = $_GET['template_url'] ?? '';

if (!filter_var($template_url, FILTER_VALIDATE_URL)) {
    echo json_encode(['status' => 'error', 'message' => '❌ Invalid or missing template URL']);
    exit;
}

// Determine ZIP file name and target folder
$zip_file = 'temp_template.zip';
$target_folder = '.';

if ($action === 'install_offer_template') {
    $target_folder = 'offers';
}

// === Step 5: Download ZIP
$ch = curl_init($template_url);
$fp = fopen($zip_file, 'w');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
fclose($fp);

if (!file_exists($zip_file) || $http_status !== 200) {
    echo json_encode(['status' => 'error', 'message' => "❌ Failed to download ZIP (HTTP $http_status)"]);
    exit;
}

// === Step 6: Clean target folder (if not root)
if ($target_folder !== '.' && is_dir($target_folder)) {
    function rrmdir($dir) {
        foreach (glob($dir . '/*') as $file) {
            is_dir($file) ? rrmdir($file) : unlink($file);
        }
        rmdir($dir);
    }
    rrmdir($target_folder);
}

// === Step 7: Create target folder if needed
if ($target_folder !== '.' && !is_dir($target_folder)) {
    mkdir($target_folder, 0755, true);
}

// === Step 8: Extract ZIP
$zip = new ZipArchive;
if ($zip->open($zip_file) === TRUE) {
    $zip->extractTo($target_folder);
    $zip->close();
    unlink($zip_file);
    echo json_encode([
        'status' => 'success',
        'message' => "✅ Template installed to " . ($target_folder === '.' ? 'root' : "'$target_folder'")
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => '❌ ZIP extraction failed']);
}
?>