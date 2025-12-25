<?php 
session_start();
    include __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BooknGo</title>
    <link rel="stylesheet" href="<?= $base_url ?>/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/css/style.css?v=<?= time(); ?>">
</head>
<body>
    <header>
        <div class="logo">
            <a href="<?= $base_url ?>/index.php">
                <img src="<?= $base_url ?>/img/logo.svg" alt="BooknGo" style="height:50px;">
            </a>
        </div>
        <nav class="topnav">
            <a href="<?= $base_url ?>/index.php">Home</a>
            <a href="#" data-bs-toggle="modal" data-bs-target="#supportModal">Hỗ trợ</a>

            <a href="#Hotel">Khách sạn</a>
            <a href="#Tour">Vui chơi</a>
            <a href="#Flight">Máy bay</a>
            <a href="#Destination">Điểm đến</a>
        </nav>


        <div class="auth">
            <?php if (isset($_SESSION['user'])): ?>
                <span>Xin chào, <?= $_SESSION['user']['fullname'] ?></span>
                <a class="Logout" href="<?= $base_url ?>/admin/logout.php">Đăng xuất</a>
            <?php else: ?>
                <a class="Login" href="<?= $base_url ?>/admin/login.php">Đăng nhập</a>
                <a class="Register" href="<?= $base_url ?>/admin/register.php">Đăng ký</a>
            <?php endif; ?>
        </div>


      
    </header>
       <!-- Modal Hỗ trợ -->
<div class="modal fade" id="supportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa-solid fa-headset me-2"></i>Liên hệ hỗ trợ
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <form id="supportForm">
          <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Nội dung hỗ trợ</label>
            <textarea class="form-control" rows="4" required></textarea>
          </div>
        </form>
        <!-- Thông báo gửi thành công -->
        <div class="alert alert-success d-none" id="supportSuccess">
            <i class="fa-solid fa-circle-check me-2"></i>
            Yêu cầu đã được gửi đi và sẽ được hỗ trợ sớm nhất.
        </div>

      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button class="btn btn-success" form="supportForm">
          <i class="fa-solid fa-paper-plane me-1"></i>Gửi
        </button>
      </div>

    </div>
  </div>
</div>

</body>
<script src="<?= $base_url ?>/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('supportForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const successAlert = document.getElementById('supportSuccess');

    // Hiện thông báo
    successAlert.classList.remove('d-none');

    // Reset form
    this.reset();

    // Tự đóng modal sau 2.5s
    setTimeout(() => {
        const modalEl = document.getElementById('supportModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        // Ẩn thông báo cho lần mở sau
        successAlert.classList.add('d-none');
    }, 2500);
});
</script>


<script src="<?= $base_url ?>/js/header.js"></script>
</html>
