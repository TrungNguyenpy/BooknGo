<?php
require_once __DIR__ . '/../../../config/config.php';

// Lấy ID khách sạn từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("ID không hợp lệ!");
}

// Lấy dữ liệu khách sạn từ DB
$sql = "SELECT * FROM hotels WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Không tìm thấy khách sạn!");
}
$hotel = $result->fetch_assoc();

// Nếu submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $city_id = $_POST['city_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $rating = $_POST['rating'];
    $price_old = $_POST['price_old'];
    $discount_percent = $_POST['discount_percent'];
    $price_new = $price_old * (1 - $discount_percent / 100);
    $label = $_POST['label'];

    // ✅ Xử lý upload ảnh mới (nếu có)
    $imagePath = $hotel['image']; // Giữ ảnh cũ mặc định
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../../../img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = '/img/' . $fileName;
        }
    }

    // ✅ Cập nhật vào DB
    $stmt = $conn->prepare("
        UPDATE hotels 
        SET city_id=?, name=?, description=?, location=?, rating=?, 
            price_old=?, discount_percent=?, price_new=?, label=?, image=? 
        WHERE id=?
    ");
    $stmt->bind_param(
        "ssssdiddssi",
        $city_id,
        $name,
        $description,
        $location,
        $rating,
        $price_old,
        $discount_percent,
        $price_new,
        $label,
        $imagePath,
        $id
    );

    if ($stmt->execute()) {
        header("Location: edit.php?id=$id&message=success");
        exit;
    } else {
        echo "<div class='alert alert-danger'>❌ Lỗi cập nhật: " . $stmt->error . "</div>";
    }
}

?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Sửa khách sạn</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
  <h3>✏️ Sửa thông tin khách sạn</h3>
  <form method="POST" enctype="multipart/form-data"  class="mt-3">
    <div class="row">
      <div class="col-md-3 mb-3">
        <label class="form-label">Mã TP</label>
        <input type="text" name="city_id" value="<?= htmlspecialchars($hotel['city_id']) ?>" class="form-control" required>
      </div>
      <div class="col-md-9 mb-3">
        <label class="form-label">Tên khách sạn</label>
        <input type="text" name="name" value="<?= htmlspecialchars($hotel['name']) ?>" class="form-control" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Mô tả</label>
      <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($hotel['description']) ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Vị trí</label>
      <input type="text" name="location" value="<?= htmlspecialchars($hotel['location']) ?>" class="form-control" required>
    </div>

    <div class="row">
      <div class="col-md-3 mb-3">
        <label class="form-label">Rating</label>
        <input type="number" name="rating" step="0.1" value="<?= $hotel['rating'] ?>" class="form-control">
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Giá cũ</label>
        <input type="number" name="price_old" value="<?= $hotel['price_old'] ?>" class="form-control" id="price_old">
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Giảm giá (%)</label>
        <input type="number" name="discount_percent" value="<?= $hotel['discount_percent'] ?>" class="form-control" id="discount_percent">
      </div>
      <div class="col-md-3 mb-3">
        <label class="form-label">Giá mới</label>
        <input type="number" value="<?= $hotel['price_new'] ?>" class="form-control" id="price_new" readonly>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Label</label>
      <input type="text" name="label" value="<?= htmlspecialchars($hotel['label']) ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh hiện tại</label><br>
      <?php if (!empty($hotel['image'])): ?>
        <img src="<?= $base_url . $hotel['image']; ?>" width="120" class="mb-2 rounded">
      <?php else: ?>
        <p class="text-muted">Chưa có ảnh</p>
      <?php endif; ?>

      <input type="file" name="image" class="form-control mt-2">
      <small class="text-muted">Chọn ảnh mới (tùy chọn)</small>
    </div>


    <div class="d-flex justify-content-end">
      <button type="submit" class="btn btn-primary me-2">💾 Lưu thay đổi</button>
      <a href="../../index.php#view-hotels" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
</div>

<script>
const priceOld = document.getElementById('price_old');
const discount = document.getElementById('discount_percent');
const priceNew = document.getElementById('price_new');
function updatePrice() {
  const oldP = parseFloat(priceOld.value) || 0;
  const d = parseFloat(discount.value) || 0;
  priceNew.value = Math.round(oldP * (1 - d / 100));
}
priceOld.addEventListener('input', updatePrice);
discount.addEventListener('input', updatePrice);
</script>
</body>
</html>
