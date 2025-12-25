<?php 
include __DIR__ . '/../config/config.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $identity = trim($_POST['identity_number']);
    $passwordPlain = $_POST['password'];

    // Role mặc định là user (0)
    $role = 0;

    // Kiểm tra trống
    if ($fullname === "" || $email === "" || $passwordPlain === "") {
        $error = "Vui lòng nhập đầy đủ họ tên, email và mật khẩu!";
    } else {

        // Kiểm tra email tồn tại
        $sql = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email đã tồn tại!";
        } else {

            // Hash mật khẩu
            $passwordHash = password_hash($passwordPlain, PASSWORD_BCRYPT);

            // Thêm user mới
            $insert = "INSERT INTO users(fullname,email,phone,identity_number,password,role)
                       VALUES(?,?,?,?,?,?)";

            $stmt2 = $conn->prepare($insert);
            $stmt2->bind_param("sssssi",
                $fullname, $email, $phone, $identity, $passwordHash, $role
            );

            if ($stmt2->execute()) {
                // Chuyển sang trang login
                header("Location: ./login.php?success=registered");
                exit();
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #f8f9fa);
        }
        .card {
            border-radius: 14px;
            padding: 25px;
        }
        .form-control {
            padding-left: 40px;
        }
        .icon-field {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .input-group-text {
            cursor: pointer;
        }
    </style>
</head>

<body class="container py-5">

<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card shadow">
            <h3 class="text-center mb-3 text-primary fw-bold">
                 Đăng ký tài khoản
            </h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">

                <!-- Họ tên -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-person icon-field"></i>
                    <input type="text" name="fullname" class="form-control" placeholder="Họ và tên" required>
                </div>

                <!-- Email -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-envelope icon-field"></i>
                    <input type="email" name="email" class="form-control" placeholder="Địa chỉ email" required>
                </div>

                <!-- Mật khẩu -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-lock icon-field"></i>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Mật khẩu" required>
                        <span class="input-group-text" onclick="togglePassword('password', this)">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>

                <!-- Nhập lại mật khẩu -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-lock-fill icon-field"></i>
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu" required>
                        <span class="input-group-text" onclick="togglePassword('confirm_password', this)">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>

                <!-- Số điện thoại -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-telephone icon-field"></i>
                    <input type="text" name="phone" class="form-control" placeholder="Số điện thoại (không bắt buộc)">
                </div>

                <!-- CCCD/CMND -->
                <div class="mb-3 position-relative">
                    <i class="bi bi-credit-card icon-field"></i>
                    <input type="text" name="identity_number" class="form-control" placeholder="CMND/CCCD (không bắt buộc)">
                </div>

                <!-- Nút đăng ký -->
                <button class="btn btn-success w-100 py-2 fs-5">Đăng ký</button>

                <div class="text-center mt-3">
                    <span>Bạn đã có tài khoản?</span>
                    <a href="./login.php" class="text-primary fw-bold">Đăng nhập</a>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    const icon = el.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye-slash", "bi-eye");
    } else {
        input.type = "password";
        icon.classList.replace("bi-eye", "bi-eye-slash");
    }
}
</script>

</body>
</html>
