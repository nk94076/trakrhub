<?php

include '../required/auth.php';
include '../required/config.php';


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
                  <form method="POST" action="create_campaign.php">
                      <div class="card-body custom-input">
                        <div class="row">
                          <div class="col">
                            <!-- Campaign Name -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Campaign Name</label>
                              <div class="col-sm-9">
                                <input class="form-control" type="text" name="campaign_name" required placeholder="Type your Campaign name (e.g. https://app.trakrhub.com)">
                              </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Description</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" name="description" rows="2" placeholder="Optional description of this campaign"></textarea>
                              </div>
                            </div>

                            <!-- Category -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Category</label>
                              <div class="col-sm-9">
                                <input class="form-control" type="text" name="category" placeholder="e.g. E-commerce, Finance, Gaming">
                              </div>
                            </div>

                            <!-- Enter URL -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Enter URL</label>
                              <div class="col-sm-9">
                                <input class="form-control" type="url" name="url" placeholder="https://example.com">
                              </div>
                            </div>
                            
                             <!-- Enter URL -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Safe URL</label>
                              <div class="col-sm-9">
                                <input class="form-control" type="url" name="safe_url" placeholder="https://example.com">
                              </div>
                            </div>
                            
                            
                           <!-- Google Pixel -->
                                <div class="mb-3 row">
                                  <label class="col-sm-3">Google Pixel</label>
                                  <div class="col-sm-9">
                                    <textarea class="form-control" name="google_pixel" rows="2" placeholder="Enter Google Pixel ID"></textarea>
                                  </div>
                                </div>
                                
                                <!-- Facebook Pixel -->
                                <div class="mb-3 row">
                                  <label class="col-sm-3">Facebook Pixel</label>
                                  <div class="col-sm-9">
                                    <textarea class="form-control" name="facebook_pixel" rows="2" placeholder="Enter Facebook Pixel ID"></textarea>
                                  </div>
                                </div>

                                <!-- Devices -->
                                <div class="mb-3 row">
                                  <label class="col-sm-3">Devices</label>
                                  <div class="col-sm-9">
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="devices[]" value="desktop" id="dev_desktop" checked><label class="form-check-label" for="dev_desktop">Desktop</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="devices[]" value="mobile" id="dev_mobile" checked><label class="form-check-label" for="dev_mobile">Mobile</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="devices[]" value="tablet" id="dev_tablet" checked><label class="form-check-label" for="dev_tablet">Tablet</label></div>
                                    <div class="form-text">Traffic from unchecked devices will be sent to the Safe URL instead.</div>
                                  </div>
                                </div>

                                <!-- OS -->
                                <div class="mb-3 row">
                                  <label class="col-sm-3">Operating System</label>
                                  <div class="col-sm-9">
                                    <select class="form-control" name="os">
                                      <option value="all" selected>ALL</option>
                                      <option value="windows">Windows</option>
                                      <option value="macos">Mac OS</option>
                                      <option value="linux">Linux</option>
                                      <option value="android">Android</option>
                                      <option value="ios">iOS (iPhone)</option>
                                    </select>
                                  </div>
                                </div>

                                <!-- Redirect Type -->
                                <div class="mb-3 row">
                                  <label class="col-sm-3">Redirect Type</label>
                                  <div class="col-sm-9">
                                    <select class="form-control" name="redirect_type">
                                      <option value="302" selected>302</option>
                                      <option value="302_hrf">302 with Hide Referrer</option>
                                      <option value="200">200 OK</option>
                                      <option value="200_hrf">200 with Hide Referrer</option>
                                    </select>
                                    <div class="form-text">We recommend using 302 as the redirect type.</div>
                                  </div>
                                </div>



                             <!-- Enter URL -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Blocked Parameters</label>
                                      <div class="col-sm-9">

                   <div class="row">
                            <div class="col-sm-9">
                              <div class="form-check-size" id="paramCheckboxes">
                                <!-- Group 1: URL-based Parameters -->
                                <div class="form-check form-check-inline mb-1"><input class="form-check-input" type="checkbox" id="gclid"><label class="form-check-label" for="gclid">gclid</label></div>
                                <div class="form-check form-check-inline mb-1"><input class="form-check-input" type="checkbox" id="fbclid"><label class="form-check-label" for="fbclid">fbclid</label></div>
                                <div class="form-check form-check-inline mb-1"><input class="form-check-input" type="checkbox" id="utm_source"><label class="form-check-label" for="utm_source">utm_source</label></div>
                                <div class="form-check form-check-inline mb-1"><input class="form-check-input" type="checkbox" id="utm_medium"><label class="form-check-label" for="utm_medium">utm_medium</label></div>
                                <div class="form-check form-check-inline mb-1"><input class="form-check-input" type="checkbox" id="utm_campaign"><label class="form-check-label" for="utm_campaign">utm_campaign</label></div>
                                <div class="form-check form-check-inline mb-1"><input class="form-check-input" type="checkbox" id="utm_campaign"><label class="form-check-label" for="utm_campaign">utm_campaign</label></div>

                                
                                <!-- "+ More" - Always Visible -->
                                <div id="moreTrigger" class="form-check form-check-inline mb-1 mt-3">
                                  <input class="form-check-input" type="checkbox" id="moreBox" data-bs-toggle="modal" data-bs-target="#moreModal">
                                  <label class="form-check-label" for="moreBox">+ More</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                       <!-- Modal: Extra Parameters -->
                            <div class="modal fade" id="moreModal" tabindex="-1" aria-labelledby="moreModalLabel" aria-hidden="true">
                              <div class="modal-dialog modal-lg modal-top">
                                <div class="modal-content p-4">
                                  <h5 class="modal-title mb-3" id="moreModalLabel">Additional blocking Options</h5>
                                  
                                  <div class="container-fluid">
                                    <div class="row g-3">
                                      <!-- One checkbox per column, 3 per row -->
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="custom1"><label class="form-check-label" for="custom1">session_id</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="custom2"><label class="form-check-label" for="custom2">click_id</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="custom3"><label class="form-check-label" for="custom3">campaign_code</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="custom4"><label class="form-check-label" for="custom4">custom_event</label></div>
                                      </div>
                            
                                      <!-- Pixels -->
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="facebook_pixel"><label class="form-check-label" for="facebook_pixel">Facebook Pixel</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="google_pixel"><label class="form-check-label" for="google_pixel">Google Pixel</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="tiktok_pixel"><label class="form-check-label" for="tiktok_pixel">TikTok Pixel</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="conversion_event"><label class="form-check-label" for="conversion_event">Track Lead/Sale</label></div>
                                      </div>
                            
                                      <!-- Environment -->
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="ip_address"><label class="form-check-label" for="ip_address">IP Address</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="user_agent"><label class="form-check-label" for="user_agent">User Agent</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="browser"><label class="form-check-label" for="browser">Browser</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="os"><label class="form-check-label" for="os">OS/Device</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="location"><label class="form-check-label" for="location">Geo Location</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="referrer"><label class="form-check-label" for="referrer">Referrer</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="language"><label class="form-check-label" for="language">Browser Language</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="timestamp"><label class="form-check-label" for="timestamp">Timestamp</label></div>
                                      </div>
                            
                                      <!-- URL params -->
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="utm_term"><label class="form-check-label" for="utm_term">utm_term</label></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" id="utm_content"><label class="form-check-label" for="utm_content">utm_content</label></div>
                                      </div>
                                    </div>
                                  </div>
                            
                                  <div class="text-end mt-4">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                  </div>
                                </div>
                              </div>
                            </div>

                            </div>
                            
                            
                    
                           
                    
                            <!-- Notes -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Notes</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" name="note" rows="4" placeholder="Optional notes or comments"></textarea>
                              </div>
                            </div>

                            <!-- KPI -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">KPI</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" name="kpi" rows="2" placeholder="e.g. CR > 2.8%"></textarea>
                              </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Terms and Conditions</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" name="terms_conditions" rows="2" placeholder="Optional terms and conditions"></textarea>
                                <div class="form-check mt-2">
                                  <input class="form-check-input" type="checkbox" name="require_tnc" value="1" id="requireTnc">
                                  <label class="form-check-label" for="requireTnc">Require acceptance of terms and conditions</label>
                                </div>
                              </div>
                            </div>

                            <!-- Status -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Status</label>
                              <div class="col-sm-9">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" checked>
                                  <label class="form-check-label" for="statusActive">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="status" id="statusPending" value="pending">
                                  <label class="form-check-label" for="statusPending">Pending</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="status" id="statusPaused" value="paused">
                                  <label class="form-check-label" for="statusPaused">Paused</label>
                                </div>
                                <div class="form-text">Only Active campaigns redirect live traffic to the Main URL; Pending/Paused send visitors to the Safe URL.</div>
                              </div>
                            </div>

                            <!-- Hidden user ID -->
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
