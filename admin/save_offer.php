<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../required/auth.php';
require_once '../required/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $domain_id = $_POST["domain_id"];

    // Handle image upload
    $image_path = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid("offer_") . "." . $ext;
        $upload_dir = "../upload/images/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $target = $upload_dir . $filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $image_path = "upload/images/" . $filename;
    }

    // Offer Keys
    $offer_key1 = md5(uniqid("OK1_", true));
    $offer_key2 = md5(uniqid("OK2_", true));

    // Get form data
    $name = $_POST["name"];
    $bonus_title = $_POST["bonus_title"];
    $button_text = $_POST["button_text"];
    $button_text2 = $_POST["button_text2"];
    $url = $_POST["url"];
    $url2 = $_POST["url2"];
    $status = isset($_POST["status"]) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO offers (user_id, domain_id, image, name, bonus_title, button_text, button_text2, url, url2, status, created_at, offer_key1, offer_key2) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("iissssssssis", $user_id, $domain_id, $image_path, $name, $bonus_title, $button_text, $button_text2, $url, $url2, $status, $offer_key1, $offer_key2);

    if ($stmt->execute()) {
        $_SESSION["success"] = "✅ Offer created successfully!";
    } else {
        $_SESSION["error"] = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    header("Location: manage-campaign.php");
    exit();
}
?>
