<?php 
include __DIR__ . '/../config/config.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Lấy id khách sạn từ URL
$hotel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($hotel_id > 0) {
    $sql = "SELECT * FROM hotels WHERE id = $hotel_id";
    $result = $conn->query($sql);
    $hotel = $result->fetch_assoc();
    if (!$hotel) {
        die("❌ Không tìm thấy khách sạn!");
    }
} else {
    die("❌ Thiếu ID khách sạn!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($hotel['name']) ?></title>
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/main.css?v=<?php echo time(); ?>">
</head>
<body>
  <div class="header">
    <?php include '../includes/header.php'; ?>
  </div>

  <main class="container my-4" style="margin: 0px 70px 0px 90px;">
      <?php
    // Lấy danh sách ảnh của khách sạn
        $sqlImg = "SELECT * FROM hotel_images WHERE hotel_id = $hotel_id ORDER BY id ASC";
        $resultImg = $conn->query($sqlImg);

        $images = [];
        while ($row = $resultImg->fetch_assoc()) {
            $images[] = $row;
        }

        // Ảnh lớn là ảnh đầu tiên
        $big    = $images[0] ?? null;
        // Ảnh nhỏ (tối đa 6)
        $thumbs = array_slice($images, 1, 6);

    ?>
    <h4 class="fw-bold" style="margin-top: 130px;">Nơi nghỉ dưỡng lý tưởng</h4> 
    <p class="text-muted">Các lựa chọn phổ biến nhất cho du khách từ Việt Nam</p>
    <div class="gallery-340">
      <!-- Ảnh lớn (40%) -->
      <?php if ($big): ?>
        <div class="gallery-left">
          <img src="<?= htmlspecialchars($base_url . '/' . $big['image_url']) ?>" 
              alt="<?= htmlspecialchars($big['caption'] ?? $hotel['name']) ?>">
        </div>
      <?php endif; ?>

      <!-- Lưới ảnh nhỏ -->
      <div class="gallery-right">
        <?php foreach ($thumbs as $i => $t): ?>
          <div class="tile <?= ($i === count($thumbs) - 1) ? 'overlay' : '' ?>">
            <img src="<?= htmlspecialchars($base_url . '/' . $t['image_url']) ?>" 
                alt="<?= htmlspecialchars($t['caption'] ?? $hotel['name']) ?>">
            <?php if ($i === count($thumbs) - 1): ?>
              <div class="overlay-text">Xem tất cả...</div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>


    <!-- Tên khách sạn -->
    <h4 class="fw-bold" style="margin-top: 20px;">
      <?= htmlspecialchars($hotel['name']) ?> 
      <span class="badge bg-primary"><?= htmlspecialchars($hotel['label']) ?></span>
    </h4>

    <div class="row">
      
  <div>
              
  <!-- Thông tin khách sạn -->
  <section class="hotel-info card shadow-sm border-0 rounded-4 mb-4">
    <div class="row g-0 align-items-center">
      
      <!-- Ảnh bên trái -->
      <div class="col-md-5">
        <img src="<?= $base_url . htmlspecialchars($hotel['image']) ?>" 
            alt="<?= htmlspecialchars($hotel['name']) ?>" 
            class="img-fluid rounded-start w-100 h-100 object-fit-cover">
      </div>

      <!-- Thông tin bên phải -->
      <div class="col-md-7">
        <div class="card-body p-4">
          <h4 class="card-title mb-3 fw-bold text-primary">
            <i class="bi bi-info-circle me-2"></i> Thông tin khách sạn
          </h4>

          <!-- Địa điểm -->
          <p class="mb-2">
            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
            <strong>Địa điểm:</strong> <?= htmlspecialchars($hotel['location']) ?>
          </p>

          <!-- Đánh giá -->
          <p class="mb-2">
            <span class="text-warning me-1">⭐</span>
            <strong><?= $hotel['rating'] ?>/10</strong> 
            <span class="text-muted">(<?= $hotel['reviews'] ?> đánh giá)</span>
          </p>

          <!-- Giảm giá -->
          <?php if ($hotel['discount_percent'] > 0): ?>
            <p class="mb-1">
              <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                Giảm giá <?= $hotel['discount_percent'] ?>%
              </span>
            </p>
          <?php endif; ?>

          <!-- Giá -->
          <div class="hotel-price mt-2">
            <div class="old-price text-decoration-line-through text-secondary small">
              <?= number_format($hotel['price_old']) ?> VND
            </div>
            <div class="new-price text-danger fw-bold fs-4">
              <?= number_format($hotel['price_new']) ?> VND 
              <span class="fs-6 fw-normal text-dark">/ đêm</span>
              <a href="../services/booking-form.php?type=hotel&id=<?= $hotel['id'] ?>" 
                class="btn btn-success" 
                style="margin-left: 10px;">
                Đặt phòng
              </a>


            </div>
          </div>
        </div>
      </div>

  </div>
</section>



        <!-- Tiện nghi (demo cứng, bạn có thể thêm bảng khác để quản lý) -->
        <section class="mb-4">
          <h4>Tiện nghi & Dịch vụ</h4>
          <div class="d-flex flex-wrap gap-3">
            <span>🏊 Hồ bơi</span>
            <span>🍳 Ăn sáng miễn phí</span>
            <span>🛜 Wifi tốc độ cao</span>
            <span>🚗 Bãi đỗ xe</span>
            <span>🏋️ Phòng gym</span>
          </div>
        </section>

        <!-- Bản đồ -->
        <section class="mb-4">
          <h4>Bản đồ</h4>
          <iframe src="https://www.google.com/maps?q=<?= urlencode($hotel['location']) ?>&output=embed" 
            width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </section>

        <!-- Chính sách -->
        <section class="mb-4">
          <h4>Chính sách & Quy định</h4>
          <ul>
            <li>Nhận phòng: 14:00</li>
            <li>Trả phòng: 12:00</li>
            <li>Hủy phòng miễn phí trước 48h</li>
          </ul>
        </section>
        <?php

        // Lấy 3 khách sạn gợi ý 
        $sql = "SELECT id, name, image, price_new FROM hotels ORDER BY RAND() LIMIT 3";
        $result = $conn->query($sql);
        ?>
       <!-- Khách sạn gợi ý -->
       <div class="mt-5">
          <h4>Các khách sạn tương tự</h4>
          <div class="row">
            <?php while($row = $result->fetch_assoc()) { ?>
              <div class="col-md-4 mb-3">
                <div class="card tour-suggestion">
                <img src="<?= $base_url . htmlspecialchars($row['image']) ?>" 
                  class="card-img-top" 
                  alt="<?= htmlspecialchars($row['name']) ?>">

                  <div class="card-body">
                    <h6 class="card-title"><?php echo $row['name']; ?></h6>
                    <p class="price"><?php echo number_format($row['price_new']); ?>đ</p>
                    <a href="hotel-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
        

<!-- Form đánh giá -->

      </div>

      <!-- Right Column -->
    

    </div>
  </main>

  <div class="footer">
    <?php include '../includes/footer.php'; ?>
  </div>
</body>


</html>
