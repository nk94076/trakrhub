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
$check = mysqli_query($conn, "SELECT id FROM domain_links WHERE domain_id = $domain_id");
if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "UPDATE domain_links SET affiliate_link = '$affiliate_link', user_id = $user_id, updated_at = NOW() WHERE domain_id = $domain_id");
} else {
    mysqli_query($conn, "INSERT INTO domain_links (user_id, domain_id, affiliate_link, updated_at) VALUES ($user_id, $domain_id, '$affiliate_link', NOW())");
}

// Fetch domain from DB
$domainRes = mysqli_query($conn, "SELECT name FROM domains WHERE id = $domain_id");
$domainRow = mysqli_fetch_assoc($domainRes);
$domainName = trim($domainRow['name']);

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
