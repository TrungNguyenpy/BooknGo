<?php
session_start();
include __DIR__ . '/../config/config.php';

if (isset($_SESSION['user'])) {
  if ($_SESSION['user']['role'] == 1) {
    header("Location: ./index.php");
  } else {
    header("Location: ../index.php");
  }
  exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Sử dụng password_verify để so sánh password plain input với hash lưu DB
    if (password_verify($password, $user['password'])) {
      // (optional) nếu cần rehash theo algo mới:
      if (password_needs_rehash($user['password'], PASSWORD_BCRYPT)) {
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $up = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $up->bind_param("si", $newHash, $user['user_id']);
        $up->execute();
      }

      // remove password before save into session for safety
      unset($user['password']);
      $_SESSION['user'] = $user;

      if ($user['role'] == 1) {
        header("Location: ./index.php");
      } else {
        header("Location: ../index.php");
      }
      exit();
    } else {
      $error = 'Sai mật khẩu!';
    }
  } else {
    $error = 'Tài khoản không tồn tại!';
  }
}
?>
<!-- rest of HTML form unchanged -->


<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <div class="card shadow p-4" style="width:400px;">
    
    <h3 class="text-center mb-4">Đăng nhập</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger py-2"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- FORM LOGIN -->
    <form method="POST" action="login.php">
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Mật khẩu</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" name="login" class="btn btn-primary w-100">Đăng nhập</button>

      <div class="text-end mt-2">
        <a href="/">Quên mật khẩu?</a>
      </div>
    </form>

    <hr>

    <!-- Đăng nhập mạng xã hội -->
    <div class="text-center mb-3 fw-bold">Hoặc đăng nhập bằng</div>

    <a href="google_login.php" class="btn btn-danger w-100 mb-2">
      <i class="bi bi-google"></i> Google
    </a>

    <a href="facebook_login.php" class="btn btn-primary w-100 mb-3">
      <i class="bi bi-facebook"></i> Facebook
    </a>

    <hr>

    <!-- CHUYỂN TRANG ĐĂNG KÝ -->
    <div class="text-center">
      Chưa có tài khoản?
      <a href="register.php">Đăng ký ngay</a>
    </div>

  </div>

  <!-- Bootstrap Icons (để có icon Google + Facebook) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</body>
</html>
