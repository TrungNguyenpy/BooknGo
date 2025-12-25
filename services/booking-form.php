<?php
include __DIR__ . '/../config/config.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Lấy loại dịch vụ (hotel | flight | tour)
$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'flight';

// Lấy id dịch vụ
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Chỉ cho phép type nằm trong danh sách hợp lệ
$validTypes = ['hotel', 'flight', 'tour'];
if (!in_array($type, $validTypes)) {
    $type = 'flight'; // mặc định
}
?>
<!DOCTYPE html>
<html lang="vi">
<head> 
  <meta charset="UTF-8">
  <title>Đặt dịch vụ - BooknGo</title>
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
  <link rel="stylesheet" href="../css/service.css?v=<?php echo time(); ?>">
</head>
<body >

<!-- Header -->
<header>
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark">
      <a class="navbar-brand" href="<?= $base_url ?>/index.php">BooknGo</a>
      <div class="ms-auto text-white">
        📞 Hotline: 1900 1234
      </div>
    </nav>
  </div>
</header>



<!-- Booking Form -->
<div class="container ">
  <div class="row">
    <div class="col-12" style="    margin-top: 40px;
    display: flex;
    justify-content: center;
    align-items: center;">
      <div class="p-5 bg-white shadow-sm">
        <h2 class="text-center text-primary mb-4">Đặt dịch vụ</h2>

        <?php
        if ($type === 'hotel') {
            include 'booking/booking-hotel-form.php';
        } elseif ($type === 'flight') {
            include 'booking/booking-flight-form.php';
        } elseif ($type === 'tour') {
            include 'booking/booking-tour-form.php';
        } else {
            echo "<p class='text-danger text-center'>Loại dịch vụ không hợp lệ!</p>";
        }
        ?>
      </div>
    </div>
  </div>
</div>


</body>
</html>
