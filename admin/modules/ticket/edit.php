<?php
require_once __DIR__ . '/../../../config/config.php';

if (!isset($_GET['id'])) {
    die("Không tìm thấy ID vé!");
}
$id = (int)$_GET['id'];

// Lấy dữ liệu vé
$sql = "SELECT * FROM tour_ticket WHERE id = $id";
$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) {
    die("Vé không tồn tại!");
}
$ticket = $res->fetch_assoc();

// Lấy danh sách Tour Details để chọn
$details = $conn->query("SELECT td.id, t.name, td.departure_place 
                         FROM tour_details td 
                         JOIN tours t ON td.tour_id = t.id");

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tour_detail_id = $_POST['tour_detail_id'];
    $date = $_POST['date'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $old_price = $_POST['old_price'];
    $discount_percent = $_POST['discount_percent'];
    $top_pick = isset($_POST['top_pick']) ? 1 : 0;

    $tour_img = $ticket['tour_img']; // giữ ảnh cũ nếu không up ảnh mới
    if (!empty($_FILES['tour_img']['name'])) {
        $targetDir = "/img/ticket/";
        $fileName = uniqid() . "_" . basename($_FILES['tour_img']['name']);
        $targetFile = $_SERVER['DOCUMENT_ROOT'] . $targetDir . $fileName;

        if (move_uploaded_file($_FILES['tour_img']['tmp_name'], $targetFile)) {
            $tour_img = $targetDir . $fileName;
        }
    }

    $update = "UPDATE tour_ticket SET 
                tour_detail_id='$tour_detail_id',
                date='$date',
                title='$title',
                tour_img='$tour_img',
                price='$price',
                old_price='$old_price',
                discount_percent='$discount_percent',
                top_pick='$top_pick'
               WHERE id=$id";

        if ($conn->query($update)) {
            header("Location: edit.php?id=$id&message=success");
            exit();
        } else {
            $message = "❌ Lỗi cập nhật: " . $conn->error;
        }

}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa vé tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h3>✏️ Sửa vé tour</h3>
<?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ Cập nhật vé thành công!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>



<form method="POST" enctype="multipart/form-data" class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Tour Details</label>
        <select class="form-select" name="tour_detail_id" required>
            <?php while ($d = $details->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>" <?= $d['id']==$ticket['tour_detail_id'] ? 'selected':'' ?>>
                    <?= $d['name'] ?> - <?= $d['departure_place'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Ngày khởi hành</label>
        <input type="date" class="form-control" name="date" value="<?= $ticket['date'] ?>" required>
    </div>

    <div class="col-12">
        <label class="form-label">Tiêu đề vé</label>
        <input type="text" class="form-control" name="title" value="<?= $ticket['title'] ?>" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Giá</label>
        <input type="numeric" class="form-control" name="price" value="<?= $ticket['price'] ?>" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Giá cũ</label>
        <input type="numeric" class="form-control" name="old_price" value="<?= $ticket['old_price'] ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">Giảm (%)</label>
        <input type="number" class="form-control" name="discount_percent" value="<?= $ticket['discount_percent'] ?>">
    </div>

    <div class="col-12">
        <label class="form-label">Ảnh hiện tại</label><br>
        <img src="<?= $base_url . $ticket['tour_img'] ?>" width="150" class="rounded">
        <input type="file" class="form-control mt-2" name="tour_img">
    </div>

    <div class="col-12">
        <label class="form-check-label">
            <input type="checkbox" name="top_pick" <?= $ticket['top_pick'] ? 'checked' : '' ?>> Nổi bật ⭐
        </label>
    </div>

    <div class="col-12">
        <button class="btn btn-success">Lưu lại</button>
        <a href="../../index.php#view-tours" class="btn btn-secondary">Hủy</a>
    </div>

</form>

</body>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const oldPriceInput = document.querySelector('input[name="old_price"]');
    const discountInput = document.querySelector('input[name="discount_percent"]');
    const priceInput = document.querySelector('input[name="price"]');

    function formatMoney(num) {
        return num.toLocaleString('vi-VN'); // ✅ dùng định dạng VN (.) phân cách nghìn
    }

    function calculatePrice() {
        let oldPrice = parseInt(oldPriceInput.value.replace(/\./g, '')) || 0;
        let discount = parseFloat(discountInput.value) || 0;

        if (oldPrice > 0 && discount >= 0 && discount <= 100) {
            let newPrice = oldPrice * (1 - discount / 100);
            priceInput.value = formatMoney(Math.round(newPrice));
        }
    }

    // ✅ Format khi nhập giá cũ
    oldPriceInput.addEventListener("input", function () {
        let raw = this.value.replace(/\./g, '').replace(/\D/g, '');
        if (raw !== "") {
            this.value = formatMoney(parseInt(raw));
        } else {
            this.value = "";
        }
        calculatePrice();
    });

    // ✅ Tính giá khi nhập % giảm
    discountInput.addEventListener("input", calculatePrice);

    // ✅ Format giá mới nếu nhập thủ công
    priceInput.addEventListener("input", function () {
        let raw = this.value.replace(/\./g, '').replace(/\D/g, '');
        if (raw !== "") {
            this.value = formatMoney(parseInt(raw));
        } else {
            this.value = "";
        }
    });

    // ✅ Trước khi submit cần bỏ dấu chấm để lưu DB chuẩn
    document.querySelector("form").addEventListener("submit", function () {
        priceInput.value = priceInput.value.replace(/\./g, '');
        oldPriceInput.value = oldPriceInput.value.replace(/\./g, '');
    });
});
</script>


</html>
