<?php
include __DIR__ . '/../../config/config.php';


$hotel_id = intval($_GET['id'] ?? 0);
if ($hotel_id <= 0) {
  die("❌ Không tìm thấy khách sạn.");
}

// Lấy dữ liệu khách sạn theo id
$sql = "SELECT * FROM hotels WHERE id = $hotel_id  LIMIT 1";
$result = mysqli_query($conn, $sql);
$hotel = mysqli_fetch_assoc($result);

if (!$hotel) {
    echo "<p class='text-danger'>Không tìm thấy khách sạn!</p>";
    exit;
}

// Ngày mặc định (ngày hôm nay + 1)
$checkin  = date("Y-m-d");
$checkout = date("Y-m-d", strtotime("+1 day"));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đặt phòng - <?= htmlspecialchars($hotel['name']) ?></title>

</head>
<body>
<div class="container my-4">
  <div class="row">
    <!-- Cột trái: Form -->
    <div class="col-lg-7">
      <form method="POST" action="bookingHotel_process.php">
        <input type="hidden" name="hotel_id" value="<?= $hotel['id'] ?>">
        <input type="hidden" name="service_type" value="hotels">

        <!-- Thông tin liên hệ -->
        <div class="card shadow-sm mb-3">
          <div class="card-body">
            <div class="section-title">Thông tin liên hệ</div>
            <div class="mb-3">
              <label class="form-label">Họ tên đầy đủ</label>
              <input type="text" class="form-control" name="fullname" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" class="form-control" name="phone" required>
              </div>
            </div>
          </div>
        </div>

       <!-- Ngày và số lượng -->
        <div class="card shadow-sm mb-3">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Ngày nhận phòng</label>
                <input type="date" class="form-control" id="checkin" name="checkin" value="<?= $checkin ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Ngày trả phòng</label>
                <input type="date" class="form-control" id="checkout" name="checkout" value="<?= $checkout ?>" required>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Số khách</label>
                <input type="number" class="form-control" id="guests" name="guests" value="2" min="1">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Số phòng</label>
                <input type="number" class="form-control" id="rooms" name="rooms" value="1" min="1">
              </div>
            </div>
          </div>
        </div>

            <!-- Chọn thời điểm thanh toán -->
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <div class="section-title">Chọn thời điểm thanh toán</div>
              
              <!-- Thanh toán khi nhận phòng -->
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment" id="pay_later" value="pay_later" checked>
                <label class="form-check-label" for="pay_later">Thanh toán khi nhận phòng</label>
              </div>

              <!-- Thanh toán ngay -->
              <div class="form-check">
                <input class="form-check-input" type="radio" name="payment" id="pay_now" value="pay_now">
                <label class="form-check-label" for="pay_now">Thanh toán ngay</label>
              </div>
            </div>
          </div>

          <!-- Modal chọn phương thức thanh toán -->
          <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Chọn phương thức thanh toán</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p>Vui lòng chọn một trong các phương thức thanh toán sau:</p>
                  
                  <div class="list-group">
                    <button type="button" class="list-group-item list-group-item-action payment-option">
                      💳 Visa / MasterCard
                    </button>
                    <button type="button" class="list-group-item list-group-item-action payment-option">
                      📱 Momo
                    </button>
                    <button type="button" class="list-group-item list-group-item-action payment-option">
                      🏦 Chuyển khoản ngân hàng (QR code)
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>


        <!-- Hiển thị giá -->
        <div class="alert alert-info">
          <p id="price-info">
            Tổng tiền: <strong>0 VND</strong>
          </p>
        </div>

        <!-- Ẩn giá 1 đêm để JS tính toán -->
        <input type="hidden" id="price_per_night" value="<?= (int)$hotel['price_new'] ?>">

        <button type="submit" class="btn btn-primary w-100 py-3">Tiếp tục thanh toán</button>
        </form>

    </div>

    <!-- Cột phải: Thông tin phòng + giá -->
        <div class="col-lg-5">
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <img src="<?= $base_url . htmlspecialchars($hotel['image']) ?>" 
                  alt="<?= htmlspecialchars($hotel['name']) ?>" 
                  class="img-fluid mb-2">

              <h5 class="fw-bold">
                <?= htmlspecialchars($hotel['name']) ?>
                <?= str_repeat("⭐", (int)$hotel['rating']) ?>
              </h5>

              <p class="text-muted mb-1">Địa điểm: <?= htmlspecialchars($hotel['location']) ?></p>
              <hr>
              <div class="d-flex justify-content-between">
                <span>Giá 1 đêm</span>
                <span class="price"><?= number_format($hotel['price_new'], 0, ',', '.') ?> VND</span>
              </div>
            </div>
          </div>
        </div>


  </div>
</div>
</body>
<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/bookingHotel.js"></script>
<script>
 const showResultModal = <?= ($payment_method ?? 'pay_later') === 'pay_later' ? 'true' : 'false' ?>;
</script>
</html>
