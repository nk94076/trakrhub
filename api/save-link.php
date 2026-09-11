<?php
include '../required/config.php';
header('Content-Type: application/json');

$API_KEY = getenv('LINK_PUSH_API_KEY') ?: "";

// Validate method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Only POST allowed."]);
    exit;
}

// Handle Authorization headers (including NGINX fallback)
$headers = getallheaders();
if (!isset($headers['Authorization']) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $headers['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
}

// Validate token
if (!isset($headers['Authorization']) || $headers['Authorization'] !== "Bearer $API_KEY") {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Read and validate JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['domain_id'], $data['affiliate_link'], $data['user_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}

// Extract fields
$domain_id = (int)$data['domain_id'];
$user_id = (int)$data['user_id'];
$affiliate_link = trim($data['affiliate_link']);

// Save in DB
$check = $conn->prepare("SELECT id FROM domain_links WHERE domain_id = ?");
$check->bind_param("i", $domain_id);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    $upd = $conn->prepare("UPDATE domain_links SET affiliate_link = ?, user_id = ?, updated_at = NOW() WHERE domain_id = ?");
    $upd->bind_param("sii", $affiliate_link, $user_id, $domain_id);
    $upd->execute();
} else {
    $ins = $conn->prepare("INSERT INTO domain_links (user_id, domain_id, affiliate_link, updated_at) VALUES (?, ?, ?, NOW())");
    $ins->bind_param("iis", $user_id, $domain_id, $affiliate_link);
    $ins->execute();
}

// Fetch domain from DB
$domainStmt = $conn->prepare("SELECT name FROM domains WHERE id = ?");
$domainStmt->bind_param("i", $domain_id);
$domainStmt->execute();
$domainRow = $domainStmt->get_result()->fetch_assoc();
$domainName = trim($domainRow['name'] ?? '');

if ($domainName) {
    $remoteUrl = "https://$domainName/update-link.php";

    $postData = json_encode([
        'affiliate_link' => $affiliate_link
    ]);

    $ch = curl_init($remoteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $API_KEY",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Optional: Debug failed pushes
    if ($httpCode !== 200) {
        file_put_contents(__DIR__ . '/../logs/link_push_errors.log', "[$domainName] Failed: $httpCode - $response\n", FILE_APPEND);
    }
}

echo json_encode(["status" => "success", "message" => "Link saved and pushed."]);
