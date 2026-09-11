<?php
require_once '../required/auth.php';
require_once '../required/config.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; // e.g., "admin" or "client"

// Adjust query based on role
if ($role === 'admin') {
    $query = "SELECT * FROM campaigns ORDER BY id DESC";
    $stmt = $conn->prepare($query);
} else {
    $query = "SELECT * FROM campaigns WHERE user_id = ? ORDER BY id DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
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
                                
                                <table class="display table table-striped border-bottom-table border my-table">
                                      <thead>
                                        <tr>
                                          <th>ID</th>
                                          <th>Campaign Name</th>
                                          <th>Preview URL</th>
                                          <th>Affiliate URL</th>
                                          <th>Landing URL</th>
                                          <th>Offer Type</th>
                                          <th>Daily Cap</th>
                                          <th>Total Cap</th>
                                          <th>Status</th>
                                          <th>Action</th>
                                        </tr>
                                      </thead>
                                     <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                      <tr>
                                        <td><?= $row['id']; ?></td>
                                        <td><?= htmlspecialchars($row['campaign_name']); ?></td>
                                        <td>
                                          <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#preview_<?= $row['id']; ?>">View</button>
                                          <div class="modal fade" id="preview_<?= $row['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                              <div class="modal-content">
                                                <div class="modal-body text-center">
                                                  <p><?= $row['preview_url']; ?></p>
                                                  <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td><a href="<?= $row['affiliate_url']; ?>" target="_blank" class="btn btn-primary btn-sm">Go</a></td>
                                        <td><a href="<?= $row['landing_url'] ? htmlspecialchars($row['landing_url']) : '#' ?>" target="_blank" class="btn btn-primary btn-sm">Go</a></td>
                                        <td><?= $row['offer_type']; ?></td>
                                        <td><?= $row['daily_cap']; ?></td>
                                        <td><?= $row['total_cap']; ?></td>
                                        <td>
                                          <label class="switch">
                                            <input type="checkbox" class="status-toggle" data-id="<?= $row['id']; ?>" <?= $row['status'] ? 'checked' : ''; ?>>
                                            <span class="switch-state bg-primary"></span>
                                          </label>
                                        </td>
                                        <td>
                                          <ul class="action">
                                            <li>
                                              <a href="javascript:void(0);" class="delete-campaign" data-id="<?= $row['id']; ?>"><i class="fa-solid fa-trash-can text-danger"></i></a>
                                            </li>
                                          </ul>
                                        </td>
                                      </tr>
                                    <?php } ?>
                                    </tbody>
                                                                        </table>
                                    <br>
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
  document.querySelectorAll('.delete-campaign').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      if (confirm('⚠ Are you sure you want to delete this campaign?')) {
        fetch('delete-campaign.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ id })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('✅ Campaign deleted successfully!');
            location.reload();
          } else {
            alert('❌ Failed to delete campaign!');
          }
        })
        .catch(err => {
          alert('❌ Something went wrong!');
          console.error(err);
        });
      }
    });
  });
</script>


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
