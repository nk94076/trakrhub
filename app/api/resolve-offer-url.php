<?php
require_once '../required/config.php';

$offer_key = $_GET['offer_key'] ?? '';

if (!$offer_key) {
    http_response_code(400);
    exit("❌ Missing offer key");
}

// ✅ Support both offer_key1 and offer_key2
$stmt = $conn->prepare("SELECT url, url2, offer_key1, offer_key2 FROM offers WHERE offer_key1 = ? Or offer_key2 = ?");
$stmt->bind_param("ss", $offer_key, $offer_key);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    exit("❌ Offer not found");
}

$offer = $result->fetch_assoc();

// ✅ Return the correct URL based on which key matched
if ($offer['offer_key1'] === $offer_key) {
    echo $offer['url'];
} else {
    echo $offer['url2'];
}
?>
