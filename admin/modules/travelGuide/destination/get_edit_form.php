<?php
// get_edit_form.php
require_once __DIR__ . '/../../../../config/config.php';
$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$city_id = $_GET['city_id'] ?? '';

if (!$type || !$id) {
    echo '<div class="text-danger">Thiếu tham số</div>'; exit;
}

if ($type === 'place') {
    $stmt = $conn->prepare("SELECT * FROM places WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) { echo '<div class="text-danger">Không tìm thấy place</div>'; exit; }
    ?>
    <form onsubmit="return submitEditForm(this)" action="update_item.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="type" value="place">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">
      <input type="hidden" name="city_id" value="<?= htmlspecialchars($city_id) ?>">
      <div class="mb-3">
        <label class="form-label">Tên</label>
        <input name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Ảnh hiện tại</label><br>
        <?php if(!empty($row['image_url'])): ?>
            <img src="<?= $base_url . htmlspecialchars($row['image_url']) ?>" class="small-img mb-2">
        <?php else: ?>
            <div class="text-muted mb-2">Chưa có ảnh</div>
        <?php endif; ?>
        <input type="file" name="image" class="form-control">
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-success">Lưu</button>
      </div>
    </form>
    <?php
    exit;
}

if ($type === 'food') {
    $stmt = $conn->prepare("SELECT * FROM foods WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) { echo '<div class="text-danger">Không tìm thấy food</div>'; exit; }
    ?>
    <form onsubmit="return submitEditForm(this)" action="update_item.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="type" value="food">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">
      <input type="hidden" name="city_id" value="<?= htmlspecialchars($city_id) ?>">
      <div class="mb-3">
        <label class="form-label">Tên</label>
        <input name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Ảnh hiện tại</label><br>
        <?php if(!empty($row['image_url'])): ?>
            <img src="<?= $base_url . htmlspecialchars($row['image_url']) ?>" class="small-img mb-2">
        <?php else: ?>
            <div class="text-muted mb-2">Chưa có ảnh</div>
        <?php endif; ?>
        <input type="file" name="image" class="form-control">
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-success">Lưu</button>
      </div>
    </form>
    <?php
    exit;
}

if ($type === 'event') {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) { echo '<div class="text-danger">Không tìm thấy event</div>'; exit; }
    ?>
    <form onsubmit="return submitEditForm(this)" action="update_item.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="type" value="event">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">
      <input type="hidden" name="city_id" value="<?= htmlspecialchars($city_id) ?>">
      <div class="mb-3">
        <label class="form-label">Tên</label>
        <input name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Ngày</label>
        <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($row['event_date'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Link</label>
        <input name="link" class="form-control" value="<?= htmlspecialchars($row['link'] ?? '') ?>">
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-success">Lưu</button>
      </div>
    </form>
    <?php
    exit;
}

echo '<div class="text-danger">Loại không hợp lệ</div>';
exit;
