<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . "/a/login.php");
    exit();
}

include '../required/config.php';


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
                        
            <div class="col-xl-12">
                <div class="card height-equal">
                  <div class="card-header">
                    <h5>Add Domain</h5>
                  </div>
                  <form class="form theme-form" method="POST" action="save-domain.php">
                      <div class="card-body custom-input">
                        <div class="row">
                          <div class="col">
                            <!-- Domain Name -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Domain Name</label>
                              <div class="col-sm-9">
                                <input class="form-control" type="text" name="domain_name" placeholder="Type your domain name (e.g. Client 1)">
                              </div>
                            </div>
                    
                            <!-- Domain URL -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Domain URL</label>
                              <div class="col-sm-9">
                                <input class="form-control" type="url" name="domain_url" placeholder="https://example.com/">
                              </div>
                            </div>
                    
                            <!-- API Path -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">API Path</label>
                              <div class="col-sm-8">
                                <input class="form-control" type="url" name="api_path" placeholder="https://example.com/agent_api.php">
                                <span style="font-size:12px;">Example : https://Domain.com/agent_api.php ( Replace Domain with your Domain Name )</span>
                              </div>
                             <div class="col-sm-1">
                                 <button class="btn btn-primary me-2" type="#">request</button>
                                  <!-- <div class="d-flex">
                                    <a href="#" onclick="downloadWithAlert()">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-download">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                      </svg>
                                    </a>
                                    <div class="flex-grow-1 align-self-center">
                                      <h6 class="mt-0"></h6>
                                    </div>
                                  </div> -->
                                </div> 
                                
                                
                                <script>
                                  function downloadWithAlert() {
                                    alert("Download and Extract zip to Main Directory");
                                    const link = document.createElement("a");
                                    link.href = "https://adhook.adtrackr.org/download/agent_api.zip";
                                    link.setAttribute("download", "agent_api.zip");
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);
                                  }
                                </script>


                            </div>
                    
                            <!-- Notes -->
                            <div class="mb-3 row">
                              <label class="col-sm-3">Notes</label>
                              <div class="col-sm-9">
                                <textarea class="form-control" name="note" rows="4" placeholder="Optional notes or comments"></textarea>
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
</body>
</html>
