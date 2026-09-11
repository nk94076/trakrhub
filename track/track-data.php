<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
ob_start();
require_once '../required/auth.php';
include '../required/config.php';

// Collect filters
$search        = trim($_GET['search'] ?? '');
$browserFilter = trim($_GET['browser'] ?? '');
$osFilter      = trim($_GET['os'] ?? '');
$ipFilter      = trim($_GET['ip_address'] ?? '');
$start_date    = $_GET['start_date'] ?? '';
$end_date      = $_GET['end_date'] ?? '';
$showReferrer  = isset($_GET['show_referrer']);
$showLanguage  = isset($_GET['show_language']);

$allowedPerPage = [10, 25, 50, 100];
$requestedPerPage = (int) ($_GET['per_page'] ?? 10);
$perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 10;
$page    = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$conn->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

// Build filter conditions with prepared-statement placeholders (no raw concatenation)
$conditions = [];
$params = [];
$types = '';

if ($search !== '') {
    $conditions[] = "(c.campaign_name LIKE ? OR cl.ip_address LIKE ? OR cl.location LIKE ? OR cl.browser LIKE ?)";
    $like = "%$search%";
    array_push($params, $like, $like, $like, $like);
    $types .= 'ssss';
}
if ($browserFilter !== '') {
    $conditions[] = "cl.browser LIKE ?";
    $params[] = "%$browserFilter%";
    $types .= 's';
}
if ($osFilter !== '') {
    $conditions[] = "cl.os LIKE ?";
    $params[] = "%$osFilter%";
    $types .= 's';
}
if ($ipFilter !== '') {
    $conditions[] = "cl.ip_address LIKE ?";
    $params[] = "%$ipFilter%";
    $types .= 's';
}
if ($start_date !== '' && $end_date !== '') {
    $conditions[] = "DATE(cl.timestamp) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

$whereSql = $conditions ? ('WHERE ' . implode(' AND ', $conditions)) : '';

// Summary stats across every matching click (not just the current page)
$statsSql = "SELECT
        COUNT(DISTINCT c.campaign_name) AS total_campaigns,
        COUNT(*) AS total_clicks,
        SUM(cl.is_real = 1) AS real_clicks,
        SUM(cl.is_real = 0) AS blank_clicks
    FROM click_logs cl
    LEFT JOIN campaigns c ON cl.campaign_id = c.id
    $whereSql";
$statsStmt = $conn->prepare($statsSql);
if ($types !== '') {
    $statsStmt->bind_param($types, ...$params);
}
$statsStmt->execute();
$stats = $statsStmt->get_result()->fetch_assoc();
$totalCampaigns = (int) ($stats['total_campaigns'] ?? 0);
$totalClicksAll = (int) ($stats['total_clicks'] ?? 0);
$realClicksAll  = (int) ($stats['real_clicks'] ?? 0);
$blankClicksAll = (int) ($stats['blank_clicks'] ?? 0);

// Count grouped rows for pagination
$countSql = "SELECT COUNT(*) AS total FROM (
    SELECT 1
    FROM click_logs cl
    LEFT JOIN campaigns c ON cl.campaign_id = c.id
    $whereSql
    GROUP BY c.campaign_name, cl.ip_address
) AS sub";
$countStmt = $conn->prepare($countSql);
if ($types !== '') {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRows  = (int) ($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

// CSV export of every matching (non-paginated) row
if (($_GET['export'] ?? '') === 'csv') {
    $exportSql = "SELECT
            c.campaign_name AS campaign_name,
            cl.ip_address,
            MAX(cl.location) AS location,
            MAX(cl.browser) AS browser,
            MAX(cl.os) AS os,
            MAX(cl.referrer) AS referrer,
            MAX(cl.language) AS language,
            SUM(cl.is_real = 1) AS real_clicks,
            SUM(cl.is_real = 0) AS blank_clicks
        FROM click_logs cl
        LEFT JOIN campaigns c ON cl.campaign_id = c.id
        $whereSql
        GROUP BY c.campaign_name, cl.ip_address
        ORDER BY real_clicks DESC";
    $exportStmt = $conn->prepare($exportSql);
    if ($types !== '') {
        $exportStmt->bind_param($types, ...$params);
    }
    $exportStmt->execute();
    $exportResult = $exportStmt->get_result();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="track-data.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Campaign Name', 'IP Address', 'Location', 'Browser', 'OS', 'Referrer', 'Language', 'Real Clicks', 'Blank Clicks'], ',', '"', '\\');
    while ($row = $exportResult->fetch_assoc()) {
        fputcsv($out, [
            $row['campaign_name'], $row['ip_address'], $row['location'], $row['browser'], $row['os'],
            $row['referrer'], $row['language'], $row['real_clicks'], $row['blank_clicks'],
        ], ',', '"', '\\');
    }
    fclose($out);
    exit;
}

// Main paginated query
$sql = "SELECT
            c.campaign_name AS campaign_name,
            cl.ip_address,
            MAX(cl.location) as location,
            MAX(cl.browser) as browser,
            MAX(cl.os) as os,
            MAX(cl.referrer) as referrer,
            MAX(cl.language) as language,
            SUM(cl.is_real = 1) AS real_clicks,
            SUM(cl.is_real = 0) AS blank_clicks
        FROM click_logs cl
        LEFT JOIN campaigns c ON cl.campaign_id = c.id
        $whereSql
        GROUP BY c.campaign_name, cl.ip_address
        ORDER BY real_clicks DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$queryParams = $params;
$queryParams[] = $perPage;
$queryParams[] = $offset;
$stmt->bind_param($types . 'ii', ...$queryParams);
$stmt->execute();
$result = $stmt->get_result();

// Builds a query string from the CURRENT filters, with the given keys overridden/removed
function buildQueryString(array $overrides = []): string
{
    $current = $_GET;
    foreach ($overrides as $key => $value) {
        if ($value === null) {
            unset($current[$key]);
        } else {
            $current[$key] = $value;
        }
    }
    return htmlspecialchars('?' . http_build_query($current));
}

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
        .track-stat-icon {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }
        .pagination .page-link {
            cursor: pointer;
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
                            <h3 class="mb-1">Track Data</h3>
                            <p class="text-muted mb-0">View, search and analyze all your campaign click data in one place.</p>
                        </div>
                    </div>

                    <!-- Summary cards -->
                    <div class="row g-3 mb-3">
                        <div class="col-xl-3 col-sm-6">
                            <div class="card mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="track-stat-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-bullhorn"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Total Campaigns</div>
                                        <h4 class="mb-0"><?= $totalCampaigns ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="track-stat-icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-arrow-pointer"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Total Clicks</div>
                                        <h4 class="mb-0"><?= $totalClicksAll ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="track-stat-icon bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-chart-column"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Real Clicks</div>
                                        <h4 class="mb-0"><?= $realClicksAll ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card mb-0">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="track-stat-icon bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-ban"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Blank Clicks</div>
                                        <h4 class="mb-0"><?= $blankClicksAll ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter card -->
                    <div class="card">
                        <div class="card-header pb-2">
                            <h5 class="mb-0"><i class="fa-solid fa-filter me-1"></i> Filter Campaigns</h5>
                            <p class="text-muted small mb-0">Select date range and apply filters to find specific data.</p>
                        </div>
                        <div class="card-body pt-2">
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
                                        <input type="text" name="search" class="form-control" placeholder="Search campaign, IP, location, browser..." value="<?= htmlspecialchars($search) ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">Search</button>
                                        <a href="track-data.php" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Browser</label>
                                        <input type="text" name="browser" class="form-control" placeholder="e.g. Chrome" value="<?= htmlspecialchars($browserFilter) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">OS</label>
                                        <input type="text" name="os" class="form-control" placeholder="e.g. Windows" value="<?= htmlspecialchars($osFilter) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">IP Address</label>
                                        <input type="text" name="ip_address" class="form-control" value="<?= htmlspecialchars($ipFilter) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label d-block">Show Columns</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="show_referrer" id="showReferrer" <?= $showReferrer ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="showReferrer">Referrer</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="show_language" id="showLanguage" <?= $showLanguage ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="showLanguage">Language</label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data table -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <form method="GET" action="" class="d-flex align-items-center gap-2 mb-0">
                                <?php foreach ($_GET as $key => $value) {
                                    if ($key === 'per_page' || $key === 'page') {
                                        continue;
                                    }
                                    foreach ((array) $value as $v) {
                                        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($v) . '">';
                                    }
                                } ?>
                                <select name="per_page" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                                    <?php foreach ($allowedPerPage as $n): ?>
                                        <option value="<?= $n ?>" <?= $perPage === $n ? 'selected' : '' ?>><?= $n ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-muted small">entries per page</span>
                            </form>
                            <a href="<?= buildQueryString(['export' => 'csv', 'page' => null]) ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-download me-1"></i>Export
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-striped border" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Campaign Name</th>
                                            <th>IP Address</th>
                                            <th>Location</th>
                                            <th>Browser</th>
                                            <th>OS</th>
                                            <?php if ($showReferrer) echo '<th>Referrer</th>'; ?>
                                            <?php if ($showLanguage) echo '<th>Language</th>'; ?>
                                            <th>Real Clicks</th>
                                            <th>Blank Clicks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sno = $offset + 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$sno}</td>
                                                <td>" . htmlspecialchars($row['campaign_name'] ?? '') . "</td>
                                                <td>" . htmlspecialchars($row['ip_address'] ?? '') . "</td>
                                                <td>" . htmlspecialchars($row['location'] ?? '') . "</td>
                                                <td>" . htmlspecialchars($row['browser'] ?? '') . "</td>
                                                <td>" . htmlspecialchars($row['os'] ?? '') . "</td>";
                                        if ($showReferrer) echo "<td>" . htmlspecialchars($row['referrer'] ?? '') . "</td>";
                                        if ($showLanguage) echo "<td>" . htmlspecialchars($row['language'] ?? '') . "</td>";
                                        echo "<td>{$row['real_clicks']}</td>
                                              <td>{$row['blank_clicks']}</td>
                                            </tr>";
                                        $sno++;
                                    }
                                    if ($totalRows === 0) {
                                        $colspan = 8 + ($showReferrer ? 1 : 0) + ($showLanguage ? 1 : 0);
                                        echo "<tr><td colspan='{$colspan}' class='text-center text-muted py-4'>No data found for the selected filters.</td></tr>";
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                                <div class="text-muted small">
                                    Showing <?= $totalRows === 0 ? 0 : $offset + 1 ?> to <?= min($offset + $perPage, $totalRows) ?> of <?= $totalRows ?> entries
                                </div>
                                <?php if ($totalPages > 1): ?>
                                <nav>
                                    <ul class="pagination mb-0">
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= buildQueryString(['page' => 1]) ?>">&laquo;</a>
                                        </li>
                                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= buildQueryString(['page' => max(1, $page - 1)]) ?>">&lsaquo;</a>
                                        </li>
                                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= buildQueryString(['page' => $i]) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= buildQueryString(['page' => min($totalPages, $page + 1)]) ?>">&rsaquo;</a>
                                        </li>
                                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="<?= buildQueryString(['page' => $totalPages]) ?>">&raquo;</a>
                                        </li>
                                    </ul>
                                </nav>
                                <?php endif; ?>
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
    <?php ob_end_flush(); ?>
</body>
</html>
