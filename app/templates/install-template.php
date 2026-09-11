<?php
require_once '../required/auth.php';
require_once '../required/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $template_id = $_POST['template_id'] ?? '';
    $domain_id = $_POST['domain_id'] ?? '';
    $user_id = $_SESSION['user_id'];

    if (!$template_id || !$domain_id) {
        echo json_encode(['status' => 'error', 'message' => 'Template or Domain ID missing']);
        exit;
    }

    // Get template info
    $stmt = $conn->prepare("SELECT zip_file FROM templates WHERE id = ?");
    $stmt->bind_param("i", $template_id);
    $stmt->execute();
    $template = $stmt->get_result()->fetch_assoc();

    if (!$template) {
        echo json_encode(['status' => 'error', 'message' => 'Template not found']);
        exit;
    }

    // Get domain info
    $stmt = $conn->prepare("SELECT domain_url, api_path, token FROM domains WHERE id = ?");
    $stmt->bind_param("i", $domain_id);
    $stmt->execute();
    $domain = $stmt->get_result()->fetch_assoc();

    if (!$domain) {
        echo json_encode(['status' => 'error', 'message' => 'Domain not found']);
        exit;
    }

    // Prepare install URL (without target_folder)
   $template_url = "https://adhook.adtrackr.org/templates/" . $template['zip_file'];
    $install_url = rtrim($domain['api_path'], '/') . "?action=install_template&token={$domain['token']}&template_url=" . urlencode($template_url);
    // No `target_folder` means it will go into root directory

    $response = @file_get_contents($install_url);
    $result = json_decode($response, true);

    if (!$response || !isset($result['status'])) {
        echo json_encode(['status' => 'error', 'message' => 'No response from domain']);
        exit;
    }

    if ($result['status'] !== 'success') {
        echo json_encode(['status' => 'error', 'message' => $result['message'] ?? 'Installation failed']);
        exit;
    }

    // Save installed record
    $stmt = $conn->prepare("INSERT INTO domain_templates (user_id, domain_id, template_id, installed_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iii", $user_id, $domain_id, $template_id);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => '✅ Template installed successfully to root!']);
    exit;

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}
?>