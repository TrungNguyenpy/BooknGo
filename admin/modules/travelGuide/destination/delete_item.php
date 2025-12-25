<?php
require_once __DIR__ . '/../../../../config/config.php';
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!isset($data['type'], $data['id'], $data['city_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
    exit;
}

$type = $data['type'];
$id   = (int)$data['id'];
$city_id = $data['city_id'];

// Ánh xạ bảng theo type
$tableMap = [
    'place' => 'places',
    'food'  => 'foods',
    'event' => 'events'
];

if (!isset($tableMap[$type])) {
    echo json_encode(['success' => false, 'message' => 'Loại không hợp lệ']);
    exit;
}

$table = $tableMap[$type];

// ✅ Xóa theo id và city_id (chống xóa nhầm)
$sql = "DELETE FROM $table WHERE id = ? AND city_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id, $city_id);
$ok = $stmt->execute();
$stmt->close();

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Xóa thành công!' : 'Không thể xóa!'
]);
exit;
