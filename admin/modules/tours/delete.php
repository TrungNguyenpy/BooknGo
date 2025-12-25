<?php
require_once __DIR__ . '/../../../config/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Thiếu ID tour cần xóa.");
}

$id = (int)$_GET['id'];

// --- Check tour có tồn tại không ---
$check = $conn->query("SELECT id FROM tours WHERE id = $id");
if (!$check || $check->num_rows == 0) {
    die("Tour không tồn tại, không thể xóa!");
}

// --- Xóa tour_ticket liên quan ---
$conn->query("
    DELETE tt FROM tour_ticket AS tt
    INNER JOIN tour_details AS td ON tt.tour_detail_id = td.id
    WHERE td.tour_id = $id
");

// --- Xóa tour_details liên quan ---
$conn->query("DELETE FROM tour_details WHERE tour_id = $id");

// --- Xóa tour chính ---
if ($conn->query("DELETE FROM tours WHERE id = $id")) {
    // 🔥 Chuyển hướng về trang danh sách tours
    header("Location:../../index.php#view-hotels");
    exit();
} else {
    die("Lỗi khi xóa tour: " . $conn->error);
}
