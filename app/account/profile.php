<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
ob_start();  // Output buffering start
require_once '../required/auth.php';
include '../required/config.php';


// Ensure user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Fetch full user details from the database
$user_email = $_SESSION['user_email'];
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

// Fetch full row as associative array
$user = $result->fetch_assoc();
$stmt->close();

// Example usage:
$user_name = $user['name'];
$company_name = $user['company_name'];
$position = $user['position'];
$phone = $user['phone'];
$address = $user['address'];
$city = $user['city'];
$username = $user['username'];
$role = $user['role'];
$postal_code = $user['postal_code'];
$about_me = $user['about_me'];
$profile_image = $user['profile_image'];
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

       

             <!-- Container-fluid starts-->
                <div class="container-fluid">
                    <div class="user-profile">
                        <div class="row">
                            <!-- user profile first-style start-->
                            <div class="col-sm-12">
                                <div class="card hovercard text-center common-user-image">
                                    <div class="cardheader">
                                        <div class="user-image">
                                            <div class="avatar">
                                                <div class="common-align">
                                                   <div>
                                                  <img src="<?= !empty($profile_image) ? $profile_image : 'https://adhook.adtrackr.org/assets/images/user/default.png' ?>" />
                                                  <input type="file" id="uploadInput" accept="image/*" style="display:none;" onchange="previewAndUpload(event)">
                                                  
                                                  <div class="icon-wrapper" onclick="triggerUpload()">
                                                    <i class="icofont icofont-pencil-alt-5"></i>
                                                  </div>
                                                </div>
                                                
                                                <script>
                                                function triggerUpload() {
                                                    document.getElementById('uploadInput').click();
                                                }
                                                
                                                function previewAndUpload(event) {
                                                    const file = event.target.files[0];
                                                if (!file) return;
                                                    const reader = new FileReader();
                                                    reader.onload = function() {
                                                        if (confirm("Upload this image?")) {
                                                            const formData = new FormData();
                                                            formData.append('profile_image', file);
                                                
                                                            fetch('upload_profile_image.php', {
                                                                method: 'POST',
                                                                body: formData
                                                            })
                                                            .then(res => res.text())
                                                            .then(data => {
                                                                alert('Image uploaded successfully!');
                                                                location.reload(); // refresh to update image
                                                            })
                                                            .catch(err => alert('Upload failed'));
                                                        }
                                                    };
                                                    reader.readAsDataURL(file);
                                                }
                                                </script>
                                                


                                                   
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- user profile first-style end-->
                            <div class="col-12">
                                <div class="card user-bio">
                                    <div class="card-body">
                                       <div class="row g-3">
    <div class="col-12">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-user-tie pe-2"></i>About Me</h6>
            <span><?php echo $user['about_me'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-user pe-2"></i>Name</h6>
            <span><?php echo $user['name'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-building pe-2"></i>Company Name</h6>
            <span><?php echo $user['company_name'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-user-tie pe-2"></i>Position</h6>
            <span><?php echo $user['position'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-envelope pe-2"></i>Email</h6>
            <span><?php echo $user['email'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-phone pe-2"></i>Phone</h6>
            <span><?php echo $user['phone'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-location-dot pe-2"></i>Address</h6>
            <span><?php echo $user['address'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-city pe-2"></i>City</h6>
            <span><?php echo $user['city'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-barcode pe-2"></i>Postal Code</h6>
            <span><?php echo $user['postal_code'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-user-circle pe-2"></i>Username</h6>
            <span><?php echo $user['username'] ?? 'N/A'; ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-user-tag pe-2"></i>Role</h6>
            <span><?php echo ucfirst($user['role'] ?? 'N/A'); ?></span>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="ttl-info text-start">
            <h6><i class="fa-solid fa-calendar-days pe-2"></i>Created At</h6>
            <span><?php echo $user['created_at'] ?? 'N/A'; ?></span>
    </div>
</div>

                                    </div>
                                </div>
                            </div>
                           
                    </div>
                </div>
            </div>
            <!-- Container-fluid Ends-->
              
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
