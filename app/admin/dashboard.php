<?php
ob_start();  // Output buffering start
require_once '../required/auth.php';
include '../required/config.php';

// Today's date
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$first_day_of_month = date('Y-m-01');

// Click count summary
$summary_sql = "
    SELECT 
        SUM(CASE WHEN DATE(timestamp) = '$today' THEN 1 ELSE 0 END) AS today_clicks,
        SUM(CASE WHEN DATE(timestamp) = '$yesterday' THEN 1 ELSE 0 END) AS yesterday_clicks,
        SUM(CASE WHEN DATE(timestamp) >= '$first_day_of_month' THEN 1 ELSE 0 END) AS mtd_clicks
    FROM click_logs";
$summary_result = $conn->query($summary_sql);
$summary = $summary_result->fetch_assoc();
$today_clicks = $summary['today_clicks'] ?? 0;
$yesterday_clicks = $summary['yesterday_clicks'] ?? 0;
$mtd_clicks = $summary['mtd_clicks'] ?? 0;

// Total domains
$domain_result = $conn->query("SELECT COUNT(*) as total_domains FROM domains");
$total_domains = $domain_result->fetch_assoc()['total_domains'] ?? 0;

// Total templates
$template_result = $conn->query("SELECT COUNT(*) as total_templates FROM templates");
$total_templates = $template_result->fetch_assoc()['total_templates'] ?? 0;

// Total users
$user_result = $conn->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $user_result->fetch_assoc()['total_users'] ?? 0;

// Date filtering logic
$filter = $_GET['filter'] ?? '10_days';
$start_date = '';
$end_date = date('Y-m-d');

switch ($filter) {
    case 'today':
        $start_date = $end_date;
        break;
    case 'week':
        $start_date = date('Y-m-d', strtotime('-7 days'));
        break;
    case 'month':
        $start_date = date('Y-m-d', strtotime('-30 days'));
        break;
    case '3_month':
        $start_date = date('Y-m-d', strtotime('-3 months'));
        break;
    case '6_month':
        $start_date = date('Y-m-d', strtotime('-6 months'));
        break;
    case 'year':
        $start_date = date('Y-m-d', strtotime('-1 year'));
        break;
    case 'custom':
        $start_date = $_GET['start_date'] ?? date('Y-m-d');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        break;
    default:
        $start_date = date('Y-m-d', strtotime('-10 days'));
}

// Click data for chart
$graph_sql = "
    SELECT DATE(timestamp) AS click_date, COUNT(*) AS total_clicks
    FROM click_logs
    WHERE DATE(timestamp) BETWEEN '$start_date' AND '$end_date'
    GROUP BY DATE(timestamp)
    ORDER BY click_date ASC";

$graph_result = $conn->query($graph_sql);
$dates = [];
$clicks = [];
while ($row = $graph_result->fetch_assoc()) {
    $dates[] = $row['click_date'];
    $clicks[] = $row['total_clicks'];
}
$dates_json = json_encode($dates);
$clicks_json = json_encode($clicks);
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
                    <div class="row">
                       <!-- Graph Card -->
<div class="col-xxl-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Performance Report</h5>
            <form method="GET" action="">
                <select name="filter" class="form-select" onchange="this.form.submit()">
                    <option value="10_days" <?php if ($filter == '10_days') echo 'selected'; ?>>Last 10 Days</option>
                    <option value="today" <?php if ($filter == 'today') echo 'selected'; ?>>Today</option>
                    <option value="week" <?php if ($filter == 'week') echo 'selected'; ?>>This Week</option>
                    <option value="month" <?php if ($filter == 'month') echo 'selected'; ?>>This Month</option>
                    <option value="3_month" <?php if ($filter == '3_month') echo 'selected'; ?>>Last 3 Months</option>
                    <option value="6_month" <?php if ($filter == '6_month') echo 'selected'; ?>>Last 6 Months</option>
                    <option value="year" <?php if ($filter == 'year') echo 'selected'; ?>>This Year</option>
                    <option value="custom" <?php if ($filter == 'custom') echo 'selected'; ?>>Custom</option>
                </select>
                <?php if ($filter == 'custom'): ?>
                <div class="d-flex gap-2 mt-2">
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" class="form-control">
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" class="form-control">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
                <?php endif; ?>
            </form>
        </div>
        <div class="card-body">
            <div id="clicks-chart"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var options = {
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false }
        },
        series: [{
            name: "Total Clicks",
            data: <?php echo $clicks_json; ?>
        }],
        xaxis: {
            categories: <?php echo $dates_json; ?>,
            labels: { rotate: -45 }
        },
        stroke: {
            curve: 'straight',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0,
                stops: [0, 90, 100]
            }
        },
        colors: ['#33b2df'],
        markers: {
            size: 0
        },
        dataLabels: {
            enabled: false
        }
    };

    var chart = new ApexCharts(document.querySelector("#clicks-chart"), options);
    chart.render();
});
</script>

                                    

                                    <div class="col-xxl-3">
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5><i class="icon-hand-point-up"></i> Clicks</h5>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 treading-product">
                        <div class="recent-table table-responsive custom-scrollbar referral-visit">
                            <table class="table">
                                        <thead>
                                            <tr><th>Today</th><th>Yesterday</th><th>MTD</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="f-w-500"><?php echo $today_clicks; ?></td>
                                                <td class="f-w-500"><?php echo $yesterday_clicks; ?></td>
                                                <td class="f-w-500"><?php echo $mtd_clicks; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                        </div>
                    </div>
                </div>
            </div>
                <div class="col-xxl-2">
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5><i class="fa-solid fa-globe"></i> Domains</h5>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 treading-product">
                        <div class="recent-table table-responsive custom-scrollbar referral-visit">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Domains</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="f-w-500"><?php echo $total_domains; ?></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xxl-2">
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5><i class="fa-solid fa-globe"></i> templaes</h5>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 treading-product">
                        <div class="recent-table table-responsive custom-scrollbar referral-visit">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Total templaes</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="f-w-500"><?php echo $total_templates; ?></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xxl-2">
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5><i class="fa-solid fa-user"></i> Users</h5>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 treading-product">
                        <div class="recent-table table-responsive custom-scrollbar referral-visit">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Total</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="f-w-500"><?php echo $total_users; ?></td>
                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
</body>
</html>

<?php ob_end_flush(); ?>
