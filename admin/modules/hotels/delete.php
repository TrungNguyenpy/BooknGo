<?php
require_once __DIR__ . '/../../../config/config.php';

// Kiểm tra nếu có ID được gửi qua URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Kiểm tra kết nối
    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }

    // Câu lệnh xóa hotel theo id
    $sql = "DELETE FROM hotels WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Đã xóa khách sạn thành công!');</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa: " . mysqli_error($conn) . "');</script>";
    }

    // Sau khi xóa xong, quay lại phần view-hotels
    echo "<script>
      window.location.href='../../index.php#view-hotels';
    </script>";

    exit;
} else {
    echo "<script>
        alert('Thiếu ID khách sạn!');
        window.location.href='../../index.php#view-hotels';
    </script>";
    exit;
}
?>
