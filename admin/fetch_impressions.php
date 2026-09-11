<?php
include '../required/config.php';

$filter = $_GET['filter'] ?? 'today';
$data = ["today" => 0, "yesterday" => 0, "mtd" => 0];

// Debug Today Query
$today_query = "SELECT SUM(impression_count) AS total FROM impressions WHERE DATE(created_at) = CURDATE()";
$today_result = $conn->query($today_query);
$today_row = $today_result->fetch_assoc();
$data['today'] = $today_row['total'] ?? 0;

echo "Today Query: $today_query <br>";
echo "Today Result: " . json_encode($today_row) . "<br>";

// Debug Yesterday Query
$yesterday_query = "SELECT SUM(impression_count) AS total FROM impressions WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$yesterday_result = $conn->query($yesterday_query);
$yesterday_row = $yesterday_result->fetch_assoc();
$data['yesterday'] = $yesterday_row['total'] ?? 0;

echo "Yesterday Query: $yesterday_query <br>";
echo "Yesterday Result: " . json_encode($yesterday_row) . "<br>";

// Debug MTD Query
$mtd_query = "SELECT SUM(impression_count) AS total FROM impressions WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
$mtd_result = $conn->query($mtd_query);
$mtd_row = $mtd_result->fetch_assoc();
$data['mtd'] = $mtd_row['total'] ?? 0;

echo "MTD Query: $mtd_query <br>";
echo "MTD Result: " . json_encode($mtd_row) . "<br>";

echo json_encode($data);

?>
