<?php
ob_start();  // Output buffering start
 require_once '../required/auth.php';
include '../required/config.php';

// Get date filter values
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$sql = "SELECT 
            user_ip AS ip, 
            SUM(click_count) AS total_clicks, 
            SUM(real_clicks) AS real_clicks,
            SUM(direct_clicks) AS direct_clicks
        FROM click_tracking
        WHERE DATE(last_clicked) BETWEEN ? AND ?
        GROUP BY user_ip
        ORDER BY total_clicks DESC";


$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error in SQL Query: " . $conn->error);
}
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
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
                            <h5>Ip Report</h5>
                            
                        </div>
                     <div class="card-body">
                            <form method="GET" action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Start Date:</label>
                                        <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label>End Date:</label>
                                        <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
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


                               <table class="display table-striped border" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>IP Address</th>
                                            <th>Total Clicks</th>
                                            <th>Real Clicks</th>
                                            <th>Direct Clicks</th> <!-- ✅ New Column Added -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sno = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                    <td>{$sno}</td>
                                                    <td>{$row['ip']}</td>
                                                    <td>{$row['total_clicks']}</td>
                                                    <td>{$row['real_clicks']}</td>
                                                    <td>{$row['direct_clicks']}</td> <!-- ✅ Display Direct Clicks -->
                                                  </tr>";
                                            $sno++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                
                                <?php
                                ob_end_flush(); // Flush the output buffer
                                ?>


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
