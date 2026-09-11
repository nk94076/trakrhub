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
                            $limit = 8;
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $offset = ($page - 1) * $limit;
                            $user_id = $_SESSION['user_id'];
                            $search = $_GET['search'] ?? '';

                            $query = "SELECT * FROM offer_templates WHERE user_id = ?";
                            $params = [$user_id];
                            $types = "i";

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
                                    <h5 class="mb-0">Manage Offer Templates</h5>
                                    <form method="GET" class="d-flex align-items-center">
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
                                                        <h6 class="mb-0 text-truncate" style="max-width: 100%"><?= htmlspecialchars($row['name']) ?></h6>
                                            </div>
                                            <div class="card-footer text-center">
                                                <a href="#" class="btn btn-primary btn-sm install-offer-trigger" data-template-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#installOfferModal<?= $row['id'] ?>">Install</a>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="installOfferModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="installModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="POST" action="install-offer-template.php">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Install Offer Template</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="template_id" value="<?= $row['id'] ?>">
                                                                    <label for="domain_id" class="form-label">Select Domain</label>
                                                                    <select name="domain_id" class="form-select" required>
                                                                        <option value="">-- Select Domain --</option>
                                                                        <?php
                                                                        $domain_sql = "SELECT d.id, d.domain_url FROM domains d 
                                                                                              JOIN domain_templates dt ON d.id = dt.domain_id
                                                                                              WHERE d.user_id = ? AND d.status = 'active' 
                                                                                              GROUP BY d.id";
                                                                                $domain_stmt = $conn->prepare($domain_sql);
                                                                                $domain_stmt->bind_param("i", $user_id);
                                                                                $domain_stmt->execute();
                                                                                $domain_result = $domain_stmt->get_result();
                                                                                while ($domain = $domain_result->fetch_assoc()) {
                                                                                    echo "<option value='{$domain['id']}'>{$domain['domain_url']}</option>";
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-success">Install</button>
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                </div>
                                                              </form>
                                                            </div>
                                                          </div>
                                                       <a href="delete-offer-template.php?id=<?= $row['id'] ?>" 
   class="btn btn-sm btn-danger" 
   onclick="return confirm('Are you sure you want to delete this offer template?')">
   Delete
</a>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>

                                <?php
                                $countStmt = $conn->prepare("SELECT COUNT(*) FROM offer_templates WHERE user_id = ?");
                                $countStmt->bind_param("i", $user_id);
                                $countStmt->execute();
                                $countStmt->bind_result($total_templates);
                                $countStmt->fetch();
                                $countStmt->close();

                                $total_pages = ceil($total_templates / $limit);
                                ?>

                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <span>Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $total_templates) ?> of <?= $total_templates ?> offer templates</span>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination mb-0">
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
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
document.querySelectorAll('.install-offer-trigger').forEach(button => {
    button.addEventListener('click', function () {
        const templateId = this.dataset.templateId;
        const modal = document.getElementById('installOfferModal' + templateId);

        if (modal) {
            const form = modal.querySelector('form');
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);

                fetch('install-offer-template.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        window.location.href = 'offer-template.php';
                    }
                })
                .catch(err => {
                    alert('❌ Something went wrong!');
            }, { once: true });
        }
    });
});
</script>

<?php include '../required/footerjs.php'; ?>

</body>
</html>