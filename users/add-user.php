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
                <form class="card" method="POST" action="save-user.php">
                      <div class="card-header">
                        <h5 class="card-title">Add User</h5>
                        <div class="card-options">
                          <a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                          <a class="card-options-remove" href="#" data-bs-toggle="card-remove"><i class="fe fe-x"></i></a>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="row custom-input">
                    
                          <div class="col-xxl-5 box-col-12">
                            <div class="mb-3">
                              <label class="form-label" for="companyName">Company</label>
                              <input class="form-control" id="companyName" name="company" type="text" placeholder="Company">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-3 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customUsername">Username</label>
                              <input class="form-control" id="customUsername" name="username" type="text" placeholder="Username">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-4 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customAddress">Email Address</label>
                              <input class="form-control" id="customAddress" name="email" type="email" placeholder="Email">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-md-6">
                            <div class="mb-3">
                              <label class="form-label" for="customPassword">Password</label>
                              <input class="form-control" id="customPassword" name="password" type="password" placeholder="Create password">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-md-6">
                            <div class="mb-3">
                              <label class="form-label" for="customPhone">Phone Number</label>
                              <input class="form-control" id="customPhone" name="phone" type="text" placeholder="Phone Number">
                              
                            </div>
                          </div>
                    
                          <div class="col-md-12">
                            <div class="mb-3">
                              <label class="form-label" for="customAddress">Address</label>
                              <textarea class="form-control" id="customAddress" name="address" rows="2.5" placeholder="Home address"></textarea>
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-4 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customCity">City</label>
                              <input class="form-control" id="customCity" name="city" type="text" placeholder="City">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-3 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customPostalCode">Postal Code</label>
                              <input class="form-control" id="customPostalCode" name="postal_code" type="number" placeholder="Postal code">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-3 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customRole">User Role</label>
                              <select class="form-control" id="customRole" name="role">
                                <option value="client">Client</option>
                                <option value="admin">Admin</option>
                              </select>
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-3 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="statusSwitch">Status</label><br>
                              <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" checked>
                                <label class="form-check-label" for="statusSwitch">Active</label>
                              </div>
                            </div>
                          </div>
                    
                          <div class="col-md-12">
                            <div>
                              <label class="form-label" for="aboutMeDesc">About Me</label>
                              <textarea class="form-control" id="aboutMeDesc" name="about_me" rows="4" placeholder="Enter about your description"></textarea>
                            </div>
                          </div>
                    
                        </div>
                      </div>
                      <div class="card-footer text-end">
                        <button class="btn btn-primary" type="submit">Create User</button>
                      </div>
                    </form>
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
