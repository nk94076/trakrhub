<?php
header('Content-Type: application/json');
require_once '../required/config.php';

$offer_key = $_GET['offer_key'] ?? '';
$gclid = $_GET['gclid'] ?? '';
$referer = $_GET['referer'] ?? '';
$ip = $_GET['ip'] ?? '';
$domain = $_GET['domain'] ?? '';
$user_agent = $_GET['user_agent'] ?? '';

// Validate
if (!$offer_key || !$ip || !$domain) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Get offer URL
$stmt = $conn->prepare("SELECT url FROM offers WHERE offer_key1 = ? LIMIT 1");
$stmt->bind_param("s", $offer_key);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Offer not found']);
    exit;
}
$offer = $result->fetch_assoc();

// Insert into clicks table
$stmt = $conn->prepare("INSERT INTO clicks (offer_key, gclid, ip, referer, domain, user_agent, clicked_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("ssssss", $offer_key, $gclid, $ip, $referer, $domain, $user_agent);
$stmt->execute();

echo json_encode([
    'status' => 'success',
    'message' => 'Click recorded',
    'redirect_url' => $offer['url']
]);
?>
