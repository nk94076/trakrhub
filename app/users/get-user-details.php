<?php
require_once '../required/config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        ?>
        <div class="container">
          <div class="row mb-2">
            <div class="col-md-4 fw-bold">👤 Name:</div>
            <div class="col-md-8"><?= htmlspecialchars($user['name']) ?></div>
          </div>

          <div class="row mb-2">
            <div class="col-md-4 fw-bold">📧 Email:</div>
            <div class="col-md-8"><?= htmlspecialchars($user['email']) ?></div>
          </div>

          <div class="row mb-2">
            <div class="col-md-4 fw-bold">🏢 Company:</div>
            <div class="col-md-8"><?= htmlspecialchars($user['company_name']) ?></div>
          </div>

          <div class="row mb-2">
            <div class="col-md-4 fw-bold">📱 Phone:</div>
            <div class="col-md-8"><?= htmlspecialchars($user['phone']) ?></div>
          </div>

          <div class="row mb-2">
            <div class="col-md-4 fw-bold">🎭 Role:</div>
            <div class="col-md-8 text-capitalize"><?= htmlspecialchars($user['role']) ?></div>
          </div>

          <div class="row mb-2">
            <div class="col-md-4 fw-bold">📍 City:</div>
            <div class="col-md-8"><?= htmlspecialchars($user['city']) ?></div>
          </div>

          <div class="row mb-2">
            <div class="col-md-4 fw-bold">📮 Postal Code:</div>
            <div class="col-md-8"><?= htmlspecialchars($user['postal_code']) ?></div>
          </div>

          <div class="row mb-2">
            <div class="col-md-4 fw-bold">📝 About:</div>
            <div class="col-md-8"><?= nl2br(htmlspecialchars($user['about_me'])) ?></div>
          </div>
        </div>
        <?php
    } else {
        echo "<div class='text-danger'>User not found.</div>";
    }
}
?>
