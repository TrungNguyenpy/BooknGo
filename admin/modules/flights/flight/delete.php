<?php
require_once __DIR__ . '/../../../../config/config.php';

if (!isset($_GET['id'])) {
    die("Không tìm thấy ID để xóa.");
}

$id = intval($_GET['id']);

// Lấy ảnh để xóa khỏi thư mục uploads
$sql = "SELECT image FROM flights WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Tuyến bay không tồn tại.");
}

$flight = $result->fetch_assoc();
$image = $flight['image'];

// Xóa ảnh trong thư mục
if (!empty($image) && file_exists("../../../uploads/" . $image)) {
    unlink("../../../uploads/" . $image);
}

// Xóa record trong database
$delete_sql = "DELETE FROM flights WHERE id = $id";

if ($conn->query($delete_sql) === TRUE) {
    header("Location: ../../../index.php#flight.php");

    exit();
} else {
    die("❌ Lỗi khi xóa: " . $conn->error);
}
?>
<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success text-center">
        ✔ Đã xóa tuyến bay thành công!
    </div>
<?php endif; ?>

