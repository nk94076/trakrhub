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
                <div class="container-fluid dashboard-10">
                    <div class="row">
                        
             <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Upload Template</h5>
                  </div>
               <form id="templateUploadForm" enctype="multipart/form-data" class="form theme-form" method="POST">
                  <div class="card-body custom-input">
                    <div class="row">
                      <div class="col">
                
                        <div class="mb-3 row">
                          <label class="col-sm-3">Template Name</label>
                          <div class="col-sm-9">
                            <input class="form-control" type="text" name="template_name" placeholder="Template Name" required>
                          </div>
                        </div>
                        <div class="mb-3 row">
                              <label class="col-sm-3">Category</label>
                              <div class="col-sm-9">
                                <select class="form-control" name="category" required>
                                  <option value="general">General</option>
                                  <option value="gaming">Gaming</option>
                                  <option value="ipl">IPL</option>
                                  <option value="casino">Casino</option>
                                  <option value="sports">Sports</option>
                                  <option value="ecommerce">E-commerce</option>
                                </select>
                              </div>
                            </div>

                
                        <div class="mb-3 row">
                          <label class="col-sm-3">Upload Image</label>
                          <div class="col-sm-9">
                            <input class="form-control form-control-sm" type="file" name="template_image" required>
                          </div>
                        </div>
                
                        <div class="mb-3 row">
                          <label class="col-sm-3">Upload Template</label>
                          <div class="col-sm-9">
                            <input class="form-control form-control-sm" type="file" name="template_zip" accept=".zip" required>
                          </div>
                        </div>
                
                        <!-- Progress Bar -->
                        <div class="mb-3 row">
                          <div class="col-sm-9 offset-sm-3">
                            <div class="progress" style="height: 25px;">
                              <div id="uploadProgress" class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                            </div>
                          </div>
                        </div>
                
                        <div class="card-footer text-end">
                          <div class="col-sm-9 offset-sm-3">
                            <button class="btn btn-primary me-3" type="submit">Submit</button>
                            <input class="btn btn-light" type="reset" value="Cancel">
                          </div>
                        </div>
                
                      </div>
                    </div>
                  </div>
                </form>
                
                <!-- JavaScript to Handle Upload Progress -->
                <script>
                 document.getElementById('templateUploadForm').addEventListener('submit', function (e) {
                      e.preventDefault();
                      const form = e.target;
                      const formData = new FormData(form);
                      const xhr = new XMLHttpRequest();
                    
                      xhr.open('POST', 'save-template.php', true);
                    
                      xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                          const percent = Math.round((e.loaded / e.total) * 100);
                          const progressBar = document.getElementById('uploadProgress');
                          progressBar.style.width = percent + '%';
                          progressBar.textContent = percent + '%';
                          if (percent === 100) {
                            progressBar.classList.add('bg-success');
                          }
                        }
                      });
                    
                      xhr.onload = function () {
                        try {
                          const res = JSON.parse(xhr.responseText);
                          alert(res.message);
                          if (res.status === 'success') {
                            window.location.href = 'manage-templates.php';
                          }
                        } catch (err) {
                          console.error('Invalid JSON:', xhr.responseText);
                          alert('❌ Unexpected response from server');
                        }
                      };
                    
                      xhr.send(formData);
                    });

                </script>



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
