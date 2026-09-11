<?php
require_once '../required/config.php';
require_once '../required/auth.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch file names to delete from server
$stmt = $conn->prepare("SELECT image, zip_file FROM templates WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    // Build full paths for files
    $imagePath =  $row['image'];
    $zipPath =  $row['zip_file'];

    // Delete files if they exist
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    if (file_exists($zipPath)) {
        unlink($zipPath);
    }

    // Delete DB entry
    $del = $conn->prepare("DELETE FROM templates WHERE id = ? AND user_id = ?");
    $del->bind_param("ii", $id, $user_id);
    $del->execute();
}

header("Location: manage-templates.php");
exit;
?>
