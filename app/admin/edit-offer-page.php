<?php
 require_once '../required/auth.php';
include '../required/config.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../required/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <style>
        
        .page-wrapper .page-body-wrapper .page-title {
            padding: 0;
            margin: 0 -27px 28px;
        }
        svg {
            vertical-align: middle;
        }


    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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

                <!-- Manage Offers Table -->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header pb-0 card-no-border">
                            <h5>Edit offers page</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="card">
                                  <div class="card-header card-no-border pb-0">
                                    <h5>Edit Title</h5>
                                  </div>
                                  <div class="card-body">
                                    <div id="area1" contenteditable="true">
                                      <h1>Title</h1>
                                       </div>
                                  </div>
                                </div>
                                
                                 <div class="card">
                                  <div class="card-header card-no-border pb-0">
                                    <h5>Edit Content</h5>
                                  </div>
                                  <div class="card-body">
                                    <div id="area1" contenteditable="true">
                                      
                                      <p>Being the best designer requires a combination of exceptional creativity, technical expertise, and a deep understanding of user experience. I pride myself on my ability to think outside the box and create visually stunning designs that captivate and inspire. With a keen eye for detail, I carefully craft every element of a design, ensuring that it is not only aesthetically pleasing but also functional and user-friendly. I stay up to date with the latest design trends and constantly push myself to explore new techniques and technologies.</p>
                                    </div>
                                  </div>
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
