<?php
require_once __DIR__ . '/../../../config/config.php';

// ---- CẤU HÌNH PHÂN TRANG ----
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// ---- LẤY DỮ LIỆU TOUR (JOIN CÁC BẢNG LIÊN QUAN) ----
// Mỗi tour có thể có nhiều tour_details và tour_ticket, 
// ta chỉ hiển thị thông tin tổng quan, 1 dòng mỗi tour (có thể sửa theo nhu cầu)
$sql = "
SELECT 
    t.id,
    t.name,
    t.location,
    t.label,
    t.price_old,
    t.reviews,
    td.departure_place,
    td.departure_schedule,
    COALESCE(tt.tour_img, t.image) AS image,
    COALESCE(tt.discount_percent, 0) AS discount_percent
FROM tours AS t
LEFT JOIN tour_details AS td ON t.id = td.tour_id
LEFT JOIN tour_ticket AS tt 
    ON tt.id = (
        SELECT MIN(tt2.id)
        FROM tour_ticket AS tt2
        WHERE tt2.tour_detail_id = td.id
    )
ORDER BY t.id ASC
LIMIT $limit OFFSET $offset
";


$result = $conn->query($sql);

// ---- ĐẾM TỔNG SỐ BẢN GHI ----
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM tours");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<!-- VIEW: TOURS -->
<div id="view-tours" class="view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Tour du lịch</h2>
        <a href="modules/tours/createTour.php" class="btn btn-primary" style="margin-left: auto;">+ Thêm tour mới</a>
        <a href="modules/ticket/ticket.php" class="btn btn-primary" style="margin-left: 10px;">+ Thêm các loại vé mới</a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên tour</th>
                    <th>Địa điểm</th>
                    <th>Điểm khởi hành</th>
                    <th>Lịch khởi hành</th>
                    <th>Giá</th>
                    <th>Đánh giá</th>
                    <th>Label</th>
                    <th>Ảnh</th>
                    <th width="150">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['location']); ?></td>
                            <td><?= htmlspecialchars($row['departure_place']); ?></td>
                            <td><?= htmlspecialchars($row['departure_schedule']); ?></td>
                            <td><?= number_format($row['price_old']); ?></td>
                            <td><?= htmlspecialchars($row['reviews']); ?></td>
                            <td><?= htmlspecialchars($row['label']); ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?= $base_url; ?><?= htmlspecialchars($row['image']); ?>" width="70">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="modules/tours/edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="modules/tours/delete.php?id=<?= $row['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa tour này?');">
                                   Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center">Chưa có dữ liệu tour</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1; ?>#tours">&laquo;</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>#tours"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1; ?>#tours">&raquo;</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

</div>
