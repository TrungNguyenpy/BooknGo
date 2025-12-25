<?php
require_once __DIR__ . '/../../../config/config.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $identity = $conn->real_escape_string($_POST['identity_number']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = (int)$_POST['role'];

    $checkEmail = $conn->query("SELECT 1 FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $error = "Email đã tồn tại!";
    } else {
        $insertQuery = "INSERT INTO users(fullname,email,phone,identity_number,password,role)
                        VALUES('$fullname','$email','$phone','$identity','$password','$role')";

        if ($conn->query($insertQuery)) {
            header("Location: ../../index.php#view-users");
            exit();
        } else {
            $error = "Lỗi thêm mới: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm người dùng</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }
        .card {
            border-radius: 12px;
            padding: 20px;
        }
    </style>
</head>

<body class="container py-5">

<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card shadow">
            <h4 class="mb-3 text-primary">➕ Thêm người dùng</h4>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <label class="form-label">Họ tên</label>
                <input type="text" name="fullname" class="form-control" required>

                <label class="form-label mt-2">Email</label>
                <input type="email" name="email" class="form-control" required>

                <label class="form-label mt-2">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>

                <label class="form-label mt-2">Số điện thoại</label>
                <input type="text" name="phone" class="form-control">

                <label class="form-label mt-2">CMND/CCCD</label>
                <input type="text" name="identity_number" class="form-control">

                <label class="form-label mt-2">Vai trò</label>
                <select name="role" class="form-select">
                    <option value="0">User</option>
                    <option value="1">Admin</option>
                </select>

                <div class="mt-4 d-flex justify-content-between">
                    <button class="btn btn-success px-4">✅ Lưu</button>
                    <a href="../../index.php#view-users" class="btn btn-secondary">⬅ Quay lại</a>
                </div>
            </form>
        </div>

    </div>
</div>

</body>
</html>
