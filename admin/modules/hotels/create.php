<?php
require_once __DIR__ . '/../../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city_id = trim($_POST['city_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $rating = (float)($_POST['rating'] ?? 0);
    $reviews = (int)($_POST['reviews'] ?? 0);
    $price_old = (float)($_POST['price_old'] ?? 0);
    $discount_percent = (float)($_POST['discount_percent'] ?? 0);
    $label = trim($_POST['label'] ?? '');

    // ✅ Tính giá mới
    $price_new = $price_old - ($price_old * $discount_percent / 100);

    // ✅ Xử lý upload ảnh
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        // Đường dẫn thật (trên server)
        $uploadDir = __DIR__ . '/../../../img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // Tạo tên file duy nhất
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;

        // Upload file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // ✅ Lưu đường dẫn tương đối đúng chuẩn
            $imageName = '/img/' . $fileName;
        }
    }


    // ✅ Thêm vào DB
    $stmt = $conn->prepare("INSERT INTO hotels (city_id, name, description, location, rating, reviews, price_old, price_new, label, image, discount_percent)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssdiiissi",
        $city_id,
        $name,
        $description,
        $location,
        $rating,
        $reviews,
        $price_old,
        $price_new,
        $label,
        $imageName,
        $discount_percent
    );

    if ($stmt->execute()) {
    echo "<script>
            alert('✅ Thêm khách sạn thành công!');
            window.location.href='../../index.php#view-hotels';
          </script>";
        exit;
    }
    else {
        $error = "❌ Lỗi thêm dữ liệu: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thêm khách sạn mới</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .form-container {
      max-width: 950px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 { font-weight: 600; color: #333; }
    .form-label { font-weight: 500; }
    .btn { border-radius: 8px; padding: 10px 18px; }
  </style>
</head>
<body>

<div class="form-container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>🏨 Thêm khách sạn mới</h2>
    <a href="../../index.php#hotels" class="btn btn-outline-secondary">← Quay lại</a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Tên khách sạn</label>
      <input type="text" name="name" class="form-control" placeholder="Ví dụ: Khách sạn Hoa Sen" required>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label class="form-label">Mã thành phố</label>
        <select name="city_id" class="form-select" required>
          <option value="">-- Chọn mã --</option>
          <option value="HN">HN - Hà Nội</option>
          <option value="DN">DN - Đà Nẵng</option>
          <option value="TPHCM">TPHCM - TP Hồ Chí Minh</option>
          <option value="NT">NT - Nha Trang</option>
          <option value="PQ">PQ - Phú Quốc</option>
        </select>
      </div>
      <div class="col-md-8">
        <label class="form-label">Vị trí chi tiết</label>
        <input type="text" name="location" class="form-control" placeholder="Ví dụ: Quận Hoàn Kiếm, Hà Nội" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Mô tả</label>
      <textarea name="description" class="form-control" rows="3" placeholder="Mô tả ngắn..."></textarea>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label class="form-label">Giá cũ (VNĐ)</label>
        <input type="number" name="price_old" id="price_old" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Giảm giá (%)</label>
        <input type="number" name="discount_percent" id="discount_percent" class="form-control" min="0" max="100" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Giá mới (tự tính)</label>
        <input type="text" id="price_new_display" class="form-control bg-light" readonly>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3">
        <label class="form-label">Rating</label>
        <input type="number" step="0.1" name="rating" class="form-control" max="5" min="0">
      </div>
      <div class="col-md-3">
        <label class="form-label">Reviews</label>
        <input type="number" name="reviews" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Nhãn (Label)</label>
        <input type="text" name="label" class="form-control" placeholder="Hot, Sale,...">
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label">Ảnh</label>
      <input type="file" name="image" class="form-control">
    </div>

    <div class="d-flex gap-3">
      <button type="submit" class="btn btn-success">💾 Lưu</button>
      <a href="../../index.php#view-hotels" class="btn btn-secondary">Hủy</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const priceOld = document.getElementById('price_old');
  const discount = document.getElementById('discount_percent');
  const priceNewDisplay = document.getElementById('price_new_display');

  function updatePriceNew() {
    const oldVal = parseFloat(priceOld.value) || 0;
    const discountVal = parseFloat(discount.value) || 0;
    const newVal = oldVal - (oldVal * discountVal / 100);
    priceNewDisplay.value = newVal > 0 ? newVal.toLocaleString('vi-VN') + ' VNĐ' : '';
  }

  priceOld.addEventListener('input', updatePriceNew);
  discount.addEventListener('input', updatePriceNew);
});


  // --- Xử lý Submit ---
  document.getElementById("hotelForm").addEventListener("submit", function (e) {
    e.preventDefault();

    // Gửi dữ liệu (ở đây là ví dụ log ra)
    const data = {
      city_id: document.getElementById("city_id").value,
      name: document.getElementById("name").value,
      description: document.getElementById("description").value,
      location: document.getElementById("location").value,
      rating: document.getElementById("rating").value,
      price_old: document.getElementById("price_old").value,
      price_new: document.getElementById("price_new").value,
      discount_percent: document.getElementById("discount_percent").value,
      label: document.getElementById("label").value,
      image: document.getElementById("image").value,
    };

    console.log("📦 Dữ liệu gửi đi:", data);

    // Sau khi lưu -> quay lại view-hotels
    document.getElementById("create").style.display = "none";
    document.getElementById("hotels").style.display = "block";
  });

  // --- Nút Hủy ---
  document.getElementById("cancelBtn").addEventListener("click", function () {
    document.getElementById("add-hotel-form").style.display = "none";
    document.getElementById("view-hotels").style.display = "block";
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
