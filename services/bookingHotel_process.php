<?php
include __DIR__ . '/../config/config.php';

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("❌ Yêu cầu không hợp lệ.");
}

// Lấy dữ liệu từ form
$fullname     = trim($_POST['fullname'] ?? '');
$email        = trim($_POST['email'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$hotel_id     = intval($_POST['hotel_id'] ?? 0);
$service_type = $_POST['service_type'] ?? 'hotels';
$checkin      = $_POST['checkin'] ?? null;
$checkout     = $_POST['checkout'] ?? null;
$guests       = intval($_POST['guests'] ?? 1);
$rooms        = intval($_POST['rooms'] ?? 1);
$payment      = $_POST['payment'] ?? 'pay_later';

// Xác định trạng thái booking theo phương thức thanh toán
$status = ($payment === 'pay_now') ? 'confirmed' : 'pending';

// Lấy giá khách sạn từ DB
$sqlHotel = "SELECT price_new FROM hotels WHERE id = ?";
$stmt = $conn->prepare($sqlHotel);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$res = $stmt->get_result();
$hotel = $res->fetch_assoc();

if (!$hotel) {
    die("❌ Không tìm thấy khách sạn.");
}

$pricePerNight = (float)$hotel['price_new'];

// Tính số đêm
$days = (strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24);
if ($days <= 0) $days = 1;
$total_price = $days * $rooms * $pricePerNight;

// 1. Lưu hoặc tìm user
$sqlUser = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$resUser = $stmtUser->get_result();

if ($rowUser = $resUser->fetch_assoc()) {
    $user_id = $rowUser['user_id'];
} else {
    $sqlInsertUser = "INSERT INTO users (fullname, email, phone, created_at) VALUES (?, ?, ?, NOW())";
    $stmtInsertUser = $conn->prepare($sqlInsertUser);
    $stmtInsertUser->bind_param("sss", $fullname, $email, $phone);
    $stmtInsertUser->execute();
    $user_id = $stmtInsertUser->insert_id;
}

// 2. Lưu booking
$payment_method = $_POST['payment'] ?? 'pay_later';
$sqlBooking = "INSERT INTO bookings 

(user_id, service_type, service_id, booking_date, status, total_price, created_at, updated_at, checkin, checkout)
VALUES (?, ?, ?, NOW(), ?, ?, NOW(), NOW(), ?, ?)";
$stmtBooking = $conn->prepare($sqlBooking);

$stmtBooking->bind_param(
    "isisdss",
    $user_id,
    $service_type,
    $hotel_id,
    $status,
    $total_price,
    $checkin,
    $checkout
);
// 2. Khởi tạo response mặc định
$response = [
    "status" => "error",
    "title" => "❌ Lỗi chưa xác định",
    "message" => "Không có thông tin đặt phòng."
];

// 3. Lưu booking vào database
$sqlBooking = "INSERT INTO bookings 
(user_id, service_type, service_id, booking_date, status, total_price, created_at, updated_at, checkin, checkout)
VALUES (?, ?, ?, NOW(), ?, ?, NOW(), NOW(), ?, ?)";
$stmtBooking = $conn->prepare($sqlBooking);

$stmtBooking->bind_param(
    "isisdss",
    $user_id,
    $service_type,
    $hotel_id,
    $status,
    $total_price,
    $checkin,
    $checkout
);

if ($stmtBooking->execute()) {
    $booking_id = $stmtBooking->insert_id;
    $response = [
        "status" => "success",
        "title" => "✅ Đặt phòng thành công!",
        "message" => "
            Mã đơn: <strong>#{$booking_id}</strong><br>
            Trạng thái: <strong>" . htmlspecialchars($status) . "</strong><br>
            Tổng tiền: <strong>" . number_format($total_price, 0, ',', '.') . " VND</strong>
        "
    ];
} else {
    $response = [
        "status" => "error",
        "title" => "❌ Lỗi đặt phòng",
        "message" => htmlspecialchars($stmtBooking->error)
    ];
}

// 4. Chỉ hiển thị modal nếu khách chọn pay_later
$showResultModal = ($payment_method === 'pay_later');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả thanh toán</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body>

<!-- Modal kết quả booking -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header <?= ($response['status'] ?? '') === 'success' ? 'bg-success text-white' : 'bg-danger text-white' ?>">
        <h5 class="modal-title"><?= $response['title'] ?? 'Không xác định' ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?= $response['message'] ?? '' ?>
      </div>
      <div class="modal-footer">
        <a href="<?= $base_url ?>/index.php" class="btn btn-primary">Quay lại trang chủ</a>
      </div>
    </div>
  </div>
</div>

<script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var showModal = <?= $showResultModal ? 'true' : 'false' ?>;
    if(showModal){
        var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
        resultModal.show();
    }
});
</script>

</body>
</html>