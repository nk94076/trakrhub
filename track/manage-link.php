<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();  // Output buffering start
require_once '../required/auth.php';
include '../required/config.php';

// Get filter values from URL or set defaults
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date   = $_GET['end_date'] ?? date('Y-m-d');
$search     = trim($_GET['search'] ?? '');

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$conditions = ["DATE(c.created_at) BETWEEN ? AND ?"];
$params = [$start_date, $end_date];
$types = 'ss';

if (!$isAdmin) {
    $conditions[] = 'c.user_id = ?';
    $params[] = $_SESSION['user_id'];
    $types .= 'i';
}
if ($search !== '') {
    $conditions[] = '(c.campaign_name LIKE ? OR c.category LIKE ?)';
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

$sql = "SELECT
            c.id, c.campaign_name, c.category, c.status, c.devices, c.os, c.redirect_type,
            c.main_url, c.safe_url, c.user_id, c.created_at,
            SUM(cl.is_real = 1) AS real_clicks,
            SUM(cl.is_real = 0) AS blank_clicks
        FROM campaigns c
        LEFT JOIN click_logs cl ON cl.campaign_id = c.id
        WHERE " . implode(' AND ', $conditions) . "
        GROUP BY c.id
        ORDER BY c.id DESC";

$campaignsStmt = $conn->prepare($sql);
$campaignsStmt->bind_param($types, ...$params);
$campaignsStmt->execute();
// Fetched into a plain array (not a mysqli_result) so it survives the header/side-bar
// includes below, which reuse $stmt/$result for their own queries in this same scope.
$campaigns = $campaignsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../required/head.php'; ?>
    <style>
        .page-wrapper .page-body-wrapper .page-title {
            padding: 0;
            margin: 0 -27px 28px;
        }
        svg {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <?php include '../required/loader.php'; ?>
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <?php include '../required/header.php'; ?>
        <div class="page-body-wrapper">
            <?php include '../required/side-bar.php'; ?>
            <div class="page-body">
                <div class="container-fluid">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-sm-6"></div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid dashboard-10">

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="mb-1">Manage Campaigns</h3>
                            <p class="text-muted mb-0">View, search and manage all your tracking campaigns.</p>
                        </div>
                        <a href="create-link.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Create Campaign</a>
                    </div>

                    <!-- Filter card -->
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Search</label>
                                        <input type="text" name="search" class="form-control" placeholder="Search campaign name or category..." value="<?= htmlspecialchars($search) ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">Search</button>
                                        <a href="manage-link.php" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Campaigns table -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-gear me-1"></i>Bulk Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item bulk-action" href="javascript:void(0)" data-action="active">Set Active</a></li>
                                    <li><a class="dropdown-item bulk-action" href="javascript:void(0)" data-action="paused">Set Paused</a></li>
                                    <li><a class="dropdown-item bulk-action" href="javascript:void(0)" data-action="pending">Set Pending</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item bulk-action text-danger" href="javascript:void(0)" data-action="delete">Delete</a></li>
                                </ul>
                            </div>
                            <span class="text-muted small"><?= count($campaigns) ?> campaign<?= count($campaigns) === 1 ? '' : 's' ?></span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="display table-striped border" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>ID</th>
                                            <th>Campaign Name</th>
                                            <th>Status</th>
                                            <th>Category</th>
                                            <th>Devices</th>
                                            <th>OS</th>
                                            <th>Redirect Type</th>
                                            <th>Real Clicks</th>
                                            <th>Blank Clicks</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $statusBadge = ['active' => 'success', 'pending' => 'warning', 'paused' => 'secondary'];
                                    foreach ($campaigns as $row) {
                                        $campaignId = (int) $row['id'];
                                        $statusVal = $row['status'];
                                        $badgeClass = $statusBadge[$statusVal] ?? 'secondary';
                                        echo "<tr>
                                                <td><input type='checkbox' class='row-check' value='{$campaignId}'></td>
                                                <td>{$campaignId}</td>
                                                <td><a href='view-link.php?id={$campaignId}'>" . htmlspecialchars($row['campaign_name']) . "</a></td>
                                                <td><span class='badge bg-{$badgeClass}'>" . htmlspecialchars(ucfirst($statusVal)) . "</span></td>
                                                <td>" . htmlspecialchars($row['category'] ?? '') . "</td>
                                                <td>" . htmlspecialchars($row['devices'] ?? 'all') . "</td>
                                                <td>" . htmlspecialchars($row['os'] ?? 'all') . "</td>
                                                <td>" . htmlspecialchars($row['redirect_type'] ?? '302') . "</td>
                                                <td>" . (int) ($row['real_clicks'] ?? 0) . "</td>
                                                <td>" . (int) ($row['blank_clicks'] ?? 0) . "</td>
                                                <td>
                                                    <ul class='action'>
                                                        <li class='edit'><a href='view-link.php?id={$campaignId}' title='View'><i class='fa-solid fa-eye'></i></a></li>
                                                        <li class='edit-campaign'><a href='edit-link.php?id={$campaignId}' title='Edit'><i class='icon-pencil-alt'></i></a></li>
                                                        <li class='delete'><a href='javascript:void(0);' class='delete-offer' data-id='{$campaignId}' title='Delete'><i class='fa-solid fa-trash-can'></i></a></li>
                                                    </ul>
                                                </td>
                                            </tr>";
                                    }
                                    if (empty($campaigns)) {
                                        echo "<tr><td colspan='11' class='text-center text-muted py-4'>No campaigns found for the selected filters.</td></tr>";
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 footer-copyright text-center">
                            <p class="mb-0">Copyright <span class="year-update"></span> © Adtrackr</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <?php include '../required/footerjs.php'; ?>
    <script>
document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-offer").forEach(function (button) {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            if (confirm("Do you want to delete this campaign?")) {
                window.location.href = `delete-link.php?id=${id}`;
            }
        });
    });

    document.querySelectorAll(".bulk-action").forEach(function (link) {
        link.addEventListener("click", async function () {
            const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
            if (ids.length === 0) {
                alert("Select at least one campaign first.");
                return;
            }
            const action = this.dataset.action;
            if (action === 'delete') {
                if (!confirm(`Delete ${ids.length} selected campaign(s)? This cannot be undone.`)) return;
                for (const id of ids) {
                    await fetch(`delete-link.php?id=${id}`);
                }
            } else {
                if (!confirm(`Set ${ids.length} selected campaign(s) to "${action}"?`)) return;
                for (const id of ids) {
                    await fetch('update-status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `id=${id}&status=${encodeURIComponent(action)}`
                    });
                }
            }
            window.location.reload();
        });
    });
});
    </script>
</body>
</html>
