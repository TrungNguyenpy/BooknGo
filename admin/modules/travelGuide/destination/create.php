<?php
require_once __DIR__ . '/../../../../config/config.php';

// Messages thông báo
$messages = [
    'destination' => '',
    'place' => '',
    'food' => '',
    'event' => ''
];

// ✅ Lấy danh sách City chuẩn
$cities = [];
$res = $conn->query("SELECT city_id, name FROM cities ORDER BY name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $cities[] = $row;
    }
}

// ✅ Hàm xử lý upload ảnh
function uploadImage($fileKey) {
    if (empty($_FILES[$fileKey]['name'])) return null;

    $uploadDir = __DIR__ . '/../../../../img/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES[$fileKey]['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
        return "/img/" . $fileName;
    }

    return null;
}

// ✅ Xử lý thêm mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $city_id = $_POST['city_id'];
    $name = $_POST['name'] ?? '';

    // ✅ Upload ảnh chung
    $image_url = uploadImage("image");

    if ($action === 'create_destination') {
        $country_flag = $_POST['country_flag'] ?? '';

        $stmt = $conn->prepare("INSERT INTO destinations (city_id, name, image_url, country_flag)
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $city_id, $name, $image_url, $country_flag);
        $messages['destination'] = $stmt->execute() ? "✅ Thêm Destination thành công!" : "❌ Lỗi: " . $stmt->error;
    }

    if ($action === 'create_place') {
        $stmt = $conn->prepare("INSERT INTO places (city_id, name, image_url)
                                VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $city_id, $name, $image_url);
        $messages['place'] = $stmt->execute() ? "✅ Thêm Place thành công!" : "❌ Lỗi: " . $stmt->error;
    }

    if ($action === 'create_food') {
        $description = $_POST['description'] ?? '';

        $stmt = $conn->prepare("INSERT INTO foods (city_id, name, description, image_url)
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $city_id, $name, $description, $image_url);
        $messages['food'] = $stmt->execute() ? "✅ Thêm Food thành công!" : "❌ Lỗi: " . $stmt->error;
    }

    if ($action === 'create_event') {
        $description = $_POST['description'] ?? '';
        $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
        $link = $_POST['link'] ?? '';

        $stmt = $conn->prepare("INSERT INTO events (city_id, name, description, event_date, link)
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $city_id, $name, $description, $event_date, $link);
        $messages['event'] = $stmt->execute() ? "✅ Thêm Event thành công!" : "❌ Lỗi: " . $stmt->error;
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container my-4">

    <h4 class="mb-3 fw-bold">Thêm dữ liệu theo loại</h4>

    <div class="mb-3">
        <label class="form-label">Loại dữ liệu *</label>
        <select name="type" id="type" class="form-select" required onchange="onTypeChange()">
            <option value="">-- Chọn loại --</option>
            <option value="destination">Destination</option>
            <option value="place">Địa điểm</option>
            <option value="food">Món ăn</option>
            <option value="event">Sự kiện</option>
        </select>
    </div>

    <?php function renderCities($cities) { ?>
        <?php foreach ($cities as $c): ?>
            <option value="<?= $c['city_id'] ?>">
                <?= htmlspecialchars($c['name']) ?> (<?= $c['city_id'] ?>)
            </option>
        <?php endforeach; ?>
    <?php } ?>

    <!-- ✅ Form Destination -->
    <form method="POST" enctype="multipart/form-data" id="form-destination" class="card p-3 shadow-sm d-none">
        <input type="hidden" name="action" value="create_destination">
        <h5 class="text-primary">Thêm Destination</h5>

        <?php if($messages['destination']): ?>
            <div class="alert alert-info"><?= $messages['destination'] ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label>City *</label>
            <select name="city_id" class="form-select" required>
                <?php renderCities($cities); ?>
            </select>
        </div>

        <div class="mb-3"><input name="name" class="form-control" placeholder="Tên"></div>
        <div class="mb-3"><input type="file" name="image" class="form-control"></div>
        <div class="mb-3"><input name="country_flag" class="form-control" placeholder="Quốc kỳ"></div>

        <button class="btn btn-primary">Thêm Destination</button>
    </form>

    <!-- ✅ Form Place -->
    <form method="POST" enctype="multipart/form-data" id="form-place" class="card p-3 shadow-sm d-none">
        <input type="hidden" name="action" value="create_place">
        <h5 class="text-primary">Thêm Địa Điểm</h5>

        <?php if($messages['place']): ?>
            <div class="alert alert-info"><?= $messages['place'] ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label>City *</label>
            <select name="city_id" class="form-select" required>
                <?php renderCities($cities); ?>
            </select>
        </div>

        <div class="mb-3"><input name="name" class="form-control" placeholder="Tên địa điểm"></div>
        <div class="mb-3"><input type="file" name="image" class="form-control"></div>

        <button class="btn btn-primary">Thêm Địa Điểm</button>
    </form>

    <!-- ✅ Form Food -->
    <form method="POST" enctype="multipart/form-data" id="form-food" class="card p-3 shadow-sm d-none">
        <input type="hidden" name="action" value="create_food">
        <h5 class="text-primary">Thêm Món Ăn</h5>

        <?php if($messages['food']): ?>
            <div class="alert alert-info"><?= $messages['food'] ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label>City *</label>
            <select name="city_id" class="form-select" required>
                <?php renderCities($cities); ?>
            </select>
        </div>

        <div class="mb-3"><input name="name" class="form-control" placeholder="Tên món ăn"></div>
        <div class="mb-3"><textarea name="description" class="form-control" placeholder="Mô tả"></textarea></div>
        <div class="mb-3"><input type="file" name="image" class="form-control"></div>

        <button class="btn btn-primary">Thêm Món Ăn</button>
    </form>

    <!-- ✅ Form Event -->
    <form method="POST" enctype="multipart/form-data" id="form-event" class="card p-3 shadow-sm d-none">
        <input type="hidden" name="action" value="create_event">
        <h5 class="text-primary">Thêm Sự Kiện</h5>

        <?php if($messages['event']): ?>
            <div class="alert alert-info"><?= $messages['event'] ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label>City *</label>
            <select name="city_id" class="form-select" required>
                <?php renderCities($cities); ?>
            </select>
        </div>

        <div class="mb-3"><input name="name" class="form-control" placeholder="Tên sự kiện"></div>
        <div class="mb-3"><textarea name="description" class="form-control" placeholder="Mô tả"></textarea></div>
        <div class="mb-3"><input type="date" name="event_date" class="form-control"></div>
        <div class="mb-3"><input name="link" class="form-control" placeholder="Link"></div>
   

        <button class="btn btn-primary">Thêm Sự Kiện</button>
    </form>
<a href="../../../index.php#view-travelGuide" class="btn btn-secondary">Hủy</a>
</div>

<script>
function onTypeChange() {
    const type = document.getElementById("type").value;
    document.querySelectorAll("form[id^='form-']").forEach(f=>f.classList.add("d-none"));
    if(type) document.getElementById("form-"+type).classList.remove("d-none");
}
</script>
