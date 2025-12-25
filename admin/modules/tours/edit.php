<?php
require_once __DIR__ . '/../../../config/config.php';

// --- LẤY ID TOUR ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID tour cần sửa.");
}
$id = (int)$_GET['id'];

// --- LẤY DỮ LIỆU TOUR TỪ DB ---
$sql = "SELECT * FROM tours WHERE id = $id";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    die("Không tìm thấy tour với ID = $id");
}
$tour = $result->fetch_assoc();

$message = "";

// --- CẬP NHẬT DỮ LIỆU KHI NGƯỜI DÙNG SUBMIT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $label = mysqli_real_escape_string($conn, $_POST['label']);
    $price_old = (float)$_POST['price_old'];
    $reviews = mysqli_real_escape_string($conn, $_POST['reviews']);

    // Xử lý ảnh nếu có upload mới
    $imagePath = $tour['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../img/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imagePath = '/img/' . $imageName;
        } else {
            $message = "Không thể upload ảnh mới.";
        }
    }

    // --- CẬP NHẬT CSDL ---
    $updateSql = "
        UPDATE tours
        SET name = '$name',
            location = '$location',
            label = '$label',
            price_old = '$price_old',
            reviews = '$reviews',
            image = '$imagePath'
        WHERE id = $id
    ";

    if ($conn->query($updateSql)) {
        $message = "✅ Cập nhật tour thành công!";
        // Lấy lại dữ liệu mới
        $result = $conn->query("SELECT * FROM tours WHERE id = $id");
        $tour = $result->fetch_assoc();
    } else {
        $message = "❌ Lỗi khi cập nhật: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sửa thông tin tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
  <h2 class="mb-4">✏️ Sửa tour: <?= htmlspecialchars($tour['name']); ?></h2>

  <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?= $message; ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Tên tour</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($tour['name']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Địa điểm</label>
      <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($tour['location']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Label</label>
      <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($tour['label']); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Giá (VND)</label>
      <input type="number" name="price_old" class="form-control" value="<?= htmlspecialchars($tour['price_old']); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Đánh giá</label>
      <input type="text" name="reviews" class="form-control" value="<?= htmlspecialchars($tour['reviews']); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh hiện tại</label><br>
      <?php if (!empty($tour['image'])): ?>
          <img src="<?= $base_url . htmlspecialchars($tour['image']); ?>" width="150" class="mb-2"><br>
      <?php else: ?>
          <span class="text-muted">Chưa có ảnh</span><br>
      <?php endif; ?>
      <input type="file" name="image" class="form-control mt-2">
    </div>

    <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
    <a href="../../index.php#view-tours" class="btn btn-secondary">⬅️ Quay lại</a>
  </form>
</div>

</body>
</html>
