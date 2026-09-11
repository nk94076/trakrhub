<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../required/config.php';
include '../required/auth.php';

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
                            <h5>Manage Offers</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                
                               <?php
                                        $user_id = $_SESSION['user_id'];
                                        $role = $_SESSION['role'];
                                        
                                        if ($role === 'admin') {
                                            $query = "SELECT * FROM domains ORDER BY id DESC";
                                            $stmt = $conn->prepare($query);
                                        } else {
                                            $query = "SELECT * FROM domains WHERE user_id = ? ORDER BY id DESC";
                                            $stmt = $conn->prepare($query);
                                            $stmt->bind_param("i", $user_id);
                                        }
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        ?>
                                        
                                        <table class="display table table-striped border-bottom-table border my-table" id="basic-1">
                                          <thead>
                                            <tr>
                                              <th>S.no</th>
                                              <th>Name</th>
                                              <th>Domain URL</th>
                                              <th>API Path</th>
                                              <th>Notes</th>
                                              <th>Status</th>
                                              <th>Action</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <?php $i = 1; while ($row = $result->fetch_assoc()) { ?>
                                            <tr>
                                              <td><?= $i++; ?></td>
                                              <td><?= htmlspecialchars($row['name']); ?></td>
                                              <td><?= htmlspecialchars($row['domain_url']); ?></td>
                                              <td><?= htmlspecialchars($row['api_path']); ?></td>
                                              <td><?= htmlspecialchars($row['note']); ?></td>
                                              <td>
                                                  <?php if ($row['status'] === 'active'): ?>
                                                    <button class="btn btn-success toggle-status-btn" data-id="<?= $row['id'] ?>" data-status="inactive">
                                                      Active
                                                    </button>
                                                  <?php else: ?>
                                                    <button class="btn btn-danger toggle-status-btn" data-id="<?= $row['id'] ?>" data-status="active">
                                                      Inactive
                                                    </button>
                                                  <?php endif; ?>
                                                </td>
                                              <td>
                                                <ul class="action">
                                                  <li class="edit-domain">
                                                    <a href="edit-domain.php?id=<?= $row['id']; ?>"><i class="icon-pencil-alt"></i></a>
                                                  </li>
                                                 <!-- Button inside your domain list -->
                                                    <li class="delete">
                                                      <a href="javascript:void(0);" class="delete-domain" data-id="<?= $row['id']; ?>">
                                                        <i class="fa-solid fa-trash-can text-danger"></i>
                                                      </a>
                                                    </li>
                                                    
                                                    <!-- jQuery AJAX Delete Script -->
                                                   <script>
                                                        document.addEventListener('DOMContentLoaded', function () {
                                                          document.querySelectorAll('.delete-domain').forEach(btn => {
                                                            btn.onclick = function () {
                                                              const domainId = this.dataset.id;
                                                              if (confirm("Are you sure you want to delete this domain?")) {
                                                                fetch('delete-domain.php', {
                                                                  method: 'POST',
                                                                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                                  body: 'id=' + encodeURIComponent(domainId)
                                                                })
                                                                .then(res => res.json())
                                                                .then(data => {
                                                                  if (data.status === 'success') {
                                                                    alert(data.message);
                                                                    location.reload();
                                                                  } else {
                                                                    alert(data.message);
                                                                  }
                                                                });
                                                              }
                                                            };
                                                          });
                                                        });
                                                        </script>


                                                </ul>
                                              </td>
                                            </tr>
                                            <?php } ?>
                                          </tbody>
                                        </table>


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
    document.querySelectorAll('.toggle-status-btn').forEach(button => {
      button.addEventListener('click', function () {
        const id = this.dataset.id;
        const newStatus = this.dataset.status;
    
        fetch('update-domain-status.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert(newStatus === 'active' ? '✅ Domain activated!' : '❌ Domain deactivated!');
            location.reload(); // Refresh to reflect change
          } else {
            alert('Error updating domain status');
          }
        });
      });
    });
    </script>
    

    
    

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>


</body>
</html>
