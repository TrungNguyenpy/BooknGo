<?php
require_once __DIR__ . '/../../../../config/config.php';

// --- LẤY id TỪ URL ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Không tìm thấy id.");
}
$id = (int)$_GET['id'];

// --- LẤY THÔNG TIN CHUYẾN BAY ---
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$flight) {
    die("❌ Không tìm thấy chuyến bay có ID = $id");
}

$message = '';

// --- XỬ LÝ GỬI FORM ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departure_time = $_POST['departure_time'] ?? '';
    $arrival_time = $_POST['arrival_time'] ?? '';
    $aircraft = $_POST['aircraft'] ?? '';
    $baggage_info = $_POST['baggage_info'] ?? '';
    $transit_info = $_POST['transit_info'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';

    if (empty($departure_time) || empty($arrival_time) || empty($price)) {
        $message = '<div class="alert alert-danger">⚠️ Vui lòng nhập đầy đủ thông tin bắt buộc!</div>';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO flight_details 
            (flight_id, departure_time, arrival_time, aircraft, baggage_info, transit_info, description, price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssssd", $id, $departure_time, $arrival_time, $aircraft, $baggage_info, $transit_info, $description, $price);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: ../view_details.php?id=" . $id);
            exit;
        } else {
            $message = '<div class="alert alert-danger">❌ Lỗi khi thêm dữ liệu: ' . $stmt->error . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm chi tiết chuyến bay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">✈️ Thêm Chi Tiết Cho Chuyến Bay</h4>
        </div>
        <div class="card-body">
            <?= $message; ?>

            <div class="alert alert-info">
                <strong>Chuyến bay:</strong> <?= htmlspecialchars($flight['flight_name']); ?><br>
                <strong>Lộ trình:</strong> <?= htmlspecialchars($flight['departure']); ?> → <?= htmlspecialchars($flight['arrival']); ?><br>
                <strong>Hãng bay:</strong> <?= htmlspecialchars($flight['airline']); ?>
            </div>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $id; ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giờ khởi hành*</label>
                        <input type="datetime-local" name="departure_time" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giờ đến*</label>
                        <input type="datetime-local" name="arrival_time" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Máy bay</label>
                    <input type="text" name="aircraft" class="form-control" placeholder="VD: Airbus A320, Boeing 737,...">
                </div>

                <div class="mb-3">
                    <label class="form-label">Hành lý</label>
                    <input type="text" name="baggage_info" class="form-control" placeholder="VD: 20kg ký gửi, 7kg xách tay">
                </div>

                <div class="mb-3">
                    <label class="form-label">Trung chuyển</label>
                    <input type="text" name="transit_info" class="form-control" placeholder="VD: Bay thẳng hoặc quá cảnh tại Đà Nẵng">
                </div>

                <div class="mb-3">
                    <label class="form-label">Giá (VNĐ)*</label>
                    <input type="number" name="price" class="form-control" required min="0" placeholder="Nhập giá vé">
         

                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Thông tin thêm..."></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../view_details.php?id=<?= $id ?>" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-success">💾 Lưu Chi Tiết</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const priceInput = document.querySelector('input[name="price"]');
  if (priceInput) {
    priceInput.addEventListener('input', function() {
      let value = this.value.replace(/\D/g, '');
      this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    });
  }
});
</script>
</html>
