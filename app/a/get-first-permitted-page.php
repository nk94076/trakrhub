<?php
session_start();
require_once '../required/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? 'client';

$permission_to_page = [
    'dashboard'           => 'admin/dashboard.php',
    'domains'             => 'domain/manage-domains.php',
    'templates'           => 'templates/manage-templates.php',
    'offers'              => 'admin/manage-campaign.php',
    'reports'             => 'admin/campaigns-reports.php',
    'offers_page_setup'   => 'admin/edit-offer-page.php',
    'users'               => 'users/manage-users.php',
    'account'             => 'admin/account.php',
    'support'             => 'admin/support.php'
];

// ✅ If admin, go to dashboard directly
if ($role === 'admin') {
    header("Location: https://app.trakrhub.com/admin/dashboard.php", true, 303);
    exit;
}

// ✅ If client, check permissions
$stmt = $conn->prepare("SELECT permission FROM user_permissions WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$permissions = [];
while ($row = $result->fetch_assoc()) {
    $permissions[] = $row['permission'];
}

// ✅ Redirect to first accessible page
foreach ($permission_to_page as $key => $path) {
    if (in_array($key, $permissions)) {
        header("Location: https://app.trakrhub.com/admin/dashboard.php");
exit();

    }
}

// ❌ If no permissions found
header("Location: https://app.trakrhub.com//no-access.php");
exit;
?>
