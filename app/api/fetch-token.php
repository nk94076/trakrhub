<?php
require_once '../required/config.php'; // ✅ Your DB config file

header('Content-Type: application/json');

// === Security ===
define('MASTER_KEY', '8853662979Ma@');

// === Auth Check ===
if (!isset($_GET['auth']) || $_GET['auth'] !== MASTER_KEY) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => '❌ Unauthorized']);
    exit;
}

// === Get & Clean Domain ===
$domain = $_GET['domain'] ?? '';
$cleanDomain = rtrim($domain, '/');

if (empty($cleanDomain)) {
    echo json_encode(['status' => 'error', 'message' => '❌ Missing domain']);
    exit;
}

// === Fetch from DB ===
$stmt = $conn->prepare("SELECT token FROM domains WHERE TRIM(TRAILING '/' FROM domain_url) = TRIM(TRAILING '/' FROM ?)");
$stmt->bind_param("s", $cleanDomain);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && !empty($row['token'])) {
    echo json_encode([
        'status' => 'success',
        'token' => $row['token']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Domain not found'
    ]);
}
?>
