<?php
require_once __DIR__ . '/../../../../config/config.php';

if (!isset($_GET['delete_id'])) {
    die("Không tìm thấy City ID");
}

$city_id = $_GET['delete_id'];

// Lấy hero_image cũ để xóa file
$imgQuery = $conn->query("SELECT hero_image FROM cities WHERE city_id = '$city_id' LIMIT 1");
if ($imgQuery && $imgQuery->num_rows > 0) {
    $imgData = $imgQuery->fetch_assoc();
    if (!empty($imgData['hero_image'])) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $imgData['hero_image'];
        if (file_exists($filePath)) unlink($filePath);
    }
}

$conn->query("DELETE FROM cities WHERE city_id = '$city_id'");
header("Location: ../../../index.php#view-travelGuide");
exit();

?>
