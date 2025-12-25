<?php
require_once __DIR__ . '/../../../config/config.php';

// Kiểm tra id (flights.id)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy ID chuyến bay (flights.id).");
}
$flightId = (int) $_GET['id'];

// Lấy thông tin tuyến (flights)
$sqlFlight = "SELECT id, flight_name, departure, arrival, airline, image, price_old, price_new FROM flights WHERE id = ?";
$stmtFlight = $conn->prepare($sqlFlight);
$stmtFlight->bind_param("i", $flightId);
$stmtFlight->execute();
$resultFlight = $stmtFlight->get_result();
if ($resultFlight->num_rows === 0) {
    die("Không tìm thấy tuyến bay có id = $flightId");
}
$flight = $resultFlight->fetch_assoc();

// Lấy danh sách flight_details theo flight_id = flights.id
$sqlDetails = "SELECT id, departure_time, arrival_time, aircraft, baggage_info, transit_info, description, price 
               FROM flight_details WHERE flight_id = ? ORDER BY departure_time ASC";
$stmtDetails = $conn->prepare($sqlDetails);
$stmtDetails->bind_param("i", $flightId);
$stmtDetails->execute();
$resultDetails = $stmtDetails->get_result();
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Chi tiết tuyến: <?= htmlspecialchars($flight['departure']) ?> → <?= htmlspecialchars($flight['arrival']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f5f7fa; }
    .card { border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
    .flight-route { font-weight:600; color:#0d6efd; }
    .small-muted { color:#6c757d; font-size:.9rem; }
    .img-thumb { width:120px; border-radius:8px; object-fit:cover; }
    thead.table-dark th { background: #0d6efd; color:#fff; }
  </style>
</head>
<body>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">✈️ Tuyến: <span class="flight-route"><?= htmlspecialchars($flight['departure']) ?> → <?= htmlspecialchars($flight['arrival']) ?></span></h3>
    <div>
      <a href="../../index.php#view-flights" class="btn btn-secondary btn-sm">⬅ Quay lại</a>
      
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-8">
      <div class="card p-3">
        <div class="mb-2"><strong>Tên chuyến (flight_name):</strong> <?= htmlspecialchars($flight['flight_name']) ?></div>
        <div class="mb-2 small-muted"><strong>Hãng mặc định (airline):</strong> <?= htmlspecialchars($flight['airline']) ?></div>
        <div class="mb-2 small-muted"><strong>Giá cũ / mới:</strong> <?= number_format($flight['price_old'] ?? 0) ?> ₫ / <?= number_format($flight['price_new'] ?? 0) ?> ₫</div>
        <div class="small-muted"><strong>Mô tả ngắn:</strong> <?= htmlspecialchars($flight['flight_name']) ?> — tuyến <?= htmlspecialchars($flight['departure']) ?> → <?= htmlspecialchars($flight['arrival']) ?></div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 text-center">
        <?php if (!empty($flight['image'])): ?>
          <img src="<?= htmlspecialchars($base_url . $flight['image']) ?>" alt="flight image" class="img-thumb mb-2">
        <?php else: ?>
          <div class="border rounded p-4 text-muted">No image</div>
        <?php endif; ?>
        <div class="mt-2">
           <div class="mt-2">
            <a href="flights_details/create.php?id=<?= urlencode($flight['id']) ?>" 
                class="btn btn-primary btn-sm">
                + Thêm chuyến cụ thể
            </a>
            </div>



      </div>
    </div>
  </div>

  <div class="card p-3">
    <h5 class="mb-3">Danh sách các chuyến bay cụ thể (flight_details)</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Hãng (airline)</th>
            <th>Giờ đi</th>
            <th>Giờ đến</th>
            <th>Máy bay</th>
            <th>Hành lý</th>
            <th>Trung chuyển</th>
            <th>Giá</th>
            <th>Ghi chú</th>
            <th width="160">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($resultDetails && $resultDetails->num_rows > 0): ?>
            <?php while ($d = $resultDetails->fetch_assoc()): ?>
              <tr>
                <td><?= $d['id'] ?></td>
                <td><?= htmlspecialchars($flight['airline']) /* nếu airline riêng ở detail, đổi thành $d['airline'] */ ?></td>
                <td><?= htmlspecialchars($d['departure_time']) ?></td>
                <td><?= htmlspecialchars($d['arrival_time']) ?></td>
                <td><?= htmlspecialchars($d['aircraft']) ?></td>
                <td><?= htmlspecialchars($d['baggage_info']) ?></td>
                <td><?= htmlspecialchars($d['transit_info']) ?></td>
                <td><?= number_format($d['price'],0,',','.') ?> ₫</td>
                
                <td><span class="small-muted"><?= htmlspecialchars($d['description']) ?></span></td>
                <td>
               

               <a href="flights_details/edit.php?id=<?= $d['id'] ?>&flight_id=<?= $flight['id'] ?>" class="btn btn-sm btn-warning">✏️ Sửa</a>
                 <a href="flights_details/delete.php?id=<?= $d['id'] ?>&flight_id=<?= $flight['id'] ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa chuyến bay này không?');">
                    🗑️ Xóa
                    </a>

                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" class="text-center text-muted">Chưa có chuyến bay cụ thể nào cho tuyến này.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>


</html>
