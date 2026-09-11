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
              <?php
                    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                      echo "<script>alert('Invalid User ID.'); window.location.href='manage-users.php';</script>";
                      exit;
                    }
                    
                    $user_id = $_GET['id'];
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    
                    if (!$user) {
                      echo "<script>alert('User not found.'); window.location.href='manage-users.php';</script>";
                      exit;
                    }
                    ?>
                    
                    <form class="card" method="POST" action="update-user.php">
                      <input type="hidden" name="id" value="<?= $user['id'] ?>">
                      <div class="card-header">
                        <h5 class="card-title">Edit User</h5>
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
                              <input class="form-control" id="companyName" name="company" type="text" value="<?= $user['company_name'] ?>">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-3 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customUsername">Username</label>
                              <input class="form-control" id="customUsername" name="username" type="text" value="<?= $user['username'] ?>">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-4 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customAddress">Email Address</label>
                              <input class="form-control" id="customAddress" name="email" type="email" value="<?= $user['email'] ?>">
                            </div>
                          </div>
                         <div class="col-sm-6 col-md-6">
                              <div class="mb-3">
                                <label class="form-label" for="customPassword">Password (leave blank to keep current)</label>
                                <div class="input-group">
                                  <input class="form-control" id="customPassword" name="password" type="password" placeholder="Change password (optional)">
                                  <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">👁</button>
                                </div>
                              </div>
                            </div>
                            
                            <script>
                            function togglePassword() {
                              const input = document.getElementById("customPassword");
                              input.type = input.type === "password" ? "text" : "password";
                            }
                            </script>


                    
                          <div class="col-sm-6 col-md-6">
                            <div class="mb-3">
                              <label class="form-label" for="customPhone">Phone Number</label>
                              <input class="form-control" id="customPhone" name="phone" type="text" value="<?= $user['phone'] ?>">
                            </div>
                          </div>
                    
                          <div class="col-md-12">
                            <div class="mb-3">
                              <label class="form-label" for="customAddress">Address</label>
                              <textarea class="form-control" name="address" rows="2.5"><?= $user['address'] ?></textarea>
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-4 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customCity">City</label>
                              <input class="form-control" id="customCity" name="city" type="text" value="<?= $user['city'] ?>">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-3 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customPostalCode">Postal Code</label>
                              <input class="form-control" id="customPostalCode" name="postal_code" type="number" value="<?= $user['postal_code'] ?>">
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-3 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="customRole">User Role</label>
                              <select class="form-control" id="customRole" name="role">
                                <option value="client" <?= $user['role'] == 'client' ? 'selected' : '' ?>>Client</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                              </select>
                            </div>
                          </div>
                    
                          <div class="col-sm-6 col-xxl-3 box-col-6">
                            <div class="mb-3">
                              <label class="form-label" for="statusSwitch">Status</label><br>
                              <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" <?= $user['status'] ? 'checked' : '' ?> >
                                <label class="form-check-label" for="statusSwitch">Active</label>
                              </div>
                            </div>
                          </div>
                    
                          <div class="col-md-12">
                            <div>
                              <label class="form-label" for="aboutMeDesc">About Me</label>
                              <textarea class="form-control" id="aboutMeDesc" name="about_me" rows="4"><?= $user['about_me'] ?></textarea>
                            </div>
                          </div>
                    
                        </div>
                      </div>
                      <div class="card-footer text-end">
                        <button class="btn btn-primary" type="submit">Update User</button>
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
