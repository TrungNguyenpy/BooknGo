<?php
// update_item.php
require_once __DIR__ . '/../../../../config/config.php';
header('Content-Type: application/json; charset=utf-8');

$type = $_POST['type'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$city_id = $_POST['city_id'] ?? '';

if (!$type || !$id) {
    echo json_encode(['success'=>false,'message'=>'Thiếu tham số']); exit;
}

// helper upload
function uploadImageLocal($fileKey) {
    if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['name'])) return null;
    $uploadDir = __DIR__ . '/../../../../img/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName = time() . "_" . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES[$fileKey]['name']));
    $targetFile = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
        return "/img/" . $fileName;
    }
    return null;
}

try {
    if ($type === 'place') {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $newImage = uploadImageLocal('image');
        if ($newImage !== null) {
            $stmt = $conn->prepare("UPDATE places SET name=?, description=?, image_url=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $description, $newImage, $id);
        } else {
            $stmt = $conn->prepare("UPDATE places SET name=?, description=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $description, $id);
        }
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>true,'message'=>'Đã cập nhật Place']);
        exit;
    }

    if ($type === 'food') {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $newImage = uploadImageLocal('image');
        if ($newImage !== null) {
            $stmt = $conn->prepare("UPDATE foods SET name=?, description=?, image_url=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $description, $newImage, $id);
        } else {
            $stmt = $conn->prepare("UPDATE foods SET name=?, description=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $description, $id);
        }
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>true,'message'=>'Đã cập nhật Food']);
        exit;
    }

    if ($type === 'event') {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
        $link = $_POST['link'] ?? '';
        $stmt = $conn->prepare("UPDATE events SET name=?, description=?, event_date=?, link=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $description, $event_date, $link, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>true,'message'=>'Đã cập nhật Event']);
        exit;
    }

    echo json_encode(['success'=>false,'message'=>'Loại không hợp lệ']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Lỗi server: '.$e->getMessage()]);
    exit;
}
