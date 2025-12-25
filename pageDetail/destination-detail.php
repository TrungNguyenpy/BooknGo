<?php
include __DIR__ . '/../config/config.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['city_id'])) {
  $city_id = mysqli_real_escape_string($conn, $_GET['city_id']); 

  // Lấy thông tin thành phố
  $sql = "SELECT * FROM cities WHERE city_id = '$city_id' LIMIT 1";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
      $city = $result->fetch_assoc();
  } else {
      die("Không tìm thấy thông tin thành phố!");
  }

  // Lấy dữ liệu liên quan
  $hotels = $conn->query("SELECT * FROM hotels WHERE city_id = '$city_id'");
  $places = $conn->query("SELECT * FROM places WHERE city_id = '$city_id'");
  $foods  = $conn->query("SELECT * FROM foods WHERE city_id = '$city_id'");
  $events = $conn->query("SELECT * FROM events WHERE city_id = '$city_id' ORDER BY event_date ASC");

} else {
  die("Thiếu tham số city_id!");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi tiết điểm đến - <?php echo htmlspecialchars($city['name']); ?></title>
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/destination-detail.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="header">
    <?php include '../includes/header.php'; ?>
</div>

<!-- Hero -->
<div class="theme" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('../img/<?php echo basename($city['hero_image']); ?>') center/cover no-repeat;">
  <div>
    <h2>Khám phá <?= htmlspecialchars($city['name']); ?></h2>
    <p><?= htmlspecialchars($city['slogan']); ?></p>
    <a href="#overview" class="btn">Khám phá ngay</a>
  </div>
</div>

<div class="container">
  <!-- Tổng quan -->
  <section id="overview">
    <h2>Tổng quan</h2>
    <p style="color:#333;"><?php echo htmlspecialchars($city['description']); ?></p>
  </section>

  <!-- Khách sạn -->
  <section id="hotels">
    <h2>Khách sạn nổi bật</h2>
    <div class="hotel-scroll">
      <?php 
      if ($hotels && $hotels->num_rows > 0) {
          while ($hotel = $hotels->fetch_assoc()) {
            echo '
            <a href="hotel-detail.php?id='.$hotel['id'].'" class="hotel-link">
              <div class="hotel-card">
                  <img src="'.$base_url.htmlspecialchars($hotel['image'] ?? 'img/default.png').'" 
                      alt="'.htmlspecialchars($hotel['name']).'">
                  <div style="padding:15px">
                    <h3>'.htmlspecialchars($hotel['name']).'</h3>
                    <p>'.htmlspecialchars($hotel['description'] ?? '').'</p>
                    <p>⭐ '.htmlspecialchars($hotel['rating'] ?? '0').'</p>
                    <p>
                      <span style="text-decoration: line-through; color:#888;">
                        '.htmlspecialchars($hotel['price_old'] ?? '').'
                      </span>
                      <span style="color:red; font-weight:bold;">
                        '.htmlspecialchars($hotel['price_new'] ?? '').'
                      </span>
                    </p>
                  </div>
              </div>
            </a>';

          }
      } else {
          echo "<p>Chưa có khách sạn nào.</p>";
      } ?>
    </div>
</section>


  <!-- Tham quan -->
  <section id="places" class="places-section">
    <h2>Điểm tham quan nổi bật</h2>
    <div class="places-grid">
      <?php 
      if ($places && $places->num_rows > 0) {
        while ($place = $places->fetch_assoc()) {
          echo '<div class="place-card">
                  <img src="'.$base_url.htmlspecialchars($place['image_url']).'" alt="'.htmlspecialchars($place['name']).'">
                  <div class="overlay">'.htmlspecialchars($place['name']).'</div>
                </div>';
        }
      } else {
        echo "<p>Chưa có dữ liệu điểm tham quan.</p>";
      }
      ?>
    </div>
  </section>

  <!-- Ẩm thực -->
  <section id="food">
    <h2>Ẩm thực đặc sắc</h2>
    <?php 
    if ($foods && $foods->num_rows > 0) {
      while ($food = $foods->fetch_assoc()) {
        echo '<div class="food-item">
                <img src="'.$base_url.htmlspecialchars($food['image_url']).'" alt="">
                <div class="food-text">
                  <h3>'.htmlspecialchars($food['name']).'</h3>
                  <p>'.htmlspecialchars($food['description']).'</p>
                </div>
              </div>';
      }
    } else {
      echo "<p>Chưa có dữ liệu ẩm thực.</p>";
    }
    ?>
  </section>

  <!-- Sự kiện -->
  <section id="events">
    <h2>Sự kiện sắp diễn ra</h2>
    <div class="timeline">
      <?php 
      if ($events && $events->num_rows > 0) {
        while ($event = $events->fetch_assoc()) {
          echo '<a href="'.htmlspecialchars($event['link']).'" target="_blank" class="timeline-item">
                  <div class="date">'.date("d/m", strtotime($event['event_date'])).'</div>
                  <div class="content">
                    <h3>'.htmlspecialchars($event['name']).'</h3>
                    <p>'.htmlspecialchars($event['description']).'</p>
                  </div>
                </a>';
        }
      } else {
        echo "<p>Chưa có sự kiện nào.</p>";
      }
      ?>
    </div>
  </section>
</div>

<div class="footer">
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
<?php $conn->close(); ?>
