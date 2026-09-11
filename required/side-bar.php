<?php
 require_once '../required/auth.php';
include '../required/config.php';
$base_url = 'https://app.trakrhub.com/';

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';
$features = [];

// Admin ko sab allow karo
if ($role === 'admin') {
  $features = ['dashboard','offers', 'domains', 'templates', 'offers', 'reports', 'offers_page_setup', 'users', 'account', 'support'];
} else {
  // Client ke liye table se fetch karo
  $stmt = $conn->prepare("SELECT permission FROM user_permissions WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $features[] = $row['permission'];
  }
}

function can_access($section, $features, $role) {
  return $role === 'admin' || in_array($section, $features);
}

?>

<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
  <div>
    <div class="logo-wrapper">
      <a href="<?= $base_url ?>admin/dashboard.php">
        <img class="img-fluid for-light" src="../assets/images/logo/logo_dark.png" alt="" width="85%">
        <img class="img-fluid for-dark" src="../assets/images/logo/logo_dark.png" width="85%" alt="">
      </a>
      <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
      <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
    </div>
    <div class="logo-icon-wrapper"><a href="<?= $base_url ?>admin/dashboard.php"><img class="img-fluid" src="../assets/images/logo/logo-icon.png" alt=""></a></div>
    <nav class="sidebar-main">
      <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
      <div id="sidebar-menu">
        <ul class="sidebar-links" id="simple-bar">
          <li class="back-btn"><a href="<?= $base_url ?>admin/dashboard.php"><img class="img-fluid" src="../assets/images/logo/logo-icon.png" alt=""></a>
            <div class="mobile-back text-end"><span>Back</span><i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i></div>
          </li>
          <li class="pin-title sidebar-main-title"><div><h6>Pinned</h6></div></li>
          <li class="sidebar-main-title"><div><h6 class="lan-1">General</h6></div></li>

          <?php if (can_access('dashboard', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title link-nav" href="<?= $base_url ?>admin/dashboard.php">
              <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-home"></use></svg>
              <svg class="fill-icon"><use href="../assets/svg/icon-sprite.svg#fill-home"></use></svg><span>Dashboard</span>
            </a>
          </li>
          <?php endif; ?>

      
       <?php if (can_access('domains', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title" href="#">
              <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-report"></use>
                      </svg>
              <svg class="fill-icon">
                        <use href="../assets/svg/icon-sprite.svg#fill-report"></use>
                      </svg>
                      
                      <span>Track Data</span></a>
            <ul class="sidebar-submenu">
              <li><a href="<?= $base_url ?>track/create-link.php">Create Tracking Link</a></li>
              <li><a href="<?= $base_url ?>track/manage-link.php">Manage Link</a></li>
            </ul>
          </li>
          <?php endif; ?>
          
          <?php if (can_access('domains', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title" href="#">
              <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-internationalization"></use>
                      </svg>
              <svg class="fill-icon">
                        <use href="../assets/svg/icon-sprite.svg#fill-internationalization"></use>
                      </svg><span>Manage Domains</span></a>
            <ul class="sidebar-submenu">
              <li><a href="<?= $base_url ?>domain/add-domain.php">Add Domain</a></li>
              <li><a href="<?= $base_url ?>domain/manage-domains.php">Manage-Domains</a></li>
            </ul>
          </li>
          <?php endif; ?>

          <?php if (can_access('templates', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title" href="#">
              <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-landing-page"></use>
                      </svg>
             <svg class="fill-icon">
                        <use href="../assets/svg/icon-sprite.svg#fill-landing-page"></use>
                      </svg><span>Safe Page Setup</span></a>
            <ul class="sidebar-submenu">
              <li><a href="<?= $base_url ?>templates/upload-template.php">Upload Themes</a></li>
              <li><a href="<?= $base_url ?>templates/manage-templates.php">Manage Themes</a></li>
             
            </ul>
          </li>
          <?php endif; ?>
          
               <?php if (can_access('offers_page_setup', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title" href="#">
              <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-sample-page"></use></svg>
              <svg class="fill-icon"><use href="../assets/svg/icon-sprite.svg#fill-sample-page"></use></svg><span>Money Page Setup</span></a>
            <ul class="sidebar-submenu">
              <li><a href="<?= $base_url ?>offer-page/upload-offer-template.php">Upload Templates</a></li>
              <li><a href="<?= $base_url ?>offer-page/offer-template.php">Offers Templates</a></li>
            </ul>
          </li>
          <?php endif; ?>
          

          <?php if (can_access('offers', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title" href="#">
              <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-calendar"></use></svg>
              <svg class="fill-icon"><use href="../assets/svg/icon-sprite.svg#fill-calendar"></use></svg><span>Manage Offers</span></a>
            <ul class="sidebar-submenu">
              <li><a href="<?= $base_url ?>admin/manage-campaign.php">Manage offers</a></li>
              
              <li><a href="<?= $base_url ?>admin/create-campaign.php">Create offers</a></li>
            </ul>
          </li>
          <?php endif; ?>

          <?php if (can_access('reports', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title" href="#">
              <svg class="stroke-icon"><use href="../assets/svg/icon-sprite.svg#stroke-reports"></use></svg>
              <svg class="fill-icon"><use href="../assets/svg/icon-sprite.svg#fill-reports"></use></svg><span>Manage Reports</span></a>
            <ul class="sidebar-submenu">
              <li><a href="<?= $base_url ?>track/track-data.php">Track Data</a></li>
              <li><a href="<?= $base_url ?>admin/campaigns-reports.php">Offers Reports</a></li>
              <li><a href="<?= $base_url ?>admin/ip-report.php">Check IP report</a></li>
              <li><a href="<?= $base_url ?>admin/ip-report.php">Check Gclid</a></li>
            </ul>
          </li>
          <?php endif; ?>

     

          <?php if (can_access('users', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title" href="#">
              <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-user"></use>
                      </svg>
              <svg class="fill-icon">
                        <use href="../assets/svg/icon-sprite.svg#fill-user"></use>
                      </svg><span>Manage Users</span></a>
            <ul class="sidebar-submenu">
              <li><a href="<?= $base_url ?>users/add-user.php">Add User</a></li>
              <li><a href="<?= $base_url ?>users/manage-users.php">Manage Users</o></li>
            
            </ul>
          </li>
          <?php endif; ?>

          <?php if (can_access('account', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title link-nav" href="<?= $base_url ?>account/profile.php">
              <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-user"></use>
                      </svg>
              <svg class="fill-icon">
                        <use href="../assets/svg/icon-sprite.svg#fill-user"></use>
                      </svg><span>Account</span></a>
          </li>
          <?php endif; ?>

          <?php if (can_access('support', $features, $role)): ?>
          <li class="sidebar-list">
            <i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title link-nav" href="#">
             <svg class="stroke-icon">
                        <use href="../assets/svg/icon-sprite.svg#stroke-support-tickets"></use>
                      </svg>
              <svg class="fill-icon">
                        <use href="../assets/svg/icon-sprite.svg#fill-support-tickets"></use>
                      </svg><span>Support</span></a>
          </li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
    </nav>
  </div>
</div>
