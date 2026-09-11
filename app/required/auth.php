<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// If not logged in, redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: /a/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'client'; // default to client
$current_page = basename($_SERVER['PHP_SELF']);

// Full permission list
$permission_map = [
    'dashboard.php' => 'dashboard',
    'add-offer.php' => 'campaigns',
    'manage-offer.php' => 'campaigns',
    'add-domain.php' => 'domains',
    'manage-domains.php' => 'domains',
    'add-template.php' => 'templates',
    'manage-templates.php' => 'templates',
    'create-campaign.php' => 'offers',
    'manage-campaign.php' => 'offers',
    'campaigns-reports.php' => 'reports',
    'ip-report.php' => 'reports',
    'edit-offer-page.php' => 'offers_page_setup',
    'add-user.php' => 'users',
    'manage-users.php' => 'users',
    'assign-access.php' => 'users',
    'account.php' => 'account',
    'support.php' => 'support'
];

// Admin gets access to everything
if ($role !== 'admin') {
    $stmt = $conn->prepare("SELECT permission FROM user_permissions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row['permission'];
    }

    // Check if this page needs permission
    if (isset($permission_map[$current_page])) {
        $required = $permission_map[$current_page];
        if (!in_array($required, $permissions)) {
            die("❌ Access Denied: You don't have permission to view this page.");
        }
    }
}
?>
