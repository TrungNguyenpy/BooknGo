<?php
require_once __DIR__ . '/../../../config/config.php';

// Cấu hình phân trang
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Lấy danh sách tuyến bay
$sql = "SELECT * FROM flights ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Đếm tổng số bản ghi
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM flights");
$totalRow = $totalResult->fetch_assoc();
$totalPages = ceil($totalRow['total'] / $limit);
?>

<div id="view-flights" class="view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>✈️ Quản lý Tuyến bay</h2>
        <a href="modules/flights/flight/create.php" class="btn btn-primary">+ Thêm tuyến bay</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Điểm đi</th>
                    <th>Điểm đến</th>
                    <th>Ảnh</th>
                    <th>Giá mới</th>
                    <th width="180">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['departure']); ?></td>
                            <td><?= htmlspecialchars($row['arrival']); ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?= $base_url . htmlspecialchars($row['image']); ?>" width="70">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                          
                            <td><?= number_format($row['price_new']); ?> ₫</td>
                            <td class="text-center">
                                <a href="modules/flights/view_details.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm">🔍 Xem chi tiết</a>
                                <a href="modules/flights/flight/edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                                <a href="modules/flights/flight/delete.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Xóa tuyến bay này?');">🗑️ Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Chưa có dữ liệu tuyến bay</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
