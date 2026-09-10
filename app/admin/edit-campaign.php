<?php
 require_once '../required/auth.php';
include '../required/config.php';

$offer = null;
if (isset($_GET['id'])) {
    $offer_id = $_GET['id'];

    // Fetch Offer Details
    $stmt = $conn->prepare("SELECT * FROM offers WHERE id = ?");
    $stmt->bind_param("i", $offer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $offer = $result->fetch_assoc();
}

// If offer is not found, set default values
$offer_name = $offer['name'] ?? '';
$bonus_title = $offer['bonus_title'] ?? '';
$button_text = $offer['button_text'] ?? '';
$button_text2 = $offer['button_text2'] ?? '';
$url = $offer['url'] ?? '';
$url2 = $offer['url2'] ?? '';
$status = isset($offer['status']) && $offer['status'] == 1 ? 'checked' : '';
$image = $offer['image'] ?? '';
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
.dispay_img {
    border: solid 1px;
    vertical-align: middle;
    padding: 5px;
    border-radius: 10px;
    background: #1d1d1d;
    margin: 10px;
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
                  <form class="form theme-form" action="update_offer.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="offer_id" value="<?php echo $_GET['id'] ?? ''; ?>">
                    
                        <div class="card-body custom-input">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3 row">
                                        <label class="col-sm-3" for="formFile">Upload Image</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" id="formFile" type="file" name="image" <?php echo empty($image) ? 'required' : ''; ?>>
                                            <?php if (!empty($image)) { ?>
                                                <img class="dispay_img" src="../image/<?php echo $image; ?>" alt="Offer Image" width="100">
                                            <?php } ?>
                                        </div>
                                    </div>
                    
                                    <div class="mb-3 row">
                                        <label class="col-sm-3">Name</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" placeholder="Name" name="name" value="<?php echo $offer_name; ?>" required>
                                        </div>
                                    </div>
                    
                                    <div class="mb-3 row">
                                        <label class="col-sm-3">Bonus Title</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" placeholder="Bonus Title" name="bonus_title" value="<?php echo $bonus_title; ?>" required>
                                        </div>
                                    </div>
                    
                                    <div class="mb-3 row">
                                        <label class="col-sm-3">Button Text</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" placeholder="Button Text" name="button_text" value="<?php echo $button_text; ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3">Button Text 2</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" placeholder="Button Text" name="button_text2" value="<?php echo $button_text2; ?>" required>
                                        </div>
                                    </div>
                    
                                    <div class="mb-3 row">
                                        <label class="col-sm-3">URL</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="url" placeholder="Enter URL" name="url" value="<?php echo $url; ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-3">URL 2</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="url" placeholder="Enter URL" name="url2" value="<?php echo $url2; ?>" required>
                                        </div>
                                    </div>
                    
                                    <div class="mb-3 row">
                                        <label class="col-sm-3">Status</label>
                                        <div class="col-sm-9">
                                            <div class="form-check form-switch form-check-inline">
                                                <input class="form-check-input check-size" id="flexSwitchCheckDefault2" type="checkbox" name="status" <?php echo $status; ?>>
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