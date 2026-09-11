<?php

include '../required/config.php';
include '../required/auth.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../required/head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
            <div class="row">
              <div class="col-sm-12">
                    <?php
                    require_once '../required/config.php';
                    require_once '../required/auth.php';
                    
                    $limit = 8;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;
                    
                    $user_id = $_SESSION['user_id'];
                    $category_filter = $_GET['category'] ?? '';
                    $search = $_GET['search'] ?? '';
                    
                    // Prepare category query
                    $category_query = "SELECT DISTINCT category FROM templates WHERE user_id = ? AND category IS NOT NULL AND category != ''";
                    $cat_stmt = $conn->prepare($category_query);
                    $cat_stmt->bind_param("i", $user_id);
                    $cat_stmt->execute();
                    $categories = $cat_stmt->get_result();
                    
                    // Prepare main query
                    $query = "SELECT * FROM templates WHERE user_id = ?";
                    $params = [$user_id];
                    $types = "i";
                    
                    if (!empty($category_filter)) {
                        $query .= " AND category = ?";
                        $params[] = $category_filter;
                        $types .= "s";
                    }
                    
                    if (!empty($search)) {
                        $query .= " AND name LIKE ?";
                        $params[] = "%$search%";
                        $types .= "s";
                    }
                    
                    $query .= " ORDER BY id DESC LIMIT ? OFFSET ?";
                    $params[] = $limit;
                    $params[] = $offset;
                    $types .= "ii";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?>
                    
                    <div class="card">
                      <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Manage Themes</h5>
                        <form method="GET" class="d-flex align-items-center">
                          <select name="category" class="form-select me-2">
                            <option value="">All Categories</option>
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                              <option value="<?= $cat['category'] ?>" <?= $cat['category'] == $category_filter ? 'selected' : '' ?>>
                                <?= $cat['category'] ?>
                              </option>
                            <?php endwhile; ?>
                          </select>
                          <input type="text" name="search" class="form-control me-2" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                          <button class="btn btn-primary">Go</button>
                        </form>
                      </div>
                    
                      <div class="card-body">
                        <div class="row">
                          <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-md-3 col-sm-6 mb-4">
                              <div class="card h-100">
                                <img src="<?= $row['image'] ?>" alt="Template Image" class="card-img-top" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                  <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-truncate" style="max-width: 60%"><?= htmlspecialchars($row['name']) ?></h6>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($row['category']) ?></span>
                                  </div>
                                </div>
                                <div class="card-footer text-center">
                                  <a href="#" class="btn btn-primary btn-sm install-trigger" data-template-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#installModal<?= $row['id'] ?>">Install</a>

                                            
                                     <!-- Modal -->
                                        <div class="modal fade" id="installModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="installModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                          <div class="modal-dialog">
                                            <form method="POST" action="install-template.php">
                                              <div class="modal-content">
                                                <div class="modal-header">
                                                  <h5 class="modal-title" id="installModalLabel<?= $row['id'] ?>">Install Template</h5>
                                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                  <input type="hidden" name="template_id" value="<?= $row['id'] ?>">
                                        
                                                  <div class="mb-3">
                                                    <label for="domain_id" class="form-label">Select Domain</label>
                                                    <select name="domain_id" class="form-select" required>
                                                      <option value="">-- Select Domain --</option>
                                                      <?php
                                                      $user_id = $_SESSION['user_id'];
                                                      $domain_query = $conn->prepare("SELECT id, domain_url FROM domains WHERE user_id = ? AND status = 'active'");
                                                      $domain_query->bind_param("i", $user_id);
                                                      $domain_query->execute();
                                                      $domain_result = $domain_query->get_result();
                                                      while ($domain = $domain_result->fetch_assoc()) {
                                                          echo "<option value='{$domain['id']}'>{$domain['domain_url']}</option>";
                                                      }
                                                      ?>
                                                    </select>
                                                  </div>
                                        
                                                </div>
                                                <div class="modal-footer">
                                                  <button type="submit" class="btn btn-success">Install</button>
                                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                          </form>
                                        </div>
                                    </div>
                                                                                    
                                   <a href="delete-template.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this template?')">
                                    Delete
                                  </a>
                                </div>
                              </div>
                            </div>
                          <?php endwhile; ?>
                        </div>
                      </div>
                    
                      <?php
                      $countQuery = "SELECT COUNT(*) FROM templates WHERE user_id = ?";
                      $countParams = [$user_id];
                      $countTypes = "i";
                    
                      if (!empty($category_filter)) {
                          $countQuery .= " AND category = ?";
                          $countParams[] = $category_filter;
                          $countTypes .= "s";
                  }
                    
                      if (!empty($search)) {
                          $countQuery .= " AND name LIKE ?";
                          $countParams[] = "%$search%";
                          $countTypes .= "s";
                  }
                    
                      $countStmt = $conn->prepare($countQuery);
                      $countStmt->bind_param($countTypes, ...$countParams);
                      $countStmt->execute();
                      $countStmt->bind_result($total_templates);
                      $countStmt->fetch();
                      $countStmt->close();
                    
                      $total_pages = ceil($total_templates / $limit);
                      ?>
                    
                      <div class="card-footer d-flex justify-content-between align-items-center">
                        <span>Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $total_templates) ?> of <?= $total_templates ?> themes</span>
                        <nav aria-label="Page navigation">
                          <ul class="pagination mb-0">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                              <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&category=<?= urlencode($category_filter) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                              </li>
                            <?php endfor; ?>
                          </ul>
                        </nav>
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
                            <p class="mb-0">Copyright <span class="year-update"></span> © Adtrackr</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    
    </div>
    <script>
document.querySelectorAll('.install-trigger').forEach(button => {
  button.addEventListener('click', function () {
    const templateId = this.dataset.templateId;
    const modal = document.getElementById('installModal' + templateId);
    
    if (modal) {
      const form = modal.querySelector('form');
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch('install-template.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if (data.status === 'success') {
            window.location.href = 'manage-templates.php';
          }
        })
        .catch(err => {
          alert('❌ Something went wrong!');
        });
      }, { once: true }); // prevent multiple bindings
    }
  });
});
</script>

    
    <?php include '../required/footerjs.php'; ?>
    
    
    
</body>
</html>
