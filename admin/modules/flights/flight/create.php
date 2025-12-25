<?php 
require_once __DIR__ . '/../../../../config/config.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_name = $conn->real_escape_string($_POST['flight_name']);
    $departure   = $conn->real_escape_string($_POST['departure']);
    $arrival     = $conn->real_escape_string($_POST['arrival']);
    $airline     = $conn->real_escape_string($_POST['airline']);
    $price_new   = $conn->real_escape_string($_POST['price_new']);

    // Upload hình ảnh
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    // SQL insert
    $sql = "INSERT INTO flights (flight_name, departure, arrival, airline, price_new, image)
            VALUES ('$flight_name', '$departure', '$arrival', '$airline', '$price_new', '$image')";

    if ($conn->query($sql) === TRUE) {
        $success = "✅ Thêm tuyến bay thành công!";
    } else {
        $error = "❌ Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Thêm Tuyến Bay</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        height: 100vh;
    }

    .card-form {
        max-width: 550px;
        margin: 40px auto;
        padding: 20px;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
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
</style>

</head>
<body>

<div class="card-form">
    <div class="form-title">✈️ Thêm Tuyến Bay Mới</div>
    
    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label class="form-label">Tên tuyến bay:</label>
        <input type="text" name="flight_name" class="form-control mb-3" required>

        <label class="form-label">Điểm đi:</label>
        <input type="text" name="departure" class="form-control mb-3" required>

        <label class="form-label">Điểm đến:</label>
        <input type="text" name="arrival" class="form-control mb-3" required>

        <label class="form-label">Hãng hàng không:</label>
        <input type="text" name="airline" class="form-control mb-3" required>

        <label class="form-label">Giá vé (VND):</label>
        <input type="number" name="price_new" class="form-control mb-3" required>

        <label class="form-label">Ảnh tuyến bay:</label>
        <input type="file" name="image" class="form-control mb-4">

        <div style="display: flex; margin: 10px;"> 
            <button style="margin-right: 10px;" type="submit" class="btn btn-primary btn-submit">➕ Thêm Tuyến Bay</button>
            <a style="border-radius: 10px;" href="../../../index.php#view-flights" class="btn btn-secondary btn-sm">⬅ Quay lại</a>
        </div>

      
     
    </form>
    
</div>

</body>
</html>
