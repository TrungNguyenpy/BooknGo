<?php
require_once __DIR__ . '/../../../config/config.php';

if (!isset($_GET['id'])) {
    die("Không tìm thấy ID người dùng!");
}

$id = (int)$_GET['id'];
$sql = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("User không tồn tại!");
$user = $result->fetch_assoc();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $identity = trim($_POST['identity_number']);
    $role = (int)$_POST['role'];
    $newPassword = trim($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ.";
    } else {
        // check email unique (exclude current user)
        $chk = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ? LIMIT 1");
        $chk->bind_param("si", $email, $id);
        $chk->execute();
        $res = $chk->get_result();
        if ($res->num_rows > 0) {
            $error = "Email đã được sử dụng bởi tài khoản khác.";
        } else {
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $error = "Mật khẩu mới phải >= 6 ký tự.";
                } else {
                    $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
                    $upd = $conn->prepare("UPDATE users SET fullname=?, email=?, phone=?, identity_number=?, role=?, password=? WHERE user_id=?");
                    $upd->bind_param("ssssisi", $fullname, $email, $phone, $identity, $role, $hashed, $id);
                }
            } else {
                // không đổi mật khẩu
                $upd = $conn->prepare("UPDATE users SET fullname=?, email=?, phone=?, identity_number=?, role=? WHERE user_id=?");
                $upd->bind_param("sssisi", $fullname, $email, $phone, $identity, $role, $id);
            }

            if (empty($error)) {
                if ($upd->execute()) {
                    header("Location: ../../index.php#view-users");
                    exit();
                } else {
                    $error = "Lỗi cập nhật: " . $conn->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa người dùng</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background-color: #f4f6f9; }
    .card { border-radius: 12px; padding: 20px; }
    .form-label { font-weight: 600; }
</style>
</head>
<body class="container py-5">
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <h4 class="mb-3 text-primary">Sửa thông tin người dùng</h4>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <label class="form-label">Họ tên</label>
                <input type="text" name="fullname" class="form-control" required value="<?= htmlspecialchars($user['fullname']) ?>">

                <label class="form-label mt-2">Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">

                <label class="form-label mt-2">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">

                <label class="form-label mt-2">CMND/CCCD</label>
                <input type="text" name="identity_number" class="form-control" value="<?= htmlspecialchars($user['identity_number']) ?>">

                <label class="form-label mt-2">Mật khẩu mới (Nếu đổi)</label>
                <input type="password" name="password" class="form-control" placeholder="Để trống nếu giữ nguyên">

                <label class="form-label mt-2">Vai trò</label>
                <select name="role" class="form-select">
                    <option value="1" <?= ($user['role']==1?'selected':'') ?>>Admin</option>
                    <option value="0" <?= ($user['role']==0?'selected':'') ?>>User</option>
                </select>

                <div class="mt-4 d-flex justify-content-between">
                    <button class="btn btn-success px-4">💾 Lưu lại</button>
                    <a href="../../index.php#view-users" class="btn btn-secondary">✖ Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
