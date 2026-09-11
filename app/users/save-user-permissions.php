<?php
require_once '../required/config.php';
 require_once '../required/auth.php';

$user_id = $_GET['user_id'] ?? 0;
$data = json_decode(file_get_contents("php://input"), true);
$permissions = $data['permissions'] ?? [];

if ($user_id && is_array($permissions)) {
    // Delete old permissions
    $stmt = $conn->prepare("DELETE FROM user_permissions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Insert new permissions
    $stmt = $conn->prepare("INSERT INTO user_permissions (user_id, permission) VALUES (?, ?)");

    foreach ($permissions as $perm) {
        $stmt->bind_param("is", $user_id, $perm);
        $stmt->execute();
    }

    echo json_encode(["status" => "success", "message" => "Permissions updated"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
}
?>
