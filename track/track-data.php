<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
ob_start();
require_once '../required/auth.php';
include '../required/config.php';

// Collect filters
$browserFilter = $_GET['browser'] ?? '';
$osFilter = $_GET['os'] ?? '';
$ipFilter = $_GET['ip_address'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$showReferrer = isset($_GET['show_referrer']);
$showLanguage = isset($_GET['show_language']);

// Pagination variables
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$conn->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

// Build filter conditions
$where = "WHERE 1";
if ($browserFilter !== '') {
    $where .= " AND cl.browser LIKE '%$browserFilter%'";
}
if ($osFilter !== '') {
    $where .= " AND cl.os LIKE '%$osFilter%'";
}
if ($ipFilter !== '') {
    $where .= " AND cl.ip_address LIKE '%$ipFilter%'";
}
if ($start_date !== '' && $end_date !== '') {
    $where .= " AND DATE(cl.timestamp) BETWEEN '$start_date' AND '$end_date'";
}

// Count total rows
$count_sql = "SELECT COUNT(*) as total FROM (
    SELECT 1
    FROM click_logs cl
    LEFT JOIN campaigns c ON cl.campaign_id = c.id
    $where
    GROUP BY c.campaign_name, cl.ip_address
) as subquery";

$count_result = $conn->query($count_sql);
$totalRows = $count_result->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $perPage);

// Main data query without LIMIT
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
        $where
        GROUP BY c.campaign_name, cl.ip_address 
        ORDER BY real_clicks DESC";

$result = $conn->query($sql);

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

                <!-- ðŸ”¹ **Date Filter Form** -->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header pb-0 card-no-border">
                            <h5>Filter</h5>
                            
                        </div>
                     <div class="card-body">
                           
                            <form method="GET" action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Start Date:</label>
                                        <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label>End Date:</label>
                                        <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- ðŸ”¹ **Report Table** -->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                              <div class="card-body">
                            <h5>Manage Campaigns</h5>
                        </div>
                       <table class="display table-striped border" id="basic-1">
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
                                    <td>{$row['campaign_name']}</td>
                                    <td>{$row['ip_address']}</td>
                                    <td>{$row['location']}</td>
                                    <td>{$row['browser']}</td>
                                    <td>{$row['os']}</td>";
                            if ($showReferrer) echo "<td>{$row['referrer']}</td>";
                            if ($showLanguage) echo "<td>{$row['language']}</td>";
                            echo "<td>{$row['real_clicks']}</td>
                                  <td>{$row['blank_clicks']}</td>
                                </tr>";
                            $sno++;
                        }
                        ?>
                        </tbody>
                        </table>
                        
                        <div style="margin-top:20px;">
                        <?php
                        for ($i = 1; $i <= $totalPages; $i++) {
                            $active = ($i == $page) ? 'font-weight:bold;' : '';
                            echo "<a href='?page=$i' style='margin:0 5px; $active'>$i</a>";
                        }
                        ?>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                Filter More
                            </button>
                        </div>
                        
                        <!-- Modal -->
                        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="GET" action="">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            
                                            <div class="mt-4">
                                                <label>Show Additional Columns:</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="show_referrer" id="showReferrer" <?php if ($showReferrer) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="showReferrer">Referrer</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="show_language" id="showLanguage" <?php if ($showLanguage) echo 'checked'; ?>>
                                                    <label class="form-check-label" for="showLanguage">Language</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <?php ob_end_flush(); ?>

                            </div>
                        </div>
                    </div>
                </div>
              
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 footer-copyright text-center">
                            <p class="mb-0">Copyright <span class="year-update"></span> Â© Adtrackr</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <?php include '../required/footerjs.php'; ?>
</body>
</html>
