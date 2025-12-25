<?php
require_once __DIR__ . '/../../../config/config.php';

$message = "";

// Đường dẫn vật lý tới folder img (trong project: BooknGo/img/)
$imgDir = __DIR__ . '/../../../img/'; // từ admin/modules/tours -> lên 3 cấp -> tới BooknGo -> img
$imgWebPrefix = '/img/'; // đường dẫn hiển thị trên web

// Lấy danh sách ảnh (chỉ lấy file ảnh cơ bản)
$images = [];
if (is_dir($imgDir)) {
    $all = scandir($imgDir);
    foreach ($all as $f) {
        if ($f === '.' || $f === '..') continue;
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp','svg'])) {
            $images[] = $f;
        }
    }
    sort($images);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ========== LẤY DỮ LIỆU FORM ==========
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $label = trim($_POST['label'] ?? '');
    $price = $_POST['price_old'] !== '' ? (int)$_POST['price_old'] : 0;
    $reviews = isset($_POST['reviews']) ? (int)$_POST['reviews'] : 0;

    // Tour details
    $departure_place = trim($_POST['departure_place'] ?? '');
    $departure_schedule = $_POST['departure_schedule'] ?? null;
    $introduction = trim($_POST['introduction'] ?? '');
    $itinerary = trim($_POST['itinerary'] ?? '');
    $package_includes = trim($_POST['package_includes'] ?? '');
    $terms = trim($_POST['terms'] ?? '');

    // ✅ Xử lý upload ảnh
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../../../img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = '/img/' . $fileName;
        } else {
            $message .= '<div class="alert alert-warning">⚠️ Lỗi khi tải ảnh lên, vui lòng thử lại.</div>';
        }
    }


    // ========== THÊM VÀO DB VỚI TRANSACTION ==========
    $conn->begin_transaction();

    try {
        // 1) Insert tours
        $sqlTour = "INSERT INTO tours (name, location, label, price_old, reviews, image)
                    VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlTour);
        if ($stmt === false) throw new Exception($conn->error);

        $stmt->bind_param("sssiss", $name, $location, $label, $price, $reviews, $imagePath);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $tour_id = $conn->insert_id;
        $stmt->close();

        // 2) Insert tour_details
        $sqlDetail = "INSERT INTO tour_details 
            (tour_id, departure_place, departure_schedule, introduction, itinerary, package_includes, terms)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sqlDetail);
        if ($stmt2 === false) throw new Exception($conn->error);

        $dep_schedule_param = $departure_schedule !== "" ? $departure_schedule : null;
        $stmt2->bind_param("issssss",
            $tour_id,
            $departure_place,
            $dep_schedule_param,
            $introduction,
            $itinerary,
            $package_includes,
            $terms
        );

        if (!$stmt2->execute()) throw new Exception($stmt2->error);
        $stmt2->close();

        $conn->commit();
        $message .= '<div class="alert alert-success">✅ Thêm tour và chi tiết tour thành công!</div>';
    } catch (Exception $e) {
        $conn->rollback();
        $message .= '<div class="alert alert-danger">❌ Lỗi khi lưu: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thêm Tour Mới</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f6f7fb; }
    .card { border-radius: 12px; }
    textarea.form-control { min-height: 120px; }
    .form-section { background: #fff; padding: 16px; border-radius: 8px; margin-bottom: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
    .img-preview { max-width: 160px; max-height: 120px; object-fit: cover; border-radius: 6px; border: 1px solid #e6e6e6; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="mb-3 d-flex align-items-center">
    <a href="../../index.php#tours" class="btn btn-light me-3">&larr; Quay lại</a>
    <h3 class="mb-0">➕ Thêm Tour Mới</h3>
  </div>

  <?= $message ?>

  <form method="POST" enctype="multipart/form-data" class="row g-3">

    <div class="col-12 form-section">
      <h5>Thông tin tour</h5>
      <div class="row g-3 mt-1">
        <div class="col-md-6">
          <label class="form-label">Tên tour <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Địa điểm <span class="text-danger">*</span></label>
          <input type="text" name="location" class="form-control" required value="<?= isset($location) ? htmlspecialchars($location) : '' ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Label</label>
          <input type="text" name="label" class="form-control" value="<?= isset($label) ? htmlspecialchars($label) : '' ?>" placeholder="VD: Ưu đãi, Hot, ...">
        </div>

        <div class="col-md-4">
          <label class="form-label">Giá (VND)</label>
          <input type="number" name="price_old" class="form-control" value="<?= isset($price) ? (int)$price : 0 ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Đánh giá</label>
          <input type="number" name="reviews" class="form-control" value="<?= isset($reviews) ? (int)$reviews : 0 ?>">
        </div>

      <div class="col-md-6">
        <label class="form-label">Ảnh đại diện tour</label>
        <input type="file" name="image" accept="image/*" class="form-control" onchange="previewUpload(event)">
        <div class="mt-2">
          <img id="img_preview" class="img-preview" style="display:none;" alt="Preview">
        </div>
      </div>

      </div>
    </div>

    <div class="col-12 form-section">
      <h5>Thông tin chi tiết (tour_details)</h5>

      <div class="row g-3 mt-1">
        <div class="col-md-6">
          <label class="form-label">Điểm khởi hành</label>
          <input type="text" name="departure_place" class="form-control" value="<?= isset($departure_place) ? htmlspecialchars($departure_place) : '' ?>" placeholder="VD: Hà Nội -> Nha Trang">
        </div>

        <div class="col-md-6">
          <label class="form-label">Lịch khởi hành</label>
          <input type="date" name="departure_schedule" class="form-control" value="<?= isset($departure_schedule) ? htmlspecialchars($departure_schedule) : '' ?>">
        </div>

        <div class="col-12">
          <label class="form-label">Giới thiệu</label>
          <textarea name="introduction" class="form-control"><?= isset($introduction) ? htmlspecialchars($introduction) : '' ?></textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Lịch trình</label>
          <textarea name="itinerary" class="form-control" placeholder="Mô tả chi tiết từng ngày"><?= isset($itinerary) ? htmlspecialchars($itinerary) : '' ?></textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Bao gồm (package_includes)</label>
          <textarea name="package_includes" class="form-control" placeholder="VD: Vé máy bay, ăn uống, hướng dẫn viên,..."><?= isset($package_includes) ? htmlspecialchars($package_includes) : '' ?></textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Điều khoản (terms)</label>
          <textarea name="terms" class="form-control" placeholder="Ghi rõ điều kiện huỷ, chính sách, v.v."><?= isset($terms) ? htmlspecialchars($terms) : '' ?></textarea>
        </div>
      </div>
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-success px-4">💾 Lưu tour</button>
      <a href="../../index.php" class="btn btn-secondary px-4">Hủy</a>
    </div>
  </form>
</div>

<script>
function previewUpload(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('img_preview');
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'inline-block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.style.display = 'none';
  }
}
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
