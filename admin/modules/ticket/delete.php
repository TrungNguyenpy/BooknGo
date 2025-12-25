<?php
require_once __DIR__ . '/../../../config/config.php';

if (!isset($_GET['id'])) {
    die("Không tìm thấy ID");
}

$id = (int)$_GET['id'];

// Xóa ảnh trong thư mục nếu tồn tại
$imgQuery = $conn->query("SELECT tour_img FROM tour_ticket WHERE id = $id");
if ($imgQuery && $imgQuery->num_rows > 0) {
    $imgData = $imgQuery->fetch_assoc();
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $imgData['tour_img'];
    if (file_exists($filePath)) unlink($filePath);
}

// Xóa dữ liệu
$sql = "DELETE FROM tour_ticket WHERE id = $id";
$conn->query($sql);

header("Location:../../index.php");
exit();
