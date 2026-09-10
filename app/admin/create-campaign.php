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
  </style>
  <body> 
    <!-- loader starts-->
    <div class="loader-wrapper">
      <div class="loader-index"> <span></span></div>
      <svg>
        <defs></defs>
        <filter id="goo(¾
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
          <form class="form theme-form" action="save_offer.php" method="POST" enctype="multipart/form-data">
    <div class="card-body custom-input">
        <div class="row">
            <div class="col">
                <!-- Domain Dropdown -->
                <div class="mb-3 row">
                    <label class="col-sm-3">Select Domain</label>
                    <div class="col-sm-9">
                        <select name="domain_id" class="form-select" required>
                            <option value="">-- Select Domain --</option>
                            <?php
                            $user_id = $_SESSION['user_id'];
                            $query = "SELECT d.id, d.domain_url 
                                      FROM domains d 
                                      JOIN domain_offer_templates dot ON d.id = dot.domain_id 
                                      WHERE d.user_id = ? AND d.status = 'active'";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>{$row['domain_url']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Rest of your fields -->
                <div class="mb-3 row">
                    <label class="col-sm-3">Upload Image</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="file" name="image" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3">Name</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="name" placeholder="Name" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3">Bonus Title</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="bonus_title" placeholder="Bonus Title" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3">Button Text</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="button_text" placeholder="Button Text" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3">Button Text 2</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="button_text2" placeholder="Button Text 2" required>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3">URL</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="url" name="url" placeholder="Enter URL" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3">URL 2</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="url" name="url2" placeholder="Enter URL" required>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3">Status</label>
                    <div class="col-sm-9">
                        <div class="form-check form-switch form-check-inline">
                            <input class="form-check-input check-size" type="checkbox" name="status" checked>
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
                <p class="mb-0">Copyright <span class="year-update"> </span> Â© Adtrackr </p>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
<?php include '../required/footerjs.php'; ?>
  </body>
</html>