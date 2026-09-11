<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
require_once '../required/auth.php';
require_once '../required/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => '❌ Invalid request method']);
    exit;
}

$template_name = $_POST['template_name'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$status = 'inactive';

// Validation
if (!$template_name || !$user_id) {
    echo json_encode(['status' => 'error', 'message' => '❌ Missing template name or user']);
    exit;
}

// Create directories if not exist
$image_dir = __DIR__ . '/Uploads/images/';
$zip_dir = __DIR__ . '/Uploads/offer-templates/';
if (!is_dir($image_dir)) mkdir($image_dir, 0777, true);
if (!is_dir($zip_dir)) mkdir($zip_dir, 0777, true);

// Upload Image
$image_path = '';
if (isset($_FILES['template_image']) && $_FILES['template_image']['error'] === 0) {
    $ext = pathinfo($_FILES['template_image']['name'], PATHINFO_EXTENSION);
    $image_name = uniqid('img_') . '.' . $ext;
    $image_target = $image_dir . $image_name;

    if (move_uploaded_file($_FILES['template_image']['tmp_name'], $image_target)) {
        $image_path = 'Uploads/images/' . $image_name;
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Failed to upload image']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '❌ Image file missing']);
    exit;
}

// Upload ZIP
$zip_path = '';
if (isset($_FILES['template_zip']) && $_FILES['template_zip']['error'] === 0) {
    $ext = pathinfo($_FILES['template_zip']['name'], PATHINFO_EXTENSION);
    if (strtolower($ext) !== 'zip') {
        echo json_encode(['status' => 'error', 'message' => '❌ Only ZIP files allowed']);
        exit;
    }

    $zip_name = uniqid('offer_') . '.zip';
    $zip_target = $zip_dir . $zip_name;

    if (move_uploaded_file($_FILES['template_zip']['tmp_name'], $zip_target)) {
        $zip_path = 'Uploads/offer-templates/' . $zip_name;
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Failed to upload ZIP']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '❌ ZIP file missing']);
    exit;
}

// Save to DB
$stmt = $conn->prepare("INSERT INTO offer_templates (user_id, name, image, zip_file, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("issss", $user_id, $template_name, $image_path, $zip_path, $status);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => '✅ Offer template uploaded successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => '❌ DB Error: ' . $stmt->error]);
}
