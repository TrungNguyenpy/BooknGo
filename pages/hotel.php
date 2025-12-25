
<?php 
    include __DIR__ . '/../config/config.php';

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>
<body>
     <!-- Lựa chọn khách sạn -->
     <div class="hotels">
       
       <h3 class="mb-4 fw-bold">🏨Nhiều lựa chọn khách sạn</h3>
        <!-- Thanh tìm kiếm -->
        <div class="container my-4">
           <form action="" method="GET" class="d-flex search_hotels">
               <input type="text" name="city" list="cities" value="<?= htmlspecialchars($_GET['city'] ?? '') ?>" class="form-control me-2" placeholder="Nhập tên thành phố...">
                       <datalist id="cities">
                           <?php
                           $result = $conn->query("SELECT DISTINCT location FROM hotels");
                           while ($row = $result->fetch_assoc()) {
                               echo '<option value="' . htmlspecialchars($row['location']) . '">';
                           }
                           ?>
                       </datalist>
                   <button type="submit" class="btn btn-primary btn_search">Tìm kiếm</button>
           </form>
       </div> 
       <div class="row gy-4 align-items-stretch" style="margin-left: 45px;" >
           <?php
               $city = isset($_GET['city']) ? trim($_GET['city']) : '';
               if (!empty($city)) {
                   $sql = "SELECT * FROM hotels WHERE location LIKE '%$city%'";
               } else {
                   $sql = "SELECT * FROM hotels";
               }
               $result = $conn->query($sql);
               
               if ($result->num_rows > 0):
                   while($row = $result->fetch_assoc()):
           ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 hotel-card" style="padding: 0px;">
                <div class="card position-relative shadow-sm border-0 rounded-4 overflow-hidden" style="height: 100%">
                    <div class="position-relative">
                    <img src="<?= $base_url . htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                        <span class="badge bg-dark position-absolute top-0 start-0 m-2 rounded-pill px-2 py-1">
                            📍 <?= $row['location'] ?>
                        </span>

                        <?php if ($row['discount_percent'] > 0): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 rounded-pill px-2 py-1 fw-bold">
                            Tiết kiệm <?= $row['discount_percent'] ?>%
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="bg-primary text-white text-center py-1 fw-bold">
                        <?= $row['label'] ?>
                    </div>

                    <div class="card-body px-3 py-2">
                        <h6 class="card-title fw-bold mb-1"><?= $row['name'] ?></h6>
                        <div class="text-muted small mb-1">⭐ <?= $row['rating'] ?>/10 (<?= $row['reviews'] ?>)</div>
                        <div class="text-decoration-line-through text-secondary small"><?= number_format($row['price_old']) ?> VND</div>
                        <div class="text-danger fw-bold"><?= number_format($row['price_new']) ?> VND</div>
                        <div class="text-muted small">Chưa bao gồm thuế và phí</div>
                    </div>

                    <!-- Link phủ toàn bộ card -->
                    <a href="<?= $base_url ?>/pageDetail/hotel-detail.php?id=<?= $row['id'] ?>" class="stretched-link"></a>

                </div>
            </div>

           <?php
               endwhile;
           else:
               echo "<p>Không có khách sạn nào phù hợp!</p>";
           endif;  
           ?>
       </div >
                       <div class="text-end mt-3">
                           <button id="hotel-next-btn" class="btn btn-outline-primary rounded-pill px-4 fw-bold">→ Xem thêm</button>
                       </div>
                       

                  
                             
       </div>    
</body>
</html>