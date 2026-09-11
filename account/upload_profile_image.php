<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
require_once '../required/auth.php';
include '../required/config.php';



if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$uploadDir = '../assets/images/user/';
$baseURL = 'https://app.trakrhub.com/assets/images/user/';

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowedExt, true) || getimagesize($_FILES['profile_image']['tmp_name']) === false) {
        http_response_code(400);
        exit("Invalid image file.");
    }

    $newName = 'user_' . $user_id . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . $newName;

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
        $imagePath = $baseURL . $newName;

        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $imagePath, $user_id);
        $stmt->execute();
        $stmt->close();

        echo "success";
    } else {
        http_response_code(500);
        echo "Error uploading file.";
    }
} else {
    http_response_code(400);
    echo "No image uploaded.";
}
?>
