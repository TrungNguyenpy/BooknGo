<?php
require_once __DIR__ . '/../../../../config/config.php';

if (!isset($_GET['id'])) {
    die("Không tìm thấy ID tuyến bay.");
}

$id = intval($_GET['id']);

// Lấy dữ liệu tuyến bay hiện tại
$sql = "SELECT * FROM flights WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Tuyến bay không tồn tại.");
}

$flight = $result->fetch_assoc();

$success = "";
$error = "";

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_name = $conn->real_escape_string($_POST['flight_name']);
    $departure   = $conn->real_escape_string($_POST['departure']);
    $arrival     = $conn->real_escape_string($_POST['arrival']);
    $airline     = $conn->real_escape_string($_POST['airline']);
    $price_new   = $conn->real_escape_string($_POST['price_new']);

    // Upload ảnh nếu có
    $newImage = $flight['image'];
    if (!empty($_FILES['image']['name'])) {

        $targetDir = "../../../uploads/";
        $newImage = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $newImage;

        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    // Update SQL
    $update_sql = "
        UPDATE flights SET
            flight_name = '$flight_name',
            departure   = '$departure',
            arrival     = '$arrival',
            airline     = '$airline',
            price_new   = '$price_new',
            image       = '$newImage'
        WHERE id = $id
    ";

    if ($conn->query($update_sql) === TRUE) {
        $success = "✔ Cập nhật thành công!";
    } else {
        $error = "❌ Lỗi cập nhật: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa Tuyến Bay</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        }

        .card-form {
            max-width: 550px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            font-size: 22px;
            font-weight: 700;
            color: #0d6efd;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-submit {
            width: 100%;
            font-weight: bold;
            padding: 10px;
            border-radius: 10px;
        }

        .img-preview {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="card-form">
    <div class="form-title">✏️ Sửa Tuyến Bay</div>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label class="form-label">Tên tuyến bay:</label>
        <input type="text" name="flight_name" class="form-control mb-3" value="<?= $flight['flight_name'] ?>" required>

        <label class="form-label">Điểm đi:</label>
        <input type="text" name="departure" class="form-control mb-3" value="<?= $flight['departure'] ?>" required>

        <label class="form-label">Điểm đến:</label>
        <input type="text" name="arrival" class="form-control mb-3" value="<?= $flight['arrival'] ?>" required>

        <label class="form-label">Hãng hàng không:</label>
        <input type="text" name="airline" class="form-control mb-3" value="<?= $flight['airline'] ?>" required>

        <label class="form-label">Giá vé (VND):</label>
        <input type="number" name="price_new" class="form-control mb-3" value="<?= $flight['price_new'] ?>" required>

        <label class="form-label">Ảnh tuyến bay hiện tại:</label>
        <img src="<?= $base_url ?>/uploads/<?= $flight['image'] ?>" class="img-preview">

        <label class="form-label mt-2">Chọn ảnh mới (nếu muốn):</label>
        <input type="file" name="image" class="form-control mb-4">

       
           <div style="display: flex; margin: 10px;"> 
             <button style="margin-right: 10px;"type="submit" class="btn btn-primary btn-submit">💾 Lưu thay đổi</button>
            <a style="border-radius: 10px;" href="../../../index.php#view-flights" class="btn btn-secondary btn-sm">⬅ Quay lại</a>
        </div>

    </form>
</div>

</body>
</html>
