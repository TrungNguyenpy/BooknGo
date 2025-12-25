<?php
// edit.php
require_once __DIR__ . '/../../../../config/config.php';

// kiểm tra city_id
if (!isset($_GET['city_id']) || empty($_GET['city_id'])) {
    die("Không tìm thấy city_id");
}
$city_id = $_GET['city_id'];

// --------------------------------------------------
// XỬ LÝ POST của Destination (nếu bạn vẫn muốn xử lý ở đây)
// mình giữ logic upload giống file trước
function uploadImageLocal($fileKey) {
    if (empty($_FILES[$fileKey]['name'])) return null;
    $uploadDir = __DIR__ . '/../../../../img/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName = time() . "_" . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES[$fileKey]['name']));
    $targetFile = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
        return "/img/" . $fileName;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_destination'])) {
    $name = $_POST['name'] ?? '';
    // lấy current
    $stmt0 = $conn->prepare("SELECT image_url, country_flag FROM destinations WHERE city_id = ? LIMIT 1");
    $stmt0->bind_param("s", $city_id);
    $stmt0->execute();
    $cur = $stmt0->get_result()->fetch_assoc();
    $stmt0->close();

    $newImage = uploadImageLocal('image');
    $image_url = $newImage ?? ($cur['image_url'] ?? null);

    $newFlag = uploadImageLocal('country_flag_file');
    $country_flag = $newFlag ?? ($_POST['country_flag_text'] ?? ($cur['country_flag'] ?? null));

    // insert or update
    $stmtCheck = $conn->prepare("SELECT id FROM destinations WHERE city_id = ? LIMIT 1");
    $stmtCheck->bind_param("s", $city_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result()->fetch_assoc();
    $stmtCheck->close();

    if ($resCheck) {
        $stmt = $conn->prepare("UPDATE destinations SET name = ?, image_url = ?, country_flag = ? WHERE city_id = ?");
        $stmt->bind_param("ssss", $name, $image_url, $country_flag, $city_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO destinations (city_id, name, image_url, country_flag) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $city_id, $name, $image_url, $country_flag);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ?city_id=" . urlencode($city_id));
    exit;
}

// --------------------------------------------------
// LẤY DỮ LIỆU
$stmt = $conn->prepare("SELECT * FROM destinations WHERE city_id = ? LIMIT 1");
$stmt->bind_param("s", $city_id);
$stmt->execute();
$destination = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM places WHERE city_id = ? ORDER BY id ASC");
$stmt->bind_param("s", $city_id);
$stmt->execute();
$places = $stmt->get_result();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM foods WHERE city_id = ? ORDER BY id ASC");
$stmt->bind_param("s", $city_id);
$stmt->execute();
$foods = $stmt->get_result();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM events WHERE city_id = ? ORDER BY id ASC");
$stmt->bind_param("s", $city_id);
$stmt->execute();
$events = $stmt->get_result();
$stmt->close();

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Destination - <?= htmlspecialchars($city_id) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.small-img{width:70px;height:auto}.flag-img{width:40px;height:auto}</style>
</head>
<body>
<div class="container my-4">
    <a href="../../../index.php#view-travelGuide" class="btn btn-secondary mb-3">⬅ Quay lại</a>
    <h3>Sửa Destination / Places / Foods / Events — City: <strong><?= htmlspecialchars($city_id) ?></strong></h3>

    <!-- Destination form (kept as normal submit) -->
    <div class="card mb-4">
      <div class="card-body">
        <h5>Destination</h5>
        <form method="POST" enctype="multipart/form-data" class="row g-3">
          <input type="hidden" name="update_destination" value="1">
          <div class="col-md-6">
            <label class="form-label">Tên</label>
            <input name="name" class="form-control" value="<?= htmlspecialchars($destination['name'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Ảnh hiện tại</label><br>
            <?php if(!empty($destination['image_url'])): ?>
              <img src="<?= $base_url . htmlspecialchars($destination['image_url']) ?>" class="small-img mb-2">
            <?php else: ?>
              <div class="text-muted mb-2">Chưa có ảnh</div>
            <?php endif; ?>
            <input type="file" name="image" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Country flag</label><br>
            <?php if(!empty($destination['country_flag'])): ?>
              <img src="<?= $base_url . htmlspecialchars($destination['country_flag']) ?>" class="flag-img mb-2">
            <?php else: ?>
              <div class="text-muted mb-2">Chưa có cờ</div>
            <?php endif; ?>
            <input type="file" name="country_flag_file" class="form-control mb-1">
            <input type="text" name="country_flag_text" class="form-control" value="<?= htmlspecialchars($destination['country_flag'] ?? '') ?>">
          </div>
          <div class="col-12">
            <button class="btn btn-success">Lưu Destination</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tạo các bảng và nút Edit/Delete (Edit mở modal via AJAX) -->
    <!-- PLACES -->
    <h5>Places</h5>
    <table class="table table-bordered">
      <thead><tr><th>Tên</th><th>Mô tả</th><th>Ảnh</th><th>Hành động</th></tr></thead>
      <tbody>
      <?php if($places && $places->num_rows>0): ?>
        <?php while($p = $places->fetch_assoc()): ?>
        <tr id="row-place-<?= $p['id'] ?>">
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td style="max-width:280px"><?= nl2br(htmlspecialchars($p['description'] ?? '')) ?></td>
          <td>
            <?php if(!empty($p['image_url'])): ?>
              <img src="<?= $base_url . htmlspecialchars($p['image_url']) ?>" class="small-img">
            <?php else: ?>
              <div class="text-muted">No image</div>
            <?php endif; ?>
          </td>
          <td style="white-space:nowrap">
            <button class="btn btn-primary btn-sm" onclick="openEditModal('place', <?= $p['id'] ?>)">✏ Sửa</button>
            <button class="btn btn-danger btn-sm" onclick="deleteItem('place', <?= $p['id'] ?>)">🗑 Xóa</button>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" class="text-center">Chưa có Place</td></tr>
      <?php endif; ?>
      </tbody>
    </table>

    <!-- FOODS -->
    <h5>Foods</h5>
    <table class="table table-bordered">
      <thead><tr><th>Tên</th><th>Mô tả</th><th>Ảnh</th><th>Hành động</th></tr></thead>
      <tbody>
      <?php if($foods && $foods->num_rows>0): ?>
        <?php while($f = $foods->fetch_assoc()): ?>
        <tr id="row-food-<?= $f['id'] ?>">
          <td><?= htmlspecialchars($f['name']) ?></td>
          <td style="max-width:280px"><?= nl2br(htmlspecialchars($f['description'] ?? '')) ?></td>
          <td>
            <?php if(!empty($f['image_url'])): ?>
              <img src="<?= $base_url . htmlspecialchars($f['image_url']) ?>" class="small-img">
            <?php else: ?>
              <div class="text-muted">No image</div>
            <?php endif; ?>
          </td>
          <td style="white-space:nowrap">
            <button class="btn btn-primary btn-sm" onclick="openEditModal('food', <?= $f['id'] ?>)">✏ Sửa</button>
            <button class="btn btn-danger btn-sm" onclick="deleteItem('food', <?= $f['id'] ?>)">🗑 Xóa</button>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4" class="text-center">Chưa có Food</td></tr>
      <?php endif; ?>
      </tbody>
    </table>

    <!-- EVENTS -->
    <h5>Events</h5>
    <table class="table table-bordered">
      <thead><tr><th>Tên</th><th>Mô tả</th><th>Ngày</th><th>Link</th><th>Hành động</th></tr></thead>
      <tbody>
      <?php if($events && $events->num_rows>0): ?>
        <?php while($e = $events->fetch_assoc()): ?>
        <tr id="row-event-<?= $e['id'] ?>">
          <td><?= htmlspecialchars($e['name']) ?></td>
          <td style="max-width:280px"><?= nl2br(htmlspecialchars($e['description'] ?? '')) ?></td>
          <td><?= htmlspecialchars($e['event_date'] ?? '') ?></td>
          <td><?= htmlspecialchars($e['link'] ?? '') ?></td>
          <td style="white-space:nowrap">
            <button class="btn btn-primary btn-sm" onclick="openEditModal('event', <?= $e['id'] ?>)">✏ Sửa</button>
            <button class="btn btn-danger btn-sm" onclick="deleteItem('event', <?= $e['id'] ?>)">🗑 Xóa</button>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center">Chưa có Event</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
</div>

<!-- Modal (dùng chung) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalTitle">Sửa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="editModalBody">
        <!-- Load nội dung từ get_edit_form.php -->
        <div class="text-center">Đang tải...</div>
      </div>
    </div>
  </div>
</div>

<script>
const basePath = '<?= htmlspecialchars(basename(__DIR__)) ?>'; // not used, kept for reference
function openEditModal(type, id) {
    const url = `get_edit_form.php?type=${encodeURIComponent(type)}&id=${encodeURIComponent(id)}&city_id=${encodeURIComponent('<?= $city_id ?>')}`;
    const modalBody = document.getElementById('editModalBody');
    modalBody.innerHTML = 'Đang tải...';
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    fetch(url).then(r=>r.text()).then(html=>{
        modalBody.innerHTML = html;
        modal.show();
    }).catch(err=>{
        modalBody.innerHTML = '<div class="text-danger">Lỗi khi tải form</div>';
        console.error(err);
    });
}

// submit form via AJAX (form inside modal)
async function submitEditForm(form) {
    const formData = new FormData(form);
    const action = form.getAttribute('action') || 'update_item.php';
    const btn = form.querySelector('[type="submit"]');
    if (btn) { btn.disabled = true; }
    try {
        const res = await fetch(action, { method: 'POST', body: formData });
        const json = await res.json();
        if (json.success) {
            // đóng modal & reload page để thấy thay đổi
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            alert(json.message || 'Cập nhật thành công');
            location.reload();
        } else {
            alert(json.message || 'Có lỗi xảy ra');
            if (btn) btn.disabled = false;
        }
    } catch (e) {
        console.error(e);
        alert('Lỗi mạng hoặc server');
        if (btn) btn.disabled = false;
    }
    return false;
}

// Delete item (place/food/event)
function deleteItem(type, id) {
    if (!confirm('Bạn có chắc muốn xóa?')) return;
    fetch('delete_item.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type, id, city_id: '<?= $city_id ?>' })
    }).then(r=>r.json()).then(json=>{
        if (json.success) {
            alert(json.message || 'Đã xóa');
            location.reload();
        } else {
            alert(json.message || 'Xóa thất bại');
        }
    }).catch(err=>{
        console.error(err);
        alert('Lỗi mạng');
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
