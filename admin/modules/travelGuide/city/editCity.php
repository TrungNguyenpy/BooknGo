<?php
require_once __DIR__ . '/../../../../config/config.php';

$message = "";

// --- Bước 1: Lấy city_id từ URL ---
if (!isset($_GET['city_id'])) {
    die("Không tìm thấy City ID!");
}

$city_id = $_GET['city_id'];

// --- Bước 2: Lấy dữ liệu hiện tại của city ---
$sql = "SELECT * FROM cities WHERE city_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $city_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("City không tồn tại!");
}
$city = $result->fetch_assoc();

// --- Bước 3: Xử lý POST khi submit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $slogan = trim($_POST['slogan']);
    $description = trim($_POST['description']);

    // ✅ Xử lý upload ảnh hero_image mới
    $heroImage = $city['hero_image']; // giữ ảnh cũ mặc định
    if (!empty($_FILES['hero_image']['name'])) {
       $uploadDir = __DIR__ . '/../../../../img/'; // folder vật lý
        $fileName = time() . '_' . basename($_FILES['hero_image']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $targetFile)) {
            // Lưu đường dẫn **tương đối từ BooknGo root**, để hiển thị trên web
            $heroImage = '../../../img/' . $fileName;
        }

    }

    // ✅ UPDATE vào DB
    $sql = "UPDATE cities 
            SET name = ?, slogan = ?, description = ?, hero_image = ?
            WHERE city_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $slogan, $description, $heroImage, $city_id);

    if ($stmt->execute()) {
        $message = "✅ Cập nhật City thành công!";
        // Cập nhật lại dữ liệu để hiển thị mới
        $city['name'] = $name;
        $city['slogan'] = $slogan;
        $city['description'] = $description;
        $city['hero_image'] = $heroImage;
    } else {
        $message = "❌ Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

    <h2 class="mb-3">Sửa City</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow">

        <div class="mb-3">
            <label>Mã City</label>
            <input type="text" name="city_id" class="form-control" value="<?= htmlspecialchars($city['city_id']) ?>" readonly>
        </div>

        <div class="mb-3">
            <label>Tên thành phố</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($city['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Slogan</label>
            <input type="text" name="slogan" class="form-control" value="<?= htmlspecialchars($city['slogan']) ?>">
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($city['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label>Ảnh Hero (banner)</label>
            <?php if (!empty($city['hero_image'])): ?>
                <div class="mb-2">
                    <img src="<?= $city['hero_image'] ?>" alt="Hero Image" style="max-width: 200px;">
                </div>
            <?php endif; ?>

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
