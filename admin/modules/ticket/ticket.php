<?php
require_once __DIR__ . '/../../../config/config.php';

// ---- CẤU HÌNH PHÂN TRANG ----
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ---- LẤY DỮ LIỆU VÉ (JOIN 3 BẢNG) ----
$sql = "
SELECT 
    tt.id,
    t.name AS tour_name,
    td.departure_place,
    tt.date,
    tt.title,
    tt.tour_img,
    tt.price,
    tt.old_price,
    tt.discount_percent,
    tt.top_pick
FROM tour_ticket tt
JOIN tour_details td ON tt.tour_detail_id = td.id
JOIN tours t ON td.tour_id = t.id
ORDER BY tt.id DESC
LIMIT $limit OFFSET $offset
";
$result = $conn->query($sql);

// ---- ĐẾM TỔNG ----
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM tour_ticket");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Quản lý Vé Tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .ticket-img {
      width: 80px;
      height: 60px;
      object-fit: cover;
      border-radius: 6px;
    }
  </style>
</head>
<body>
<div class="container my-5">
  <div class="d-flex align-items-center mb-4">
      <h2>🎟️ Danh sách vé tour</h2>
       <a href="../../index.php#tours" class="btn btn-light me-3" style="margin-left: auto;">&larr; Quay lại</a>
      <a href="createTicket.php" class="btn btn-primary" style="margin-left: 10px;">+ Thêm vé mới</a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr class="text-center">
          <th>ID</th>
          <th>Tên tour</th>
          <th>Điểm khởi hành</th>
          <th>Ngày</th>
          <th>Tiêu đề vé</th>
          <th>Ảnh</th>
          <th>Giá</th>
          <th>Giá cũ</th>
          <th>Giảm (%)</th>
          <th>Nổi bật</th>
          <th width="150">Hành động</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="text-center"><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['tour_name']); ?></td>
            <td><?= htmlspecialchars($row['departure_place']); ?></td>
            <td class="text-center"><?= htmlspecialchars($row['date']); ?></td>
            <td><?= htmlspecialchars($row['title']); ?></td>
            <td class="text-center">
              <?php if (!empty($row['tour_img'])): ?>
                <img src="<?= $base_url . $row['tour_img']; ?>" class="ticket-img" alt="Ảnh vé">
              <?php else: ?>
                <span class="text-muted">No image</span>
              <?php endif; ?>
            </td>
            <td class="text-end"><?= number_format($row['price']); ?>₫</td>
            <td class="text-end text-muted"><?= number_format($row['old_price']); ?>₫</td>
            <td class="text-center"><?= $row['discount_percent']; ?>%</td>
            <td class="text-center">
              <?= $row['top_pick'] ? '<span class="badge bg-success">⭐</span>' : ''; ?>
            </td>
            <td class="text-center">
              <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
              <a href="delete.php?id=<?= $row['id']; ?>"
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Bạn có chắc muốn xóa vé này?');">Xóa</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="11" class="text-center">Chưa có vé nào.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- PHÂN TRANG -->
  <?php if ($totalPages > 1): ?>
  <nav>
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page - 1; ?>#ticket">&laquo;</a></li>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
          <a class="page-link" href="?page=<?= $i; ?>#ticket"><?= $i; ?></a>
        </li>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page + 1; ?>#ticket">&raquo;</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>
</div>
</body>
</html>
