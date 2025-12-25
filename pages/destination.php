<?php 
include __DIR__ . '/../config/config.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Truy vấn dữ liệu
$sql = "SELECT * FROM destinations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Điểm đến thịnh hành</title>
</head>
<body>
<div class="container my-4">
    <h4 class="fw-bold">Điểm đến đang thịnh hành</h4>
    <p class="text-muted">Cẩm nang du lịch Việt Nam</p>
    <div class="row g-3">
    <?php
    if ($result && $result->num_rows > 0) {
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $i++;
            $city_id = $row["city_id"]; // giữ nguyên chuỗi

            // 2 ảnh đầu -> col-md-6
            if ($i <= 2) {
                echo '
                <div class="col-md-6">
                  <div class="destination-card">
                  <a href="pageDetail/destination-detail.php?city_id='.urlencode($city_id).'">
                      <img src="'.$base_url . htmlspecialchars($row["image_url"]).'" alt="'.htmlspecialchars($row["name"]).'">
                    </a>
                    <h5>
                    <a href="pageDetail/destination-detail.php?city_id='.urlencode($city_id).'">

                        '.htmlspecialchars($row["name"]).'
                      </a>
                      <img src="'.$base_url . htmlspecialchars($row["country_flag"]).'" alt="VN">
                    </h5>
                  </div>
                </div>';
            } else {
                // 3 ảnh sau -> col-md-4
                echo '
                <div class="col-md-4">
                  <div class="destination-card">
                  <a href="pageDetail/destination-detail.php?city_id='.urlencode($city_id).'">
                      <img src="'.$base_url . htmlspecialchars($row["image_url"]).'" alt="'.htmlspecialchars($row["name"]).'">
                    </a>
                    <h5>
                    <a href="pageDetail/destination-detail.php?city_id='.urlencode($city_id).'">

                        '.htmlspecialchars($row["name"]).'
                      </a>
                      <img src="'.$base_url . htmlspecialchars($row["country_flag"]).'" alt="VN">
                    </h5>
                  </div>
                </div>';
            }
        }
    } else {
        echo "<p>Chưa có dữ liệu điểm đến.</p>";
    }
    ?>
    </div>
</div>
</body>
</html>
