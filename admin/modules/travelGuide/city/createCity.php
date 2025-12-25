<?php
require_once __DIR__ . '/../../../../config/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $city_id = trim($_POST['city_id']);
    $name = trim($_POST['name']);
    $slogan = trim($_POST['slogan']);
    $description = trim($_POST['description']);

    // ✅ Xử lý upload ảnh hero_image (giống Destination)
    $heroImage = null;
    if (!empty($_FILES['hero_image']['name'])) {

        $uploadDir = __DIR__ . '/../../../../img/'; // ✅ thư mục giống bạn dùng
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['hero_image']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $targetFile)) {
            $heroImage = '/img/' . $fileName; // ✅ lưu tương đối
        }
    }

    // ✅ Thêm DB
    $sql = "INSERT INTO cities (city_id, name, slogan, description, hero_image)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $city_id, $name, $slogan, $description, $heroImage);

    if ($stmt->execute()) {
        $message = "✅ Thêm City thành công!";
    } else {
        $message = "❌ Lỗi: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-4">

    <h2 class="mb-3">Thêm City</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow">

        <div class="mb-3">
            <label>Mã City (Ví dụ: HCM, HN)</label>
            <input type="text" name="city_id" class="form-control" required maxlength="10">
        </div>

        <div class="mb-3">
            <label>Tên thành phố</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Slogan</label>
            <input type="text" name="slogan" class="form-control">
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label>Ảnh Hero (banner)</label>
            <input type="file" name="hero_image" class="form-control">
        </div>

        <div class="d-flex">
            <button type="submit" class="btn btn-primary me-2">✅ Lưu</button>
            <a href="../../../index.php#view-travelGuide" class="btn btn-secondary">⬅ Quay lại</a>
        </div>

    </form>

</div>
</body>
</html>