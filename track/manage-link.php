<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();  // Output buffering start
require_once '../required/auth.php';
include '../required/config.php';

// Get date filter values from URL or set defaults
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$end_date   = $_GET['end_date'] ?? date('Y-m-d');

// Prepare SQL with date filter
$sql = "SELECT 
            c.id,
            c.campaign_name,
            c.main_url,
            c.safe_url,
            c.status,
            c.user_id,
            c.created_at
        FROM campaigns c
        WHERE DATE(c.created_at) BETWEEN ? AND ?
        ORDER BY c.id DESC";


$stmt = $conn->prepare($sql);
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
       
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.switch-state {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    transition: .4s;
    border-radius: 24px;
    background-color: #dc3545; /* Red by default */
}
.switch input:checked + .switch-state {
    background-color: #28a745; /* Green when checked */
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


                              <div class="card-body">
    <h5>Manage Links</h5>
</div>

<table class="display table-striped border" id="basic-1">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Campaign Name</th>
            <th>Main URL</th>
            <th>Safe URL</th>
            <th>View URL</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sno = 1;
        while ($row = $result->fetch_assoc()) {
            $campaignId = $row['id'];
            $userId = $row['user_id'];
            $viewUrl = "https://app.trakrhub.com/click.php?aff_id={$userId}&offer_id={$campaignId}";
            echo "<tr>
                    <td>{$sno}</td>
                    <td>{$row['campaign_name']}</td>
                    <td>{$row['main_url']}</td>
                    <td>{$row['safe_url']}</td>
                    <td><button class='btn btn-success' type='button' data-bs-toggle='modal' data-bs-target='#modalUrl{$campaignId}'>View URL</button></td>
                    <td>
                       <label class='switch'>
                            <input type='checkbox' class='status-toggle' data-id='{$campaignId}' " . ($row['status'] == 1 ? "checked" : "") . ">
                            <span class='switch-state'></span>
                        </label>
                    </td>
                    <td>
                        <ul class='action'>
                            <li class='edit'>
                                <a href='duplicate-link.php?id={$campaignId}'><i class='icon-layers'></i></a>
                            </li>
                            <li class='edit-campaign'>
                                <a href='edit-link.php?id={$campaignId}'><i class='icon-pencil-alt'></i></a>
                            </li>
                            <li class='delete'>
                                <a href='javascript:void(0);' class='delete-offer' data-id='{$campaignId}'><i class='fa-solid fa-trash-can'></i></a>
                            </li>
                        </ul>
                    </td>
                </tr>";

            echo "<div class='modal fade' id='modalUrl{$campaignId}' tabindex='-1' aria-labelledby='modalUrlLabel{$campaignId}' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content p-3'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='modalUrlLabel{$campaignId}'>Tracking Link</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <input type='text' class='form-control' value='{$viewUrl}' readonly onclick='this.select();'>
                            </div>
                        </div>
                    </div>
                </div>";
            $sno++;
        }
        ?>
    </tbody>
</table>

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
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".delete-offer");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            
            if (confirm("Do you want to delete this campaign?")) {
                // Redirect to deletion script
                window.location.href = `delete-link.php?id=${id}`;
            }
        });
    });
});
</script>
<script>
document.querySelectorAll('.status-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        const campaignId = this.dataset.id;
        const newStatus = this.checked ? 1 : 0;

        fetch('update-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${campaignId}&status=${newStatus}`
        })
        .then(res => res.text())
        .then(msg => {
            console.log(msg); // Optional: you can show a toast here
        });
    });
});
</script>

</body>
</html>
