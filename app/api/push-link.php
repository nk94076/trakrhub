<?php
include '../required/config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['domain_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing domain_id"]);
    exit;
}

$domain_id = (int)$data['domain_id'];

// 1. Get domain name
$res1 = mysqli_query($conn, "SELECT name FROM domains WHERE id = $domain_id");
$row1 = mysqli_fetch_assoc($res1);
$domainName = trim($row1['name']);

// 2. Get link from DB
$res2 = mysqli_query($conn, "SELECT affiliate_link FROM domain_links WHERE domain_id = $domain_id");
$row2 = mysqli_fetch_assoc($res2);
$link = trim($row2['affiliate_link']);

// 3. Prepare POST payload
$payload = json_encode(["affiliate_link" => $link]);

// 4. Push to remote domain
$ch = curl_init("https://$domainName/receive-link.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5. Output response
echo json_encode([
    "status" => "sent",
    "link" => $link,
    "domain" => $domainName,
    "response" => $response,
    "httpCode" => $httpCode,
    "error" => $error
]);
