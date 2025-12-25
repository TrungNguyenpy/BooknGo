<?php
require_once __DIR__ . '/../../../config/config.php';

// --- Xử lý Xóa City nếu có ---
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Lấy ảnh cũ để xóa file
    $sql = "SELECT hero_image FROM cities WHERE city_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $city = $result->fetch_assoc();
        if (!empty($city['hero_image'])) {
            // File vật lý
            $filePath = __DIR__ . '/' . $city['hero_image']; // tương đối từ PHP file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    // Xóa bản ghi
    $sql = "DELETE FROM cities WHERE city_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $delete_id);
    $stmt->execute();
}

// --- Lấy danh sách City ---
$sql = "SELECT * FROM cities ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách City</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-3">Danh sách City</h2>

    <a href="city/createCity.php" class="btn btn-success mb-3">➕ Thêm City mới</a>
    <a href="../../index.php#view-travelGuide" class="btn btn-light me-3" style="margin-left: auto;">&larr; Quay lại</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Mã City</th>
                <th>Tên</th>
                <th>Slogan</th>
                <th>Mô tả</th>
                <th>Ảnh Hero</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['city_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['slogan']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <?php if (!empty($row['hero_image'])): ?>
                            <img src="<?= $row['hero_image'] ?>" alt="Hero" style="max-width: 100px;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="city/editCity.php?city_id=<?= $row['city_id'] ?>" class="btn btn-primary btn-sm">✏️ Sửa</a>
                        <a href="city/deleteCity.php?delete_id=<?= $row['city_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa City này?');">🗑️ Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">Chưa có dữ liệu City</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
