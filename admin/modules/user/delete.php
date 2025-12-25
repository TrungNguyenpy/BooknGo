<?php
require_once __DIR__ . '/../../../config/config.php';

if (!isset($_GET['id'])) {
    die("Không tìm thấy ID người dùng!");
}

$id = (int)$_GET['id'];

$deleteQuery = "DELETE FROM users WHERE user_id = $id";

if ($conn->query($deleteQuery)) {
    header("Location: ../../index.php#view-users");
    exit();
} else {
    echo "Lỗi xóa: " . $conn->error;
}
