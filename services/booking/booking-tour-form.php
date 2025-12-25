<?php
include __DIR__ . '/../../config/config.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Nhận ticket_id từ URL
$ticket_id = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;
if ($ticket_id <= 0) {
    die("Vé không hợp lệ!");
}

// Query thông tin vé + tour
$sql = "SELECT tt.*, td.departure_place, td.departure_schedule, td.introduction
        FROM tour_ticket tt
        JOIN tour_details td ON tt.tour_detail_id = td.id
        WHERE tt.id = $ticket_id";

$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Không tìm thấy vé!");
}

$ticket = $result->fetch_assoc();

// Giá trẻ em = 60% giá người lớn
$childPrice = (int)($ticket['price'] * 0.6);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đặt vé - <?= htmlspecialchars($ticket['title']) ?></title>
</head>
<body>
<form id="bookingForm" method="POST" action="bookingTour_process.php">

  <!-- ✅ FIX QUAN TRỌNG: Thêm ticketData chứa giá -->
  <div id="ticketData"
       data-price-adult="<?= $ticket['price'] ?>"
       data-price-child="<?= $childPrice ?>">
  </div>
  <!-- /end ticketData -->

  <div class="container">
    <!-- LEFT -->
   <div class="left">
    <!-- Tóm tắt vé -->
    <div class="box">
      <h2>Tóm tắt vé</h2>
      <div class="ticket-item" style="display:flex; gap:15px; align-items:flex-start;">
        <?php
          // Xử lý ảnh
          $img = $ticket['tour_img'] ?? $ticket['image'] ?? $ticket['image_path'] ?? 'img/default.png';
          $img_url = preg_match('#^https?://#i', $img) ? $img : "../" . ltrim($img, '/');
        ?>
        <div class="ticket-thumb" style="flex:0 0 164px;">
          <img src="<?= htmlspecialchars($img_url) ?>" 
               alt="<?= htmlspecialchars($ticket['title']) ?>" 
               style="width:164px; height:110px; object-fit:cover; border-radius:6px; border:1px solid #eee;">
          <p><b><?= htmlspecialchars($ticket['departure_place']) ?></b></p>
        </div>

        <div class="ticket-info" style="flex:1;">
          <h3><?= htmlspecialchars($ticket['title']) ?></h3>
          <p><b>Ngày tham quan:</b> <?= htmlspecialchars(date("d/m/Y", strtotime($ticket['date']))) ?></p>
          <p style="color:#444;"><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>

          <div style="display:flex; align-items:center; gap:10px;">
            <div style="font-weight:700; color:#e63946; font-size:16px;">
              <?= number_format($ticket['price'] ?? 0, 0, ',', '.') ?> VND
            </div>
            <?php if (!empty($ticket['old_price'])): ?>
              <div style="text-decoration:line-through; color:#888;">
                <?= number_format($ticket['old_price'], 0, ',', '.') ?> VND
              </div>
            <?php endif; ?>
            <?php if (!empty($ticket['discount_percent'])): ?>
              <div style="color:green;">-<?= htmlspecialchars($ticket['discount_percent']) ?>%</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Chọn loại vé -->
    <div class="box">
      <h2>Chọn loại vé</h2>
      <div class="select-ticket">
        <span>Người lớn - <b><?= number_format($ticket['price'], 0, ',', '.') ?> VND</b></span>
        <div class="quantity">
          <button type="button" onclick="changeQty('adult', -1)">-</button>
          <input id="adultQty" value="0" readonly>
          <button type="button" onclick="changeQty('adult', 1)">+</button>
        </div>
      </div>
      <div class="select-ticket">
        <span>Trẻ em - <b><?= number_format($childPrice, 0, ',', '.') ?> VND</b></span>
        <div class="quantity">
          <button type="button" onclick="changeQty('child', -1)">-</button>
          <input id="childQty" value="0" readonly>
          <button type="button" onclick="changeQty('child', 1)">+</button>
        </div>
      </div>
    </div>

    <!-- Thông tin liên hệ -->
    <div class="box contact-info">
      <h2>Thông tin liên hệ</h2>
      <input type="text" name="fullname" placeholder="Họ tên (như CMND)" required style="width:100%; margin-bottom:10px;">
      <input type="tel" name="phone" placeholder="Số điện thoại" required style="width:100%; margin-bottom:10px;">
      <input type="email" name="email" placeholder="Email" required style="width:100%; margin-bottom:10px;">
    </div>

    <!-- Các input ẩn -->
    <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">
    <input type="hidden" name="adult_qty" id="adultQtyInput">
    <input type="hidden" name="child_qty" id="childQtyInput">
    <input type="hidden" name="total_price" id="totalPriceInput">
  </div>

  <!-- Footer Payment -->
  <div class="footer-payment">
    <div class="total">Tổng cộng: <span id="footerTotal">0 VND</span></div>
    <button type="submit" class="btn">Tiếp tục</button>
  </div>
</form>

<!-- RIGHT -->
<div class="right">
  <div class="summary">
    <h3>Tóm tắt đặt chỗ</h3>
    <p>Người lớn: <span id="adultCount">0</span></p>
    <p>Trẻ em: <span id="childCount">0</span></p>
    <hr>
    <p class="price" id="totalPrice">0 VND</p>
  </div>
</div>

<!-- Modal thông báo -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <h2>🎉 Đặt vé thành công!</h2>
    <p>Cảm ơn bạn đã đặt vé. Email xác nhận đã được gửi tới bạn.</p>
    <button onclick="closeModal()">Đóng</button>
  </div>
</div>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/bookingTour.js"></script>
</body>
</html>
