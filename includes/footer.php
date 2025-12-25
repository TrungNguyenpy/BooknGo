<?php 
    include __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Footer BooknGo</title>
  <link rel="stylesheet" href="<?= $base_url ?>/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $base_url ?>/fontawesome-free-6.6.0-web/css/all.min.css">
  <link rel="stylesheet" href="<?= $base_url ?>/css/main.css?v=<?= time(); ?>">
</head>
<body>

  <footer class="footer-wrapper mt-5">
    <div class="container">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
          
        <!-- Thanh toán -->
        <div class="col mb-3">
          <h5>Thanh toán an toàn</h5>
          <div class="d-flex gap-2 flex-wrap pay_group">
            <img src="<?= $base_url ?>/img/visa.png" alt="Visa" width="60">
            <img src="<?= $base_url ?>/img/mastercard.png" alt="MasterCard" width="60">
            <img src="<?= $base_url ?>/img/momo.png" alt="Momo" width="60">
            <img src="<?= $base_url ?>/img/vnpay.png" alt="VNPAY" width="60">
            <img src="<?= $base_url ?>/img/myqr.png" alt="QR Code" width="60">
          </div>
        </div>

        <!-- Hướng dẫn -->
        <div class="col mb-3 guide_group">
          <h5>Hỗ trợ</h5>
          <ul class="nav flex-column">
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Cách đặt chỗ</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Liên hệ chúng tôi</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Trợ giúp</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Tuyển dụng</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Về chúng tôi</a></li>
          </ul>
          <div class="mt-3 network">
            <h5>Theo dõi chúng tôi trên</h5>
            <a href="#" class="me-3"><i class="fab fa-facebook fa-lg"></i></a>
            <a href="#" class="me-3"><i class="fab fa-instagram fa-lg"></i></a>
            <a href="#" class="me-3"><i class="fab fa-tiktok fa-lg"></i></a>
            <a href="#"><i class="fab fa-youtube fa-lg"></i></a>
          </div>
        </div>

        <!-- Sản phẩm -->
        <div class="col mb-3 product_group">
          <h5>Sản phẩm</h5>
          <ul class="nav flex-column">
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Khách sạn</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Vé máy bay</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Vé xe khách</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Đưa đón sân bay</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Du thuyền</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Cho thuê xe</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Biệt thự</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Hoạt động & Vui chơi</a></li>
          </ul>
        </div>

        <!-- Khác -->
        <div class="col mb-3 other_group">
          <h5>Thông tin khác</h5>
          <ul class="nav flex-column">
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">BooknGo Blog</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Chính sách quyền riêng</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Điều khoản & Điều kiện</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Đăng ký nơi nghỉ</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link p-0 text-muted">Quy chế hoạt động</a></li>
          </ul>

          <div class="dowload_app mt-3">
            <h5>Tải ứng dụng BooknGo</h5>
            <div class="d-flex align-items-center gap-3 mt-2">
              <img src="<?= $base_url ?>/img/myqr.png" alt="QR Code" width="90">
              <div class="d-flex flex-column gap-2">
                <a href="#"><img src="<?= $base_url ?>/img/chplay.png" alt="Google Play" width="50"></a>
                <a href="#"><img src="<?= $base_url ?>/img/appstore.png" alt="App Store" width="50"></a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </footer>

</body>
</html>
