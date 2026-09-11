<?php
ob_start();  // Output buffering start
require_once '../required/auth.php';
include '../required/config.php';

$campaignId = (int) ($_GET['id'] ?? 0);
if (!$campaignId) {
    echo "Invalid campaign ID.";
    exit;
}

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

if ($isAdmin) {
    $stmt = $conn->prepare("SELECT * FROM campaigns WHERE id = ?");
    $stmt->bind_param("i", $campaignId);
} else {
    $stmt = $conn->prepare("SELECT * FROM campaigns WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $campaignId, $_SESSION['user_id']);
}
$stmt->execute();
$campaign = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$campaign) {
    echo "Campaign not found.";
    exit;
}

$clicksStmt = $conn->prepare("SELECT
        SUM(is_real = 1) AS real_clicks,
        SUM(is_real = 0) AS blank_clicks,
        COUNT(*) AS total_clicks
    FROM click_logs WHERE campaign_id = ?");
$clicksStmt->bind_param("i", $campaignId);
$clicksStmt->execute();
$clickStats = $clicksStmt->get_result()->fetch_assoc();
$clicksStmt->close();

$statusBadge = ['active' => 'success', 'pending' => 'warning', 'paused' => 'secondary'];
$badgeClass = $statusBadge[$campaign['status']] ?? 'secondary';

$trackingLink = "https://app.trakrhub.com/click.php?aff_id=" . (int) $campaign['user_id'] . "&offer_id=" . (int) $campaign['id'];

$deviceLabels = $campaign['devices'] === 'all' ? 'All Devices' : implode(', ', array_map('ucfirst', explode(',', $campaign['devices'])));
$osLabels = $campaign['os'] === 'all' ? 'All OS' : $campaign['os'];
$redirectLabels = [
    '302' => '302',
    '302_hrf' => '302 with Hide Referrer',
    '200' => '200 OK',
    '200_hrf' => '200 with Hide Referrer',
    'custom' => 'Custom (Set Referral Source)',
];
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
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            width: 220px;
            flex-shrink: 0;
            color: #6c757d;
            font-weight: 500;
        }
        .detail-value {
            flex: 1;
            word-break: break-word;
        }
        .stat-box {
            text-align: center;
            padding: 16px;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .stat-box h4 {
            margin-bottom: 4px;
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
                            <h3 class="mb-1"><?= htmlspecialchars($campaign['campaign_name']) ?></h3>
                            <span class="badge bg-<?= $badgeClass ?>"><?= htmlspecialchars(ucfirst($campaign['status'])) ?></span>
                        </div>
                        <div>
                            <a href="edit-link.php?id=<?= $campaignId ?>" class="btn btn-primary"><i class="icon-pencil-alt me-1"></i>Edit</a>
                            <a href="manage-link.php" class="btn btn-light">Back to List</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-4">
                            <div class="stat-box mb-3">
                                <h4><?= (int) ($clickStats['total_clicks'] ?? 0) ?></h4>
                                <span class="text-muted">Total Clicks</span>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="stat-box mb-3">
                                <h4 class="text-success"><?= (int) ($clickStats['real_clicks'] ?? 0) ?></h4>
                                <span class="text-muted">Real Clicks</span>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="stat-box mb-3">
                                <h4 class="text-secondary"><?= (int) ($clickStats['blank_clicks'] ?? 0) ?></h4>
                                <span class="text-muted">Blank Clicks</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-7">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="detail-row">
                                        <div class="detail-label">Campaign Name</div>
                                        <div class="detail-value"><?= htmlspecialchars($campaign['campaign_name']) ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Description</div>
                                        <div class="detail-value"><?= nl2br(htmlspecialchars($campaign['description'] ?? '')) ?: '—' ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Category</div>
                                        <div class="detail-value"><?= htmlspecialchars($campaign['category'] ?? '') ?: '—' ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Status</div>
                                        <div class="detail-value"><span class="badge bg-<?= $badgeClass ?>"><?= htmlspecialchars(ucfirst($campaign['status'])) ?></span></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Main URL</div>
                                        <div class="detail-value"><a href="<?= htmlspecialchars($campaign['main_url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($campaign['main_url']) ?></a></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Safe URL</div>
                                        <div class="detail-value"><a href="<?= htmlspecialchars($campaign['safe_url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($campaign['safe_url']) ?></a></div>
                                    </div>
                                    <?php if (($campaign['redirect_type'] ?? '302') === 'custom' && !empty($campaign['custom_url'])): ?>
                                    <div class="detail-row">
                                        <div class="detail-label">Custom Referral Source</div>
                                        <div class="detail-value"><?= htmlspecialchars($campaign['custom_url']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="detail-row">
                                        <div class="detail-label">Devices</div>
                                        <div class="detail-value"><?= htmlspecialchars($deviceLabels) ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Operating System</div>
                                        <div class="detail-value"><?= htmlspecialchars(ucfirst($osLabels)) ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Redirect Type</div>
                                        <div class="detail-value"><?= htmlspecialchars($redirectLabels[$campaign['redirect_type'] ?? '302'] ?? '302') ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">KPI</div>
                                        <div class="detail-value"><?= nl2br(htmlspecialchars($campaign['kpi'] ?? '')) ?: '—' ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Terms and Conditions</div>
                                        <div class="detail-value">
                                            <?= nl2br(htmlspecialchars($campaign['terms_conditions'] ?? '')) ?: '—' ?>
                                            <?php if (!empty($campaign['require_tnc'])): ?>
                                                <span class="badge bg-info ms-1">Acceptance Required</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Notes</div>
                                        <div class="detail-value"><?= nl2br(htmlspecialchars($campaign['note'] ?? '')) ?: '—' ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Created</div>
                                        <div class="detail-value"><?= htmlspecialchars($campaign['created_at'] ?? '') ?></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Unique ID</div>
                                        <div class="detail-value">#<?= $campaignId ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-5">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Tracking Link</h5>
                                </div>
                                <div class="card-body">
                                    <label class="form-label">Generated Link</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="trackingLinkInput" value="<?= htmlspecialchars($trackingLink) ?>" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="copyLinkBtn"><i class="fa-solid fa-copy me-1"></i>Copy</button>
                                    </div>
                                    <div class="form-text">Share this link with your affiliate/traffic source. It always redirects to the Main URL (subject to Status/Device/OS targeting above).</div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5>Blocked Parameters</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $blockedParams = array_filter(array_map('trim', explode(',', $campaign['blocked_params'] ?? '')));
                                    if (!empty($blockedParams)) {
                                        foreach ($blockedParams as $bp) {
                                            echo "<span class='badge bg-light text-dark border me-1 mb-1'>" . htmlspecialchars($bp) . "</span>";
                                        }
                                    } else {
                                        echo "<span class='text-muted'>None</span>";
                                    }
                                    ?>
                                </div>
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
document.getElementById('copyLinkBtn').addEventListener('click', function () {
    const input = document.getElementById('trackingLinkInput');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = document.getElementById('copyLinkBtn');
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check me-1"></i>Copied';
        setTimeout(() => { btn.innerHTML = original; }, 1500);
    });
});
    </script>
</body>
</html>
