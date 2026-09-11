<?php
session_start();
require_once '../required/config.php';  // ✅ path correct if a/ is inside root

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';

// ✅ Admin user — always go to dashboard
if ($role === 'admin') {
    header("Location: https://app.trakrhub.com/admin/dashboard.php");
    exit;
}

// ✅ For client users
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

$stmt = $conn->prepare("SELECT permission FROM user_permissions WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$permissions = [];
while ($row = $result->fetch_assoc()) {
    $permissions[] = $row['permission'];
}

foreach ($permission_to_page as $key => $page) {
    if (in_array($key, $permissions)) {
        header("Location: https://app.trakrhub.com/$page");
        exit;
    }
}

// ❌ If no access
echo "❌ You don’t have access to any section. Contact admin.";
exit;
?>
