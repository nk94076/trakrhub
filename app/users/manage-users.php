<?php
 require_once '../required/auth.php';
include '../required/config.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php include '../required/head.php'; ?>
    <style>
        .page-wrapper .page-body-wrapper .page-title {
            padding: 0;
            margin: 0 -27px 28px;
        }
        svg {
            vertical-align: middle;
        }
  #userDetailContent .row {
    padding: 4px 0;
    border-bottom: 1px solid #eee;
  }
  #userDetailContent .fw-bold {
    color: #2c3e50;
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
          <div class="container-fluid user-list-wrapper">
            <div class="row">
              <!-- Zero Configuration  Starts-->
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header pb-0 card-no-border">
                    <h5>Manage users</h5>
                     </div>
                  <div class="card-body">
                    <div class="table-responsive custom-scrollbar">
                      <?php
                                $users = [];
                                $sql = "SELECT id, name, email, company_name, role,username, status FROM users ORDER BY id DESC";
                                $result = $conn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                  while ($row = $result->fetch_assoc()) {
                                    $users[] = $row;
                                  }
                                }
                                ?>
                                
                                <table class="display table-striped border" id="basic-1">
                                  <thead>
                                    <tr>
                                      <th>S.No</th>
                                      <th>Company</th>
                                      <th>Name</th>
                                      <th>Email</th>
                                      <th>Username</th>
                                      <th>Status</th>
                                      <th>Action</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php if (!empty($users)): $i = 1; foreach ($users as $user): ?>
                                    <tr>
                                     <td>
                                          <img src="../assets/images/plus.png" width="20px;" style="cursor:pointer"
                                               data-id="<?= $user['id'] ?>" class="open-user-modal">
                                          <?= $i++; ?>
                                        </td>

                                      <td><?= htmlspecialchars($user['company_name']) ?></td>
                                      <td><?= htmlspecialchars($user['name']) ?></td>
                                      <td><?= htmlspecialchars($user['email']) ?></td>
                                      <td><?= htmlspecialchars($user['username']) ?></td>
                                      <td>
                                        <span class="badge rounded-pill badge-<?= $user['status'] ? 'success' : 'danger' ?>">
                                          <?= $user['status'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                      </td>
                                      <td>
                                        <ul class="action">
                                          <li class="edit">
                                            <a href="edit-user.php?id=<?= $user['id'] ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                          </li>
                                          <li class="delete">
                                            <a href="delete-user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">
                                              <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                          </li>
                                          <li class="login">
                                            <a href="https://adhook.adtrackr.org/a/login.php?user_id=<?= $user['id'] ?>" target="_blank">
                                              <i class="fa-solid fa-right-to-bracket"></i>
                                            </a>
                                          </li>
                                          <!-- triger code -->
                                         <?php if ($user['role'] === 'client'): ?>
                                                  <li>
                                                    <a href="#" class="key-icon" data-user-id="<?= $user['id'] ?>" data-bs-toggle="modal" data-bs-target="#permissionsModal">
                                                      <i class="fa fa-key" aria-hidden="true"></i>
                                                    </a>
                                                  </li>
                                                <?php endif; ?>
                                                
                                                
                                                <!-- triger code end-->



                                        </ul>
                                      </td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                  </tbody>
                                </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Zero Configuration  Ends-->
            </div>
          </div>
          <!-- Container-fluid Ends-->
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
    
                <!-- user permission model--->
            <!-- user permission modal -->
            <div class="modal fade" id="permissionsModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="permissionsModalLabel">Assign Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form id="permissionsForm">
                      <div class="table-responsive">
                        <table class="table table-bordered">
                          <thead class="table-light">
                            <tr>
                              <th>Module</th>
                              <th class="text-center">Allow</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr><td>Dashboard</td><td class="text-center"><input type="checkbox" name="permissions[]" value="dashboard"></td></tr>
                            <tr><td>Campaigns</td><td class="text-center"><input type="checkbox" name="permissions[]" value="campaigns"></td></tr>
                            <tr><td>Domains</td><td class="text-center"><input type="checkbox" name="permissions[]" value="domains"></td></tr>
                            <tr><td>Templates</td><td class="text-center"><input type="checkbox" name="permissions[]" value="templates"></td></tr>
                            <tr><td>Offers</td><td class="text-center"><input type="checkbox" name="permissions[]" value="offers"></td></tr>
                            <tr><td>Reports</td><td class="text-center"><input type="checkbox" name="permissions[]" value="reports"></td></tr>
                            <tr><td>Offers Page Setup</td><td class="text-center"><input type="checkbox" name="permissions[]" value="offers_page_setup"></td></tr>
                            <tr><td>Users</td><td class="text-center"><input type="checkbox" name="permissions[]" value="users"></td></tr>
                            <tr><td>Account</td><td class="text-center"><input type="checkbox" name="permissions[]" value="account"></td></tr>
                            <tr><td>Support</td><td class="text-center"><input type="checkbox" name="permissions[]" value="support"></td></tr>
                          </tbody>
                        </table>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="savePermissions">Save</button>
                  </div>
                </div>
              </div>
            </div>
            
            
          <script>
let selectedUserId = null;

// Load permissions when icon is clicked
document.querySelectorAll('.key-icon').forEach(keyIcon => {
  keyIcon.addEventListener('click', function () {
    selectedUserId = this.dataset.userId;

    fetch(`get-user-permissions.php?user_id=${selectedUserId}`)
      .then(response => response.json())
      .then(data => {
        document.querySelectorAll('input[name="permissions[]"]').forEach(box => {
          box.checked = data.includes(box.value);
        });
      });
  });
});

// Save permissions
document.getElementById('savePermissions').addEventListener('click', () => {
  const permissions = [];
  document.querySelectorAll('input[name="permissions[]"]:checked').forEach(box => {
    permissions.push(box.value);
  });

  fetch(`save-user-permissions.php?user_id=${selectedUserId}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ permissions })
  })
  .then(res => res.json())
  .then(res => {
    if (res.status === 'success') {
      alert('✅ Permissions saved!');

      // ✅ Properly hide the modal
      const modalEl = document.getElementById('permissionsModal');
      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.hide();

      // ✅ Force remove backdrop (main fix)
      setTimeout(() => {
        document.body.classList.remove('modal-open');
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
      }, 500);
    } else {
      alert('❌ Error: ' + res.message);
    }
  })
  .catch(err => alert('❌ Error: ' + err.message));
});

</script>



    
    
            <!-- User Detail Modal -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="userDetailContent">
                <!-- User info will load here -->
                <div class="text-center">Loading...</div>
              </div>
            </div>
          </div>
        </div>
        
        <script>
                document.addEventListener("DOMContentLoaded", () => {
                  document.querySelectorAll(".open-user-modal").forEach(img => {
                    img.addEventListener("click", function () {
                      const userId = this.dataset.id;
                      const modalBody = document.getElementById("userDetailContent");
                      modalBody.innerHTML = "Loading...";
                
                      fetch(`get-user-details.php?id=${userId}`)
                        .then(response => response.text())
                        .then(data => {
                          modalBody.innerHTML = data;
                          const modal = new bootstrap.Modal(document.getElementById('userModal'));
                          modal.show();
                        })
                        .catch(() => {
                          modalBody.innerHTML = "<div class='text-danger'>Failed to load user data.</div>";
                        });
                    });
                  });
                });
                </script>


    <?php include '../required/footerjs.php'; ?>
</body>
</html>
