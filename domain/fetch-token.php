<?php
require_once '../required/config.php';

$domain = $_GET['domain'] ?? '';

$stmt = $conn->prepare("SELECT token FROM domains WHERE domain_url LIKE CONCAT('%', ?, '%') AND status = 'active'");
$stmt->bind_param("s", $domain);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo $row['token'];
} else {
    echo '';
}
?>
