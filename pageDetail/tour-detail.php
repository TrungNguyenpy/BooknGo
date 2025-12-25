<?php 
include __DIR__ . '/../config/config.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Lấy id tour từ URL
if (!isset($_GET['id'])) {
  echo "Thiếu tham số ID!";
  exit;
}
$id = intval($_GET['id']); 

// Lấy thông tin tour + chi tiết
$sql = "SELECT t.*, d.departure_place, d.departure_schedule, d.introduction, d.itinerary, d.package_includes, d.terms
      FROM tours t
      LEFT JOIN tour_details d ON t.id = d.tour_id
      WHERE t.id = $id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  $tour = $result->fetch_assoc();
} else {
  echo "Không tìm thấy tour!";
  exit;
}
// Lấy ảnh tour từ bảng tour_images
$sqlImages = "SELECT * FROM tour_images WHERE tour_id = $id";
$resultImages = $conn->query($sqlImages);

$images = [];
if ($resultImages && $resultImages->num_rows > 0) {
    while ($row = $resultImages->fetch_assoc()) {
        $images[] = $row;
    }
}

// Lấy danh sách vé/khuyến mãi
$tourDetailId = $tour['id']; // hoặc $tour['tour_detail_id'] nếu JOIN ra sẵn
$sqlTickets = "SELECT * FROM tour_ticket WHERE tour_detail_id = $tourDetailId";

$tickets = $conn->query($sqlTickets);

// Tách departure_schedule thành mảng
$schedules = [];
if (!empty($tour['departure_schedule'])) {
  $schedules = explode(',', $tour['departure_schedule']);
}
// Nhóm vé theo ngày
$ticketsByDate = [];
if ($tickets->num_rows > 0) {
    while ($t = $tickets->fetch_assoc()) {
        $date = $t['date'] ?? 'unknown';
        $ticketsByDate[$date][] = $t;
    }
}

// Sinh 30 ngày tiếp theo từ ngày đầu tour hoặc hiện tại
$dates = [];
$startDateStr = !empty($tour['departure_schedule']) ? trim(explode(',', $tour['departure_schedule'])[0]) : date('Y-m-d');
$startDate = new DateTime($startDateStr);
$daysToShow = 15;
for ($i = 0; $i < $daysToShow; $i++) {
    $d = clone $startDate;
    $d->modify("+$i day");
    $dates[] = $d->format('Y-m-d');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($tour['name']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/main.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="header">
    <?php include '../includes/header.php'; ?>
</div>

<div class="container my-4">
  <div class="row" style="margin-top: 120px;">

    <!-- Tour Title -->
    <h1 style="font-weight: 600; font-size: 35px; color: #1b4f9d;">
      <?= htmlspecialchars($tour['name']) ?>
    </h1>

    <!-- Main Content -->
    <div >

    
       <!-- Banner -->
       <div class="gallery">
    <!-- Ảnh chính -->
    <div class="gallery-main">
        <?php if (!empty($images)): ?>
            <img src="../<?= htmlspecialchars($images[0]['image_path']) ?>" class="gallery-main-image"/>
        <?php else: ?>
            <img src="../img/default.png" class="gallery-main-image"/>
        <?php endif; ?>

        <div class="gallery-control gallery-control-prev"> 
            <i class="fa-solid fa-chevron-left"></i>
        </div>
        <div class="gallery-control gallery-control-next"> 
            <i class="fa-solid fa-chevron-right"></i>
        </div>
    </div>

    <!-- Thumbnails -->
    <div class="gallery-thumbnails">
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $img): ?>
                <div>
                    <img src="../<?= htmlspecialchars($img['image_path']) ?>" alt="">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div><img src="../img/default.png" alt="No image"></div>
        <?php endif; ?>
    </div>
</div>

      <!-- Tour Info -->
      <div class="card p-3 mb-4">
        <p><i class="bi bi-geo-alt"></i> Khởi hành: <?= htmlspecialchars($tour['departure_place'] ?? 'Đang cập nhật') ?></p>
        <p class="price">
          <span class="text-decoration text-secondary"><?= number_format($tour['price_old']) ?> VND</span><br>
         
        </p>
        <button class="btn btn-danger">Đặt tour ngay</button>
      </div>

      <!-- Tabs -->
      <ul class="nav nav-tabs mb-3" id="tourTab" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#gioithieu" type="button">Giới thiệu</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lichtrinh" type="button">Lịch trình</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#giatour" type="button">Tour bao gồm</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#dieukhoan" type="button">Điều khoản</button></li>
      </ul>

      <div class="tab-content" id="tourTabContent">
        <div class="tab-pane fade show active" id="gioithieu">
          <h5>Giới thiệu</h5>
          <p><?= nl2br(htmlspecialchars($tour['introduction'] ?? 'Đang cập nhật')) ?></p>
        </div>
        <div class="tab-pane fade" id="lichtrinh">
          <h5>Lịch trình</h5>
          <p><?= nl2br(htmlspecialchars($tour['itinerary'] ?? 'Đang cập nhật')) ?></p>
        </div>
        <div class="tab-pane fade" id="giatour">
          <h5>Tour Trọn Gói bao gồm</h5>
          <p><?= nl2br(htmlspecialchars($tour['package_includes'] ?? 'Đang cập nhật')) ?></p>
        </div>
        <div class="tab-pane fade" id="dieukhoan">
          <h5>Điều khoản</h5>
          <p><?= nl2br(htmlspecialchars($tour['terms'] ?? 'Đang cập nhật')) ?></p>
        </div>
      </div>

    <!-- Lịch chọn ngày -->
    <div class="date-list d-flex gap-2 mb-3" id="dateList">
        <?php foreach ($dates as $index => $date): 
            $d = new DateTime($date);
            $dayName = ['CN','Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7'][$d->format('w')];
            $day = $d->format('d');
            $month = $d->format('m');
        ?>
        <div class="date-item <?= $index === 0 ? 'active' : '' ?>" data-date="<?= $date ?>">
            <?= $dayName ?><br><?= $day ?>/<?= $month ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Vé sẽ hiển thị ở đây -->
    <div id="ticketsContainer"></div>
      
    

    <!-- Tour gợi ý -->
        <?php
        // Lấy 3 tour gợi ý (ngẫu nhiên hoặc theo điều kiện)
        $sql = "SELECT id, name, image FROM tours ORDER BY RAND() LIMIT 3";
        $result = $conn->query($sql);
        ?>
        <div class="mt-5">
          <h4>Tour gợi ý</h4>
          <div class="row">
            <?php while($row = $result->fetch_assoc()) { ?>
              <div class="col-md-4 mb-3">
                <div class="card tour-suggestion">
                <img src="<?= $base_url . htmlspecialchars($row['image']) ?>" 
                  class="card-img-top" 
                  alt="<?= htmlspecialchars($row['name']) ?>">

                  <div class="card-body">
                    <h6 class="card-title"><?php echo $row['name']; ?></h6>
                    <a href="tour-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
    </div>
     

   
  </div>
</div>


<div class="footer">
    <?php include '../includes/footer.php'; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
<script src="../js/slideShow.js"></script>
<script>
const ticketsByDate = <?php echo json_encode($ticketsByDate); ?>;
</script>

<script src="../js/tourTicket.js?v=<?php echo time(); ?>"></script>


</html>
