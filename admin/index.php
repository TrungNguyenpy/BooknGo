<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();


if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}

if ($_SESSION['user']['role'] != 1) {
  header("Location: ../user/index.php");
  exit();
}

$user = $_SESSION['user'];
?>


<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Panel - Bootstrap 5 (Demo)</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/admin.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>


  <!-- ADMIN LAYOUT -->
  <div id="adminView">
 <!-- Sidebar -->
         <?php include 'sidebar.php'; ?>

    <!-- Main content -->
    <div class="main-content">
      <div class="topbar">
        <div class="d-flex align-items-center gap-3">
          <button id="mobileMenuBtn" class="btn btn-light d-lg-none"><i class="bi bi-list"></i></button>
          <h5 id="pageTitle" class="mb-0">Dashboard</h5>
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">
          <div class="d-md-block text-muted small">Xin chào, <b id="currentUser">Admin</b></div>
          <div class="dropdown">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="profileMenu" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="https://ui-avatars.com/api/?name=Admin&background=0d6efd&color=fff&size=32" alt="avatar" class="rounded-circle me-2">
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileMenu">
              <li><a class="dropdown-item" href="#" data-view="settings">Cài đặt</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php" id="logoutBtn2">Đăng xuất</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="p-4">
        <!-- Views container -->
        <div id="viewsContainer">
          <!-- Dashboard -->
          <?php include 'dashboard.php';?>
          
          <!-- Users -->
          <?php include 'modules/user/users.php';?>


          <!--  -->
          <?php include 'modules/flights/flight.php';?>
          <?php include 'modules/hotels/hotels.php';?>
          <?php include 'modules/tours/tours.php';?>
          <!--  -->
          <?php include 'modules/travelGuide/destination.php';?>
          <!-- Roles -->
          <?php include 'roles.php';?>
          <!-- Settings -->
          <?php include 'settings.php';?>
        </div>
      </div>
    </div>
  </div>

</body>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script src="assets/js/main.js"></script>
</html>