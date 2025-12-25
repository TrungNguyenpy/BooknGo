<?php
session_start();
include __DIR__ . '/../config/config.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['booking_id'])) {
    die("Không có booking để thanh toán!");
}

$booking_id = intval($_SESSION['booking_id']);

// Lấy thông tin booking
$stmt = $conn->prepare("
    SELECT b.*, f.flight_name
    FROM bookings b
    LEFT JOIN flight_details fd ON b.service_id = fd.id
    LEFT JOIN flights f ON fd.flight_id = f.id
    WHERE b.booking_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Không tìm thấy booking!");
}

// Xử lý form thanh toán
$payment_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    $new_status = 'pending';
    $now = date('Y-m-d H:i:s');

    $up = $conn->prepare("UPDATE bookings SET status=?, updated_at=? WHERE booking_id=?");
    $up->bind_param("ssi", $new_status, $now, $booking_id);
    if ($up->execute()) {
        $payment_success = true;
        unset($_SESSION['booking_id']);
    } else {
        $error = "Lỗi cập nhật thanh toán: " . $up->error;
    }
    $up->close();
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Thanh toán</title>
<!-- Bootstrap CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">

<?php if ($payment_success): ?>
    <div class="alert alert-success">
        <h4 class="alert-heading">Thanh toán thành công!</h4>
        <p>Phương thức: <strong><?php echo htmlspecialchars($payment_method); ?></strong></p>
        <p>Trạng thái đơn: <strong>pending</strong></p>
        <hr>
        <a href="/index.php" class="btn btn-primary">Về trang chủ</a>
    </div>
<?php elseif (isset($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php else: ?>
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="card-title mb-3">Xác nhận thanh toán</h3>
            
            <p>Chuyến bay: <?php echo htmlspecialchars($booking['flight_name']); ?></p>
            <p>Tổng tiền: <strong><?php echo number_format($booking['total_price'],0,',','.'); ?> VND</strong></p>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Chọn phương thức thanh toán:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="Visa" required>
                        <label class="form-check-label">Visa</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="MasterCard">
                        <label class="form-check-label">MasterCard</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" value="JCB">
                        <label class="form-check-label">JCB</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số thẻ</label>
                    <input type="text" class="form-control" name="card_number" placeholder="1111 2222 3333 4444" required>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Ngày hết hạn</label>
                        <input type="text" class="form-control" name="card_exp" placeholder="MM/YY" required>
                    </div>
                    <div class="col">
                        <label class="form-label">CVV</label>
                        <input type="text" class="form-control" name="card_cvv" placeholder="123" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100">Thanh toán</button>
            </form>
        </div>
    </div>
<?php endif; ?>

</div>

<!-- Bootstrap JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
