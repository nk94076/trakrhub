<?php
require_once '../required/auth.php';
require_once '../required/config.php';

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

                <!-- Add Campaign Form -->
            <div class="col-xl-12">
                <div class="card height-equal">
                  <div class="card-header">
                    <h5>Add Campaign</h5>
                  </div>
            <form class="form theme-form" method="POST" action="save-campaign.php">
                  <div class="card-body custom-input">
                    <div class="row">
                      <div class="col">
                
                        <!-- Campaign Name -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Campaign Name</label>
                          <div class="col-sm-9">
                            <input class="form-control" type="text" name="campaign_name" placeholder="e.g. Summer Offers - iGaming" required>
                          </div>
                        </div>
                
                        <!-- Preview URL -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Preview URL</label>
                          <div class="col-sm-9">
                            <input class="form-control" type="url" name="preview_url" placeholder="https://example.com/preview" required>
                          </div>
                        </div>
                
                        <!-- Affiliate Offer URL -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Affiliate URL</label>
                          <div class="col-sm-9">
                            <input class="form-control" type="url" name="affiliate_url" placeholder="https://example.com/offer-link" required>
                          </div>
                        </div>
                
                        <!-- Landing Page URL (optional override) -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Landing Page</label>
                          <div class="col-sm-9">
                            <input class="form-control" type="url" name="landing_url" placeholder="(Optional) Direct LP link">
                          </div>
                        </div>
                        
                        <!-- Referer Tracking Option -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Referer Tracking</label>
                              <div class="col-sm-9">
                                <select name="referer_tracking" class="form-select" required>
                                  <option value="track">Track Referer</option>
                                  <option value="no_track">Do NOT Track Referer</option>
                                </select>
                              </div>
                            </div>

                
                        <!-- Description -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Description</label>
                          <div class="col-sm-9">
                            <textarea class="form-control" name="description" rows="4" placeholder="Describe this campaign (Geo, Targeting, Rules etc.)"></textarea>
                          </div>
                        </div>
                
                        <!-- Offer Type -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Offer Type</label>
                          <div class="col-sm-9">
                            <select name="offer_type" class="form-select" required>
                              <option value="CPL">CPL</option>
                              <option value="CPA">CPA</option>
                              <option value="CPI">CPI</option>
                              <option value="CPS">CPS</option>
                            </select>
                          </div>
                        </div>
                
                        <!-- Daily Cap -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Daily Cap</label>
                          <div class="col-sm-9">
                            <input class="form-control" type="number" name="daily_cap" placeholder="e.g. 100 conversions per day">
                          </div>
                        </div>
                
                        <!-- Total Cap -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Total Cap</label>
                          <div class="col-sm-9">
                            <input class="form-control" type="number" name="total_cap" placeholder="e.g. 1000 total conversions">
                          </div>
                        </div>
                
                
                        <!-- Notes -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Internal Notes</label>
                          <div class="col-sm-9">
                            <textarea class="form-control" name="notes" rows="3" placeholder="For internal reference (optional)"></textarea>
                          </div>
                        </div>
                
                        <!-- Status -->
                        <div class="mb-3 row">
                          <label class="col-sm-3">Status</label>
                          <div class="col-sm-9">
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" checked>
                              <label class="form-check-label" for="statusSwitch">Active</label>
                            </div>
                          </div>
                        </div>
                
                        <!-- Hidden User ID -->
                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                      </div>
                    </div>
                  </div>
                
                  <div class="card-footer text-end">
                    <div class="col-sm-9 offset-sm-3">
                      <button class="btn btn-primary me-3" type="submit">Submit</button>
                      <input class="btn btn-light" type="reset" value="Cancel">
                    </div>
                  </div>
                </form>


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
