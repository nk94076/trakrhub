<?php
require_once '../required/config.php';
header('Content-Type: application/json');

define('MASTER_KEY', '8853662979Ma@');

$domain = $_GET['domain'] ?? '';
$auth = $_GET['auth'] ?? '';

if ($auth !== MASTER_KEY || !$domain) {
    echo json_encode(["status" => "error", "message" => "❌ Unauthorized"]);
    exit;
}

$domain = rtrim($domain, '/'); // clean

$stmt = $conn->prepare("SELECT status FROM domains WHERE TRIM(TRAILING '/' FROM domain_url) = ?");
$stmt->bind_param("s", $domain);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['status'] === 'active') {
    echo json_encode(["status" => "active"]);
} else {
    echo json_encode(["status" => "inactive"]);
}
?>
