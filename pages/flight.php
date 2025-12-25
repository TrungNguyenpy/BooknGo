
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
    <!-- Đặt vé máy bay -->
<div class="flight">
    <div class="flight row gy-4 align-items-stretch ">
        <h3 class=" fw-bold">✈️ Đặt vé máy bay</h3>
        <h4 style="margin: 0; font-size: 17px; color:#a39999">Nhanh chóng, tiện lợi, dễ dàng đặt vé</h4>
        <div class="select_flight mb-3">
            <button class="btn btn_select_flight btn-primary">Chuyến bay nội địa</button>
            <button class="btn btn_select_flight">Chuyến bay quốc tế</button>
            <button class="btn btn_select_flight">Khuyến mãi</button>
        </div>

        <div class="row align-items-stretch" style="margin-left: 45px;">
            <?php
                $sql = "SELECT * FROM flights"; 
                $result = $conn->query($sql);
            ?>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($flight = $result->fetch_assoc()): ?>
                    <div class=" flight-card col-12 col-sm-6 col-md-4 col-lg-3" style="margin-right: 15px; padding: 0;">
                        <div class="position-relative shadow-sm border-0 rounded-4 overflow-hidden" style="width: 100%; height: 100%;">
                            <div class="position-relative">
                                <img src="<?= $base_url . htmlspecialchars ($flight['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($flight['flight_name']) ?>">
                                
                                <span class="badge bg-dark position-absolute top-0 start-0 m-2 rounded-pill px-2 py-1">
                                    ✈️ <?= $flight['departure'] ?> → <?= $flight['arrival'] ?>
                                </span>
                            </div>
                            <div class="bg-info text-white text-center py-1 fw-bold">
                                <?= $flight['airline'] ?>
                            </div>
                            <div class="card-body px-3 py-2">
                                <h6 class="card-title fw-bold mb-1">    <?= htmlspecialchars($flight['departure']) ?> → <?= htmlspecialchars($flight['arrival']) ?></h6>
                               
                                <div class="text-danger fw-bold">
                                    <?= number_format($flight['price_new']) ?> VND
                                </div>
                                <div class="text-muted small">Bao gồm thuế & phí</div>
                            </div>
                                    <!-- Link phủ toàn bộ card -->
                      
                            <a href="<?= $base_url ?>/pageDetail/flight-detail.php?id=<?= $flight['id'] ?>" class="stretched-link"></a>

                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không có chuyến bay nào được tìm thấy.</p>
            <?php endif; ?>
        </div>

        <div class="text-end mt-3">
            <button id="flight-next-btn" class="btn btn-outline-primary rounded-pill px-4 fw-bold">→ Xem thêm</button>
        </div>

      
    </div>
</div>

</body>
</html>