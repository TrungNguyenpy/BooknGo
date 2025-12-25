<?php
require_once __DIR__ . '/../../../../config/config.php';

// --- LẤY ID CHI TIẾT (flight_details.id) ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Không tìm thấy ID chi tiết chuyến bay!");
}
$detail_id = (int)$_GET['id'];

// --- LẤY flight_id từ URL để quay lại ---
$flight_id = isset($_GET['flight_id']) ? (int)$_GET['flight_id'] : 0;

// --- LẤY THÔNG TIN CHI TIẾT ---
$stmt = $conn->prepare("SELECT * FROM flight_details WHERE id = ?");
$stmt->bind_param("i", $detail_id);
$stmt->execute();
$detail = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$detail) {
    die("❌ Không tìm thấy dữ liệu cho chi tiết ID = $detail_id");
}

// --- LẤY THÔNG TIN CHUYẾN BAY CHA ---
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $detail['flight_id']);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();
$stmt->close();

$message = "";

// --- XỬ LÝ CẬP NHẬT ---
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
            UPDATE flight_details
            SET departure_time = ?, arrival_time = ?, aircraft = ?, baggage_info = ?, transit_info = ?, description = ?, price = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssssssdi", $departure_time, $arrival_time, $aircraft, $baggage_info, $transit_info, $description, $price, $detail_id);

        if ($stmt->execute()) {
            header("Location: ../view_details.php?id=" . $flight['id']);
            exit;
        } else {
            $message = '<div class="alert alert-danger">❌ Lỗi khi cập nhật: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa chi tiết chuyến bay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">✈️ Sửa Chi Tiết Chuyến Bay</h4>
        </div>
        <div class="card-body">
            <?= $message ?>

            <div class="alert alert-info">
                <strong>Chuyến bay:</strong> <?= htmlspecialchars($flight['flight_name']); ?><br>
                <strong>Lộ trình:</strong> <?= htmlspecialchars($flight['departure']); ?> → <?= htmlspecialchars($flight['arrival']); ?><br>
                <strong>Hãng bay:</strong> <?= htmlspecialchars($flight['airline']); ?>
            </div>

            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giờ khởi hành*</label>
                        <input type="datetime-local" name="departure_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($detail['departure_time'])) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giờ đến*</label>
                        <input type="datetime-local" name="arrival_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($detail['arrival_time'])) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Máy bay</label>
                    <input type="text" name="aircraft" class="form-control" value="<?= htmlspecialchars($detail['aircraft']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Thông tin hành lý</label>
                    <textarea name="baggage_info" class="form-control" rows="2"><?= htmlspecialchars($detail['baggage_info']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Thông tin trung chuyển</label>
                    <textarea name="transit_info" class="form-control" rows="2"><?= htmlspecialchars($detail['transit_info']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($detail['description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giá vé (VNĐ)*</label>
                    <input type="number" name="price" step="0.01" class="form-control" value="<?= htmlspecialchars($detail['price']); ?>" required>
                </div>

                <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
                <a href="../view_details.php?id=<?= $flight['id'] ?>" class="btn btn-secondary">↩ Quay lại</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
