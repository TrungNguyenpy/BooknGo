<?php
require_once __DIR__ . '/../../../config/config.php';
$message = "";

// Lấy danh sách tour_details để chọn khi thêm vé
$tourDetailQuery = "
SELECT td.id, t.name AS tour_name, td.departure_place 
FROM tour_details td 
JOIN tours t ON td.tour_id = t.id
ORDER BY td.id ASC
";
$tourDetails = $conn->query($tourDetailQuery);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tour_detail_id = (int)($_POST['tour_detail_id'] ?? 0);
    $date = $_POST['date'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $extra = trim($_POST['extra'] ?? '');
    $old_price = (int)($_POST['old_price'] ?? 0);
    $discount_percent = (int)($_POST['discount_percent'] ?? 0);
    $top_pick = isset($_POST['top_pick']) ? 1 : 0;

    // ✅ Tự động tính giá mới
    $price = $old_price - ($old_price * $discount_percent / 100);

    // ✅ Xử lý upload ảnh tour_img
    $tour_img = null;
    if (!empty($_FILES['tour_img']['name'])) {
        $uploadDir = __DIR__ . '/../../../img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['tour_img']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['tour_img']['tmp_name'], $targetFile)) {
            $tour_img = '/img/' . $fileName;
        } else {
            $message = '<div class="alert alert-warning">⚠️ Không thể upload ảnh!</div>';
        }
    }

    // ✅ Thêm dữ liệu vào DB
    $sql = "INSERT INTO tour_ticket 
            (tour_detail_id, tour_img, date, title, description, extra, price, old_price, discount_percent, top_pick)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            "isssssiiii",
            $tour_detail_id,
            $tour_img,
            $date,
            $title,
            $description,
            $extra,
            $price,
            $old_price,
            $discount_percent,
            $top_pick
        );
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">✅ Thêm vé tour mới thành công! (Giá sau giảm: ' . number_format($price) . ' VNĐ)</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ Lỗi khi lưu: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger">❌ Lỗi chuẩn bị câu lệnh SQL.</div>';
    }
}
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thêm vé tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .card { border-radius: 12px; }
    textarea.form-control { min-height: 100px; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="mb-3 d-flex align-items-center">
    <a href="ticket.php" class="btn btn-light me-3">&larr; Quay lại</a>
    <h3 class="mb-0">➕ Thêm vé mới</h3>
  </div>

  <?= $message ?>

  <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Thuộc Tour chi tiết <span class="text-danger">*</span></label>
        <select name="tour_detail_id" class="form-select" required>
          <option value="">-- Chọn tour chi tiết --</option>
          <?php if ($tourDetails && $tourDetails->num_rows > 0): ?>
            <?php while ($row = $tourDetails->fetch_assoc()): ?>
              <option value="<?= $row['id']; ?>">
                [#<?= $row['id']; ?>] <?= htmlspecialchars($row['tour_name']); ?> - <?= htmlspecialchars($row['departure_place']); ?>
              </option>
            <?php endwhile; ?>
          <?php endif; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Ngày khởi hành</label>
        <input type="date" name="date" class="form-control">
      </div>

      <div class="col-md-6">
        <label class="form-label">Tiêu đề vé</label>
        <input type="text" name="title" class="form-control" required placeholder="VD: Vé tiêu chuẩn, Vé VIP...">
      </div>

      <div class="col-md-6">
        <label class="form-label">Giá cũ (VND)</label>
        <input type="number" name="old_price" id="old_price" class="form-control" placeholder="0">
      </div>

      <div class="col-md-6">
        <label class="form-label">Giảm giá (%)</label>
        <input type="number" name="discount_percent" id="discount_percent" class="form-control" placeholder="0">
      </div>

      <!-- Giá mới tự động hiển thị -->
      <div class="col-md-6">
        <label class="form-label">Giá sau giảm (tự động tính)</label>
        <input type="text" id="price_preview" class="form-control bg-light" readonly>
      </div>

      <div class="col-md-6">
        <label class="form-label">Ảnh vé (tour_img)</label>
        <input type="file" name="tour_img" class="form-control" accept="image/*">
      </div>

      <div class="col-md-6 d-flex align-items-center mt-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="top_pick" id="top_pick">
          <label class="form-check-label" for="top_pick">Đánh dấu nổi bật (⭐)</label>
        </div>
      </div>

      <div class="col-12">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control"></textarea>
      </div>

      <div class="col-12">
        <label class="form-label">Thông tin bổ sung (extra)</label>
        <textarea name="extra" class="form-control"></textarea>
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-success px-4">💾 Lưu vé</button>
        <a href="ticket.php" class="btn btn-secondary px-4">Hủy</a>
      </div>
    </div>
  </form>
</div>

<script>
  // ✅ Cập nhật giá mới theo thời gian thực
  const oldPriceInput = document.getElementById('old_price');
  const discountInput = document.getElementById('discount_percent');
  const pricePreview = document.getElementById('price_preview');

  function updatePrice() {
    const oldPrice = parseFloat(oldPriceInput.value) || 0;
    const discount = parseFloat(discountInput.value) || 0;
    const newPrice = oldPrice - (oldPrice * discount / 100);
    pricePreview.value = newPrice.toLocaleString('vi-VN') + ' VNĐ';
  }

  oldPriceInput.addEventListener('input', updatePrice);
  discountInput.addEventListener('input', updatePrice);
</script>
</body>
</html>
