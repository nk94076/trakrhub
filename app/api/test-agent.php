<?php
require_once '../required/config.php';

// Dummy/test values for testing purpose
$domain_id = $_GET['id'] ?? 1; // domain ID from URL
// Fetch token from DB
$stmt = $conn->prepare("SELECT api_path, token FROM domains WHERE id = ?");
$stmt->bind_param("i", $domain_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$api_path = $row['api_path'];
$token = $row['token'];

echo "Sending token: $token<br>";

// Now send it properly
$postData = http_build_query([
    'token' => $token
]);

$ch = curl_init($api_path);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

$response = curl_exec($ch);
curl_close($ch);

echo "<pre>$response</pre>";

?>
