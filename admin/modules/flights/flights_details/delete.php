<?php
require_once __DIR__ . '/../../../../config/config.php';

// Kiểm tra có id không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy ID chi tiết chuyến bay!");
}

$id = $_GET['id'];

// Xóa dữ liệu
$sql = "DELETE FROM flight_details WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Quay lại trang danh sách
    header("Location: ../view_details.php?id=" . $_GET['flight_id']);
    exit;
} else {
    echo "❌ Lỗi khi xóa: " . $stmt->error;
}
