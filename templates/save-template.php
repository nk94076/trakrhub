<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../required/auth.php';
require_once '../required/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $template_name = $_POST['template_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;
    $status = 'inactive';

    if (empty($template_name) || empty($category) || empty($user_id)) {
        echo json_encode(['status' => 'error', 'message' => '❌ Template Name, Category, or User ID missing']);
        exit;
    }

    // Create folders
    $imgDir = __DIR__ . '/Uploads/images/';
    $zipDir = __DIR__ . '/Uploads/themes/';
    if (!is_dir($imgDir)) mkdir($imgDir, 0777, true);
    if (!is_dir($zipDir)) mkdir($zipDir, 0777, true);

    // Image Upload
    $image_path = '';
    if (isset($_FILES['template_image']) && $_FILES['template_image']['error'] === 0) {
        $ext = pathinfo($_FILES['template_image']['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('img_') . '.' . $ext;
        $target_image_path = $imgDir . $image_name;

        if (move_uploaded_file($_FILES['template_image']['tmp_name'], $target_image_path)) {
            $image_path = 'Uploads/images/' . $image_name;
        } else {
            echo json_encode(['status' => 'error', 'message' => '❌ Image upload failed']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ No image selected']);
        exit;
    }

    // ZIP Upload
    $zip_path = '';
    if (isset($_FILES['template_zip']) && $_FILES['template_zip']['error'] === 0) {
        $ext = pathinfo($_FILES['template_zip']['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) !== 'zip') {
            echo json_encode(['status' => 'error', 'message' => '❌ Only ZIP files allowed']);
            exit;
        }

        $zip_name = uniqid('theme_') . '.' . $ext;
        $target_zip_path = $zipDir . $zip_name;

        if (move_uploaded_file($_FILES['template_zip']['tmp_name'], $target_zip_path)) {
            $zip_path = 'Uploads/themes/' . $zip_name;
        } else {
            echo json_encode(['status' => 'error', 'message' => '❌ ZIP upload failed']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ No ZIP file selected']);
        exit;
    }

    // DB Insert
    $stmt = $conn->prepare("INSERT INTO templates (user_id, category, name, image, zip_file, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssss", $user_id, $category, $template_name, $image_path, $zip_path, $status);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => '✅ Template uploaded successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ DB Error: ' . $stmt->error]);
    }
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => '❌ Invalid request']);
    exit;
}
?>
