<?php
include __DIR__ . '/../../config/config.php';
session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Lấy detail_id từ GET hoặc POST
$detail_id = $_POST['id'] ?? $_GET['id'] ?? 0;
$detail_id = intval($detail_id);

if ($detail_id <= 0) {
    echo "<p class='text-danger'>❌ Thiếu ID chuyến bay.</p>";
    return;
}

// Lấy chi tiết chuyến bay
$stmt = $conn->prepare("
    SELECT fd.*, f.flight_name, f.departure AS dep_airport, f.arrival AS arr_airport, f.airline, f.price_new
    FROM flight_details fd
    JOIN flights f ON fd.flight_id = f.id
    WHERE fd.id = ?
");
$stmt->bind_param("i", $detail_id);
$stmt->execute();
$flightDetail = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$flightDetail) {
    echo "<p class='text-danger'>❌ Không tìm thấy chuyến bay.</p>";
    return;
    
}

// Giá cơ bản
$basePrice = (float)$flightDetail['price_new'];
?>
<div class="row">
  <!-- Cột trái: Form -->
  <div class="col-lg-8">
    <form method="POST" action="bookingFlight_process.php" id="bookingForm">
      <input type="hidden" name="type" value="flight">
      <input type="hidden" name="detail_id" value="<?= $detail_id ?>">
      <input type="hidden" id="base_price" value="<?= htmlspecialchars($basePrice) ?>">

      <!-- Thông tin liên hệ -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">Thông tin liên hệ (nhận vé/phiếu thanh toán)</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Họ*</label>
              <input type="text" class="form-control" name="last_name" placeholder="như trên CMND" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tên đệm và tên*</label>
              <input type="text" class="form-control" name="first_name" placeholder="như trên CMND" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Điện thoại*</label>
              <input type="text" class="form-control" name="phone" placeholder="+84..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email*</label>
              <input type="email" class="form-control" name="email" placeholder="email@example.com" required>
            </div>
          </div>
        </div>
      </div>

      <!-- Chọn số vé theo loại -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">Chọn số vé</div>
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Người lớn (>=12 tuổi)</label>
              <select class="form-select ticket-qty" id="adult_count" name="adult_count" required>
                <?php for($i=1;$i<=9;$i++): ?>
                  <option value="<?= $i ?>" <?= $i==1 ? 'selected' : '' ?>><?= $i ?> vé</option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Trẻ em (2-11 tuổi)</label>
              <select class="form-select ticket-qty" id="child_count" name="child_count">
                <?php for($i=0;$i<=9;$i++): ?>
                  <option value="<?= $i ?>"><?= $i ?> vé</option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Em bé (&lt;2 tuổi)</label>
              <select class="form-select ticket-qty" id="infant_count" name="infant_count">
                <?php for($i=0;$i<=5;$i++): ?>
                  <option value="<?= $i ?>"><?= $i ?> vé</option>
                <?php endfor; ?>
              </select>
            </div>
          </div>

          <hr>

          <div id="passenger_list">
            <!-- JS sẽ sinh form hành khách vào đây -->
          </div>

        </div>
      </div>

      <!-- Submit -->
      <div class="mb-4 text-end">
        <div class="mb-2 text-start">
          <small class="text-muted">Giá: Người lớn = 100%, Trẻ em = 75%, Em bé = 10% (mặc định)</small>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <strong>Tổng:</strong> <span id="total_price"><?= number_format($basePrice,0,',','.') ?> VND</span>
          </div>
          <button type="submit" class="btn btn-primary btn-lg">Thanh toán & đặt vé</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Cột phải: Tóm tắt chuyến bay -->
  <div class="col-lg-4">
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light fw-bold">Tóm tắt chuyến bay</div>
      <div class="card-body">
        <p><b><?= htmlspecialchars($flightDetail['dep_airport']) ?></b> → <b><?= htmlspecialchars($flightDetail['arr_airport']) ?></b></p>
        <p>Ngày: <?= date('d/m/Y', strtotime($flightDetail['departure_time'])) ?> - <?= date('H:i', strtotime($flightDetail['departure_time'])) ?></p>
        <p>Hãng: <?= htmlspecialchars($flightDetail['airline']) ?></p>
        <hr>
        <h5 class="text-danger"><?= number_format((float)$flightDetail['price_new'],0,',','.') ?> VND</h5>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-light fw-bold">Hướng dẫn đặt tên</div>
      <div class="card-body">
        <img src="https://dummyimage.com/300x120/ddd/000&text=CMND+Mẫu" class="img-fluid mb-2" alt="">
        <p class="small text-muted">
          Đảm bảo tên hành khách nhập chính xác như giấy tờ.
        </p>
      </div>
    </div>
  </div>
</div>

<script src="../js/bookingFlight.js"></script>