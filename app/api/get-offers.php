<?php
header('Content-Type: application/json');
require_once '../required/config.php';

$domain = $_GET['domain'] ?? '';
$token = $_GET['token'] ?? '';
$gclid = $_GET['gclid'] ?? null;

if (!$domain) {
    echo json_encode(['status' => 'error', 'message' => '❌ Domain missing']);
    exit;
}

// === Auto fetch token if not sent
if (!$token) {
    $stmt = $conn->prepare("SELECT token FROM domains WHERE domain_url = ? AND status = 'active'");
    $stmt->bind_param("s", $domain);
    $stmt->execute();
    $token_result = $stmt->get_result();

    if ($token_result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => '❌ Domain not found or inactive']);
        exit;
    }

    $token_row = $token_result->fetch_assoc();
    $token = $token_row['token'];
    $stmt->close();
}

// === Now validate domain + token
$stmt = $conn->prepare("SELECT id FROM domains WHERE domain_url = ? AND token = ? AND status = 'active'");
$stmt->bind_param("ss", $domain, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => '❌ Invalid token for domain']);
    exit;
}

$domain_row = $result->fetch_assoc();
$domain_id = $domain_row['id'];

// === Fetch Offers
$offers = [];
$query = "SELECT id, name, image, bonus_title, button_text, button_text2, url, url2, offer_key1, offer_key2 
          FROM offers 
          WHERE domain_id = ? AND status = 1 
          ORDER BY position ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $domain_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $offers[] = $row;
}

echo json_encode([
    'status' => 'success',
    'domain' => $domain,
    'gclid' => $gclid,
    'offers' => $offers
]);
