<?php
session_start(); // Start the session
// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    // If not logged in, redirect to login.php
    header("Location: https://" . $_SERVER['HTTP_HOST'] . "/cms/a/login.php");
    exit();
}

// If logged in, continue loading the dashboard
?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <?php include '../required/head.php'; ?>
  </head>
  <style>
      .page-wrapper .page-body-wrapper .page-title {
    padding: 0px 0px;
    margin: 0 -27px 28px;
  
      }
.form-switch .form-check-input {

    width: 4em;

}   

.form-check-input {
    width: 1em;
    height: 2em;
}

      svg {
    vertical-align: middle;
}
  </style>
  <body> 
    <!-- loader starts-->
    <div class="loader-wrapper">
      <div class="loader-index"> <span></span></div>
      <svg>
        <defs></defs>
        <filter id="goo">
          <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
          <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo"> </fecolormatrix>
        </filter>
      </svg>
    </div>
    <!-- loader ends-->
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
      <!-- Page Header Start-->
      <?php include '../required/header.php'; ?>
      <!-- Page Header Ends -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
         <!-- Page Sidebar Start-->
       <?php include '../required/side-bar.php'; ?>
        <!-- Page Sidebar Ends-->
         <div class="page-body">
          <div class="container-fluid">
            <div class="page-title">
              <div class="row">
                
                <div class="col-sm-6">
                  
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid dashboard-10">
            <div class="row">
                    <div class="col-xl-12">
                <div class="card height-equal">
                  <div class="card-header">
                    <h5>Add New Affiliate</h5>
                    <p class="f-m-light mt-1">
                       Affiliate Info</p>
                  </div>
                  <form class="form theme-form">
                    <div class="card-body custom-input">
                      <div class="row">
                        <div class="col">
                            
                            <div class="mb-3 row">
                            <label class="col-sm-3">Page Heading</label>
                            <div class="col-sm-9">
                              <input class="form-control" type="text" placeholder="Page Title" name="title">
                            </div>
                          </div>
                          
                          
                            <div class="mb-3 row">
                            <label class="col-sm-3">Page discription</label>
                            <div class="col-sm-9">
                              <input class="form-control" type="text" placeholder="discription" name="discription">
                            </div>
                          </div>
                                    
                          <div class="mb-3 row">
                            <label class="col-sm-3" for="formFile">Background image</label>
                            <div class="col-sm-9">
                              <input class="form-control" id="formFile" type="file">
                            </div>
                          </div>
                          
                          
                          <div class="mb-3 row">
                            <label class="col-sm-3 pt-0">Color Picker</label>
                            <div class="col-sm-2">
                              <input class="form-control form-control-color" type="color" value="#7366FF">
                            </div>
                          </div>
                          
                           <div class="mb-3 row">
                            <label class="col-sm-3" for="formFile">Upload Image</label>
                            <div class="col-sm-9">
                              <input class="form-control" id="formFile" type="file">
                            </div>
                          </div>
                          
                          <div class="mb-3 row">
                            <label class="col-sm-3">Name</label>
                            <div class="col-sm-9">
                              <input class="form-control" type="text" placeholder="Name" name="name">
                            </div>
                          </div>
                          
                          <div class="mb-3 row">
                            <label class="col-sm-3">Bonus Title</label>
                            <div class="col-sm-9">
                              <input class="form-control" type="text" placeholder="Bonus Title" name="Bonus Title">
                            </div>
                          </div>
                          
                          <div class="mb-3 row">
                            <label class="col-sm-3">Button Text</label>
                            <div class="col-sm-9">
                              <input class="form-control" type="text" placeholder="Button Text" name="Button Text">
                            </div>
                          </div>
                         
                         
                          <div class="mb-3 row">
                            <label class="col-sm-3">URL</label>
                            <div class="col-sm-9">
                              <input class="form-control" type="url" value="https://getbootstrap.com">
                            </div>
                          </div>
                          
                           <div class="mb-3 row">
                            <label class="col-sm-3">Status</label>
                            <div class="col-sm-9">
                              <div class="form-check form-switch form-check-inline">
                              <input class="form-check-input check-size" id="flexSwitchCheckDefault2" type="checkbox" role="switch" checked="">
                            </div>
                            </div>
                          </div>
                          
                         
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
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
        <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12 footer-copyright text-center">
                <p class="mb-0">Copyright <span class="year-update"> </span> © Adtrackr </p>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
<?php include '../required/footerjs.php'; ?>
  </body>
</html>