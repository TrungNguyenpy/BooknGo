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
     <!-- Hoạt động du lịch -->
     <div class="tour">
            <div class="tour row gy-4 align-items-stretch">
                <h3 class=" fw-bold">Hoạt động du lịch</h3>
                    <h4 style="margin: 0; font-size: 17px; color:#a39999">Khám phá các điểm đến hàng đầu tại Việt Nam</h4>
                <div class="select_tour">
                    <button class="btn btn_select_tour btn-primary">Điểm vui chơi</button>
                    <button class="btn btn_select_tour">Tour du lịch</button>
                    <button class="btn btn_select_tour">Di tích lịch sử</button>
                    <button class="btn btn_select_tour">Bãi biển</button>
             
                </div>
                
                <div class="row gy-2 align-items-stretch" style="margin-left: 30px;">
                <?php
                            $sql = "SELECT * FROM tours"; 
                            $result = $conn->query($sql);
                            
                        ?>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($tour = $result->fetch_assoc()): ?>
                            <div class="tour-card col-12 col-sm-6 col-md-4 col-lg-3 ">
                                <div class="position-relative shadow-sm border-0 rounded-4 overflow-hidden " style=" width: 100%; height: 100%;">
                                    <div class="position-relative">
                                        <img src="<?= $base_url . htmlspecialchars ($tour['image'] )?>" class="card-img-top" alt="<?= htmlspecialchars($tour['name']) ?>">
                                        
                                        <span class="badge bg-dark position-absolute top-0 start-0 m-2 rounded-pill px-2 py-1">
                                            📍 <?= $tour['location'] ?>
                                        </span>
                                    </div>
                                    <div class="bg-success text-white text-center py-1 fw-bold">
                                        <?= $tour['label'] ?>
                                    </div>
                                    <div class="card-body px-3 py-2">
                                        <h6 class="card-title fw-bold mb-1" style="font-size: 22px;"><?= $tour['name'] ?></h6>
                                         <div class="text-muted small">Giá vé chỉ từ /người</div>
                                      
                                        <div class="text-danger fw-bold" style="margin-top: 10px;">
                                            <?= number_format($tour['price_old']) ?> VND
                                        </div>
                                       
                                    </div>

                                    <a href="<?= $base_url ?>/pageDetail/tour-detail.php?id=<?= $tour['id'] ?>" class="stretched-link"></a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Không có tour nào được tìm thấy.</p>
                    <?php endif; 
              
                    ?>
                </div>
                    <div class="text-end mt-3">
                        <button id="tour-next-btn" class="btn btn-outline-primary rounded-pill px-4 fw-bold">→ Xem thêm</button>
                    </div>

                  
            </div>
        </div>


</body>
</html>