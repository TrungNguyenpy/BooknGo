<?php
require_once __DIR__ . '/../../../config/config.php';

// ---- CẤU HÌNH PHÂN TRANG ----
$limit = 10; // số bản ghi mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Tính vị trí bắt đầu
$offset = ($page - 1) * $limit;

// ---- LẤY DỮ LIỆU HOTELS (GIỚI HẠN 10 DÒNG) ----
// Đổi DESC thành ASC để ID nhỏ ở trên (hoặc dùng id ASC)
$sql = "SELECT * FROM hotels ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// ---- ĐẾM TỔNG SỐ BẢN GHI ----
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM hotels");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<!-- VIEW: HOTELS -->
<div id="view-hotels" class="view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Hotels</h2>
        <a href="modules/hotels/create.php" class="btn btn-primary">+ Thêm khách sạn</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>City</th>
                    <th>Tên</th>
                    <th>Giá cũ</th>
                    <th>Giá mới</th>
                    <th>Rating</th>
                    <th>Reviews</th>
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
                            <td><?= htmlspecialchars($row['location']); ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= number_format($row['price_old']); ?></td>
                            <td><?= number_format($row['price_new']); ?></td>
                            <td><?= $row['rating']; ?></td>
                            <td><?= $row['reviews']; ?></td>
                            <td><?= htmlspecialchars($row['label']); ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?= $base_url; ?><?= htmlspecialchars($row['image']); ?>" width="70">

                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="modules/hotels/edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="modules/hotels/delete.php?id=<?= $row['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa?');">
                                   Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Chưa có dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <!-- Nút Previous -->
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1; ?>#hotels">&laquo;</a>
                    </li>
                <?php endif; ?>

                <!-- Các trang -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>#hotels"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Nút Next -->
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1; ?>#hotels">&raquo;</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>


</div>
