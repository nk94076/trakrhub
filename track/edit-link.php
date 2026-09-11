<?php
ob_start();  // Output buffering start
require_once '../required/auth.php';
include '../required/config.php';

$campaignId = $_GET['id'] ?? 0;
if (!$campaignId) {
    echo "Invalid campaign ID.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM campaigns WHERE id = ?");
$stmt->bind_param("i", $campaignId);
$stmt->execute();
$result = $stmt->get_result();
$campaign = $result->fetch_assoc();
$stmt->close();

if (!$campaign) {
    echo "Campaign not found.";
    exit;
}

$blockedParams = explode(',', $campaign['blocked_params']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../required/head.php'; ?>
    <style>
        .page-wrapper .page-body-wrapper .page-title {
            padding: 0;
            margin: 0 -27px 28px;
        }
        svg {
            vertical-align: middle;
        }
        .modal.modal-top .modal-dialog {
        margin-top: 2rem !important; /* Adjust as needed */
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
                        
            <div class="col-xl-12">
                <div class="card height-equal">
                  <div class="card-header">
                    <h5>Create Link</h5>
                  </div>
                 <form method="POST" action="update_campaign.php">
  <input type="hidden" name="campaign_id" value="<?= $campaignId ?>">
  <div class="card-body custom-input">
    <div class="row">
      <div class="col">
        <div class="mb-3 row">
          <label class="col-sm-3">Campaign Name</label>
          <div class="col-sm-9">
            <input class="form-control" type="text" name="campaign_name" value="<?= htmlspecialchars($campaign['campaign_name']) ?>">
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3">Enter URL</label>
          <div class="col-sm-9">
            <input class="form-control" type="url" name="url" value="<?= htmlspecialchars($campaign['main_url']) ?>">
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3">Safe URL</label>
          <div class="col-sm-9">
            <input class="form-control" type="url" name="safe_url" value="<?= htmlspecialchars($campaign['safe_url']) ?>">
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3">Google Pixel</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="google_pixel" rows="2"><?= htmlspecialchars($campaign['google_pixel']) ?></textarea>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3">Facebook Pixel</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="facebook_pixel" rows="2"><?= htmlspecialchars($campaign['facebook_pixel']) ?></textarea>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3">Blocked Parameters</label>
          <div class="col-sm-9">
            <div class="form-check-size">
              <?php
              $allParams = [
                'gclid', 'fbclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
                'session_id', 'click_id', 'campaign_code', 'custom_event',
                'facebook_pixel', 'google_pixel', 'tiktok_pixel', 'conversion_event',
                'ip_address', 'user_agent', 'browser', 'os', 'location', 'referrer', 'language', 'timestamp'
              ];
              foreach ($allParams as $param) {
                  $checked = in_array($param, $blockedParams) ? 'checked' : '';
                  echo "<div class='form-check form-check-inline mb-1'><input class='form-check-input' type='checkbox' name='blocked_params[]' value='{$param}' id='{$param}' {$checked}><label class='form-check-label' for='{$param}'>{$param}</label></div>";
              }
              ?>
            </div>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3">Notes</label>
          <div class="col-sm-9">
            <textarea class="form-control" name="note" rows="4"><?= htmlspecialchars($campaign['note']) ?></textarea>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-sm-3">Status</label>
          <div class="col-sm-9">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" <?= $campaign['status'] == 1 ? 'checked' : '' ?>>
              <label class="form-check-label" for="statusSwitch">Active</label>
            </div>
          </div>
        </div>

        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
      </div>
    </div>
  </div>

  <div class="card-footer text-end">
    <div class="col-sm-9 offset-sm-3">
      <button class="btn btn-primary me-3" type="submit">Update</button>
      <a href="manage-link.php" class="btn btn-light">Cancel</a>
    </div>
  </div>
</form>
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
                            <p class="mb-0">Copyright <span class="year-update"></span> © Adtrackr</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <?php include '../required/footerjs.php'; ?>
     <script>
                    // Show "+ More" box after 8 checkboxes
                    document.querySelectorAll('#paramCheckboxes input[type="checkbox"]').forEach((checkbox, index) => {
                      checkbox.addEventListener('change', () => {
                        const selected = Array.from(document.querySelectorAll('#paramCheckboxes input[type="checkbox"]:checked')).length;
                        if (selected >= 8) {
                          document.getElementById('moreTrigger').classList.remove('d-none');
                        }
                      });
                    });
                    </script>
</body>
</html>
