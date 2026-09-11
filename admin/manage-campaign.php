<?php
 require_once '../required/auth.php';
include '../required/config.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . "/cms/a/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'client';
$selected_domain = $_GET['domain_id'] ?? '';

// Fetch domains
if ($role === 'admin') {
    $domain_query = "SELECT id, domain_url FROM domains ORDER BY domain_url ASC";
    $domain_stmt = $conn->prepare($domain_query);
} else {
    $domain_query = "SELECT id, domain_url FROM domains WHERE user_id = ? ORDER BY domain_url ASC";
    $domain_stmt = $conn->prepare($domain_query);
    $domain_stmt->bind_param("i", $user_id);
}
$domain_stmt->execute();
$domain_result = $domain_stmt->get_result();

// Fetch offers based on domain
$offer_query = "SELECT * FROM offers WHERE 1";
$params = [];
$types = '';

if ($selected_domain !== '') {
    $offer_query .= " AND domain_id = ?";
    $params[] = $selected_domain;
    $types .= 'i';
}

$offer_query .= " ORDER BY position ASC";
$offer_stmt = $conn->prepare($offer_query);

if (!empty($params)) {
    $offer_stmt->bind_param($types, ...$params);
}

$offer_stmt->execute();
$result = $offer_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../required/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <style>
            .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: var(--bs-modal-padding);
            background: #ffffff;
            border-radius: 25px;
        }
        .page-wrapper .page-body-wrapper .page-title {
            padding: 0;
            margin: 0 -27px 28px;
        }
        svg {
            vertical-align: middle;
        }
        .loading {
            display: none;
            color: red;
        }
        .drag-handle {
            cursor: grab;
        }
        .switch input:checked + .switch-state {
    background-color: green !important; /* Active - Green */
}

.switch input:not(:checked) + .switch-state {
    background-color: red !important; /* Inactive - Red */
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
                                <form method="GET" class="mb-3">
                                  <label for="domainFilter" class="form-label fw-bold">Filter by Domain:</label>
                                  <div class="row">
                                    <div class="col-md-4">
                                      <select id="domainFilter" name="domain_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">-- All Domains --</option>
                                        <?php while ($domain = $domain_result->fetch_assoc()) { ?>
                                            <option value="<?= $domain['id']; ?>" <?= ($selected_domain == $domain['id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($domain['domain_url']); ?>
                                            </option>
                                        <?php } ?>
                                      </select>
                                    </div>
                                  </div>
                                </form>

                                <table class="display table table-striped border-bottom-table border my-table" id="sortable">
                                    <thead>
                                        <tr>
                                            <th>Sort</th>
                                            <th>Name</th>
                                            <th>Bonus</th>
                                            <th>Button Text</th>
                                            <th>Button Text 2</th>
                                            <th>URL</th>
                                            <th>URL 2</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr data-id="<?php echo $row['id']; ?>">
                                        <td class="drag-handle">☰</td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['bonus_title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['button_text']); ?></td>
                                        <td><?php echo htmlspecialchars($row['button_text2']); ?></td>
                                        <td>
                                            <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modalUrl1_<?php echo $row['id']; ?>">View Url</button>
                                            <div class="modal fade" id="modalUrl1_<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalUrl1_<?php echo $row['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body"> 
                                                            <div class="modal-toggle-wrapper">  
                                                                <p class="text-center c-light"><?php echo htmlspecialchars($row['url']); ?></p>
                                                                <button class="btn btn-secondary d-flex m-auto" type="button" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modalUrl2_<?php echo $row['id']; ?>">View Url 2</button>
                                            <div class="modal fade" id="modalUrl2_<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalUrl2_<?php echo $row['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body"> 
                                                            <div class="modal-toggle-wrapper">  
                                                                <p class="text-center c-light"><?php echo htmlspecialchars($row['url2']); ?></p>
                                                                <button class="btn btn-secondary d-flex m-auto" type="button" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" class="status-toggle" data-id="<?php echo $row['id']; ?>" <?php echo ($row['status'] == 1) ? 'checked' : ''; ?>>
                                                <span class="switch-state bg-primary"></span>
                                            </label>
                                        </td>
                                        <td> 
                                            <ul class="action"> 
                                                <li class="edit"> 
                                                    <a href="duplicate-campaign.php?id=<?php echo $row['id']; ?>">
                                                        <i class="icon-layers"></i>
                                                    </a>
                                                </li>
                                                <li class="edit-campaign"> 
                                                    <a href="edit-campaign.php?id=<?php echo $row['id']; ?>">
                                                        <i class="icon-pencil-alt"></i>
                                                    </a>
                                                </li>
                                                <li class="delete">
                                                    <a href="javascript:void(0);" class="delete-offer" data-id="<?php echo $row['id']; ?>">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <?php } ?>

                                    </tbody>
                                </table><br>
                                <div class="loading">Processing...</div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    $(".delete-offer").click(function () {
        var offerId = $(this).data("id");
        
        if (confirm("Are you sure you want to delete this offer?")) {
            $(".loading").show();

            $.ajax({
                url: "delete_offer.php",
                type: "POST",
                data: { id: offerId },
                success: function (response) {
                    $(".loading").hide();

                    if (response.trim() === "success") {
                        alert("Offer deleted successfully!");
                        location.reload();
                    } else {
                        alert("Error deleting offer.");
                    }
                },
                error: function () {
                    $(".loading").hide();
                    alert("Network error. Try again.");
                }
            });
        }
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
$(document).ready(function () {
    // Drag & Drop Sorting
    $("#sortable tbody").sortable({
        handle: ".drag-handle", // ☰ Drag Handle
        update: function () {
            var order = [];
            $("#sortable tbody tr").each(function () {
                order.push($(this).attr("data-id"));
            });

            $.post("update_order.php", { order: order }, function (response) {
                if (response.trim() === "success") {
                    alert("Sorting updated successfully!");
                } else {
                    alert("Error updating sorting.");
                }
            });
        }
    }).disableSelection();
});
</script>

<!-- jQuery & DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize DataTable
    var table = $(".my-table").DataTable({
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 10,
        "ordering": true,
        "paging": true,
        "info": true,
        "language": {
            "lengthMenu": "Show _MENU_ entries per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "<<",
                "last": ">>",
                "next": "»",
                "previous": "«"
            }
        }
    });

    // Fix Toggle Button Issue (Delegated Event Binding)
    $(".my-table").on("change", ".status-toggle", function () {
        var offerId = $(this).data("id");
        var newStatus = $(this).prop("checked") ? 1 : 0;

        $(".loading").show();

        $.ajax({
            url: "update_status.php",
            type: "POST",
            data: { id: offerId, status: newStatus },
            success: function (response) {
                $(".loading").hide();
                if (response == "success") {
                    alert("Offer status updated!");
                } else {
                    alert("Error updating status.");
                }
            },
            error: function () {
                $(".loading").hide();
                alert("Network error. Try again.");
            }
        });
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".status-toggle").forEach(function (checkbox) {
            updateSwitchColor(checkbox);

            checkbox.addEventListener("change", function () {
                updateSwitchColor(checkbox);
            });
        });
    });

    function updateSwitchColor(checkbox) {
        let switchState = checkbox.nextElementSibling;
        if (checkbox.checked) {
            switchState.style.backgroundColor = "green";
        } else {
            switchState.style.backgroundColor = "red";
        }
    }
</script>


</body>
</html>
