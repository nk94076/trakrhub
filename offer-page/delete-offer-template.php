<?php
require_once '../required/auth.php';
require_once '../required/config.php';

if (!isset($_GET['id'])) {
    die("❌ Invalid request.");
}

$template_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

// Step 1: Get image & zip path
$stmt = $conn->prepare("SELECT image, zip_file FROM offer_templates WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $template_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $imagePath = '../uploads/images/' . basename($row['image']);
    $zipPath   = '../uploads/offer-template/' . basename($row['zip_file']);

    // Step 2: Delete files if exist
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
    if (file_exists($zipPath)) {
        unlink($zipPath);
    }

    // Step 3: Delete from DB
    $delete = $conn->prepare("DELETE FROM offer_templates WHERE id = ? AND user_id = ?");
    $delete->bind_param("ii", $template_id, $user_id);
    $delete->execute();
}

// Step 4: Redirect
header("Location: offer-template.php");
exit;
